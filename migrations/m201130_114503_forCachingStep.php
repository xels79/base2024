<?php

use yii\db\Migration;

/**
 * Class m201130_114503_forCachingStep
 */
class m201130_114503_forCachingStep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('materialtypes', 'update_time', $this->integer()->defaultValue(0)->comment('Время последнего обновления'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('materialtypes', 'update_time');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201130_114503_forCachingStep cannot be reverted.\n";

        return false;
    }
    */
}
