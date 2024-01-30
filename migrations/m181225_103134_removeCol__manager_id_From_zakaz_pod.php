<?php

use yii\db\Migration;

/**
 * Class m181225_103134_removeCol__manager_id_From_zakaz_pod
 */
class m181225_103134_removeCol__manager_id_From_zakaz_pod extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('zakaz_pod', 'manager_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('zakaz_pod', 'manager_id',$this->integer()->null()->comment('Идентификатор менеджера'));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181225_103134_removeCol__manager_id_From_zakaz_pod cannot be reverted.\n";

        return false;
    }
    */
}
