<?php

namespace app\models\Dashboard;

use Yii;
use app\models\Filter;
use app\models\Dashboard;

/**
 * This is the model class for table "dashboard_widgets".
 *
 * @property integer $id
 * @property integer $dashboard_id
 * @property integer $filter_id
 * @property string $config
 * @property integer $order
 * @property string $data_type
 * @property string $data_param
 *
 * @property Filter $filter
 * @property Dashboard $dashboard
 * @property WidgetLayout $layout
 */
class DashboardWidget extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dashboard_widgets';
    }

    /**
     * @inheritdoc
     */
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
    public function getDashboard()
    {
        return $this->hasOne(Dashboard::className(), ['id' => 'dashboard_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLayout()
    {
        return $this->hasOne(WidgetLayout::className(), ['widget_id' => 'id']);
    }
}
