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
        Friendship::deleteAll(['user_id'=>$this->id]);
        for($i=0;$i<count($id_friends);$i++)
        {
            $f = new Friendship();
            $f->user_id = $this->id;
            $f->friend_id = $id_friends[$i];
            $f->save();
        }
    }

    public function getCurrentLevelId(){
        $usersOnLevels = UsersOnLevels::find()
            ->select(['level_id'])
            ->where(['user_id' => $this->id])
            ->all();
        $levels_id = [];
        foreach($usersOnLevels as $lvl)
            $levels_id[] = $lvl->level_id;
        $levels = Levels::find()
            ->where(['in','id',$levels_id])
            ->all();
        $maxLvlId = 0;
        $maxLvlNumber = 0;
        foreach($levels as $lvl)
            if($lvl->number >= $maxLvlNumber) {
                $maxLvlId = $lvl->id;
                $maxLvlNumber = $lvl->number;
            }

        return $maxLvlId;
    }

    public function getProgress()
    {
        $friends = Friendship::find()
            ->select(['friend_id'])
            ->where(['user_id'=>$this->id])
            ->all();
        $result = [];

        foreach($friends as $fr) {

            $friend = Users::find()
                ->where(['id' =>$fr->friend_id])
                ->one();

            $frCurLevel = $friend->getCurrentLevelId();
            if ($frCurLevel != 0){
                $level = UsersOnLevels::find()
                    ->where(['user_id' => $friend->id,
                        'level_id' => $frCurLevel])
                    ->one();
            } else {
                $level = new UsersOnLevels();
                $level->level_id = null;
                $level->reached_at = null;
            }

            $result[] = ['user_id' => $friend->id,
                        'current_level_id' => $level->level_id,
                        'reached_at' => $level->reached_at];
        }
        return $result;
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
                    ->insert('users_on_levels', $data)  // $data itself contains all those key-value pairs that
                    ->execute();                        // you specified manually
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
        $friends = Friendship::find()

            ->select(['friend_id'])
            ->where(['user_id'=>$this->id])
            ->all();
        $friends_id = [];
        foreach($friends as $fr)
            $friends_id[] = $fr->friend_id;

        $scores = UsersOnLevels::find()
            ->select(['user_id','max_score'])
            ->where(['level_id'=>$level->id])
            ->andWhere(['in','user_id',$friends_id])
            ->all();
        if($scores == null)
            return("NO FRIENDS AT THIS LEVEL HAS BEEN FOUND");
        return $scores;
    }

}