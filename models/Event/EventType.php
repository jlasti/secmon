<?php

namespace app\models\Event;

use Yii;

/**
 * This is the model class for table "event_types".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 *
 * @property Event[] $events
 */
class EventType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\SluggableBehavior::className(),
                'attribute' => 'name',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['type_id' => 'id']);
    }

    public function __toString()
    {
        return $this->name;
    }

    public static function getAllAsArray()
    {
        return EventType::find()->indexBy('id')->all();
    }
}
