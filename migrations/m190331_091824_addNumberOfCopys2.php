<?php

use yii\db\Migration;

/**
 * Class m190331_091824_addNumberOfCopys2
 */
class m190331_091824_addNumberOfCopys2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'number_of_copies1', $this->string(14)->notNull()->comment('Тираж2'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'number_of_copies1');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190331_091824_addNumberOfCopys2 cannot be reverted.\n";

        return false;
    }
    */
}
