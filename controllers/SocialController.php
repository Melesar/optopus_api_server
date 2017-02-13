<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10.02.2017
 * Time: 15:51
 */

namespace app\controllers;

use app\models\Application;
use Yii;
use yii\rest\Controller;
use app\models\Social;
use app\models\AppUser;
use Facebook;

class SocialController extends Controller
{
    public function actionPosttoken()
    {
        $accessToken = Yii::$app->request->getBodyParam("FAC");

        $app_id = file_get_contents('https://graph.facebook.com/app/?access_token='.$accessToken, NULL, NULL, 134, 16);
        $app_obj = Application::findOne(['APP_ID' => $app_id]);

        $fb = new Facebook\Facebook([
            'app_id'                 => $app_obj['APP_ID'],
            'app_secret'             => $app_obj['APP_SECRET'],
            'default_graph_version'  => 'v2.8',
            'default_access_token'   => $app_obj['APP_SECRET'],
        ]);

        $appsecret_proof = hash_hmac('sha256', $accessToken, $app_obj['APP_SECRET']);

        $res = $fb->get('me?fields=id,first_name,last_name,gender,age_range',$accessToken);
        if($res != null) {
            $user = $res->getGraphUser();
            //$user->getName();
            return $user->getFieldNames();
        }
        else
            return 'null';

//
//        $newSAC = rand(0, 1); // 20 numbers => 100000000000000000000
//        while(AppUser::findOne(['SAC' => $newSAC]) != null)
//            $newSAC = rand(0, 2);
//
//        return $newSAC;
        

    }

}