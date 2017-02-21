<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10.02.2017
 * Time: 15:51
 */

namespace app\controllers;

use app\models\Application;
use app\models\AppUser;
use app\models\Booster;
use app\models\Product;
use app\models\Social;
use app\models\UserBooster;
use app\models\Users;


use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;


use Facebook;


class SocialController extends Controller
{
    const DB_NAME = 'octopus';
    const DB_HOST = 'localhost';
    public function actionPosttoken()
    {
        $accessToken = Yii::$app->request->getBodyParam("FAC");

        $app_obj = Application::findApp($accessToken);

        $fbUser = $app_obj->getUser($accessToken);
        $user_db = Users::findOne(['id' => $fbUser->getId()]);

        if($user_db == null)
        {
            $user_db = new Users();
        }
        $user_db->fbUpdate($fbUser);

        $user_db = AppUser::findOne(['app_id' => $app_obj['APP_ID'], 'user_id' => $fbUser->getId()]);
        if($user_db == null)
        {
            $user_db = new AppUser;
            $user_db->APP_ID = $app_obj['APP_ID'];
            $user_db->USER_ID = $fbUser->getId();
            $user_db->save();
        }
        return $user_db->setSAC();
    }

    public function actionGetuser()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);

        return Social::getUserMultitableData($app_user);
    }

    public function actionGetbooster()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            return Booster::find()->all();
        }
        else
            throw new NotFoundHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionGetlives()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            $app_user->refreshDate();
            $app_user->save();
            return $app_user->find()->select('LIVES, NEXT_UPDATE, SERVER_TIMESTAMP')->one();
        }
        else
            throw new NotFoundHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionGetproduct()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            return Product::find()->all();
        }
        else
            throw new NotFoundHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostprogress()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            $binData = fopen($_FILES['binary']['tmp_name'],'r');
            if($binData == null)
                throw new BadRequestHttpException();
            $path = Yii::getAlias('@web').'test_save';

            if(!is_dir($path))
                mkdir($path);

            if(is_uploaded_file($_FILES['binary']['tmp_name']))
            {
                $path .= '/'.$_FILES['binary']['name'];
                move_uploaded_file($_FILES['binary']['tmp_name'], $path);


            }

            return $path;

        }
        else
            throw new NotFoundHttpException("Please, make sure, that you have a correct one access token");
    }
}