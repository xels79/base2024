<?php

use yii\db\Migration;

/**
 * Class m180424_132702_addColumnComerCoastToZakaz
 */
class m180424_132702_addColumnComerCoastToZakaz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'material_coast_comerc', $this->double()->null()->comment('Комерч. стоимость материала'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'material_coast_comerc');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180424_132702_addColumnComerCoastToZakaz cannot be reverted.\n";

        return false;
    }
    */
}
