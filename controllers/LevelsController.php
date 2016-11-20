<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.11.2016
 * Time: 22:58
 */

namespace app\controllers;


use app\models\Levels;
use app\models\Users; // added to find user_id in actionGetprogress
use yii\rest\Controller;
use Yii;

class LevelsController extends Controller
{
    public function actionGetdata()
    {
        return Yii::$app->request->get('id');
    }
    public function actionPostdata($id)
    {
        return $id; // unused => Putdata consumed Postdata
    }
    public function actionPutdata($id)
    {
        $level = Levels::findone($id);
        if($level === null) // POST
        {
            $data = Yii::$app->request->getBodyParams();
            $level = new Levels();
            $level->id = $id;
            $level->setAttributes($data,false);
            $level->save();
            return $level->attributes;
        }
        else // PUT
        {
            $data = Yii::$app->request->getBodyParams();
            $level->setAttributes($data,false);
            $level->save();
            return $level->attributes;
        }
    }
    public function actionGetprogress()
    {
        $user_id = Yii::$app->request->get("user_id");
        $user = Users::findOne($user_id);
        if($user != null) // user was found in db
        {
            return $user->getLevels();
        }
    }
    public function actionPostprogress()
    {
        $data = Yii::$app->request->getBodyParams();
        $user = Users::findOne($data['user_id']); // доступ к элементу по клбючу user_id
        $user->updateProgress($data);
        return ("POST PROGRESS");

    }
    public function actionPutprogress()
    {
        return("PUT PROGRESS");
    }
    public function actionScore()
    {
        return("GET SCORE");
    }

}