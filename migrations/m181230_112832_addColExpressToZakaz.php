<?php

use yii\db\Migration;

/**
 * Class m181230_112832_addColExpressToZakaz
 */
class m181230_112832_addColExpressToZakaz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'is_express', $this->boolean()->defaultValue(false)->comment('Экспресс заказ'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'is_express');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181230_112832_addColExpressToZakaz cannot be reverted.\n";

        return false;
    }
    */
}
