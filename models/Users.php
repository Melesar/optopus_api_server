<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.11.2016
 * Time: 22:24
 */

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
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
        Friendship::deleteAll(['user_id' => $this->id]);
        foreach ($id_friends as $friendId)
        {
            $f = new Friendship();
            $f->load(['user_id' => $this->id, 'friend_id' => $friendId], "");
            $f->save();
        }
    }

    /**
     * This thing is terrific.
     * Consider using ActiveRecord::hasMany for merging tables
     * and sql orderBy for sorting. Please, never use any kind of custom sort
     * with database entities
     */
    public function getCurrentLevelId()
    {
        /*I also didn't come to the way of using hasMany/hasOne
        I've just avoided like it's shown below*/
        $userOnLevel = UsersOnLevels::findAll(['user_id' => $this->id]);
        $userOnLevel = ArrayHelper::getColumn($userOnLevel, 'level_id');
        $level = Levels::findAll(['id' => $userOnLevel]);
        $level = ArrayHelper::toArray($level,['id','number']);
        ArrayHelper::multisort($level,['id','number'],[SORT_DESC,SORT_DESC]);
        return ArrayHelper::getValue($level[0],['id']);
    }

    public function getProgress()
    {
        $result = [];
        $friends = Friendship::findAll(['user_id' => $this->id]);
        $friends = ArrayHelper::getColumn($friends,'friend_id');
        $friend = Users::findAll(['id' => $friends]);
        foreach($friend as $f)
        {
            $lvl = $f->getCurrentLevelId();
            $level = UsersOnLevels::findOne(['user_id' => $f->id,'level_id' => $lvl]);
            /*I understand, that above string shouldn't exist at all, but I have no idea how to avoid using it*/
            $result[] = ['user_id' => $f->id,
                'current_level_id' => $level->level_id,
                'reached_at' => $level->reached_at];
        }
        return $result;
    }
    public function updateProgress($data)
    {
        if($data['user_id'] != null && $data['level_id'] != null)
        {
            $req = UsersOnLevels::findOne(['user_id' => $data['user_id'], 'level_id' => $data['level_id']]);
            $data = $this->updateDates($data, $req);
            if($req == null)
            {
                $newOne = new UsersOnLevels();
                $newOne->setAttributes($data,false);
                $newOne->save();
                /*I used setAttributes() instead of load(), because second one
                requires a value in the field $formName, which I didn't get at all*/
            }
            else
            {
                $oldOne = UsersOnLevels::findOne(['user_id' => $data['user_id'], 'level_id' => $data['level_id']]);
                $oldOne->setAttributes($data,false);
                $oldOne->save();
            }
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
        $friends = Friendship::findAll(['user_id'=>$this->id]);
        $friends_id = ArrayHelper::getColumn($friends,'friend_id');
        $result = [];
        $scores = UsersOnLevels::findAll(['level_id'=>$level->id,'user_id'=>$friends_id]);
        /*To create result I used foreach -
        if you know how to extract values with out it, please explain me how*/
        foreach ($scores as $sc)
            $result[] = ['user_id' => $sc['user_id'],
                       'max_score' => $sc['max_score']];
        if($result == null)
            return("NO FRIENDS AT THIS LEVEL HAS BEEN FOUND");
        return $result;
    }

}