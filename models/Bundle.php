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

    /**
     * Specifies extension of the files being processed.
     * Currently, asset bundles don't have any extension
     */
    const FILE_EXTENSION = "";

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
            $pathFile = $path . "/" . $uploadedData['name'];
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

        if(is_dir($path)) {
            $zip = new \ZipArchive();
            $zip->open($path . '.zip', \ZipArchive::CREATE);
            $zip->addEmptyDir($path);

            $file_count = -1; //Even empty directory has one *.* file

            $dir = opendir($path);
            while (false !== ($file = readdir($dir)))
                if (strpos($file, ".", 1))
                    $file_count++;

            $file_count_2 = count(scandir($path))-2; //Using this method, empty directory has two hidden files

            $count = $file_count_2 - $file_count;

            for($i = 1; $i <= $count; $i++)
            {
                $pathFile = preg_replace('/\{0\}/',$i,$requestData['name_format']);
                $zip->addFile($path . '/' . $pathFile . self::FILE_EXTENSION);
            }

            $zip->close();

            $header = Yii::$app->response->headers;
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
        $pathFile = preg_replace('/\{0\}/',$requestData['number'],$pathFile);
        $path .= $pathFile.self::FILE_EXTENSION;
        if(file_exists($path))
        {
            $header = Yii::$app->response->headers;
            $header->add('Content-Type','application/octet-stream');
            $header->add('Content-Disposition','attachment; filename='.basename($path));
            $header->add('Content-Length',filesize($path));

            $byte = readfile($path);
            return $byte;
        }
        else
            throw new NotFoundHttpException();
    }
}
