<?php

namespace app\models\Dashboard;

use Yii;

/**
 * This is the model class for table "widget_layout".
 *
 * @property integer $id
 * @property integer $widget_id
 * @property integer $x
 * @property integer $y
 * @property integer $w
 * @property integer $h
 *
 * @property DashboardWidget $widget
 */
class WidgetLayout extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'widget_layout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['widget_id', 'x', 'y', 'w', 'h'], 'required'],
            [['widget_id', 'x', 'y', 'w', 'h'], 'integer'],
            [['widget_id'], 'exist', 'skipOnError' => true, 'targetClass' => DashboardWidget::className(), 'targetAttribute' => ['widget_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'widget_id' => 'Widget ID',
            'x' => 'X Position',
            'y' => 'Y Position',
            'w' => 'Width',
            'h' => 'Height',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWidget()
    {
        return $this->hasOne(DashboardWidget::className(), ['id' => 'widget_id']);
    }
}
