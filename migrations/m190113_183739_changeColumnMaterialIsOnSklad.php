<?php

use yii\db\Migration;

/**
 * Class m190113_183739_changeColumnMaterialIsOnSklad
 */
class m190113_183739_changeColumnMaterialIsOnSklad extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('zakaz', 'material_is_on_sklad',$this->smallInteger()->defaultValue(0)->comment("Материал на складе"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('zakaz', 'material_is_on_sklad',$this->boolean()->defaultValue(false)->comment("Материал на складе"));
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190113_183739_changeColumnMaterialIsOnSklad cannot be reverted.\n";

        return false;
    }
    */
}
