<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.11.2016
 * Time: 22:24
 */

namespace app\models;


use yii\db\ActiveRecord;
use yii;

class Users extends ActiveRecord
{
    public function setFriends($id_friends)
    {
        $id = $this->id;
        //$comand = Yii::$app->db->createCommand('SELECT * FROM frendship WHERE user_id = $id')->queryAll(); // подсоединяемся к базе данных друзей пользователя
        Yii::$app->db->createCommand()->delete("friendship","user_id = $id")->execute();
        for($i=0,$f_size=count($id_friends);$i<$f_size;$i++)
        {
            Yii::$app->db->createCommand()->insert('friendship',['user_id' => $id,
                                                                'friend_id' =>$id_friends[$i] ])->execute();


        }

    }



}