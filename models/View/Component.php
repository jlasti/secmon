<?php

namespace app\models\View;

use Yii;
use app\models\Filter;
use app\models\View;

/**
 * This is the model class for table "view_components".
 *
 * @property integer $id
 * @property integer $view_id
 * @property integer $filter_id
 * @property string $config
 * @property integer $order
 * @property string $data_type
 * @property string $data_param
 *
 * @property Filter $filter
 * @property View $view
 */
class Component extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_components';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['view_id'], 'required'],
            [['view_id', 'filter_id', 'order'], 'integer'],
            [['config', 'data_type'], 'string'],
            [['data_param'], 'match', 'pattern' => '/^\d{1,5}[YMWDHmS]{1}$/', 'when' => function () {
                return $this->data_type == 'barChart';
            }, 'message' => 'Enter valid format(nY/nM/nW/nD/nH/nm/nS)!'],
            [['filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filter::className(), 'targetAttribute' => ['filter_id' => 'id']],
            [['view_id'], 'exist', 'skipOnError' => true, 'targetClass' => View::className(), 'targetAttribute' => ['view_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'view_id' => Yii::t('app', 'View ID'),
            'filter_id' => Yii::t('app', 'Filter ID'),
            'config' => Yii::t('app', 'Config'),
            'order' => Yii::t('app', 'Order'),
            'data_type' => Yii::t('app', 'Content Type'),
            'data_param' => Yii::t('app', 'Content Type Parameters')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilter()
    {
        return $this->hasOne(Filters::className(), ['id' => 'filter_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getView()
    {
        return $this->hasOne(Views::className(), ['id' => 'view_id']);
    }
}
