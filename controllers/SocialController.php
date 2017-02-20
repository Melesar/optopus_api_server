<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10.02.2017
 * Time: 15:51
 */

namespace app\controllers;

use app\models\Application;
use app\models\AppUser;
use app\models\Booster;
use app\models\Social;
use app\models\UserBooster;
use app\models\Users;
use yii\db\Query;

use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

use Facebook;


class SocialController extends Controller
{
    public function actionPosttoken()
    {
        $accessToken = Yii::$app->request->getBodyParam("FAC");

        $app_obj = Application::findApp($accessToken);

        $fbUser = $app_obj->getUser($accessToken);
        $user_db = Users::findOne(['id' => $fbUser->getId()]);

        if($user_db == null)
        {
            $user_db = new Users();
        }
        $user_db->fbUpdate($fbUser);

        $user_db = AppUser::findOne(['app_id' => $app_obj['APP_ID'], 'user_id' => $fbUser->getId()]);
        if($user_db == null)
        {
            $user_db = new AppUser;
            $user_db->APP_ID = $app_obj['APP_ID'];
            $user_db->USER_ID = $fbUser->getId();
            $user_db->save();
        }
        return $user_db->setSAC();
    }

    public function actionGetuser()
    {
        $SAC = Yii::$app->request->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        $user = Users::findOne(['id' => $app_user['USER_ID']]);
        $booster_array = $user->getBooster();
       // Users::find()->join('LEFT JOIN', 'USER_BOOSTER', 'user_booster.user_id = users.id');
        $q1 = (new \yii\db\Query())
            ->select('*')
            ->from('users')
            ->where(['id' => $app_user['USER_ID']]);

        $q2 = (new \yii\db\Query())
            ->select('*')
            ->from('user_booster')
            ->where(['USER_ID' => $app_user['USER_ID']]);

        $q3 = $q1->innerJoin('user_booster','user_booster.USER_ID = users.id');
        $q5 = $q3->innerJoin('booster','booster.ID = BOOSTER_ID');

        $q4 = $q5->all();

        return $q4;

        //$res = Users::find()->innerJoinWith('booster')->all();
        //$res = UserBooster::find()->innerJoinWith('users')->where(['id'=>$user['id']])->all();

        // workable
        // SELECT id, user_booster.booster_id
        // FROM users
        // INNER JOIN user_booster
        // ON user_booster.USER_ID = users.id
        // WHERE user_booster.user_id = 263711717395626;

//        $answ = Users::find()
//        ->select('id, user_booster.booster_id')
//            ->from('users')
//            ->innerJoin('user_booster', '`user_booster`.`USER_ID` = `users`.`id`')
//            ->where(['user_booster.user_id' => $user['id']])
//            ->with('userBooster')
//            ->all();
//        $res2 = Users::find()
//            ->where(['id'=>$user['id']])
//            ->with(['userBooster','userBooster.booster'])
//            ->all();




    }
}