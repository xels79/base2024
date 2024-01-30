<?php

use yii\db\Migration;

class m170726_151624_changeColumnTypeForeZakaz extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('zakaz', 'colors', 'text');
        $this->alterColumn('zakaz', 'post_print', 'text');
    }

    public function safeDown()
    {
        $this->alterColumn('zakaz', 'colors', 'text');
        $this->alterColumn('zakaz', 'post_print', 'text');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170726_151624_changeColumnTypeForeZakaz cannot be reverted.\n";

        return false;
    }
    */
}
