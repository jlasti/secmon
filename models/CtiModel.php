<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\ErrorException;
use yii\httpclient\Client;

class CtiModel extends ActiveRecord
{
    public $as_name;
    public $as_num;
    public $ip_range;
    public $ip_range_rep;
    public $events;
    public $city;
    public $country;
    public $hostname;
    public $first_seen;
    public $last_seen;
    public $reputation;


    public static function tableName()
    {
        return 'cti';
    }

    public function getCrowdsec()
    {
        return $this->hasOne(CtiCrowdsec::className(), ['id' => 'fk_crowdsec_id']);
    }

    public function getNerd()
    {
        return $this->hasOne(CtiNerd::className(), ['id' => 'fk_nerd_id']);
    }

    public function rules()
    {
        return [
            [['fk_crowdsec_id', 'fk_nerd_id', 'ip'], 'required'],
            [['fk_crowdsec_id', 'fk_nerd_id'], 'integer'],
            [['ip'], 'string', 'max' => 255],
        ];
    }

    public static function columns()
    {
        return [
            'cti.id',
            'cti.fk_crowdsec_id',
            'cti.fk_nerd_id',
            'cti.ip',
            'crowdsec.id',
            'crowdsec.first_seen',
            'crowdsec.last_seen',
            'crowdsec.behavior',
            'crowdsec.false_pos',
            'crowdsec.classification',
            'crowdsec.score_overall',
            'crowdsec.as_num',
            'crowdsec.as_name',
            'crowdsec.ip_range_24',
            'crowdsec.ip_range_24_rep',
            'crowdsec.geo_city',
            'crowdsec.geo_country',
            'crowdsec.reverse_dns',
            'crowdsec.last_checked_at',
            'nerd.id',
            'nerd.as_name',
            'nerd.as_id',
            'nerd.ip_range',
            'nerd.ip_range_rep',
            'nerd.events',
            'nerd.geo_city',
            'nerd.geo_country',
            'nerd.hostname',
            'nerd.first_activity',
            'nerd.last_activity',
            'nerd.fmp',
            'nerd.blacklists',
            'nerd.rep',
            'nerd.last_checked_at',
        ];
    }

    public static function getCtiInfo($id)
    {
        $cti_row = CtiModel::find()->joinWith('nerd')->joinWith('crowdsec')->where(['cti.id' => $id])->one();
        if ($cti_row == null) {
            return new CtiModel();
        }

        $cti_row->reputation = isset($cti_row['nerd']) 
            ? $cti_row['nerd']['rep'] 
            : CsvService::getRepFromCsv($cti_row['ip']);
            
        $cti_row->as_name = (object)["nerd" => $cti_row['nerd']['as_name'] ?? null, 'crowd' => $cti_row['crowdsec']['as_name'] ?? null];
        $cti_row->as_num = (object)["nerd" => $cti_row['nerd']['as_id'] ?? null, 'crowd' => $cti_row['crowdsec']['as_num'] ?? null];
        $cti_row->ip_range = (object)["nerd" => $cti_row['nerd']['ip_range'] ?? null, 'crowd' => $cti_row['crowdsec']['ip_range_24'] ?? null];
        $cti_row->ip_range_rep = (object)["nerd" => $cti_row['nerd']['ip_range_rep'] ?? null, 'crowd' => $cti_row['crowdsec']['ip_range_24_rep'] ?? null];
        $cti_row->events = (object)["nerd" => $cti_row['nerd']['events'] ?? null, 'crowd' => $cti_row['crowdsec']['behavior'] ?? null];
        $cti_row->city = (object)["nerd" => $cti_row['nerd']['geo_city'] ?? null, 'crowd' => $cti_row['crowdsec']['geo_city'] ?? null];
        $cti_row->country = (object)["nerd" => $cti_row['nerd']['geo_country'] ?? null, 'crowd' => $cti_row['crowdsec']['geo_country'] ?? null];
        $cti_row->hostname = (object)["nerd" => $cti_row['nerd']['hostname'] ?? null, 'crowd' => $cti_row['crowdsec']['reverse_dns'] ?? null];
        $cti_row->first_seen = (object)["nerd" => $cti_row['nerd']['first_activity'] ?? null, 'crowd' => $cti_row['crowdsec']['first_seen'] ?? null];
        $cti_row->last_seen = (object)["nerd" => $cti_row['nerd']['last_activity'] ?? null, 'crowd' => $cti_row['crowdsec']['last_seen'] ?? null];
        
        return $cti_row;
    }

    public static function labels()
    {
        return [
            'cti.id' => 'ID',
            'cti.fk_crowdsec_id' => 'Crowdsec ID',
            'cti.fk_nerd_id' => 'Nerd ID',
            'cti.ip' => 'IP Address',
            'crowdsec.id' => 'ID',
            'crowdsec.first_seen' => 'First Seen',
            'crowdsec.last_seen' => 'Last Seen',
            'crowdsec.behavior' => 'Behavior',
            'crowdsec.false_pos' => 'False Positive',
            'crowdsec.classification' => 'Classification',
            'crowdsec.score_overall' => 'Overall Score',
            'crowdsec.last_checked_at' => 'Last Checked At',
            'nerd.id' => 'ID',
            'nerd.as_name' => 'AS Name',
            'nerd.as_id' => 'AS ID',
            'nerd.ip_range' => 'IP range',
            'nerd.ip_range_rep' => 'IP range rep',
            'nerd.events' => 'Events',
            'nerd.geo_city' => 'City',
            'nerd.geo_country' => 'Country',
            'nerd.hostname' => 'Hostname',
            'nerd.first_activity' => 'First Seen',
            'nerd.last_activity' => 'Last Seen',
            'nerd.fmp' => 'FMP',
            'nerd.blacklists' => 'Blacklists',
            'nerd.rep' => 'Reputation',
            'nerd.last_checked_at' => 'Last Checked At',
        ];
    }
}

class CsvService {
    private static function getFileExpirationDuration(){
        $json = file_get_contents('/var/www/html/secmon/config/cti_config.json');
		$json_data = json_decode($json, true);
		return $json_data["file_validity"];
    }

	public static function getRepFromCsv($ip)
	{
        $expirationInHours = CsvService::getFileExpirationDuration();

        $client = new Client(['responseConfig' => [
			'format' => Client::FORMAT_JSON
		]]);

		$filePath = "/var/www/html/secmon/config/ip_rep.csv";

		if (file_exists($filePath)) {
			$csv = file_get_contents($filePath);

			$csvData = str_getcsv($csv, "\n", "", "#");

			$date = [];
			preg_match('/[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])/', $csvData[0], $date);
			$stamp = new \DateTime(date('Y-m-d H:i:s', strtotime($date[0])));
			$now = new \DateTime(date('Y-m-d H:i:s', strtotime('now')));
			$hoursDifference = $now->diff($stamp)->h + ($now->diff($stamp)->days * 24);

			if ($hoursDifference > $expirationInHours) {
				//print("File is old enough, needs refresh\n");
				$csvData = CsvService::getCsvData($client);
                $arr = CsvService::parseCsvArray($csvData);
                return $arr[$ip] ?? null;
			}
			//print("NO NEED TO REFRESH CSV\n");
			$arr = CsvService::parseCsvArray($csvData);
            return $arr[$ip] ?? null;
		} else {
			//print("NO FILE FOUND\n");
			$csvData = CsvService::getCsvData($client);
            $arr = CsvService::parseCsvArray($csvData);
            return $arr[$ip] ?? null;
		}
	}

	private static function parseCsvArray($csvData)
	{
		$arr = [];
		foreach ($csvData as $row) {
			$line = explode(",", $row);
			$arr[$line[0]] = $line[1] ?? null;
		}
		return $arr;
	}

	private static function getCsvData($client)
	{
		$response = $client->createRequest()
			->setMethod('GET')
			->setUrl('https://nerd.cesnet.cz/nerd/data/ip_rep.csv')
			->send();
		if ($response->isOk) {
			//print("GOT CSV" . PHP_EOL);
			$csvData = $response->getContent();
			file_put_contents('/var/www/html/secmon/config/ip_rep.csv', $csvData);
			$csvData = str_getcsv($csvData, "\n", "", "#");
            return $csvData;
		}
		return null;
	}
}