<?php

namespace app\models;

use app\components\filter\FilterQuery;
use app\models\Event\EventType;
use Yii;

/**
 * This is the model class for table "events".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $timestamp
 * @property integer $type_id
 *
 * @property EventType $type
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'events_correlated';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /*[['description'], 'string'],
            [['timestamp'], 'safe'],
            [['type_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventType::className(), 'targetAttribute' => ['type_id' => 'id']],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'type_id' => Yii::t('app', 'Type ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(EventType::className(), ['id' => 'type_id']);
    }

	/**
	 * @return FilterQuery
	 */
	public static function find()
	{
		return Yii::createObject(FilterQuery::className(), [get_called_class()]);
	}
}
