<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:13
 */

namespace app\models;

use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    public static function tableName()
    {
        return "product"; //возвращаем название таблицы для дальнейшей работы модели
    }

    public function getApp()
    {
        return $this->hasMany(Application::className(),["id => app_id"]) // "id" of Application to "app_id" of App_Product
        ->viaTable("App_Product",["product_id"=>"id"]) // "product_id" of App_Product to "id" of Product
        ->all();
    }
}