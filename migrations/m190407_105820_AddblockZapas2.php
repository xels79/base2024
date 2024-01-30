<?php

use yii\db\Migration;

/**
 * Class m190407_105820_AddblockZapas2
 */
class m190407_105820_AddblockZapas2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'block_zapas1', $this->integer()->defaultValue(0)->comment('Блок запас в ручн.2'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'block_zapas1');

        return true;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190407_105820_AddblockZapas2 cannot be reverted.\n";

        return false;
    }
    */
}
