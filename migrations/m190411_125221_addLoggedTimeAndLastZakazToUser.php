<?php

use yii\db\Migration;

/**
 * Class m190411_125221_addLoggedTimeAndLastZakazToUser
 */
class m190411_125221_addLoggedTimeAndLastZakazToUser extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $this->addColumn('tbl_user', 'login_time', $this->integer()->defaultValue(0)->comment('Последнее вход'));
//        $this->addColumn('tbl_user', 'control_time', $this->integer()->defaultValue(0)->comment('Внутрений контроль'));
//        $this->addColumn('tbl_user', 'logout_time', $this->integer()->defaultValue(0)->comment('Последнее выход'));
//        $this->addColumn('tbl_user', 'last_zakaz', $this->integer()->defaultValue(0)->comment('Последнее заказ'));
        echo "Исправления добавленны в стартовую миграцию\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        $this->dropColumn('tbl_user', 'login_time');
//        $this->dropColumn('tbl_user', 'last_zakaz');
//        $this->dropColumn('tbl_user', 'control_time');
//        $this->dropColumn('tbl_user', 'logout_time');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_125221_addLoggedTimeAndLastZakazToUser cannot be reverted.\n";

        return false;
    }
    */
}
