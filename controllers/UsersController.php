<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.2016
 * Time: 22:31
 */

namespace app\controllers;

use yii\rest\Controller;
use yii;

class UsersController extends Controller
{

    public function actionData() //��� - ������ �����, �.�. Data
    {
       return("HELLO!");
    }

    public function actionGet()
    {

        return Yii::$app->request->get('id'); //���������� id ������������
    }

    public function actionPost($id)
    {
        return $id;
    }

    public function actionPut()
    {
        return ("PUT");
    }

    public function actionPostfriends()
    {
        return("POST FRIENDS");
    }

    public function actionPutfriends()
    {
        return("PUT FRIENDS");
    }
}
