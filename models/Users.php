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
    /**
     * А что будет, если данные будут невалидны?
     * Например, прийдет JSON типа {"A":"B", "C":"D"}
     * Или, например, null? Или вообще строка вместо массива?
     */
    public function setFriends($id_friends)
    {
        if($id_friends != null)
        {
            $id = $this->id;
            // подсоединяемся к базе данных друзей пользователя и выполняем удаление всех записей "друзей"
            // для заданого позльзователя, чей id равен user_id
            Yii::$app->db->createCommand()->delete("friendship", "user_id = $id")->execute();
            // запоняем пустую базу данных значениями массива $id_friends
            for ($i = 0, $f_size = count($id_friends); $i < $f_size; $i++)
                Yii::$app->db->createCommand()->insert('friendship', ['user_id' => $id, 'friend_id' => $id_friends[$i]])->execute();
        }
    }
}