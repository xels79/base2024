<?php

use yii\db\Migration;

/**
 * Class m190206_135448_AlterColumnPaidInZakazPod
 */
class m190206_135448_AlterColumnPaidInZakazPod extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('zakaz_pod', 'paid', $this->boolean()->defaultValue(false)->comment('Статус оплаты'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('zakaz_pod', 'paid', $this->double()->defaultValue(0)->comment('Оплата'));

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190206_135448_AlterColumnPaidInZakazPod cannot be reverted.\n";

        return false;
    }
    */
}
