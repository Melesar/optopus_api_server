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
use app\models\Social;
use app\models\Users;

use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

use Facebook;


class SocialController extends Controller
{
    public function actionPosttoken()
    {

        $accessToken = Yii::$app->request->getBodyParam("FAC");

        $app_id = @file_get_contents('https://graph.facebook.com/app/?access_token='.$accessToken, NULL, NULL, 134, 16);
        if ($app_id === FALSE)
        {
            throw new NotFoundHttpException("There is a problem with your access token");
        }

        $app_obj = Application::findOne(['APP_ID' => $app_id]);

        $fbUser = $app_obj->getUser($accessToken);
        $user_db = Users::findOne(['id' => $fbUser->getId()]);

        if($user_db == null)
        {
            $user_db = new Users();
        }
        $user_db->fbUpdate($fbUser);

        $user_db = AppUser::findOne(['user_id' => $fbUser->getId()]);
        return $user_db->setSAC();

    }

}