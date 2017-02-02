<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.01.2017
 * Time: 21:19
 */

namespace app\models;

use Symfony\Component\Finder\Expression\Regex;
use yii\db\ActiveRecord;
use yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;

class Bundle extends ActiveRecord
{
    public $file_name; //name of the file

//    public function rules()
//    {
//        return[
//            [],
//            [['file_name'],'file','skipOnEmpty' => false, 'mimeTypes' => 'application/zip'],
//        ];
//    }

    public function unpackAndSave($uploadedData, $requestData)
    {
        $binData = fopen($uploadedData['tmp_name'],'r');
        if($binData == null)
            throw new BadRequestHttpException();

        $path = Yii::getAlias('@web').$requestData['bundle_size']."@".$requestData['name_format'];

        if(!is_dir($path))
            mkdir($path);

        if(!is_uploaded_file($uploadedData['tmp_name']))
            throw new ForbiddenHttpException();
        else
        {
            $pathFile = $path . "/" . $_FILES['byte_array']['name'];
            move_uploaded_file($uploadedData['tmp_name'], $pathFile);

            $zip = new \ZipArchive();
            $zip->open($pathFile);
            $zip->extractTo($path);
            $zip->close();
            unlink($pathFile);
        }
    }

    public function packAndSend($requestData)
    {
        $path = Yii::getAlias('@web').$requestData['bundle_size'].'@'.$requestData['name_format'];

        if(is_dir($path))
        {
            $zip = new \ZipArchive();
            $zip->open($path.'.zip', \ZipArchive::CREATE);
            $zip->addEmptyDir($path);

            $name_format = preg_replace('/[^_]+$/s', '', $requestData['name_format']);

            for($i = 1; $i <= $requestData['bundle_size']; $i++)
            {

                $zip->addFile($path.'/'.$name_format.$i.'.txt');
            }
            $zip->close();

            $header = Yii::$app->request->headers;
            $header->add('Content-Type','application/zip');
            $header->add('Content-Disposition','attachment; filename='.basename($path.'.zip'));
            $header->add('Content-Length',filesize($path.'.zip'));

            $byte = readfile($path.'.zip');
            unlink($path.'.zip');
            return $byte;
        }
        else
            throw new NotFoundHttpException();
    }

    public function findAndSend($requestData)
    {
        $path = Yii::getAlias('@web').$requestData['bundle_size'].'@'.$requestData['name_format'];
        $pathFile = '/'.$requestData['name_format'];
        $pathFile = preg_replace('/\d{1,}/',$requestData['number'],$pathFile);
        $path .= $pathFile.'.txt';
        if(file_exists($path))
        {
            //if you send *.txt file, server writes at the end of it the amount of symbols
            header('Content-Type: text/*');
            header('Content-Disposition: attachment; filename=' . basename($path));
            header('Content-Length: ' . filesize($path));
            $byte = readfile($path);
            return $byte;
        }
        else
            throw new NotFoundHttpException();
    }
}
