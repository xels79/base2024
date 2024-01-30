<?php

use yii\db\Migration;

/**
 * Class m190630_181047_blackList
 */
class m190630_181047_blackList extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firmZak', 'blackList', $this->text()->null()->comment('Заказчик в черном списке. Причина'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firmZak', 'blackList');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190630_181047_blackList cannot be reverted.\n";

        return false;
    }
    */
}
