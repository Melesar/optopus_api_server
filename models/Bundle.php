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
}
