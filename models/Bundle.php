<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.01.2017
 * Time: 21:19
 */

namespace app\models;

use yii\db\ActiveRecord;
use yii;
use yii\web\NotFoundHttpException;

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
            return;

        $path = Yii::getAlias('@web').$requestData['bundle_size'];

        if(!is_dir($path))
            mkdir($path);

        $path .= "/".$requestData['name_format'];

        if(!is_dir($path))
            mkdir($path);

        if(!is_uploaded_file($uploadedData['tmp_name']))
            echo "File \"".$uploadedData['name']."\" is not uploaded!";
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
        $path = Yii::getAlias('@web').$requestData['bundle_size'].'/location_'.$requestData['name_format'];

        if(is_dir($path))
        {
            $zip = new \ZipArchive();
            $zip->open($path.'.zip', \ZipArchive::CREATE);
            $zip->addEmptyDir($path);
            $count = count(scandir($path)) - 2;

            for($i = 1; $i <= $count; $i++)
            {
                $zip->addFile($path.'/location_'.$i.'.txt');
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename=' . basename($path.'.zip'));
            header('Content-Length: ' . filesize($path.'.zip'));

            $byte = readfile($path.'.zip');
            unlink($path.'.zip');
            return $byte;
        }
    }

    public function findAndSend($requestData)
    {
        $path = Yii::getAlias('@web').$requestData['bundle_size'].'/location_'.$requestData['name_format'];


        if(file_exists($path.'/location_'.$requestData['number'].'.txt'))
        {
            $path.='/location_'.$requestData['number'].'.txt';
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
