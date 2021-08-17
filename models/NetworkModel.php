<?php

namespace app\models;

use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "network_model".
 *
 * @property string $id
 * @property string $ip_address
 * @property string $criticality
 * @property string $mac_address
 * @property string $description
 * @property string $hostname
 * @property string $operation_system
 * @property integer $open_ports
 * @property string $ports
 * @property integer $services
 * @property string $vulnerabilities
 */
class NetworkModel extends BaseEvent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'network_model';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip_address'], 'required'],
            [['ip_address'], 'ip'],
            [['criticality'], 'integer'],
            [['mac_address', 'description', 'hostname', 'operation_system', 'open_ports', 'ports', 'services', 'vulnerabilities'], 'string', 'max' => 255],
        ];
    }

    public static function columns()
    {
        return [
            'id' => [ FilterTypeEnum::COMPARE ],
            'ip_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'mac_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'description' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'hostname' => [  FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'operation_system' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'open_ports' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'ports' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'services' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'vulnerabilities' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'criticality' => [ FilterTypeEnum::COMPARE ],
        ];
    }

    public static function labels()
    {
        return [
            'id' => 'ID',
            'ip_address' => 'IP Address',
            'mac_address' => 'MAC Address',
            'description' => 'Description',
            'hostname' => 'Hostname',
            'operation_system' => 'Operation system',
            'open_ports' => 'Open ports',
            'ports' => 'All ports',
            'services' => 'Services',
            'vulnerabilities' => 'Vulnerabilities',
            'criticality' => 'Criticality',
        ];
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        try {
            return Yii::createObject(FilterQuery::className(), [get_called_class()]);
        } catch (InvalidConfigException $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    public static function getNetworkDevice($id){
        if (($model = self::findOne($id)) !== null) {
            return $model;
        } else {
            return new NetworkModel();
        }
       
    }

    public static function getNetworkDevices($id){
        
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere([
            '<>','id', $id 
        ]);
        $query->all();

        return $dataProvider;
    }

}