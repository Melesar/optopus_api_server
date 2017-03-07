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
        return "boosters"; //возвращаем название таблицы для дальнейшей работы модели
    }

    public function getApp()
    {
        return $this->hasMany(Application::className(),["id" => "app_id"]) // "id" of Application to "app_id" of App_Booster
        ->viaTable("App_Booster",["booster_id"=>"id"]) // "booster_id" of App_Booster to "id" of Booster
        ->all();
    }

    public function getUserBooster()
    {
        return $this->hasMany(UserBooster::className(),['booster_id' => 'id']);
    }
}