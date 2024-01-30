<?php

use yii\db\Migration;

/**
 * Class m210528_174316_SpendingToYourselfTableAdd
 */
class m210528_174316_SpendingToYourselfTableAdd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('spendingtoyourselftable', [
            'id'=>$this->integer()->unique()->notNull(),
            'summ'=>$this->decimal()->defaultValue(0)
        ]);
        $this->createIndex('spendingtoyourselftable_prymk', 'spendingtoyourselftable', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropIndex('spendingtoyourselftable_prymk', 'spendingtoyourselftable');
        $this->dropTable('spendingtoyourselftable');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210528_174316_SpendingToYourselfTableAdd cannot be reverted.\n";

        return false;
    }
    */
}
