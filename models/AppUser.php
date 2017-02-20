<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:14
 */

namespace app\models;

use yii\db\ActiveRecord;

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
}