<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.11.2016
 * Time: 20:23
 */

namespace app\models;

use yii\db\ActiveRecord;
use yii;

class Levels extends ActiveRecord
{
    public function getUsers() //declaring relations
    {
        // we use table USERS_ON_LEVELS to merge two tables
        return $this->hasMany(Users::className(),["id => user_id"]) // "id" of Users to "users_id" of Users_on_levels
            ->viaTable("users_on_levels",["level_id"=>"id"]) // "level_id" of Users_on_levels to "id" of Levels
            ->all();
    }
}