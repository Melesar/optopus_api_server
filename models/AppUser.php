<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:14
 */

namespace app\models;

use yii\db\ActiveRecord;

class AppUser extends ActiveRecord
{
    public static function tableName()
    {
        return "APP_USER"; //возвращаем название таблицы для дальнейшей работы модели
    }
}