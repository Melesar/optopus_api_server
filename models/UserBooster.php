<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:14
 */

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;

class UserBooster extends ActiveRecord
{
    public static function tableName()
    {
        return "USER_BOOSTER"; //возвращаем название таблицы для дальнейшей работы модели
    }

    public function useBooster()
    {
        $b = Booster::findOne($this->BOOSTER_ID);
        if($b && $this->AMOUNT > 0)
        {
            $this->AMOUNT--;
            $this->save();
        }
        else
            throw new BadRequestHttpException("The amount of this booster type on your account is 0");
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