<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:14
 */

namespace app\models;

use yii\db\ActiveRecord;

class AppBooster extends ActiveRecord
{
    public static function tableName()
    {
        return "app_booster"; //возвращаем название таблицы для дальнейшей работы модели
    }
}