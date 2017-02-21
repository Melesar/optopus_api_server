<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:14
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;

class AppUser extends ActiveRecord
{
    const DATE_FORMAT = "Y-m-d H:i:s";

    public function setSAC()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTYVWXYZ1234567890';
        $numChars = strlen($chars);

        do {
            $newSAC = '';
            for ($i = 0; $i < 18; $i++)
                $newSAC .= substr($chars, rand(1, $numChars) - 1, 1);
        }while($this::findOne(['SAC' => $newSAC]) != null);

        $this->SAC = $newSAC;
        $this->save();
        return $this;
    }

    public function refreshDate()
    {
        $currentDate = new \DateTime();
        $interval = new \DateInterval('PT5M');
        $this->SERVER_TIMESTAMP = $currentDate->format(self::DATE_FORMAT);
        if ($this->SERVER_TIMESTAMP >= $this->NEXT_UPDATE && $this->LIVES < 5)
        {
            $this->LIVES++;
            $this->NEXT_UPDATE = $currentDate->add($interval)->format(self::DATE_FORMAT);
        }
    }

    public function uploadSavedGame($uploadedData)
    {
        $binData = fopen($_FILES['binary']['tmp_name'],'r');
        if($binData == null)
            throw new BadRequestHttpException();
        $path = Yii::getAlias('@web').'test_save';

        if(!is_dir($path))
            mkdir($path);

        if(is_uploaded_file($uploadedData['tmp_name']))
        {
            $path .= '/'.$uploadedData['name'];
            move_uploaded_file($uploadedData['tmp_name'], $path);
        }
        $this->SAVED_GAME = $path;
        $this->save();
    }
}