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
     * This method used to contain too much redundant code.
     */
    public function setSAC()
    {
        /**
         * This method used to contain too much redundant code.
         * Refactored it for the sake of optimization
         */

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

}