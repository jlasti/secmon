<?php

namespace app\models\Rules;


use yii\base\Model;

/**
 * 
 * Class representing .rule file.
 *
 * @param string $name  Custom name of rule.
 * @param string $active  State of rule (e.g ACTIVE/INACTIVE)
 * @param integer $size  Size of rule in Bytes.
 * @param integer $uid  User ID of owner.
 * @param string $gid  Group ID of owner.
 * @param string $modified_at  Latest modification time.
 * @param string $accessed_at  Latest access time.
 * @param string $ruleFileName  Name of rule file. (e.g apache.rule)
 * @param string $content  Content of rule .
 */
class Rule extends Model
{
    public $name;
    public $active;
    public $size;
    public $uid;
    public $gid;
    public $modified_at;
    public $accessed_at;
    public $ruleFileName;
    public $content;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'ruleFileName'], 'required'],
            ['name', 'string', 'max' => 60],
            ['content', 'string'],
            ['active', 'boolean'],
            [['created_at', 'accessed_at'], 'safe'],
        ];
    }
}
