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
    public function actionPostauth()
    {
        $accessToken = Yii::$app->request->getBodyParam("fac");

        $app_obj = Application::findApp($accessToken);

        $fbUser = $app_obj->getUser($accessToken);
        $user_db = Users::findOne(['id' => $fbUser->getId()]);

        if($user_db == null)
        {
            $user_db = new Users();
        }
        $user_db->fbUpdate($fbUser);

        $user_db = AppUser::findOne(['app_id' => $app_obj['app_id'], 'user_id' => $fbUser->getId()]);
        if($user_db == null)
        {
            $user_db = new AppUser;
            $user_db->app_id = $app_obj['app_id'];
            $user_db->user_id = $fbUser->getId();
            $user_db->save();
        }
        return $user_db->setSAC();
    }

    public function actionGetuser()
    {
        $sac = Yii::$app->request->getHeaders()->get('sac');
        $app_user = AppUser::findOne(['sac' => $sac]);

        return Social::getUserMultitableData($app_user);
    }

    public function actionGetboosters()
    {
        $sac = Yii::$app->request->getHeaders()->get('sac');
        $app_user = AppUser::findOne(['sac' => $sac]);
        if($app_user)
        {
            return Booster::find()->all();
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionGetlives()
    {
        $sac = Yii::$app->request->getHeaders()->get('sac');
        $app_user = AppUser::findOne(['sac' => $sac]);
        if($app_user)
        {
            $app_user->refreshDate();
            $app_user->liveIncrement();
            return $app_user->find()->select('lives, next_update, server_timestamp')->one();
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionGetproducts()
    {
        $sac = Yii::$app->request->getHeaders()->get('sac');
        $app_user = AppUser::findOne(['sac' => $sac]);
        if($app_user)
        {
            return Product::find()->all();
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostprogress()
    {
        $sac = Yii::$app->request->getHeaders()->get('sac');
        $app_user = AppUser::findOne(['sac' => $sac]);
        if($app_user)
        {
            $app_user->uploadSavedGame($_FILES['binary']);
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostboosters()
    {
        $sac = Yii::$app->request->getHeaders()->get('sac');
        $app_user = AppUser::findOne(['sac' => $sac]);
        if($app_user)
        {
            $booster_id = Yii::$app->request->getBodyParam('booster_id');
            $ub = UserBooster::findOne(['user_id' => $app_user['user_id'], 'booster_id' => $booster_id]);
            $ub->useBooster();
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostboostersbuy()
    {
        $sac = Yii::$app->request->getHeaders()->get('sac');
        $app_user = AppUser::findOne(['sac' => $sac]);
        if($app_user)
        {
            $booster_id = Yii::$app->request->getBodyParam('booster_id');
            return $app_user->buyBooster($booster_id);
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostlives()
    {
        $sac = Yii::$app->request->getHeaders()->get('sac');
        $app_user = AppUser::findOne(['sac' => $sac]);
        if($app_user)
        {
            $app_user->liveIncrement();
            $app_user->setDate();
            return $app_user->find()->select('lives, next_update, server_timestamp')->one();
        }
        else
            throw new UnauthorizedHttpException("Please, make sure, that you have a correct one access token");
    }

    public function actionPostproducts()
    {
        $sac = Yii::$app->request->getHeaders()->get('sac');
        $app_user = AppUser::findOne(['sac' => $sac]);
        if($app_user)
        {
            $app = Application::findOne($app_user['app_id']);
            $signed_request = Yii::$app->request->getBodyParam('signed_request');
            //return $app;
            $fbApp = new Facebook\FacebookApp($app['app_id'], $app['app_secret']);
            $accessToken = $fbApp->getAccessToken();
            $signedRequest = new Facebook\SignedRequest($fbApp, $signed_request);
            $encodeSR = $signedRequest->getPayload();
            if($encodeSR['status'] == 'completed')
            {
                $req = @file_get_contents('https://graph.facebook.com/'.$encodeSR['payment_id'].'?access_token='.$accessToken.'&fields=id,application,items');
                $info = json_decode($req, true);
                $prod = Product::findOne(['product_url' => $info['items']['product']]);
                if($prod && $info['id'] == $app_user['user_id'])
                {
                    $app_user['money'] += $prod['money'];
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