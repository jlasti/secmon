<?php

namespace app\models\Dashboard;

use Yii;
use app\models\Filter;
use app\models\Dashboard;

class DashboardWidget extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dashboard_widgets';
    }

    public function rules()
    {
        return [
            [['title', 'dashboard_id'], 'required'],

            [['dashboard_id', 'filter_id'], 'integer'],
            
            [['config'], 'safe'],

            [['title', 'chart_type', 'timeframe'], 'string'],

            [['filter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filter::className(), 'targetAttribute' => ['filter_id' => 'id']],
            [['dashboard_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dashboard::className(), 'targetAttribute' => ['dashboard_id' => 'id']],
        ];
    }

    public function getFilter()
    {
        return $this->hasOne(Filters::className(), ['id' => 'filter_id']);
    }

    public function getDashboard()
    {
        return $this->hasOne(Dashboard::className(), ['id' => 'dashboard_id']);
    }

    public function getLayout()
    {
        return $this->hasOne(WidgetLayout::className(), ['widget_id' => 'id']);
    }
}
