<?php

use yii\db\Migration;

/**
 * Class m181108_085710_addColumsBlocks_per_sheet
 */
class m181108_085710_addColumsBlocks_per_sheet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'blocks_per_sheet', $this->integer()->defaultValue(0)->comment('Количество блокв с листа'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'blocks_per_sheet');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181108_085710_addColumsBlocks_per_sheet cannot be reverted.\n";

        return false;
    }
    */
}
