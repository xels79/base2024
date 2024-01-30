<?php

use yii\db\Migration;

class m170421_094315_tableProduction extends Migration
{
    public $tName='production';
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

}
