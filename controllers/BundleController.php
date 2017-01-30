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

class BundleController extends Controller
{
    public function actionGet()
    {
        return 1;
    }

    public function actionPost()
    {
        $newBundle = new Bundle();
        $data = Yii::$app->request->getBodyParams();
        $newBundle->setAttributes($data,false);
        $newBundle->save();
//        return $newBundle->attributes;

        $binData = fopen($_FILES['byte_array']['tmp_name'],'r');
        if($binData != null)
        {
            $path = Yii::getAlias('@web');
            $path .= $data['bundle_size'];

            if(!is_dir($path))
                mkdir($path);

            $path .= "/".$data['name_format'];

            if(!is_dir($path))
                mkdir($path);

            if(!is_uploaded_file($_FILES['byte_array']['tmp_name']))
                echo "File \"".$_FILES['byte_array']['name']."\" is not uploaded";
            else {
                $pathFile = $path . "/" . $_FILES['byte_array']['name'];
                move_uploaded_file($_FILES['byte_array']['tmp_name'], $pathFile);


                $zip = new \ZipArchive();
                $zip->open($pathFile);
                //if($zip->open($pathFile))
                $zip->extractTo($path);
                $zip->close();
                if(unlink($pathFile))
                    echo 'DELETED';
            }


            //file_put_contents("D://file.txt", print_r($_FILES, true));



            //echo $_FILES['byte_array']['type'];

            //$fp = fopen($path."/"."any.zip", "a");
            //$contents = fread($binData, $_FILES['byte_array']['size']);
            //exec('unzip any.zip');
            //$test = fwrite($fp, $contents);
            //fclose($fp);
        }
    }

    public function actionGetnumber()
    {
        return 222;
    }
}
