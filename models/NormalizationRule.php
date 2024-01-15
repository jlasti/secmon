<?php

namespace app\models;

use DateTime;
use yii\base\Model;

class NormalizationRule extends Model
{
    public $name;
    public $uiFileName;
    public $id;
    public $active;
    public $created_at;
    public $modified_at;
    public $normalizationRuleFile;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uiFileName', 'name', 'id', 'created_at', 'modified_at', 'active', 'normalizationRuleFile'], 'required'],
            [['id'], 'integer'],
            [['active'], 'boolean'],
            [['created_at', 'modified_at'], 'safe'],
        ];
    }

    function getPrettyDateTime($property)
    {
        $createdAt = $this->$property;
        $dateTime = DateTime::createFromFormat("Y-m-d\TH:i:s.u\Z", $createdAt);
        $formattedDate = $dateTime->format("d.m.Y H:i:s");
        return $formattedDate;
    }
}
