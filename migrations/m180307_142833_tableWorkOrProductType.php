<?php

use yii\db\Migration;

/**
 * Class m180307_142833_tableWorkOrProductType
 */
class m180307_142833_tableWorkOrProductType extends Migration
{
    public $tName='workOrproductType';
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
