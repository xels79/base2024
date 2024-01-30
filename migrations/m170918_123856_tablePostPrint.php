<?php

use yii\db\Migration;

class m170918_123856_tablePostPrint extends Migration
{
    public $tName='postprint';
    public function up()
    {
        $this->createTable($this->tName, [
            'id'=>$this->primaryKey(),
            'name'=>$this->text()->notNull()->comment('Название'),
            'category'=>$this->integer()->defaultValue(0)->comment('Категория')
        ]);
        $this->addCommentOnTable($this->tName, 'Постпечатка');
    }

    public function down()
    {
        $this->dropTable($this->tName);
        return true;
    }
}
