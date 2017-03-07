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
use PDO;
use PDOStatement;
use app\models\AppUser;

class Social extends ActiveRecord
{
    public static function getUserMultitableData($app_user)
    {
        $q1 = (new \yii\db\Query())
            ->select('*')
            ->from('user_booster');

        $q2 = $q1->innerJoin('users','user_booster.user_id = users.id')
            ->innerJoin('booster','booster.ID = user_booster.booster_id')
            ->innerJoin('app_user','user_booster.user_id = app_user.user_id');

        $q3 = $q2
            ->select('users.id AS user_id, users.name AS first_name,
            users.last_name AS last_name, app_user.money,
            users.avatar_url AS picture, users.first_authorized AS signed_up,
            users.last_online AS last_online, booster.id AS booster_id,
            booster.alias, booster.name AS booster_name,
            booster.description, booster.cost')
            ->where(['users.id' => $app_user['user_id']])
            ->all();

        return $q3;
    }


}