<?php

use yii\db\Migration;

/**
 * Class m190326_165208_forTowMaterials
 */
class m190326_165208_forTowMaterials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'blocks_type1',$this->smallInteger()->defaultValue(0)->comment("Тип блока 1"));
        $this->addColumn('zakaz', 'material_is_on_sklad1',$this->boolean()->defaultValue(false)->comment("Материал на складе 1"));
        $this->addColumn('zakaz', 'num_of_products_in_block1', $this->integer()->null()->comment('Кол-во изделий в блоке 1'));
        $this->addColumn('zakaz', 'num_of_printing_block1', $this->integer()->null()->comment('Кол-во печатных блоков 1'));
        $this->addColumn('zakaz', 'format_printing_block1', $this->string(12)->null()->comment('Формат печатного блока 1'));
        $this->addColumn('zakaz', 'date_of_receipt1', $this->date()->null()->comment("Дата поступления 1"));
        $this->addColumn('zakaz', 'blocks_per_sheet1', $this->integer()->defaultValue(0)->comment('Количество блокв с листа 1'));
        $this->addColumn('zakaz', 'material_block_format1',$this->text()->defaultValue(null)->comment("Параметры блока 1"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'blocks_type1');
        $this->dropColumn('zakaz', 'material_is_on_sklad1');
        $this->dropColumn('zakaz', 'num_of_products_in_block1');
        $this->dropColumn('zakaz', 'num_of_printing_block1');
        $this->dropColumn('zakaz', 'format_printing_block1');
        $this->dropColumn('zakaz', 'date_of_receipt1');
        $this->dropColumn('zakaz', 'blocks_per_sheet1');
        $this->dropColumn('zakaz', 'material_block_format1');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190326_165208_forTowMaterials cannot be reverted.\n";

        return false;
    }
    */
}
