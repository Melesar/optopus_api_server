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
use yii\web\BadRequestHttpException;

/**
 * You should get rid of request params validation checks in your actions,
 * since they are handled in the url manager
 */
class UsersController extends Controller
{
    public function actionGet()
    {
        if(!is_numeric(Yii::$app->request->get("id"))) //id isn't a number
        {
            throw new BadRequestHttpException();
        }
        $id = Yii::$app->request->get("id");
        $users = Users::findOne($id);
        if($users == null)
        {
            throw new NotFoundHttpException();
        }
        return $users->attributes;
    }

    public function actionPost($id)
    {
        if(!is_numeric($id))
        {
            throw new BadRequestHttpException();
        }
        $users = Users::findOne($id);
        if($users != null)
        {
            throw new BadRequestHttpException();
        }
        $data = Yii::$app->request->getBodyParams();
        $users = new Users();
        $users->id=$id;
        $users->setAttributes($data,false);
        $users->save();
        return $users->attributes;
    }

    public function actionPut($id)
    {
        if(!is_numeric($id))
        {
            throw new BadRequestHttpException();
        }
        $users = Users::findOne($id);
        if($users == null)
        {
            return $this->actionPost($id);
            //throw new yii\web\ConflictHttpException(); //409 � user already exist
        }
        else
        {
            $data = Yii::$app->request->getBodyParams();
            $users->setAttributes($data,false);
            $users->update();
            return $users->attributes;
        }
    }

    public function actionPutfriends($user_id)
    {
        if(!is_numeric($user_id))
        {
            throw new BadRequestHttpException();
        }
        $data = Yii::$app->request->getBodyParams();
        $users = Users::findOne($user_id);
        if($users == null)
        {
            throw new NotFoundHttpException();
        }
        $users->setfriends($data);
        return("PUT FRIENDS");
    }

    public function actionError(){
        $exception = new yii\web\HttpException(404, Yii::t('yii', 'Page not found.'));

        /** This exception is never null since you've just initialized it with 'new' keyword */
        if($exception !== null) {
            return ['name'      =>   $exception->getName(),
                    'code'      =>   $exception->statusCode,
                    'message'   =>   $exception->getMessage()];

        }
    }


}
