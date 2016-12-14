<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.12.2016
 * Time: 12:30
 */

namespace app\models;

use yii\db\ActiveRecord;

class Friendship extends ActiveRecord
{
    public static function tableName()
    {
        return "friendship"; //возвращаем название таблицы для дальнейшей работы модели
    }
}