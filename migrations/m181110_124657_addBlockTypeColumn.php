<?php

use yii\db\Migration;

/**
 * Class m181110_124657_addBlockTypeColumn
 */
class m181110_124657_addBlockTypeColumn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'blocks_type',$this->smallInteger()->defaultValue(0)->comment("Тип блока"));
        $this->addColumn('zakaz', 'material_is_on_sklad',$this->boolean()->defaultValue(false)->comment("Материал на складе"));
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'blocks_type');
        $this->dropColumn('zakaz', 'material_is_on_sklad');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181110_124657_addBlockTypeColumn cannot be reverted.\n";

        return false;
    }
    */
}
