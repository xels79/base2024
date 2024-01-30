<?php

use yii\db\Migration;

/**
 * Class m230208_141529_rekomendet_coast
 */
class m230208_141529_rekomendet_coast extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //materials_on_firms
        $this->addColumn('materials_on_firms', 'recomendetcoast', $this->double()->defaultValue(0)->comment('Рекомендуемая цена'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('materials_on_firms', 'recomendetcoast');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230208_141529_rekomendet_coast cannot be reverted.\n";

        return false;
    }
    */
}
