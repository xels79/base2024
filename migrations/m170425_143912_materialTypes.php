<?php

use yii\db\Migration;

class m170425_143912_materialTypes extends Migration
{
    public $tName='materialtypes';
    public function up()
    {
        $this->createTable($this->tName, [
            'id'=>$this->primaryKey(),
            'name'=>$this->text()->notNull()->comment('Название типа материалов'),
            'struct'=>$this->text()->null()->comment('Структура'),
            'translitName'=>$this->text()->notNull()->comment('Название транслит')
        ]);
    }

    public function down()
    {
        $this->dropTable($this->tName);
        return true;
    }

}
