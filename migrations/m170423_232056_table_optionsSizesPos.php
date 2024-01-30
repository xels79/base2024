<?php

use yii\db\Migration;

class m170423_232056_table_optionsSizesPos extends Migration
{
    public $tName='optionsSizePos';
    public function up()
    {
        $this->createTable($this->tName, [
            'id'=>$this->primaryKey(),
            'name'=>$this->text()->notNull()->comment('Название'),
            'user_id'=>$this->integer()->notNull()->comment('id пользов'),
            'top'=>$this->integer()->notNull()->comment('Позиц. верх'),
            'left'=>$this->integer()->notNull()->comment('Позиц. лев.'),
            'width'=>$this->integer()->notNull()->comment('ширина'),
            'height'=>$this->integer()->notNull()->comment('высота'),
        ]);
        $this->createIndex('fg_user_id', $this->tName, 'user_id');
        $this->addForeignKey('fg_user_id', $this->tName, 'user_id', 'tbl_user', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fg_user_id', $this->tName);
        $this->dropIndex('fg_user_id', $this->tName);
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
