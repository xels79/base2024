<?php

use yii\db\Migration;

/**
 * Class m190205_140637_ChangeColunPaidToBooleanInZakaz_Materials
 */
class m190205_140637_ChangeColunPaidToBooleanInZakaz_Materials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //$this->dropColumn('zakaz_materials', 'paid');
        $this->alterColumn('zakaz_materials', 'paid', $this->boolean()->defaultValue(false)->comment('Статус оплаты'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('zakaz_materials', 'paid', $this->integer()->defaultValue(0)->comment('Оплата'));

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190205_140637_ChangeColunPaidToBooleanInZakaz_Materials cannot be reverted.\n";

        return false;
    }
    */
}
