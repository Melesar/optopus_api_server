<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.12.2016
 * Time: 14:00
 */

namespace app\models;

use yii\db\ActiveRecord;

class UsersOnLevels extends ActiveRecord
{
    public static function tableName()
    {
        return 'users_on_levels';
    }
}