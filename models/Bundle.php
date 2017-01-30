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

class Bundle extends ActiveRecord
{
    public $file_name;// = "nomatter_noname.zip"; //name of the file

    public function rules()
    {
        return[
        [['file_name'],'file','skipOnEmpty' => false, 'mimeTypes' => 'application/zip'],
        ];
    }

    /**
     * Need to set the 'path' attribute somewhere here too
     * @param $uploadedData - data from $_FILES array
     * @param $requestData - data from POST request
     */
    public function unpackAndSave($uploadedData, $requestData)
    {
        $binData = fopen($uploadedData['tmp_name'],'r');
        if($binData == null) {
            return;
        }
            $path = Yii::getAlias('@web');
            $path .= $requestData['bundle_size'];

            if(!is_dir($path))
                mkdir($path);

            $path .= "/".$requestData['name_format'];

            if(!is_dir($path))
                mkdir($path);

            if(!is_uploaded_file($uploadedData['tmp_name']))
                echo "File \"".$uploadedData['name']."\" is not uploaded";
            else {
                $pathFile = $path . "/" . $_FILES['byte_array']['name'];
                move_uploaded_file($uploadedData['tmp_name'], $pathFile);


                $zip = new \ZipArchive();
                $zip->open($pathFile);
                //if($zip->open($pathFile))
                $zip->extractTo($path);
                $zip->close();
                if(unlink($pathFile))
                    echo 'DELETED';
            }

    }
}
