<?php

use yii\db\Migration;

/**
 * Class m190118_165001_addColDeadlineTime
 */
class m190118_165001_addColDeadlineTime extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'deadline_time', $this->string(5)->null()->comment('Время'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'deadline_time');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190118_165001_addColDeadlineTime cannot be reverted.\n";

        return false;
    }
    */
}
