<?php

use yii\db\Migration;

/**
 * Class m190109_145204_chengeReprintColumnType
 */
class m190109_145204_chengeReprintColumnType extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('zakaz', 're_print', $this->smallInteger()->defaultValue(0)->comment("Перепечатка"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('zakaz', 're_print', $this->boolean()->defaultValue(false)->comment("Перепечатка"));

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190109_145204_chengeReprintColumnType cannot be reverted.\n";

        return false;
    }
    */
}
