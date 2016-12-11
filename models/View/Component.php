<?php

namespace app\models\View;

use Yii;

/**
 * This is the model class for table "view_components".
 *
 * @property integer $id
 * @property integer $view_id
 * @property integer $filter_id
 * @property integer $column
 * @property integer $row
 * @property integer $width
 * @property integer $height
 *
 * @property Filters $filter
 * @property Views $view
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
            [['view_id', 'filter_id', 'column', 'row', 'width', 'height'], 'integer'],
            [['filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filters::className(), 'targetAttribute' => ['filter_id' => 'id']],
            [['view_id'], 'exist', 'skipOnError' => true, 'targetClass' => Views::className(), 'targetAttribute' => ['view_id' => 'id']],
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
            'column' => Yii::t('app', 'Column'),
            'row' => Yii::t('app', 'Row'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
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
