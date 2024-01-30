<?php

use yii\db\Migration;

class m170823_135527_options extends Migration
{
    public function safeUp()
    {
        $this->createTable('options', [
            'id'=>$this->primaryKey(),
            'userid'=>$this->integer()->notNull()->comment('Идентификатор пользователя'),
            'optionid'=>$this->char(64)->notNull()->comment('Идентификатор опции'),
            'value'=>$this->text()->null()->comment('Массив')
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('options');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170823_135527_options cannot be reverted.\n";

        return false;
    }
    */
}
