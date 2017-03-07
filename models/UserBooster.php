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
        return "user_booster"; //возвращаем название таблицы для дальнейшей работы модели
    }

    public function useBooster()
    {
        $b = Booster::findOne($this->booster_id);
        if($b && $this->amount > 0)
        {
            $this->amount--;
            $this->save();
        }
        else
            throw new BadRequestHttpException("The amount of this booster type on your account is 0");
    }

    public function getUsers()
    {
        return $this->hasOne(Users::className(),["id"=>"user_id"]);
    }

    public function getBooster()
    {
        return $this->hasOne(Booster::className(),["id"=>"booster_id"]);
    }
}