<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:13
 */

namespace app\models;

use yii\db\ActiveRecord;

use Facebook;
use yii\web\NotFoundHttpException;

class Application extends ActiveRecord
{

    public function getUser($accessToken)
    {
        $fb = new Facebook\Facebook([
            'app_id'                 =>     $this->APP_ID,         //1360389767383525
            'app_secret'             =>     $this->APP_SECRET,     //0816c6f7a545d8d930710cbe4d5c12e8
            'default_graph_version'  =>     'v2.8',                // name: Match Me Octopus Adventure
            'default_access_token'   =>     $this->APP_SECRET,
        ]);

        $res = $fb->get('me?fields=id,first_name,last_name,gender,age_range,picture{url}',$accessToken);
        if($res != null)
        {
            $fbUser = $res->getGraphUser();
            return $fbUser;
        }
        else
            throw new NotFoundHttpException("There is no such a user in Facebook");

    }

    public static function findApp($accessToken)
    {
        $app_id = @file_get_contents('https://graph.facebook.com/app/?access_token='.$accessToken, NULL, NULL, 134, 16);
        if ($app_id === FALSE)
        {
            throw new NotFoundHttpException("There is a problem with your access token");
        }

        return Application::findOne(['APP_ID' => $app_id]);
    }
}