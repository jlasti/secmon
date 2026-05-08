<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\httpclient\Client;
use app\models\MispEvents;
use app\models\MispAttributes;
use app\models\MispSettings;
use yii\db\Expression;
use yii\helpers\Json;
use ZMQ;
use ZMQContext;

class SynchronizerController extends Controller
{
    private $mispUrl;
    private $apiKey;
    private $syncEnabled;
    private $exportEnabled;
    private $syncInterval;
    private $zmqListenPort;
    private $zmqContext;
    private $zmqReceiver;
    private $ipFilters = [];
    private $organizationName;
    private $attributeTypes = [];
    private $exportTags = [];

    /** Initialize controller and load configuration. */
    public function init()
    {
        parent::init();
        $this->loadConfig();
    }

    /** Load MISP settings and ZeroMQ port config. */
    private function loadConfig()
    {
        $settings = MispSettings::getSettings();

        $this->mispUrl = $settings->misp_url;
        $this->apiKey = $settings->misp_api_key;
        $this->syncEnabled = (bool)$settings->sync_enabled;
        $this->exportEnabled = (bool)($settings->export_enabled ?? false);
        $this->syncInterval = (int)$settings->sync_interval;
        $this->ipFilters = $settings->getIpFiltersArray();
        $this->organizationName = $settings->organization_name ?? null;
        $this->attributeTypes = $settings->getAttributeTypesArray();
        $this->exportTags = $settings->getExportTagsArray();

        $configPath = Yii::getAlias('@app/config/aggregator_config.ini');
        $config = parse_ini_file($configPath, false, INI_SCANNER_RAW);
        $this->zmqListenPort = (int)($config['Misp_synchronizer_port'] ?? 9000);

        if (!$this->syncEnabled && !$this->exportEnabled) {
            $this->stdout("Both MISP sync and export are disabled.\n");
        } elseif (!$this->syncEnabled) {
            $this->stdout("MISP sync is off in settings.\n");
        } elseif (!$this->exportEnabled) {
            $this->stdout("MISP export is off in settings.\n");
        }

        if (empty($this->mispUrl) || empty($this->apiKey)) {
            if ($this->syncEnabled || $this->exportEnabled) {
                $this->stderr("ERROR: MISP URL or API key are not set!\n");
            }
        }
    }

    /** Run daemon loop for sync, export, and triggers. */
    public function actionDaemon()
    {
        $this->stdout("[" . date("Y-m-d H:i:s") . "] MISP Synchronizer daemon is on.\n");

        $this->initZmqReceiver();

        $lastSync = 0;
        $lastExport = 0;
        $lastTriggerCheck = 0;
        $lastConfigReload = 0;

        while (true) {
            $now = time();

            if ($now - $lastConfigReload >= 60) {
                $this->loadConfig();
                $lastConfigReload = $now;
            }

            $this->processIncomingCorrelations();

            if ($now - $lastTriggerCheck >= 1) {
                $this->checkTriggers();
                $lastTriggerCheck = $now;
            }

            if ($this->syncEnabled && ($now - $lastSync >= $this->syncInterval)) {
                $this->stdout("[" . date("Y-m-d H:i:s") . "] Starting a synchronization cycle...\n");
                $this->actionSync();
                $lastSync = $now;
            }

            if ($this->exportEnabled && ($now - $lastExport >= $this->syncInterval)) {
                $this->stdout("[" . date("Y-m-d H:i:s") . "] Starting an export cycle...\n");
                $this->actionExport();
                $lastExport = $now;
            }

            usleep(100000);
        }
    }

    /** Initialize and bind ZeroMQ PULL socket. */
    private function initZmqReceiver()
    {
        $this->zmqContext = new ZMQContext();
        $this->zmqReceiver = $this->zmqContext->getSocket(ZMQ::SOCKET_PULL);
        $endpoint = "tcp://*:{$this->zmqListenPort}";
        $this->zmqReceiver->bind($endpoint);
        $this->stdout("ZeroMQ listening on $endpoint\n");
    }

    /** Receive and store correlation events via ZMQ. */
    private function processIncomingCorrelations()
    {
        while (true) {
            $msg = $this->zmqReceiver->recv(ZMQ::MODE_DONTWAIT);
            if ($msg === false) {
                break;
            }
            echo "Zapisane:" . $msg . PHP_EOL;
            $this->storeCorrelationEvent($msg);
        }
    }

    /** Extract clean IP from attribute value string. */
    private function extractIpFromValue($value)
    {
        if (strpos($value, ':') !== false && substr_count($value, '.') == 3) {
            $value = explode(':', $value)[0];
        }
        if (strpos($value, '|') !== false) {
            $parts = explode('|', $value);
            if (filter_var($parts[1] ?? '', FILTER_VALIDATE_IP)) {
                return $parts[1];
            }
            if (filter_var($parts[0] ?? '', FILTER_VALIDATE_IP)) {
                return $parts[0];
            }
        }
        return $value;
    }

    /** Store event and attributes from JSON data. */
    private function storeFromJson(array $data)
    {
        $event = new MispEvents();
        $event->creator_org = $this->organizationName;
        $event->info = $data['info'] ?? null;
        $event->threat_level = $data['threat_level'] ?? 4;
        $event->analysis = $data['analysis'] ?? 0;
        $event->timestamp = time();
        $event->tags = isset($data['tags']) ? Json::encode($data['tags']) : null;
        $event->is_sent = false;

        if (!$event->save()) {
            Yii::error("Error saving MispEvent: " . Json::encode($event->errors));
            return;
        }

        if (!empty($data['attributes']) && is_array($data['attributes'])) {
            foreach ($data['attributes'] as $attr) {
                $attribute = new MispAttributes();
                $attribute->event_id = $event->event_id;
                $attribute->category = $attr['category'] ?? 'Network activity';
                $attribute->type = $attr['type'] ?? 'unknown';
                $attribute->value = $attr['value'] ?? '';
                $attribute->to_ids = (bool)($attr['to_ids'] ?? true);
                $attribute->disable_correlation = (bool)($attr['disable_correlation'] ?? false);
                $attribute->comment = $attr['comment'] ?? null;
                $attribute->timestamp = time();

                if (!$attribute->save()) {
                    Yii::error("Error saving MispAttribute: " . Json::encode($attribute->errors));
                }
            }
        }

        $this->stdout("New correlation event ID: {$event->event_id}\n");
    }

    /** Store CEF correlation event, skip existing hits. */
    private function storeCorrelationEvent($cefLine)
    {
        $srcIp = $this->extractField($cefLine, "src=");
        if (!$srcIp) {
            return;
        }

        if (strpos($cefLine, 'SecmonMispSrcHit=true') !== false || 
            strpos($cefLine, 'SecmonMispDstHit=true') !== false) {
            
            $mispAttrId = null;
            if (preg_match('/SecmonMispSrcId=(\d+)/', $cefLine, $matches)) {
                $mispAttrId = $matches[1];
            } elseif (preg_match('/SecmonMispDstId=(\d+)/', $cefLine, $matches)) {
                $mispAttrId = $matches[1];
            }
            
            if ($mispAttrId) {
                $attribute = MispAttributes::findOne($mispAttrId);
                if ($attribute) {
                    $existingEvent = MispEvents::findOne($attribute->event_id);
                    if ($existingEvent) {
                        $this->stdout("Event already exists (ID: {$existingEvent->event_id}), skipping creation.\n");
                        
                        $existingEvent->threat_level = min($existingEvent->threat_level ?? 4, 2);
                        $existingEvent->timestamp = time();
                        $existingEvent->save(false);
                        
                        return;
                    }
                }
            }
            
            $this->stdout("MISP hit found but event not located, creating new event anyway.\n");
        }

        $event = new MispEvents();
        $event->creator_org = $this->organizationName;
        $event->info = 'Correlation from Secmon: ' . $srcIp;
        $event->threat_level = 2;
        $event->analysis = 0;
        $event->timestamp = time();
        $event->tags = Json::encode(['secmon', 'correlated']);
        $event->is_sent = false;

        if (!$event->save()) {
            Yii::error("Error saving MispEvent from CEF: " . Json::encode($event->errors));
            return;
        }

        $attribute = new MispAttributes();
        $attribute->event_id = $event->event_id;
        $attribute->category = 'Network activity';
        $attribute->type = 'ip-src';
        $attribute->value = $this->normalizeIp($srcIp);
        $attribute->to_ids = true;
        $attribute->comment = 'Generated from correlation';
        $attribute->timestamp = time();

        if (!$attribute->save()) {
            Yii::error("Error saving MispAttribute from CEF: " . Json::encode($attribute->errors));
        }

        $this->stdout("New correlation event from CEF, ID: {$event->event_id}\n");
    }

    /** Extract a field value from CEF message. */
    private function extractField($msg, $field)
    {
        $pos = strpos($msg, $field);
        if ($pos === false) {
            return null;
        }
        $start = $pos + strlen($field);
        $end = strpos($msg, " ", $start);
        if ($end === false) {
            return substr($msg, $start);
        }
        return substr($msg, $start, $end - $start);
    }

    /** Remove port and whitespace from IP address. */
    private function normalizeIp($ip)
    {
        $ip = trim($ip);
        if (strpos($ip, ':') !== false && substr_count($ip, '.') == 3) {
            return explode(':', $ip)[0];
        }
        return $ip;
    }

    /** Download new or updated IP attributes from MISP. */
    public function actionSync($full = false)
    {
        $this->loadConfig();
        $settings = MispSettings::getSettings();

        $settings = MispSettings::getSettings();
        if (!$this->syncEnabled && !$settings->trigger_sync) {
            $this->stdout("Sync is off.\n");
            return ExitCode::OK;
        }
        if (empty($this->mispUrl) || empty($this->apiKey)) {
            Yii::error("MISP Sync: Missing URL or API key.");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($full) {
            $this->stdout("Full sync requested. Purging all remote events & attributes...\n");
            $this->purgeRemoteEvents();
        }

        $stateFile = Yii::getAlias('@runtime/misp_last_sync.txt');
        $lastSync = $full ? 0 : (int)(file_exists($stateFile) ? file_get_contents($stateFile) : 0);
        $this->stdout("Requesting attributes modified since: " . date('Y-m-d H:i:s', $lastSync) . " (timestamp $lastSync)\n");

        $client = new Client();
        $page = 1;
        $limit = 500;
        $totalProcessed = 0;
        $syncSuccess = true;

        while (true) {
            $requestData = [
                'returnFormat' => 'json',
                'limit'        => $limit,
                'page'         => $page,
                'timestamp'    => $lastSync,
                'to_ids'       => 1,
                'published'    => 1,
                'includeEventTags' => 1,
            ];

            if (!empty($this->attributeTypes)) {
                $requestData['type'] = $this->attributeTypes;
            } else {
                $this->stdout("No attribute types selected in settings. Nothing to sync.\n");
                break;
            }

            $requestData['tags'][] = '!secmon:exported';

            try {
                $response = $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl($this->mispUrl . '/attributes/restSearch')
                    ->addHeaders([
                        'Authorization' => $this->apiKey,
                        'Accept'        => 'application/json',
                    ])
                    ->setData($requestData)
                    ->send();
            } catch (\Exception $e) {
                Yii::error("MISP Sync: Connection error: " . $e->getMessage());
                $this->stderr("ERROR: Failed to connect to MISP server.\n");
                $syncSuccess = false;
                break;
            }

            if (!$response->isOk) {
                Yii::error("MISP Sync: HTTP error " . $response->statusCode . " - " . $response->content);
                $this->stderr("ERROR: MISP server returned HTTP " . $response->statusCode . "\n");
                $syncSuccess = false;
                break;
            }

            $data = $response->data;
            $attributes = $data['response']['Attribute'] ?? [];

            if (empty($attributes)) {
                break;
            }

            try {
                $this->processDownloadedData($attributes);
            } catch (\Exception $e) {
                Yii::error("MISP Sync: Processing error: " . $e->getMessage());
                $this->stderr("ERROR: Failed to process data: " . $e->getMessage() . "\n");
                $syncSuccess = false;
                break;
            }

            $totalProcessed += count($attributes);
            $this->stdout("Page $page: processed " . count($attributes) . " attributes...\n");

            if (count($attributes) < $limit) {
                break;
            }
            $page++;
        }

        $this->stdout("Total downloaded: $totalProcessed attributes.\n");
        Yii::info("MISP Sync: Total downloaded $totalProcessed attributes.");

        if ($syncSuccess) {
            file_put_contents($stateFile, time());
            if ($full) {
                $this->stdout("Full sync successful. Last sync timestamp updated.\n");
            } else {
                $this->stdout("Sync successful. Last sync timestamp updated.\n");
            }
        } else {
            $this->stdout("Sync failed. Timestamp NOT updated.\n");
        }

        return $syncSuccess ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    /** Group attributes by event and store new ones. */
    private function processDownloadedData(array $attributes)
    {
        $eventGroups = [];
        foreach ($attributes as $attr) {
            $attrType = $attr['type'] ?? '';
            if (!empty($this->attributeTypes) && !in_array($attrType, $this->attributeTypes)) {
                continue;
            }
            $eventGroups[$attr['event_id']][] = $attr;
        }

        foreach ($eventGroups as $mispEventId => $attrs) {
            $firstAttr = $attrs[0];
            $eventData = $firstAttr['Event'] ?? [];

            if (empty($eventData['uuid'])) {
                Yii::warning("Attribute without Event UUID, skipping.");
                continue;
            }

            $eventUuid = $eventData['uuid'];
            $mispEvent = MispEvents::find()->where(['event_uuid' => $eventUuid])->one();

            if (!$mispEvent) {
                $mispEvent = new MispEvents();
                $mispEvent->event_uuid   = $eventUuid;
                $mispEvent->creator_org  = $eventData['Orgc']['name'] ?? null;
                $mispEvent->info         = $eventData['info'] ?? null;
                $mispEvent->threat_level = $eventData['threat_level_id'] ?? 4;
                $mispEvent->analysis     = $eventData['analysis'] ?? 0;
                $mispEvent->timestamp    = $eventData['timestamp'] ?? time();
                $mispEvent->tags         = Json::encode(array_column($eventData['Tag'] ?? [], 'name'));
                $mispEvent->is_sent      = true;
                $mispEvent->sent_at      = new Expression('NOW()');

                if (!$mispEvent->save()) {
                    Yii::error("Error writing MispEvent: " . Json::encode($mispEvent->errors));
                    continue;
                }
            }

            $incomingUuids = array_column($attrs, 'uuid');
            $existingUuids = MispAttributes::find()
                ->select('attribute_uuid')
                ->where(['attribute_uuid' => $incomingUuids])
                ->column();
            $existingSet = array_flip($existingUuids);

            foreach ($attrs as $attr) {
                if (isset($existingSet[$attr['uuid']])) {
                    continue;
                }

                $mispAttr = new MispAttributes();
                $mispAttr->attribute_uuid = $attr['uuid'];
                $mispAttr->event_id       = $mispEvent->event_id;
                $mispAttr->category       = $attr['category'] ?? null;
                $mispAttr->type           = $attr['type'] ?? null;

                $value = $attr['value'] ?? '';
                if (mb_strlen($value) > 191) {
                    $value = mb_substr($value, 0, 191);
                    Yii::warning("Attribute value truncated: $value");
                }
                $mispAttr->value = $value;

                $mispAttr->to_ids         = (bool)($attr['to_ids'] ?? true);
                $mispAttr->timestamp      = $attr['timestamp'] ?? time();
                $mispAttr->tags           = Json::encode(array_column($attr['Tag'] ?? [], 'name'));

                $mispAttr->comment        = $attr['comment'] ?? null;
                $mispAttr->first_seen     = isset($attr['first_seen']) ? date('Y-m-d H:i:s', (int)$attr['first_seen']) : null;
                $mispAttr->last_seen      = isset($attr['last_seen']) ? date('Y-m-d H:i:s', (int)$attr['last_seen']) : null;
                $mispAttr->disable_correlation = (bool)($attr['disable_correlation'] ?? false);
                $mispAttr->object_relation = $attr['object_relation'] ?? null;
                $mispAttr->galaxy_clusters = isset($attr['Galaxy']) ? Json::encode($this->extractGalaxyClusters($attr['Galaxy'])) : null;

                if (!$mispAttr->save()) {
                    Yii::error("Error writing MispAttribute: " . Json::encode($mispAttr->errors));
                }
            }
        }
    }

    /** Extract galaxy cluster data from MISP response. */
    private function extractGalaxyClusters(array $galaxies): array
    {
        $clusters = [];
        foreach ($galaxies as $galaxy) {
            foreach ($galaxy['GalaxyCluster'] ?? [] as $cluster) {
                $clusters[] = [
                    'type'  => $galaxy['type'] ?? null,
                    'value' => $cluster['value'] ?? null,
                    'tag_name' => $cluster['tag_name'] ?? null,
                ];
            }
        }
        return $clusters;
    }

    /** Export unsent events to MISP with IP filtering. */
    public function actionExport()
    {
        $settings = MispSettings::getSettings();
        $this->stdout("Aktuálne IP filtre: " . json_encode($this->ipFilters) . "\n");

        if (!$this->exportEnabled && !$settings->trigger_export) {
            $this->stdout("Export is disabled in settings.\n");
            return ExitCode::OK;
        }

        $this->stdout("Looking for unsent events in misp_events...\n");

        $unsentEvents = MispEvents::find()
            ->where(['is_sent' => false])
            ->with('mispAttributes')
            ->all();

        if (empty($unsentEvents)) {
            $this->stdout("No new events to export.\n");
            return ExitCode::OK;
        }

        $exportSuccess = true;

        foreach ($unsentEvents as $event) {
            $this->stdout("Exporting Event ID: {$event->event_id}...\n");
            $singleResult = $this->sendEventToMisp($event);
            if (!$singleResult) {
                $exportSuccess = false;
            }
        }

        $this->stdout("Export completed.\n");
        return $exportSuccess ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    /** Send a single event to MISP after IP filtering. */
    /** Send a single event to MISP after IP filtering. */
    private function sendEventToMisp(MispEvents $event)
    {
        try {
            $attributes = $event->mispAttributes ?? [];
            $filteredAttributes = [];

            $ipLikeTypes = ['ip-src', 'ip-dst', 'ip', 'domain|ip', 'ip-src|port', 'ip-dst|port'];

            foreach ($attributes as $attr) {
                $ip = $this->extractIpFromValue($attr->value);

                if (in_array($attr->type, $ipLikeTypes) && $this->isIpFiltered($ip)) {
                    Yii::info("Attribute omitted (IP filter): " . $attr->value);
                    $this->stdout("Filtered out: {$attr->value}\n");
                    continue;
                }
                $filteredAttributes[] = $attr;
            }

            if (empty($filteredAttributes)) {
                $this->stdout("Event {$event->event_id} has no attributes after filtering. Skipping.\n");
                $event->is_sent = true;
                $event->sent_at = new Expression('NOW()');
                $event->save(false);
                return true;
            }

            $attributeData = [];
            foreach ($filteredAttributes as $attr) {
                $attributeData[] = [
                    'category' => $attr->category,
                    'type'     => $attr->type,
                    'value'    => $attr->value,
                    'to_ids'   => $attr->to_ids,
                    'comment'  => $attr->comment,
                    'disable_correlation' => $attr->disable_correlation,
                ];
            }

            $eventInfo = $event->info ?? 'SecMon Correlation Event';

            $tags = [];
            foreach ($this->exportTags as $tag) {
                $tags[] = ['name' => trim($tag)];
            }

            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl($this->mispUrl . '/events/add')
                ->setFormat(Client::FORMAT_JSON)
                ->addHeaders([
                    'Authorization' => $this->apiKey,
                    'Accept'        => 'application/json',
                ])
                ->setData([
                    'distribution'    => 1,
                    'threat_level_id' => $event->threat_level ?? 4,
                    'analysis'        => $event->analysis ?? 0,
                    'info'            => $eventInfo,
                    'date'            => date('Y-m-d'),
                    'published'       => false,
                    'Attribute'       => $attributeData,
                    'Tag'             => $tags,   // Dynamické tagy
                ])
                ->send();

            if ($response->isOk) {
                $responseData = $response->data;
                if (isset($responseData['Event']['uuid'])) {
                    $event->event_uuid = $responseData['Event']['uuid'];
                }
                $event->is_sent = true;
                $event->sent_at = new Expression('NOW()');
                $event->save(false);
                $this->stdout("Event {$event->event_id} successfully exported.\n");
                return true;
            } else {
                Yii::error("MISP export HTTP error: " . $response->statusCode . " - " . $response->content);
                $this->stderr("ERROR while exporting event {$event->event_id}: HTTP " . $response->statusCode . "\n");
                return false;
            }
        } catch (\Exception $e) {
            Yii::error("MISP export exception: " . $e->getMessage());
            $this->stderr("ERROR while exporting event {$event->event_id}: " . $e->getMessage() . "\n");
            return false;
        }
    }

    /** Check if IP matches any filter rules. */
    private function isIpFiltered($ip)
    {
        foreach ($this->ipFilters as $filter) {
            if (strpos($filter, '/') !== false) {
                if ($this->ipInCidr($ip, $filter)) {
                    return true;
                }
            } else {
                if ($ip === $filter) {
                    return true;
                }
            }
        }
        return false;
    }

    /** Check if IP is in CIDR subnet. */
    private function ipInCidr($ip, $cidr)
    {
        list($subnet, $bits) = explode('/', $cidr);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        return ($ip & $mask) == $subnet;
    }

    /** Check for manual sync/export trigger flags. */
    private function checkTriggers()
    {
        $settings = \app\models\MispSettings::getSettings();
        $changed = false;

        if ($settings->trigger_sync) {
            $this->stdout("Starting manual synchronization (download)...\n");
            $this->actionSync();
            $settings->trigger_sync = false;
            $changed = true;
        }

        if ($settings->trigger_export) {
            $this->stdout("Starting manual export...\n");
            $this->actionExport();
            $settings->trigger_export = false;
            $changed = true;
        }

        if ($settings->trigger_full_sync) {
            $this->stdout("Starting FULL synchronization...\n");
            $this->actionSync(true);
            $settings->trigger_full_sync = false;
            $changed = true;
        }

        if ($changed) {
            $settings->save(false);
        }
    }
    
    /** Delete all remote (downloaded) events and their attributes. */
    private function purgeRemoteEvents()
    {
        $remoteEventIds = MispEvents::find()
            ->select('event_id')
            ->where(['not', ['event_uuid' => null]])
            ->column();

        if (!empty($remoteEventIds)) {
            $count = count($remoteEventIds);
            MispAttributes::deleteAll(['event_id' => $remoteEventIds]);
            MispEvents::deleteAll(['event_id' => $remoteEventIds]);
            $this->stdout("Purged $count remote events and their attributes.\n");
        } else {
            $this->stdout("No remote events to purge.\n");
        }
    }
}