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
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

use Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;


class SocialController extends Controller
{
    public function actionPostfb()
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
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionGetlive()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            $app_user->refreshDate();
            $app_user->liveIncrement();
            return $app_user->find()->select('LIVES, NEXT_UPDATE, SERVER_TIMESTAMP')->one();
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
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
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostprogress()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            $app_user->uploadSavedGame($_FILES['binary']);
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostbooster()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            $booster_id = Yii::$app->request->getBodyParam('BOOSTER_ID');
            $ub = UserBooster::findOne(['USER_ID' => $app_user['USER_ID'], 'BOOSTER_ID' => $booster_id]);
            $ub->useBooster();
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostboosterbuy()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            $booster_id = Yii::$app->request->getBodyParam('BOOSTER_ID');
            return $app_user->buyBooster($booster_id);

        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostlive()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            $app_user->liveIncrement();
            $app_user->setDate();
            return $app_user->find()->select('LIVES, NEXT_UPDATE, SERVER_TIMESTAMP')->one();

        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostproduct()
    {
        $SAC = Yii::$app->request->getHeaders()->get('SAC');
        $app_user = AppUser::findOne(['SAC' => $SAC]);
        if($app_user)
        {
            $app = Application::findOne($app_user['APP_ID']);
            $signed_request = Yii::$app->request->getBodyParam('signed_request');
            //return $app;
            $fbApp = new Facebook\FacebookApp($app['APP_ID'], $app['APP_SECRET']);
            $accessToken = $fbApp->getAccessToken();
            $signedRequest = new Facebook\SignedRequest($fbApp, $signed_request);
            $encodeSR = $signedRequest->getPayload();
            if($encodeSR['status'] == 'completed')
            {
                $info = @file_get_contents('https://graph.facebook.com/'.$encodeSR['payment_id'].'?access_token='.$accessToken.'&fields=id,application,items',NULL,NULL,134,16);
                $prod = Product::findOne(['PRODUCT_URL' => $info['items']['product']]);
                if($prod && $info['id'] == $app_user['USER_ID'])
                {
                    $app_user['MONEY'] += $prod['MONEY'];
                    $app_user->save();
                }
                else
                    throw new BadRequestHttpException('Please, make sure, that you have choose proper product and you are using your account');

            }
            else
                throw new FacebookSDKException('Your transaction status is '.$encodeSR['status']);
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }
}