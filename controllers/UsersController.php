<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.2016
 * Time: 22:31
 */

namespace app\controllers;

use yii\rest\Controller;

class UsersController extends Controller
{
    public function actionData() //имя - вторая часть, т.е. Data
    {
        return("HELLO!");
    }

    public function actionGet()
    {
        return("GET");
    }

    public function actionPost()
    {
        return("POST");
    }

    public function actionPut()
    {
        return("PUT");
    }

    public function actionGetFriends()
    {
        return("GET FRIENDS");
    }

    public function actionPostFriends()
    {
        return("POST FRIENDS");
    }
}
