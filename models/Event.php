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

	public static function fromCef($cefString)
	{
		$event = new static();

		$data = explode('|', $cefString);

		$dateHost = explode(' ', strrev(array_shift($data)), 3);

		$event->cef_version = str_replace('CEF:', '', strrev(array_shift($dateHost)));
		$event->host = strrev(array_shift($dateHost));
		$event->datetime = strrev(array_shift($dateHost));

		$event->cef_vendor = array_shift($data);
		$event->cef_dev_prod = array_shift($data);
		$event->cef_dev_version = array_shift($data);
		$event->cef_event_class_id = array_shift($data);
		$event->cef_name = array_shift($data);
		$event->cef_severity = array_shift($data);

		$event->raw = $cefString;

		return $event;
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
