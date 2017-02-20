<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10.02.2017
 * Time: 16:33
 */

namespace app\models;

use yii;
use yii\db\ActiveRecord;

class Social extends ActiveRecord
{
    public static function getUserMultitableData($app_user)
    {
        $q1 = (new \yii\db\Query())
            ->select('*')
            ->from('user_booster');

        $q2 = $q1->innerJoin('users','user_booster.USER_ID = users.id')
            ->innerJoin('booster','booster.ID = user_booster.BOOSTER_ID')
            ->innerJoin('app_user','user_booster.USER_ID = app_user.USER_ID');

        $q3 = $q2
            ->select('users.id AS USER_ID, users.name AS FIRST_NAME,
            users.last_name AS LAST_NAME, app_user.MONEY,
            users.avatar_url AS PICTURE, users.first_authorized AS SIGNED_UP,
            users.last_online AS LAST_ONLINE, booster.ID AS BOOSTER_ID,
            booster.ALIAS, booster.NAME AS BOOSTER_NAME,
            booster.DESCRIPTION, booster.COST')
            ->where(['users.id' => $app_user['USER_ID']])
            ->all();

        return $q3;
    }
}