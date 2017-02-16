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

    /**
     * Please, search for some way to generate a random string, not only a number
     */
    public function setSAC()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTYVWXYZ1234567890';
        $numChars = strlen($chars);
        $newSAC = '';
        for ($i = 0; $i < 18; $i++)
            $newSAC .= substr($chars, rand(1, $numChars) - 1, 1);

        while($this::findOne(['SAC' => $newSAC]) != null)
        {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTYVWXYZ1234567890';
            $numChars = strlen($chars);
            $newSAC = '';
            for ($i = 0; $i < 18; $i++)
                $newSAC .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        $this->SAC = $newSAC;
        $this->save();
        return $this;
    }

}