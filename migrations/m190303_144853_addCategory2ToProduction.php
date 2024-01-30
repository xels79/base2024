<?php

use yii\db\Migration;

/**
 * Class m190303_144853_addCategory2ToProduction
 */
class m190303_144853_addCategory2ToProduction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('production', 'category2', $this->integer()->defaultValue(0)->comment('Категория 2'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('production', 'category2');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190303_144853_addCategory2ToProduction cannot be reverted.\n";

        return false;
    }
    */
}
