<?php

use yii\db\Migration;

class m170215_153913_apps extends Migration
{
    public $user_boosterUserFk = 'user_booster_user_fk';
    public $user_boosterBoosterFk = 'user_booster_booster_fk';
    public $app_userUserFk = 'app_user_user_fk';
    public $app_userAppFk = 'app_user_app_fk';
    public $app_boosterAppFk = 'app_booster_app_fk';
    public $app_boosterBoosterFk = 'app_booster_booster_fk';
    public $app_productAppFk = 'app_product_app_fk';
    public $app_productProductFk = 'app_product_product_fk';

    public function up()
    {
        $this->createTable('product', [
            'id' => $this->bigInteger(),
            'name' => $this->string(),
            'description' => $this->string(),
            'image_url' => $this->string(),
            'price' => $this->float(),
            'currency'=> $this->string(),
            'product_url' => $this->string(),
            'money' => $this->integer(),
            'PRIMARY KEY(id)'
        ]);

        $this->createTable('booster', [
            'id' => $this->bigInteger(),
            'alias' => $this->string(),
            'name' => $this->string(),
            'description' => $this->string(),
            'cost' => $this->float(),
            'PRIMARY KEY(id)'
        ]);

        $this->createTable('application', [
            'app_id' => $this->bigInteger(),
            'app_secret' => $this->string(),
            'app_name' => $this->string(),
            'PRIMARY KEY(app_id)'
        ]);

        /* USER_BOOSTER */
        $this->createTable('user_booster', [
            'user_id' => $this->bigInteger(),
            'booster_id' => $this->bigInteger(),
            'amount' => $this->integer(),
            'PRIMARY KEY(user_id, booster_id)'
        ]);
        $this->addForeignKey(
            $this->user_boosterBoosterFk,
            'user_booster',
            'booster_id',
            'booster',
            'id'
        );

        $this->addForeignKey(
            $this->user_boosterUserFk,
            'user_booster',
            'user_id',
            'users',
            'id'
        );
        /* APP_PRODUCT */
        $this->createTable('app_product', [
            'app_id' => $this->bigInteger(),
            'product_id' => $this->bigInteger(),
            'PRIMARY KEY(app_id, product_id)'
        ]);

        $this->addForeignKey(
            $this->app_productProductFk,
            'app_product',
            'product_id',
            'product',
            'id'
        );

        $this->addForeignKey(
            $this->app_productAppFk,
            'app_product',
            'app_id',
            'application',
            'app_id'
        );
        /* APP_BOOSTER */
        $this->createTable('app_booster', [
            'app_id' => $this->bigInteger(),
            'booster_id' => $this->bigInteger(),
            'PRIMARY KEY(app_id, booster_id)'
        ]);

        $this->addForeignKey(
            $this->app_boosterBoosterFk,
            'app_booster',
            'booster_id',
            'booster',
            'id'
        );

        $this->addForeignKey(
            $this->app_boosterAppFk,
            'app_booster',
            'app_id',
            'application',
            'app_id'
        );
        /* APP_USER */
        $this->createTable('app_user', [
            'app_id' => $this->bigInteger(),
            'user_id' => $this->bigInteger(),
            'SAC' => $this->string(),
            'money' => $this->float()->defaultValue(0),
            'lives' => $this->integer()->defaultValue(5),
            'next_update' => $this->dateTime(),
            'server_timestamp' => $this->dateTime(),
            'saved_game' => $this->string(),
            'PRIMARY KEY(app_id, user_id)'
        ]);

        $this->addForeignKey(
            $this->app_userUserFk,
            'app_user',
            'user_id',
            'users',
            'id'
        );

        $this->addForeignKey(
            $this->app_userAppFk,
            'app_user',
            'app_id',
            'application',
            'app_id'
        );
    }

    public function down()
    {
        $this->dropForeignKey($this->app_userAppFk, 'app_user');
        $this->dropForeignKey($this->app_userUserFk, 'app_user');
        $this->dropTable('app_user');

        $this->dropForeignKey($this->app_boosterAppFk, 'app_booster');
        $this->dropForeignKey($this->app_boosterBoosterFk, 'app_booster');
        $this->dropTable('app_booster');

        $this->dropForeignKey($this->app_productAppFk, 'app_product');
        $this->dropForeignKey($this->app_productProductFk, 'app_product');
        $this->dropTable('app_product');

        $this->dropForeignKey($this->user_boosterBoosterFk, 'user_booster');
        $this->dropForeignKey($this->user_boosterUserFk, 'user_booster');
        $this->dropTable('user_booster');

        $this->dropTable('application');
        $this->dropTable('booster');
        $this->dropTable('product');
        return true;
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
