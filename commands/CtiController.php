<?php

namespace app\commands;

use app\models\SecurityEvents;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use ZMQ;
use ZMQContext;
use ZMQSocketException;
use yii\httpclient\Client;
use Symfony\Component\Yaml\Yaml;

require '/var/www/html/secmon/vendor/autoload.php';


class CtiController extends Controller
{

	public function actionIndex()
	{

		$temp_config = $this->openNonBlockingStream("/var/www/html/secmon/config/aggregator_config.ini");
		$save_to_db = 0;
		$module_loaded = false;			#variable used for reading line after CTI module in config file
		$next_module = "correlator";
		if ($temp_config) {
			while (($line = fgets($temp_config)) !== false) {
				if ($module_loaded == true) {
					$parts = explode(":", $line);
					$next_module = strtolower(trim($parts[0]));
					$module_loaded = false;
				}

				if (strpos($line, "CTI:") !== FALSE) {
					$parts = explode(":", $line);
					$port = trim($parts[1]);
					$module_loaded = true;
				}
			}
		} else {
			throw new Exception('Could not open a config file');
		}

		$yaml_secmon = file_get_contents('/var/www/html/secmon/config/secmon_config.yaml');
		$yaml_secmon_data = Yaml::parse($yaml_secmon);
		$db_config = $yaml_secmon_data["DATABASE"];
		
		if ($db_config) {
			if ($db_config["host"] !== FALSE) {
				$host = $db_config["host"];
			}
			if ($db_config["database"] !== FALSE) {
				$database = $db_config["database"];
			}
			if ($db_config["user"] !== FALSE) {
				$user = $db_config["user"];
			}
			if ($db_config["password"] !== FALSE) {
				$password = $db_config["password"];
			}
		} else {
			throw new Exception('Not all arguments were specified');
		}

		fclose($temp_config);
		$temp_config = escapeshellarg("/var/www/html/secmon/config/aggregator_config.ini");
		$last_line = `tail -n 1 $temp_config`; 		#get last line of temp file

		if (strpos($last_line, "CTI:") !== FALSE) {
			$save_to_db = 1;
		}

		if (!is_numeric($port)) {
			throw new Exception('One of ports is not a numeric value');
		}

		$zmq = new ZMQContext();
		$recSocket = $zmq->getSocket(ZMQ::SOCKET_PULL);
		$recSocket->bind("tcp://*:" . $port);

		$sendSocket = $zmq->getSocket(ZMQ::SOCKET_PUSH);
		$sendSocket->connect("tcp://secmon_" . $next_module . ":" . $port);

		$nerd_auth = getenv("NERD_API_KEY");
		if (empty($nerd_auth)) {
			Yii::info("No NERD API authorization key" . PHP_EOL);
		}
		$crowd_auth = getenv("CROWD_API_KEY");
		if (empty($crowd_auth)) {
			Yii::info("No CrowdSec API authorization key" . PHP_EOL);
		}

		date_default_timezone_set("Europe/Bratislava");
		echo "[" . date("Y-m-d H:i:s") . "] CTI module started!" . PHP_EOL;

		$client = new Client(['responseConfig' => [
			'format' => Client::FORMAT_JSON
		]]);

		$json_cti = file_get_contents('/var/www/html/secmon/config/cti_config.json');
		$json_cti_data = json_decode($json_cti, true);
		$api_time_validity = $json_cti_data["api_validity"];
		$file_time_validity = $json_cti_data["file_validity"];
		$whitelist = $json_cti_data["whitelist"];

		while (true) {
			$srcIp = $dstIp = -1;
			$msg = $recSocket->recv(ZMQ::MODE_NOBLOCK);

			if (empty($msg)) {
				usleep(30000);
			} else {
				//print("GOT SOME MESSAGE:\n");
				$position1 = strpos($msg, "src=");
				if ($position1 != FALSE) {
					$position2 = strpos($msg, " ", $position1);
					$position3 = $position2 - $position1 - strlen("src=");
					$srcIp = substr($msg, $position1 + strlen("src="), $position3);
				}

				$position1 = strpos($msg, "dst=");
				if ($position1 != FALSE) {
					$position2 = strpos($msg, " ", $position1);
					$position3 = $position2 - $position1 - strlen("dst=");
					$dstIp = substr($msg, $position1 + strlen("dst="), $position3);
				}
				$connection = pg_connect("host=" . $host . " dbname=" . $database . " user=" . $user . " password=" . $password);
				
				if ($srcIp != -1) {
					//print("PROCESSING SRC\n");
					$src_cti_id = $this->processIp($srcIp, $whitelist, $connection, $client, $nerd_auth, $crowd_auth, $api_time_validity, $file_time_validity);
					$msg = str_replace("\n", "", $msg);
					$msg = $msg . " src_cti_id=" . strval($src_cti_id) . " ";
				}
				
				if ($dstIp != -1) {
					//print("PROCESSING DST\n");
					$dst_cti_id = $this->processIp($dstIp, $whitelist, $connection, $client, $nerd_auth, $crowd_auth, $api_time_validity, $file_time_validity);
					$msg = $msg . " dst_cti_id=" . strval($dst_cti_id) . " ";
				}

				//print("FINAL MESSAGE:\n");
				pg_close($connection);

				if ($save_to_db) {
					//print("SAVING TO DB\n");
					$event = SecurityEvents::extractCefFields($msg, 'normalized');
					if ($event->save()) {
						$sendSocket->send($event->id . ':' . $msg, ZMQ::MODE_NOBLOCK);
					}
				} else {
					//print("SOMEONE ELSE SAVING TO DB\n");
					$sendSocket->send($msg, ZMQ::MODE_NOBLOCK);
				}
			}
		}
	}

	function processIp($ip, $whitelist, $connection, $client, $nerd_auth, $crowd_auth, $api_time_validity, $file_time_validity)
	{
		if (in_array($ip, $whitelist)) {
			//print($ip . "is in whitelist");
			return "0";
		}

		if ($connection) {
			//print("Successfuly connected to DB\n");
			$main = $this->selectFromPairingTable($ip, $connection);
			if ($main == null) {
				//print("IP was not recorded before\n");
				$main[0] = $this->recordToPairingTable($ip, $connection);
			}

			############# NERD #############
			if (($main[2] ?? null) != null) {
				//print("Pairing table has NERD table linked\n");
				$object = $this->selectFromNERDTable($main[2], $connection, $ip, $client, $nerd_auth, $api_time_validity);
			} else {
				//print("Pairing table hasn't NERD table linked\n");
				$object = $this->updateFromNERDapi($ip, $client, $nerd_auth);
				if ($object != null) {
					$nerd_id = $this->recordToNERDTable($object, $connection);
					$this->updatePairingTableNERD($connection, $main[0], $nerd_id);
					$main[2] = $nerd_id;
				}
			}

			############# CROWD #############
			if (($main[1] ?? null) != null) {
				//print("Pairing table has CROWD table linked\n");
				$object = $this->selectFromCROWDTable($main[1], $connection, $ip, $client, $crowd_auth, $api_time_validity);
			} else {
				//print("Pairing table hasn't CROWD table linked\n");
				$object = $this->updateFromCROWDapi($ip, $client, $crowd_auth);
				if ($object != null) {
					$crowd_id = $this->recordToCROWDTable($object, $connection);
					$this->updatePairingTableCROWD($connection, $main[0], $crowd_id);
					$main[1] = $crowd_id;
				}
			}
		} else {
			throw new Exception("Error while connecting to database!". PHP_EOL);
		}

		return $main[0];
	}

	function selectFromPairingTable($ip, $connection)
	{
		//print("selectFromPairingTable started\n");
		if ($ip != -1) {
			//print("IP is defined\n");
			$result = pg_query_params($connection, 'SELECT id, fk_crowdsec_id, fk_nerd_id from cti where ip = $1', array($ip));
			if (pg_num_rows($result) > 0) {
				//print("Found IP in DB\n");
				$row = pg_fetch_row($result);
				return $row;
			}

			//print("No IP in DB\n");
			return null;
		}
	}

	function recordToPairingTable($ip, $connection)
	{
		//print("recordToPairingTable started\n");
		if ($ip != -1) {
			//print("IP is defined\n");
			$result = pg_query_params($connection, 'INSERT INTO cti(ip) VALUES ($1) RETURNING id', array($ip));
			
			if ($result == false) {
				//print("Query failed\n");
				return -1;
			}
			//print("Successfuly inserted\n");
			$id = pg_fetch_row($result)[0];
			return $id;
		}
	}

	function selectFromNERDTable($id, $connection, $ip, $client, $nerd_auth, $api_time_validity)
	{
		//print("selectFromNERDTable started\n");
		if ($id != -1) {
			//print("ID is defined\n");
			$result = pg_query_params($connection, 'SELECT * from cti_nerd where id = $1', array($id));
			if (pg_num_rows($result) > 0) {
				//print("Found IP in DB\n");
				$row = pg_fetch_row($result);

				$object = new \stdClass();
				$object->nerd_timestamp = date('Y-m-d H:i:s', strtotime((string)$row[5]));
				// flag = 0, selected from DB, no update on main table needed
				$object->nerd = 0;

				$now = new \DateTime(date('Y-m-d H:i:s', strtotime('now')));
				$stamp = new \DateTime($object->nerd_timestamp);
				$interval = $now->diff($stamp);
				$hours = $interval->h + ($interval->days * 24);

				if ($hours > $api_time_validity) {
					//print("Record is old enough, needs refresh\n");
					$object = $this->updateFromNERDapi($ip, $client, $nerd_auth);
					if ($object) {
						$this->updateNERDTable($id, $object, $connection);
					}
				}
			}
		}
		return $object;
	}

	function updateNERDTable($id, $object, $connection)
	{
		//print("updateNERDTable started\n");
		$result = pg_query_params(
			$connection,
			'UPDATE cti_nerd SET 
				fmp=$1, blacklists=$2, rep=$3, last_checked_at=$4,
				as_id=$5, as_name=$6, ip_range=$7, ip_range_rep=$8, 
				events=$9, geo_city=$10, geo_country=$11, 
				hostname=$12, last_activity=$13, first_activity=$14
				WHERE id = $15',
			array(
				$object->fmp, $object->blacklists, $object->rep, $object->nerd_timestamp,
				$object->nerd_AS_id, $object->nerd_AS_name, $object->nerd_ip_range, $object->nerd_ip_range_rep,
				$object->events, $object->nerd_city, $object->nerd_country,
				$object->nerd_hostname, $object->nerd_last_activity, $object->nerd_first_activity,
				$id
			)
		);
		if ($result == false) {
			//print("Query failed\n");
		}
		//print("Successfuly updated\n");
	}

	function updateFromNERDapi($ip, $client, $nerd_auth)
	{
		//print("updateFromNERDapi started\n");
		$nerd_response = $client->createRequest()
			->setMethod('GET')
			->setUrl('https://nerd.cesnet.cz/nerd/api/v1/ip/' . (string)$ip . '/full')
			->addHeaders(['Authorization' => $nerd_auth])
			->send();
		if ($nerd_response->statusCode != 200) {
			//print("NERD API call failed\n");
			return null;
		}

		$object = new \stdClass();
		$object->fmp = $nerd_response->data["fmp"]["general"];
		$bl = array();
		foreach ($nerd_response->data["bl"] as $list) {
			array_push($bl, $list["name"]);
		}
		$object->blacklists = implode(", ", $bl);
		$object->blacklists = substr($object->blacklists, 0, 254);
		$object->rep = $nerd_response->data["rep"];
		$object->nerd_AS_id = $nerd_response->data["asn"][0]["_id"] ?? null;
		$object->nerd_AS_name = $nerd_response->data["asn"][0]["name"] ?? null;
		$object->nerd_ip_range = $nerd_response->data["bgppref"]["_id"];
		$object->nerd_ip_range_rep = $nerd_response->data["bgppref"]["rep"];

		$events_cat = array();
		foreach ($nerd_response->data["events"] as $list) {
			array_push($events_cat, $list["cat"]);
		}
		$object->events = implode(", ", array_unique($events_cat));
		$object->events = substr($object->events, 0, 254);
		$object->nerd_city = $nerd_response->data["geo"]["city"];
		$object->nerd_country = $nerd_response->data["geo"]["ctry"];
		$object->nerd_hostname = $nerd_response->data["hostname"];
		$object->nerd_last_activity = $nerd_response->data["last_activity"];
		$object->nerd_first_activity = $nerd_response->data["ts_added"];

		$object->nerd_timestamp = date('Y-m-d H:i:s', strtotime('now'));
		// flag = 1, update on main table needed, new data from API
		$object->nerd = 1;
		return $object;
	}

	function recordToNERDTable($object, $connection)
	{
		//print("recordToNERDTable started\n");
		$result = pg_query_params(
			$connection,
			'INSERT INTO cti_nerd(fmp, blacklists, rep, last_checked_at,
			as_id, as_name, ip_range, ip_range_rep, 
			events, geo_city, geo_country, 
			hostname, last_activity, first_activity) 
			VALUES ($1, $2, $3, $4, 
			$5, $6, $7, $8, 
			$9, $10, $11,
			$12, $13, $14) RETURNING id',
			array(
				$object->fmp, $object->blacklists, $object->rep, $object->nerd_timestamp,
				$object->nerd_AS_id, $object->nerd_AS_name, $object->nerd_ip_range, $object->nerd_ip_range_rep,
				$object->events, $object->nerd_city, $object->nerd_country,
				$object->nerd_hostname, $object->nerd_last_activity, $object->nerd_first_activity
			)
		);
		
		if ($result == false) {
			//print("Query failed\n");
			return -1;
		}
		$id = pg_fetch_row($result)[0];
		return $id;
	}

	function updatePairingTableNERD($connection, $main_id, $nerd_id)
	{
		//print("updatePairingTableNERD started\n");
		$result = pg_query_params($connection, 'UPDATE cti SET fk_nerd_id = $1 WHERE id = $2 RETURNING id', array($nerd_id, $main_id));

		if ($result == false) {
			//print("Query failed\n");
		}
	}

	function selectFromCROWDTable($id, $connection, $ip, $client, $crowd_auth, $api_time_validity)
	{
		//print("selectFromCROWDTable started\n");
		if ($id != -1) {
			//print("ID is defined\n");
			$result = pg_query_params($connection, 'SELECT * from cti_crowdsec where id = $1', array($id));
			if (pg_num_rows($result) > 0) {
				//print("Found IP in DB\n");
				$row = pg_fetch_row($result);

				$object = new \stdClass();
				$object->crowd_timestamp = date('Y-m-d H:i:s', strtotime((string)$row[5]));
				// flag = 0, selected from DB, no update on main table needed
				$object->crowd = 0;

				$now = new \DateTime(date('Y-m-d H:i:s', strtotime('now')));
				$stamp = new \DateTime($object->crowd_timestamp);
				$interval = $now->diff($stamp);
				$hours = $interval->h + ($interval->days * 24);

				if ($hours > $api_time_validity) {
					//print("Record is old enough, needs refresh\n");
					$object = $this->updateFromCROWDapi($ip, $client, $crowd_auth);
					if ($object) {
						$this->updateCROWDTable($id, $object, $connection);
					}
				}
			}
		}
		return $object;
	}

	function updateCROWDTable($id, $object, $connection)
	{
		//print("updateCROWDTable started\n");
		$result = pg_query_params(
			$connection,
			'UPDATE cti_crowdsec SET 
				behavior=$1, classification=$2, score_overall=$3, last_checked_at=$4,
				as_num=$5, as_name=$6, ip_range_24=$7, 
				ip_range_24_rep=$8, geo_city=$9, geo_country=$10,
				reverse_dns=$11, last_seen=$12, first_seen=$13, false_pos=$14
				WHERE id = $15',
			array(
				$object->behavior, $object->classification, $object->score_overall, $object->crowd_timestamp,
				$object->crowd_AS_id, $object->crowd_AS_name, $object->crowd_ip_range,
				$object->crowd_ip_range_rep, $object->crowd_city, $object->crowd_country,
				$object->crowd_reverse_dns, $object->crowd_last_seen, $object->crowd_first_seen, $object->false_pos,
				$id
			)
		);
		
		if ($result == false) {
			//print("Query failed\n");
		}
	}


	function updateFromCROWDapi($ip, $client, $crowd_auth)
	{
		//print("updateFromCROWDapi started\n");
		$crowd_response = $client->createRequest()
			->setMethod('GET')
			->setUrl('https://cti.api.crowdsec.net/v2/smoke/' . (string)$ip)
			->addHeaders(['x-api-key' => $crowd_auth])
			->send();

		if ($crowd_response->statusCode != 200) {
			//print("CROWD API call failed\n");
			return null;
		}

		$object = new \stdClass();
		$beh = array();
		foreach ($crowd_response->data["behaviors"] as $list) {
			array_push($beh, $list["label"]);
		}
		$object->behavior = implode(", ", $beh);
		$class = array();
		foreach ($crowd_response->data["classifications"]["classifications"] as $list) {
			array_push($class, $list["label"]);
		}
		$false_pos = array();
		foreach ($crowd_response->data["classifications"]["false_positives"] as $list) {
			array_push($false_pos, $list["label"]);
		}
		$object->classification = implode(", ", $class);
		$object->false_pos = implode(", ", $false_pos);
		$object->score_overall = $crowd_response->data["scores"]["overall"]["total"];
		$object->crowd_AS_id = $crowd_response->data["as_num"];
		$object->crowd_AS_name = $crowd_response->data["as_name"];
		$object->crowd_ip_range = $crowd_response->data["ip_range_24"];
		$object->crowd_ip_range_rep = $crowd_response->data["ip_range_24_reputation"] . " " . $crowd_response->data["ip_range_24_score"];
		$object->crowd_city = $crowd_response->data["location"]["city"];
		$object->crowd_country = $crowd_response->data["location"]["country"];
		$object->crowd_reverse_dns = $crowd_response->data["reverse_dns"];
		$object->crowd_last_seen = $crowd_response->data["history"]["last_seen"];
		$object->crowd_first_seen = $crowd_response->data["history"]["first_seen"];
		$object->crowd_timestamp = date('Y-m-d H:i:s', strtotime('now'));
		// flag = 1, update on main table needed, new data from API
		$object->crowd = 1;
		return $object;
	}

	function recordToCROWDTable($object, $connection)
	{
		//print("recordToCROWDTable started\n");
		$result = pg_query_params(
			$connection,
			'INSERT INTO cti_crowdsec(behavior, classification, score_overall, last_checked_at,
				as_num, as_name, ip_range_24, 
				ip_range_24_rep, geo_city, geo_country,
				reverse_dns, last_seen, first_seen, false_pos) 
				VALUES ($1, $2, $3, $4,
				$5, $6, $7,
				$8, $9, $10,
				$11, $12, $13, $14) 
				RETURNING id',
			array(
				$object->behavior, $object->classification, $object->score_overall, $object->crowd_timestamp,
				$object->crowd_AS_id, $object->crowd_AS_name, $object->crowd_ip_range,
				$object->crowd_ip_range_rep, $object->crowd_city, $object->crowd_country,
				$object->crowd_reverse_dns, $object->crowd_last_seen, $object->crowd_first_seen, $object->false_pos
			)
		);
		
		if ($result == false) {
			//print("Query failed\n");
			return -1;
		}
		$id = pg_fetch_row($result)[0];
		return $id;
	}

	function updatePairingTableCROWD($connection, $main_id, $crowd_id)
	{
		//print("updatePairingTableCROWD started\n");
		$result = pg_query_params($connection, 'UPDATE cti SET fk_crowdsec_id = $1 WHERE id = $2 RETURNING id', array($crowd_id, $main_id));

		if ($result == false) {
			//print("Query failed\n");
		}
	}

	function openNonBlockingStream($file)
	{
		$stream = fopen($file, 'r+');

		if ($stream === false) {
			return null;
		}

		stream_set_blocking($stream, false);

		return $stream;
	}
}
