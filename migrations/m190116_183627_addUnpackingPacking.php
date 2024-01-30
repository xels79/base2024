<?php

use yii\db\Migration;

/**
 * Class m190116_183627_addUnpackingPacking
 */
class m190116_183627_addUnpackingPacking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //$this->addColumn('zakaz', 'unpacking_packing_coast', $this->money()->defaultValue(0)->comment('Распаковка упаковка стоимость'));
        $this->addColumn('zakaz', 'print_unpacking_coast', $this->text()->defaultValue(null)->comment('Стоимость распаковки'));
        $this->addColumn('zakaz', 'print_packing_coast', $this->text()->defaultValue(null)->comment('Стоимость упаковки'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        $this->dropColumn('zakaz', 'unpacking_packing_coast');
        $this->dropColumn('zakaz', 'print_unpacking_coast');
        $this->dropColumn('zakaz', 'print_packing_coast');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190116_183627_addUnpackingPacking cannot be reverted.\n";

        return false;
    }
    */
}
