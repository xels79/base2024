<?php

use yii\db\Migration;

class m171004_222403_rbac extends Migration
{
    public $tblName='rbac';
    public function safeUp()
    {
        $this->createTable($this->tblName, [
            'id'=>$this->primaryKey()->comment('Идентификатор'),
            'name'=>$this->text()->notNull()->comment('Название'),
            'value'=>$this->text()->notNull()->comment('Содержимое')
        ]);
        $this->addCommentOnTable($this->tblName, 'Настройки уровней доступа');
    }

    public function safeDown()
    {
        $this->dropTable($this->tblName);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171004_222403_rbac cannot be reverted.\n";

        return false;
    }
    */
}
