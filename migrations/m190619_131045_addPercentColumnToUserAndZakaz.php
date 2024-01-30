<?php

use yii\db\Migration;

/**
 * Class m190619_131045_addPercentColumnToUserAndZakaz
 */
class m190619_131045_addPercentColumnToUserAndZakaz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'percent1', $this->decimal()->null()->comment('№ Шелкуха'));
        $this->addColumn('zakaz', 'percent2', $this->decimal()->null()->comment('№ Подрядчик'));
        $this->addColumn('zakaz', 'percent3', $this->decimal()->null()->comment('№ доп'));
        
//        $this->addColumn('tbl_user', 'wages', $this->decimal()->defaultValue(0)->comment('Оклад'));
//        $this->addColumn('tbl_user', 'percent1', $this->decimal()->defaultValue(0)->comment('№ Шелкуха'));
//        $this->addColumn('tbl_user', 'percent2', $this->decimal()->defaultValue(0)->comment('№ Подрядчик'));
//        $this->addColumn('tbl_user', 'percent3', $this->decimal()->defaultValue(0)->comment('№ доп'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'percent1');
        $this->dropColumn('zakaz', 'percent2');
        $this->dropColumn('zakaz', 'percent3');
        
//        $this->dropColumn('tbl_user', 'wages');
//        $this->dropColumn('tbl_user', 'percent1');
//        $this->dropColumn('tbl_user', 'percent2');
//        $this->dropColumn('tbl_user', 'percent3');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190619_131045_addPercentColumnToUserAndZakaz cannot be reverted.\n";

        return false;
    }
    */
}
