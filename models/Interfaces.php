<?php

namespace app\models;

use app\components\filter\FilterQuery;
use app\components\filter\FilterTypeEnum;
use yii\data\ActiveDataProvider;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "interfaces".
 *
 * @property string $id
 * @property string $network_model_id
 * @property string $ip_address
 * @property string $mac_address
 * @property string $name
 */
class Interfaces extends BaseEvent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'interface';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['network_model_id', 'ip_address'], 'required'],
            [['ip_address'], 'ip'],
            [['mac_address', 'name' ], 'string', 'max' => 255],
        ];
    }

    public static function columns()
    {
        return [
            'id' => [ FilterTypeEnum::COMPARE ],
            'network_model_id' => [ FilterTypeEnum::COMPARE ],
            'ip_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'mac_address' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
            'name' => [ FilterTypeEnum::REGEX, FilterTypeEnum::COMPARE ],
        ];
    }

    public static function labels()
    {
        return [
            'id' => 'ID',
            'network_model_id' => 'Network model device ID',
            'ip_address' => 'IP Address',
            'mac_address' => 'MAC Address',
            'name' => 'Name',
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

    public static function getInterfacesByNetworkModel($network_model_id){
        $query = self::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere([
            'network_model_id' => $network_model_id,
        ]);
        $query->orderBy([
            'id' => SORT_ASC
        ]);
        
        return $dataProvider;
    }

}
