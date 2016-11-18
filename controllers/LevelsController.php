<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.11.2016
 * Time: 22:58
 */

namespace app\controllers;


use app\models\Levels;
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
        return $id;
    }
    public function actionPutdata($id)
    {
        $level = Levels::findone($id);
        if($level == null)
        {
            $data = Yii::$app->request->getBodyParams();
            $level = new Levels();
            $level->id = $id;
            $level->setAttributes($data,false);
            $level->save();
            return $level->attributes;
        }
        else if($level != null)
        {
            $data = Yii::$app->request->getBodyParams();
            $level->setAttributes($data,false);
            $level->save();
            return $level->attributes;
        }
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