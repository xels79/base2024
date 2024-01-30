<?php

use yii\db\Migration;

class m170423_213633_table_workTypes extends Migration
{
    public $tName='worktypes';
    public function up()
    {
        $this->createTable($this->tName, [
            'id'=>$this->primaryKey(),
            'name'=>$this->text()->notNull()->comment('Название'),
            'category'=>$this->integer()->defaultValue(0)->comment('Категория')
        ]);
    }

    public function down()
    {
        $this->dropTable($this->tName);
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
