<?php

use yii\db\Migration;

/**
 * Class m181117_124405_addColBlockFormatToZakaz
 */
class m181117_124405_addColBlockFormatToZakaz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //
        $this->addColumn('zakaz', 'material_block_format',$this->text()->defaultValue(null)->comment("Параметры блока"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'material_block_format');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181117_124405_addColBlockFormatToZakaz cannot be reverted.\n";

        return false;
    }
    */
}
