<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:11
 */

namespace app\models;

use yii\db\ActiveRecord;

class Booster extends ActiveRecord
{
    public static function tableName()
    {
        return "BOOSTER"; //возвращаем название таблицы для дальнейшей работы модели
    }


    public function getApp()
    {
        return $this->hasMany(Application::className(),["id" => "APP_ID"]) // "id" of Application to "app_id" of App_Booster
        ->viaTable("App_Booster",["BOOSTER_ID"=>"ID"]) // "booster_id" of App_Booster to "id" of Booster
        ->all();
    }

//    public function getUsers()
//    {
//        return $this->hasMany(Users::className(),["id" => "USER_ID"])
//        ->viaTable("User_Booster",["BOOSTER_ID"=>"ID"])
//        ->all();
//    }

    public function getUserBooster()
    {
        return $this->hasMany(UserBooster::className(),['BOOSTER_ID' => 'ID']);
    }
}