<?php

use yii\db\Migration;

/**
 * Class m190118_124711_addColPostPrintFitmName
 */
class m190118_124711_addColPostPrintFitmName extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'post_print_firm_name', $this->text()->null()->comment('Вызвать на печать фирма'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'post_print_firm_name');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190118_124711_addColPostPrintFitmName cannot be reverted.\n";

        return false;
    }
    */
}
