<?php

use yii\db\Migration;

class m170215_153913_apps extends Migration
{
//    public $productPk = 'product_pk';
//    public $boosterPk = 'booster_pk';
//    public $applicationPk = 'application_pk';
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
        $this->createTable('PRODUCT', [
            'ID' => $this->bigInteger(),
            'NAME' => $this->string(),
            'DESCRIPTION' => $this->string(),
            'NAME_URL' => $this->string(),
            'PRICE' => $this->float(),
            'CURRENCY' => $this->string(),
            'PRODUCT_URL' => $this->string(),
            'PRIMARY KEY(ID)'
        ]);
//        $this->addPrimaryKey($this->productPk,'PRODUCT','ID');

        $this->createTable('BOOSTER', [
            'ID' => $this->bigInteger(),
            'ALIAS' => $this->string(),
            'NAME' => $this->string(),
            'DESCRIPTION' => $this->string(),
            'COST' => $this->float(),
            'PRIMARY KEY(ID)'
        ]);
//        $this->addPrimaryKey($this->boosterPk,'BOOSTER','ID');

        $this->createTable('APPLICATION', [
            'APP_ID' => $this->bigInteger(),
            'APP_SECRET' => $this->string(),
            'APP_NAME' => $this->string(),
            'PRIMARY KEY(APP_ID)'
        ]);
//        $this->addPrimaryKey($this->applicationPk,'APPLICATION','ID');

        /* USER_BOOSTER */
        $this->createTable('USER_BOOSTER', [
            'USER_ID' => $this->bigInteger(),
            'BOOSTER_ID' => $this->bigInteger(),
            'AMOUNT' => $this->integer(),
            'PRIMARY KEY(USER_ID, BOOSTER_ID)'
        ]);
        $this->addForeignKey(
            $this->user_boosterBoosterFk,
            'USER_BOOSTER',
            'BOOSTER_ID',
            'BOOSTER',
            'ID'
        );

        $this->addForeignKey(
            $this->user_boosterUserFk,
            'USER_BOOSTER',
            'USER_ID',
            'users',
            'id'
        );
        /* APP_PRODUCT */
        $this->createTable('APP_PRODUCT', [
            'APP_ID' => $this->bigInteger(),
            'PRODUCT_ID' => $this->bigInteger(),
            'PRIMARY KEY(APP_ID, PRODUCT_ID)'
        ]);

        $this->addForeignKey(
            $this->app_productProductFk,
            'APP_PRODUCT',
            'PRODUCT_ID',
            'PRODUCT',
            'ID'
        );

        $this->addForeignKey(
            $this->app_productAppFk,
            'APP_PRODUCT',
            'APP_ID',
            'APPLICATION',
            'APP_ID'
        );
        /* APP_BOOSTER */
        $this->createTable('APP_BOOSTER', [
            'APP_ID' => $this->bigInteger(),
            'BOOSTER_ID' => $this->bigInteger(),
            'PRIMARY KEY(APP_ID, BOOSTER_ID)'
        ]);

        $this->addForeignKey(
            $this->app_boosterBoosterFk,
            'APP_BOOSTER',
            'BOOSTER_ID',
            'BOOSTER',
            'ID'
        );

        $this->addForeignKey(
            $this->app_boosterAppFk,
            'APP_BOOSTER',
            'APP_ID',
            'APPLICATION',
            'APP_ID'
        );
        /* APP_USER */
        $this->createTable('APP_USER', [
            'APP_ID' => $this->bigInteger(),
            'USER_ID' => $this->bigInteger(),
            'SAC' => $this->string(),
            'MONEY' => $this->float(),
            'LIVES' => $this->integer(),
            'NEXT_UPDATE' => $this->dateTime(),
            'SERVER_TIMESTAMP' => $this->dateTime(),
            'SAVED_GAME' => $this->binary(),
            'PRIMARY KEY(APP_ID, USER_ID)'
        ]);

        $this->addForeignKey(
            $this->app_userUserFk,
            'APP_USER',
            'USER_ID',
            'users',
            'id'
        );

        $this->addForeignKey(
            $this->app_userAppFk,
            'APP_USER',
            'APP_ID',
            'APPLICATION',
            'APP_ID'
        );

    }

    public function down()
    {
        $this->dropForeignKey($this->app_userAppFk, 'APP_USER');
        $this->dropForeignKey($this->app_userUserFk, 'APP_USER');
        $this->dropTable('APP_USER');

        $this->dropForeignKey($this->app_boosterAppFk, 'APP_BOOSTER');
        $this->dropForeignKey($this->app_boosterBoosterFk, 'APP_BOOSTER');
        $this->dropTable('APP_BOOSTER');

        $this->dropForeignKey($this->app_productAppFk, 'APP_PRODUCT');
        $this->dropForeignKey($this->app_productProductFk, 'APP_PRODUCT');
        $this->dropTable('APP_PRODUCT');

        $this->dropForeignKey($this->user_boosterBoosterFk, 'USER_BOOSTER');
        $this->dropForeignKey($this->user_boosterUserFk, 'USER_BOOSTER');
        $this->dropTable('USER_BOOSTER');

        $this->dropTable('APPLICATION');
        $this->dropTable('BOOSTER');
        $this->dropTable('PRODUCT');
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
