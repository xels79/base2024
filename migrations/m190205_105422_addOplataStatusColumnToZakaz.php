<?php

use yii\db\Migration;

/**
 * Class m190205_105422_addOplataStatusColumnToZakaz
 */
class m190205_105422_addOplataStatusColumnToZakaz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'oplata_status', $this->smallInteger()->defaultValue(0)->comment('Статус оплаты'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'oplata_status');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190205_105422_addOplataStatusColumnToZakaz cannot be reverted.\n";

        return false;
    }
    */
}
