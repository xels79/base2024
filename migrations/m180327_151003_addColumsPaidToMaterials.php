<?php

use yii\db\Migration;

/**
 * Class m180327_151003_addColumsPaidToMaterials
 */
class m180327_151003_addColumsPaidToMaterials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz_materials', 'paid', $this->double()->comment('Выплачено'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz_materials', 'paid');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180327_151003_addColumsPaidToMaterials cannot be reverted.\n";

        return false;
    }
    */
}
