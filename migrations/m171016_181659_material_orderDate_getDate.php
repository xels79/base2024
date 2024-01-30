<?php

use yii\db\Migration;

class m171016_181659_material_orderDate_getDate extends Migration
{
    public $tblN='zakaz_materials';
    public function safeUp()
    {
        $this->addColumn($this->tblN, 'order_date', $this->date()->null()->comment('Заказан'));
        $this->addColumn($this->tblN, 'delivery_date', $this->date()->null()->comment('Получен'));
    }

    public function safeDown()
    {
        $this->dropColumn($this->tblN, 'order_date');
        $this->dropColumn($this->tblN, 'delivery_date');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171016_181659_material_orderDate_getDate cannot be reverted.\n";

        return false;
    }
    */
}
