<?php

use yii\db\Migration;

/**
 * Class m190206_153643_AddColumnsOtherPaidToZakaz
 */
class m190206_153643_AddColumnsOtherPaidToZakaz extends Migration
{
    public $keys=['speed', 'delivery', 'markup', 'bonus', 'transport', 'transport2'];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ($this->keys as $name){
            $this->addColumn('zakaz', 'exec_'.$name.'_paid', $this->boolean()->defaultValue(false)->comment('Сотатус оплаты доп поля'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->keys as $name){
            $this->dropColumn('zakaz', 'exec_'.$name.'_paid');
        }
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190206_153643_AddColumnsOtherPaidToZakaz cannot be reverted.\n";

        return false;
    }
    */
}
