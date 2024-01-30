<?php

use yii\db\Migration;

/**
 * Class m190331_120636_changeNumOfProductInBlockType
 */
class m190331_120636_changeNumOfProductInBlockType extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('zakaz', 'num_of_products_in_block', $this->double()->null()->comment('Кол-во изделий в блоке 1'));
        $this->alterColumn('zakaz', 'num_of_products_in_block1', $this->double()->null()->comment('Кол-во изделий в блоке 1'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('zakaz', 'num_of_products_in_block', $this->integer()->null()->comment('Кол-во изделий в блоке 1'));
        $this->alterColumn('zakaz', 'num_of_products_in_block1', $this-integer()->null()->comment('Кол-во изделий в блоке 1'));
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190331_120636_changeNumOfProductInBlockType cannot be reverted.\n";

        return false;
    }
    */
}
