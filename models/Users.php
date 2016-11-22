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
    public function setAttributes($values, $safeOnly = true)
    {
        parent::setAttributes($values, $safeOnly);

        $currentDate = new \DateTime();

        /** if this is the newly created user, its first_authorized value will be set to the current date */
        $this->first_authorized = $this->isNewRecord ? $currentDate->format(self::DATE_FORMAT) : $this->first_authorized;
        $this->last_online = $currentDate->format(self::DATE_FORMAT);
    }

    public function setFriends($id_friends)
    {
        if($id_friends != null) // check the array
        {
            $id = $this->id; // $id assign the value of current user (he's id)
            Yii::$app->db->createCommand()->delete("friendship", "user_id = $id")->execute(); // 'clearance' of the table "friendship"
            for ($i = 0, $f_size = count($id_friends); $i < $f_size; $i++)
                Yii::$app->db->createCommand()->insert('friendship', ['user_id' => $id, 'friend_id' => $id_friends[$i]])->execute();
                // the insert of the array "id_friends", which is related to our user ("id"),
                // to the empty table "friendship"
        }
    }
    public function getProgress()
    {
        $user = $this->id;
        $param = [':id_u' => $user];
        $user_friends = Yii::$app->db->createCommand("SELECT friend_id FROM friendship WHERE user_id=:id_u",$param)->queryAll();
        $req=[];
        for($i = 0,$user_size = count($user_friends); $i < $user_size; $i++)
        {
            $param = [':id_u' => $user_friends[$i]['friend_id']];
            $level = Yii::$app->db->createCommand("SELECT level_id, number FROM users_on_levels
                                                    JOIN levels ON users_on_levels.level_id = levels.id WHERE user_id=:id_u
                                                    ORDER BY number DESC, level_id DESC",$param)->queryOne();
            $params = [':id_u' => $user_friends[$i]['friend_id'], ':id_l' => $level['level_id']];
            $req[$i] = Yii::$app->db->createCommand("SELECT user_id,
                                                            level_id AS current_level_id,
                                                            reached_at
                                                     FROM users_on_levels
                                                     WHERE user_id=:id_u AND level_id=:id_l",$params)->queryOne();
        }

        file_put_contents("d:/file.txt", print_r($req, true));
        return $req;
    }
    public function updateProgress($data)
    {
        if($data['user_id'] != null && $data['level_id'] != null)
        {
            $id_u = $data['user_id'];
            $id_l = $data['level_id'];
            $params = [':id_u' => $id_u, ':id_l' => $id_l];
            $req = Yii::$app->db->createCommand("SELECT * FROM users_on_levels WHERE user_id=:id_u AND level_id=:id_l",$params)->queryOne();

            $data = $this->updateDates($data, $req);

            if($req == null)
                Yii::$app->db->createCommand()
                    ->insert('users_on_levels', $data)  /** $data itself contains all those key-value pairs that */
                    ->execute();                        /** you specified manually */
            else
                Yii::$app->db->createCommand()
                    ->update("users_on_levels", $data, ['user_id' => $id_u, 'level_id' => $id_l])
                    ->execute();
        }
    }

    const DATE_FORMAT = "Y-m-d H:i:s";

    /**
     * This function sets fields reached_at and completed_at based on the
     * information received from client and fetched from database
     *
     * @param $newRecord - information from client
     * @param null $oldRecord - information already stored in database
     * @return array - updated data
     */
    private function updateDates($newRecord, $oldRecord = null)
    {
        $now = new \DateTime();       /** Create date object which represents the current date */

        if ($oldRecord != null) {                                      /** If such line already exists in database, */
            $newRecord['reached_at'] = $oldRecord['reached_at'];       /** then user reached this level not in the first time */

            if ($oldRecord['is_completed'])                                /** Level was completed earlier */
            {
                $newRecord['completed_at'] = $oldRecord['completed_at'];   /** Set its completed date to database value */
                $newRecord['is_completed'] = true;
            }
            else if ($newRecord['is_completed'])                               /** Level was just completed */
                $newRecord['completed_at'] = $now->format(self::DATE_FORMAT);  /** Set the completed date to current one */
            else
                $newRecord['completed_at'] = null;                             /** Level wasn't completed so far. Leave date field as null */
        }
        else
        {
            $newRecord['reached_at'] = $now->format(self::DATE_FORMAT);
            $newRecord['completed_at'] = $newRecord['is_completed'] ? $now->format(self::DATE_FORMAT) : null;
        }

        return $newRecord;
    }

    public function getScore($level) // new method
    {
        $user = $this->id;
        $params = [':id_u' => $user, ':id_l' => $level['id']];
        $req = Yii::$app->db->createCommand(" SELECT friend_id, max_score AS score
                                              FROM users_on_levels
                                              LEFT JOIN friendship
                                              ON users_on_levels.user_id = friendship.friend_id
                                              WHERE friendship.user_id=:id_u
                                              AND level_id=:id_l",$params)->queryOne();
        return $req;
    }

}