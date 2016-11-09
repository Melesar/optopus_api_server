<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.11.2016
 * Time: 22:58
 */

namespace app\controllers;


use yii\rest\Controller;
use Yii;

class LevelsController extends Controller
{
    public function actionData()
    {
        return("HI, THERE!!");
    }
    public function actionGetdata()
    {
        return Yii::$app->request->get('id');
    }
    public function actionPostdata($id)
    {
        return $id;
    }
    public function actionPutdata()
    {
        return("PUT DATA");
    }
    public function actionGetprogress()
    {
        return("GET PROGRESS");
    }
    public function actionPostprogress()
    {
        return("POST PROGRESS");
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