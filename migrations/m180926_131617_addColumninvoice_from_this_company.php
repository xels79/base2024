<?php

use yii\db\Migration;

/**
 * Class m180926_131617_addColumninvoice_from_this_company
 */
class m180926_131617_addColumninvoice_from_this_company extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'invoice_from_this_company', $this->integer()->null()->comment('Счёт от фирмы'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'invoice_from_this_company');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180926_131617_addColumninvoice_from_this_company cannot be reverted.\n";

        return false;
    }
    */
}
