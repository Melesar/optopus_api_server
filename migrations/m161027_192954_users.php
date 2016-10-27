<?php

use yii\db\Migration;
use yii\db\Schema;

class m161027_192954_users extends Migration
{
    private $usersOnLevelPk = "users_on_levels_pk";
    private $usersOnLevelUsersFk = "users_on_levels_users_fk";
    private $usersOnLevelLevelFk = "users_on_levels_levels_fk";
    private $friendshipPk = "friendship_pk";
    private $friendshipFk1 = "friendship_fk_1";
    private $friendshipFk2 = "friendship_fk_2";

    public function up()
    {
        $this->createTable('users', [
            'id' => Schema::TYPE_INTEGER. " PRIMARY KEY",
            'name' => Schema::TYPE_STRING . " NOT NULL",
            'last_name' => Schema::TYPE_STRING. " NOT NULL",
            'avatar_url' => Schema::TYPE_INTEGER,
            'first_authorized' => Schema::TYPE_DATE,
            'last_online' => Schema::TYPE_DATE,
        ]);

        $this->createTable('levels', [
            'id' => Schema::TYPE_INTEGER. " PRIMARY KEY",
            'number' => Schema::TYPE_INTEGER . " NOT NULL",
        ]);

        $this->createTable('users_on_levels', [
            'user_id' => $this->integer(),
            'level_id' => $this->integer(),
            'max_score' => $this->integer(),
            'is_completed' => $this->boolean(),
            'completed_at' => $this->date(),
            'reached_at' => $this->date()
        ]);

        $this->addPrimaryKey($this->usersOnLevelPk, 'users_on_levels', ['user_id', 'level_id']);
        $this->addForeignKey(
            $this->usersOnLevelUsersFk,
            'users_on_levels',
            'user_id',
            'users',
            'id'
        );
        $this->addForeignKey(
            $this->usersOnLevelLevelFk,
            'users_on_levels',
            'level_id',
            'levels',
            'id'
        );

        $this->createTable('friendship', [
            'user_id' => $this->integer(),
            'friend_id' => $this->integer(),
        ]);

        $this->addPrimaryKey($this->friendshipPk, 'friendship', ['user_id', 'friend_id']);
        $this->addForeignKey(
            $this->friendshipFk1,
            'friendship',
            'user_id',
            'users',
            'id'
            );
        $this->addForeignKey(
            $this->friendshipFk2,
            'friendship',
            'friend_id',
            'users',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey($this->friendshipFk1, 'friendship');
        $this->dropForeignKey($this->friendshipFk2, 'friendship');
        $this->dropTable('friendship');

        $this->dropForeignKey($this->usersOnLevelUsersFk, 'users_on_levels');
        $this->dropForeignKey($this->usersOnLevelLevelFk, 'users_on_levels');
        $this->dropTable('users_on_levels');

        $this->dropTable('users');
        $this->dropTable('levels');

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
