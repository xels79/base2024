<?php

use yii\db\Migration;

/**
 * Class m181223_111001_addDateColTozakaz_pod
 */
class m181223_111001_addDateColTozakaz_pod extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz_pod', 'date_info', $this->date()->null()->comment('Дата передачи'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz_pod', 'date_info');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181223_111001_addDateColTozakaz_pod cannot be reverted.\n";

        return false;
    }
    */
}
