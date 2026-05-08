<?php
namespace app\models;

use yii\db\ActiveRecord;

// MISP settings model – single row configuration
class MispSettings extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%misp_settings}}';
    }

    public function rules()
    {
        return [
            [['sync_interval'], 'integer', 'min' => 1],
            [['ip_filters'], 'safe'],
            [['misp_url', 'misp_api_key'], 'string', 'max' => 255],
            [['sync_enabled'], 'boolean'],
            [['sync_interval'], 'default', 'value' => 3600],
            [['ip_filters'], 'default', 'value' => '[]'],
            [['sync_enabled'], 'default', 'value' => true],
            [['export_enabled'], 'boolean'],
            [['trigger_sync', 'trigger_export', 'trigger_full_sync'], 'boolean'],
            [['attribute_types'], 'safe'],
            [['organization_name'], 'string', 'max' => 255],
            [['export_tags'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sync_interval' => 'Sync interval (minutes)',
            'ip_filters' => 'IP filters',
            'misp_url' => 'MISP URL',
            'misp_api_key' => 'MISP API key',
            'sync_enabled' => 'Sync enabled',
            'updated_at' => 'Last updated',
            'organization_name' => 'Organization name',
        ];
    }

    // Convert stored seconds to minutes for UI
    public function getSyncIntervalMinutes()
    {
        return (int)($this->sync_interval / 60);
    }

    // Store minutes as seconds in database
    public function setSyncIntervalMinutes($minutes)
    {
        $this->sync_interval = $minutes * 60;
    }

    // Decode IP filters JSON to PHP array
    public function getIpFiltersArray()
    {
        $filters = $this->ip_filters;
        if (is_string($filters)) {
            $filters = json_decode($filters, true);
        }
        return is_array($filters) ? $filters : [];
    }

    // Fetch or create the single settings record (ID=1)
    public static function getSettings()
    {
        $settings = self::findOne(1);
        if (!$settings) {
            $settings = new self();
            $settings->id = 1;
            $settings->sync_interval = 3600;
            $settings->ip_filters = json_encode([]);
            $settings->attribute_types = json_encode([]);   // <-- pridaj
            $settings->trigger_full_sync = false;           // <-- pridaj
            $settings->save();
        }
        return $settings;
    }

    // Determine overall sync status string
    public function getStatus()
    {
        if (!$this->sync_enabled) {
            return 'disabled';
        }
        if (empty($this->misp_url) || empty($this->misp_api_key)) {
            return 'not_configured';
        }
        return 'active';
    }

    // Get human-readable status label with color
    public function getStatusLabel()
    {
        $map = [
            'active' => ['label' => 'Active', 'color' => 'green'],
            'not_configured' => ['label' => 'Not configured', 'color' => 'orange'],
            'disabled' => ['label' => 'Disabled', 'color' => 'grey'],
        ];
        $status = $this->getStatus();
        return $map[$status] ?? ['label' => 'Unknown', 'color' => 'grey'];
    }

    // Test MISP API connectivity with current settings
    public function testConnection()
    {
        if (empty($this->misp_url) || empty($this->misp_api_key)) {
            return ['success' => false, 'message' => 'URL or API key not set.'];
        }

        try {
            $client = new \yii\httpclient\Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl(rtrim($this->misp_url, '/') . '/servers/getVersion.json')
                ->addHeaders([
                    'Authorization' => $this->misp_api_key,
                    'Accept' => 'application/json',
                ])
                ->setOptions(['timeout' => 10])
                ->send();

            if ($response->isOk) {
                $data = $response->data;
                $version = $data['version'] ?? 'unknown';
                return ['success' => true, 'message' => "Connection successful. MISP version: $version"];
            } else {
                Yii::error("MISP test connection HTTP error: " . $response->statusCode . " - " . $response->content);
                return ['success' => false, 'message' => "Connection error: HTTP " . $response->statusCode];
            }
        } catch (\Exception $e) {
            Yii::error("MISP test connection exception: " . $e->getMessage());
            return ['success' => false, 'message' => "Connection error: " . $e->getMessage()];
        }
    }

    // Set flag to request a sync (download) by the daemon
    public static function triggerSync()
    {
        $settings = self::getSettings();
        $settings->trigger_sync = true;
        $settings->save(false);
    }

    // Set flag to request an export (upload) by the daemon
    public static function triggerExport()
    {
        $settings = self::getSettings();
        $settings->trigger_export = true;
        $settings->save(false);
    }

    public function getAttributeTypesArray()
    {
        $types = $this->attribute_types;
        if (is_string($types)) {
            $decoded = json_decode($types, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($types) ? $types : [];
    }

    public function setAttributeTypesArray(array $types)
    {
        $this->attribute_types = json_encode(array_values($types));
    }
    public static function triggerFullSync()
    {
        $settings = self::getSettings();
        $settings->trigger_full_sync = true;
        $settings->save(false);
    }
    public function __set($name, $value)
    {
        if (in_array($name, ['attribute_types', 'trigger_full_sync'])) {
            $this->setAttribute($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }
    public function getExportTagsArray()
    {
        $tags = $this->export_tags;
        if (is_string($tags)) {
            $decoded = json_decode($tags, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($tags) ? $tags : [];
    }

    public function setExportTagsArray(array $tags)
    {
        $this->export_tags = json_encode(array_values($tags));
    }
}