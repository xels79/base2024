<?php

use yii\db\Migration;

/**
 * Class m200705_104108_changeHoursColInZarplata
 */
class m200705_104108_changeHoursColInZarplata extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('zarplata','hours',$this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->alterColumn('zarplata','hours',$this->smallInteger());

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200705_104108_changeHoursColInZarplata cannot be reverted.\n";

        return false;
    }
    */
}
