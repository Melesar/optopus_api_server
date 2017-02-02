<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.01.2017
 * Time: 21:09
 */

namespace app\controllers;

use Composer\Downloader\ZipDownloader;
use Yii;
use yii\rest\Controller;
use yii\web\UploadedFile;
use app\models\Bundle;
use yii\web\BadRequestHttpException;

class BundleController extends Controller
{
    public function actionGet()
    {
        $data = Yii::$app->request->get();
        $serverBundle = Bundle::findOne([
            'project_id' => $data['project_id'],
            'name_format'=> $data['name_format'],
            'bundle_size'=> $data['bundle_size']
        ]);
        if($serverBundle != null)
            return $serverBundle->packAndSend($data);
        else
            throw new BadRequestHttpException();
    }

    public function actionPost()
    {
        $newBundle = new Bundle();
        $data = Yii::$app->request->getBodyParams();
        $path = $data['bundle_size']."@".$data['name_format'];
        $newBundle->unpackAndSave($_FILES['archive'], $data);
        $newBundle->setAttributes($data,false);
        $newBundle->setAttribute('path',$path);
        //$newBundle->save();

        if(!$newBundle->save() || $newBundle == null)
            throw new BadRequestHttpException();

        return $newBundle->attributes;
    }

    public function actionGetnumber()
    {
        $data = Yii::$app->request->get();
        $serverBundle = Bundle::findOne([
            'project_id' => $data['project_id'],
            'name_format'=> $data['name_format'],
            'bundle_size'=> $data['bundle_size']
        ]);
        if($serverBundle != null)
            return $serverBundle->findAndSend($data);
        else
            throw new BadRequestHttpException();
    }
}
