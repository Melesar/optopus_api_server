<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10.02.2017
 * Time: 15:51
 */

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\social;
use Facebook;

class SocialController extends Controller
{
    public function actionPosttoken()
    {

//        $fb = new Facebook\Facebook([
//           'app_id'                 => '1360400847382417',
//           'app_secret'             => '4dec3438df76223efe0e5557539c15e1',
//           'default_graph_version'  => 'v2.8',
//        ]);
        $data = Yii::$app->request->getBodyParams();
        return $data["FAC"];




    }

}