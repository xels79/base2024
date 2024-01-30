<?php

use yii\db\Migration;

/**
 * Class m190112_122525_addColumn_DateOfReceipt
 */
class m190112_122525_addColumn_DateOfReceipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'date_of_receipt', $this->date()->null()->comment("Дата поступления"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'date_of_receipt');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190112_122525_addColumn_DateOfReceipt cannot be reverted.\n";

        return false;
    }
    */
}
