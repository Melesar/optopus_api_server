<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.02.2017
 * Time: 19:14
 */

namespace app\models;

use app\commands\HelloController;
use app\controllers\SocialController;
use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\base\Model;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class AppUser extends ActiveRecord
{
    const DATE_FORMAT = "Y-m-d H:i:s";
    const LIVE_LIMIT = 5;

    public function setSAC()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTYVWXYZ1234567890';
        $numChars = strlen($chars);

        do {
            $newSAC = '';
            for ($i = 0; $i < 18; $i++)
                $newSAC .= substr($chars, rand(1, $numChars) - 1, 1);
        }while($this::findOne(['sac' => $newSAC]) != null);

        $this->SAC = $newSAC;
        $this->save();
        return $this;
    }

    public function refreshDate()
    {
        $currentDate = new \DateTime();
        $this->server_timestamp = $currentDate->format(self::DATE_FORMAT);
        $this->save();
    }

    public function setDate()
    {
        $this->refreshDate();
        $currentDate = new \DateTime();
        $interval = new \DateInterval('PT15M');
        if ($this->lives > 0)
        {
            $this->lives--;
            $this->next_update = $currentDate->add($interval)->format(self::DATE_FORMAT);
            $this->save();
        }
        elseif($this->lives == 0)
        {
            $timeToWait = date("i:s", strtotime($this->next_update) - strtotime($this->server_timestamp));
            throw new BadRequestHttpException("You are out of lives! So wait " . $timeToWait . " minutes for recovery.");
        }
    }

    public function liveIncrement()
    {
        $currentDate = new \DateTime();
        $interval = new \DateInterval('PT15M');
        $this->refreshDate();
        if ($this->lives < self::LIVE_LIMIT)
        {
            if ($this->server_timestamp >= $this->next_update)
            {
                $this->lives++;
                if($this->lives < self::LIVE_LIMIT)
                    $this->next_update = $currentDate->add($interval)->format(self::DATE_FORMAT);
                $this->save();
            }
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
        $this->saved_game = $path;
        $this->save();
    }

    public function buyBooster($booster_id)
    {
        $b = Booster::findOne(['id' => $booster_id]);
        if($b)
        {
            if($this->money >= $b["cost"])
            {
                $this->money -= $b["cost"];
                $ub = UserBooster::findOne(['user_id' => $this->user_id,'booster_id' => $booster_id]);
                $ub['amount'] += 1;
                $ub -> save();
                return $ub;
            }
            else
                throw new BadRequestHttpException("You have not enough money");
        }
        else
            throw new NotFoundHttpException("There is no such a booster");
    }

    public function rules()
    {
        return [
            [ ['money'], 'default', 'value' => '0'],
            [ ['lives'], 'default', 'value' => '0'],
            [ ['next_update'], 'default', 'value' => function(){
                $nextUpdate = new \DateTime();
                $interval = new \DateInterval('PT15M');
                $nextUpdate->add($interval)->format(self::DATE_FORMAT);
                return $nextUpdate;
            }],
            [ ['server_timestamp'], 'default', 'value' => function(){
                $currentDate = new \DateTime();
                $currentDate->format(self::DATE_FORMAT);
                return $currentDate;
            }],
        ];
    }
}