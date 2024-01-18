<?php

namespace app\models;

use yii\base\Model;

class NormalizationRule extends Model
{
    public $name;
    public $active;
    public $size;
    public $modified_at;
    public $ruleFileName;
    public $content;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'ruleFileName'], 'required'],
            ['name', 'string','max'=> 50],
            ['content', 'string'],
            ['active', 'boolean'],
            ['created_at', 'safe'],
        ];
    }
}
