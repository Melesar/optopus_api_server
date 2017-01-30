<?php

use yii\db\Migration;

class m170130_133949_bundle extends Migration
{
    private $bundlePk = "bundle_pk";

    public function up()
    {
        $this->createTable('bundle', [
            'project_id' => $this->integer(),
            'name_format' => $this->string(),
            'bundle_size' => $this->integer(),
            'path' => $this->string()->notNull(),
        ]);
        $this->addPrimaryKey($this->bundlePk, 'bundle', ['project_id','name_format','bundle_size']);
    }

    public function down()
    {
        $this->dropTable('bundle');

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
