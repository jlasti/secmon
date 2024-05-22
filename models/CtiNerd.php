<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\ErrorException;

class CtiNerd extends ActiveRecord
{
 public static function tableName()
 {
 return 'cti_nerd';
 }

}
