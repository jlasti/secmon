<?php
/**
 * Created by PhpStorm.
 * User: langr
 * Date: 12.04.2017
 * Time: 21:44
 */

namespace app\models;


abstract class BaseEvent extends \yii\db\ActiveRecord
{
    public abstract static function columns();

    public abstract static function labels();

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return static::labels();
    }

    public static function getColumnsDropdown()
    {
        $res = array();
        $cols = static::columns();
        $labels = static::labels();
        foreach ($cols as $key => $types)
        {
            $res[$key] = $labels[$key];
        }
        return $res;
    }

    public static function getColumnsDropdownOptions()
    {
        $res = array();
        $cols = static::columns();
        foreach ($cols as $key => $types)
        {
            $res[$key] = array( 'data-types' => join(',', $types));
        }
        return array('options' => $res);
    }
}