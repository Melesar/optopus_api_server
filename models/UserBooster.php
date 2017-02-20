<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:14
 */

namespace app\models;

use yii\db\ActiveRecord;

class UserBooster extends ActiveRecord
{
    public static function tableName()
    {
        return "USER_BOOSTER"; //возвращаем название таблицы для дальнейшей работы модели
    }

    public function getUsers()
    {
        return $this->hasOne(Users::className(),["id"=>"USER_ID"]);
    }

    public function getBooster()
    {
        return $this->hasOne(Booster::className(),["ID"=>"BOOSTER_ID"]);
    }
}