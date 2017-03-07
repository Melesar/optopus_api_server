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
            'app_id'                 =>     $this->app_id,         //1360389767383525
            'app_secret'             =>     $this->app_secret,     //0816c6f7a545d8d930710cbe4d5c12e8
            'default_graph_version'  =>     'v2.8',                // name: Match Me Octopus Adventure
            'default_access_token'   =>     $this->app_secret,
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
        $req = @file_get_contents('https://graph.facebook.com/app/?access_token='.$accessToken);
        $app = json_decode($req, true);
        if ($app == null)
        {
            throw new NotFoundHttpException("There is a problem with your access token");
        }
        return $app['id'];
    }
}