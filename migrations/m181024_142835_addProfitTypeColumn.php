<?php

use yii\db\Migration;

/**
 * Class m181024_142835_addProfitTypeColumn
 */
class m181024_142835_addProfitTypeColumn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'profit_type', $this->smallInteger()->defaultValue(0)->comment('Тип прибыли'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'profit_type');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181024_142835_addProfitTypeColumn cannot be reverted.\n";

        return false;
    }
    */
}
