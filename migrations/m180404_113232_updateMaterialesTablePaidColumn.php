<?php

use yii\db\Migration;

/**
 * Class m180404_113232_updateMaterialesTablePaidColumn
 */
class m180404_113232_updateMaterialesTablePaidColumn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('zakaz_materials', 'paid', $this->double()->defaultValue(0)->comment('Выплачено'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180404_113232_updateMaterialesTablePaidColumn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180404_113232_updateMaterialesTablePaidColumn cannot be reverted.\n";

        return false;
    }
    */
}
