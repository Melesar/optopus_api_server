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
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class LevelsController extends Controller
{
    public function actionGetdata()
    {
        if(is_numeric(Yii::$app->request->get("id")) == false)
        {
            throw new BadRequestHttpException();
        }
        $id =  Yii::$app->request->get('id');
        $level = Levels::findOne($id);
        if($level != null)
        {
            return $level->attributes;
        }
        else
        {
            throw new NotFoundHttpException();
        }
    }
    public function actionPostdata($id)
    {
        if(is_numeric($id) == false)
        {
            throw new BadRequestHttpException();
        }
        return $this->actionPutdata($id);   /** Reroute to the PUT action */
    }
    public function actionPutdata($id)
    {
        if(is_numeric($id) == false)
        {
            throw new BadRequestHttpException();
        }
        $level = Levels::findone($id);
        $data = Yii::$app->request->getBodyParams();

        if ($level == null)
        {
            $level = new Levels();          /**Create new level if it wasn't found in db*/
            $level->id = $id;
        }

        $level->setAttributes($data,false);
        $level->save();                     /**Set attributes and save the model regardless of whether it was in db or just created */

        return $level->attributes;
    }
    public function actionGetprogress()
    {
        if(is_numeric(Yii::$app->request->get("user_id")) == false)
        {
            throw new BadRequestHttpException();
        }
        $user_id = Yii::$app->request->get("user_id");
        $user = Users::findOne($user_id);
        if($user != null) // user was found in db
        {
            return $user->getProgress();
        }
        else
        {
            throw new NotFoundHttpException();  /** User wasn't found - send an 404 error */
        }
    }
    public function actionPostprogress()
    {
        $data = Yii::$app->request->getBodyParams();

        if(is_numeric(Yii::$app->request->get("user_id")) == false)
        {
            throw new BadRequestHttpException();
        }

        $user = Users::findOne($data['user_id']);

        if ($user == null)
        {
            throw new NotFoundHttpException();   /** Generate 404 if there is no such user */
        }
        return $user->updateProgress($data);

    }
    public function actionPutprogress()
    {
        return $this->actionPostprogress(); /** Reroute to POST action */
    }
    public function actionScore()
    {
        if(is_numeric(Yii::$app->request->get("user_id")) == false || is_numeric(Yii::$app->request->get("level_id")) == false)
        {
            throw new BadRequestHttpException();
        }
        $user_id = Yii::$app->request->get("user_id");
        $level_id = Yii::$app->request->get("level_id");
        $user = Users::findOne($user_id);
        $level = Levels::findOne($level_id);

        if ($user == null || $level == null)
        {
            throw new NotFoundHttpException();
        }

        return $user->getScore($level);
    }
}