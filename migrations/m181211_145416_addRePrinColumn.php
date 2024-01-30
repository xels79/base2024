<?php

use yii\db\Migration;

/**
 * Class m181211_145416_addRePrinColumn
 */
class m181211_145416_addRePrinColumn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //
        $this->addColumn('zakaz', 're_print',$this->boolean()->defaultValue(false)->comment("Перепечатка"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 're_print');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181211_145416_addRePrinColumn cannot be reverted.\n";

        return false;
    }
    */
}
