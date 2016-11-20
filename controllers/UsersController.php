<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.2016
 * Time: 22:31
 */

namespace app\controllers;

use app\models\Users;
use yii\rest\Controller;
use yii;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{

    public function actionData()
    {
       return("HELLO!");
    }

    public function actionGet()
    {
        $id = Yii::$app->request->get('id');
        $users = Users::findOne($id);
        return $users->attributes;
    }

    public function actionPost($id)
    {
        $data = Yii::$app->request->getBodyParams();
        $users = new Users();
        $users->id=$id;
        $users->setAttributes($data,false);
        $users->save();
        return $users->attributes;
    }

    public function actionPut($id)
    {
        $users = Users::findOne($id);
        if($users === null) // POST
        {
            $data = Yii::$app->request->getBodyParams();
            $users = new Users();
            $users->id = $id;
            $users->setAttributes($data,false);
            $users->save();
            return $users->attributes;
        }
        else // PUT
        {
            $data = Yii::$app->request->getBodyParams();
            $users->setAttributes($data,false);
            $users->update();
            return $users->attributes;
        }
    }

    public function actionPutfriends($user_id)
    {
        $data = Yii::$app->request->getBodyParams();
        $users = Users::findOne($user_id);
        if($users === null)
        {
            throw new NotFoundHttpException();
        }
        $users->setfriends($data);
        return("PUT FRIENDS");
    }
}
