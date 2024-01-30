<?php

use yii\db\Migration;

/**
 * Class m180309_140351_addColumnAdditionalPhone
 */
class m180309_140351_addColumnAdditionalPhone extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('contactPod', 'additional', $this->string(10)->defaultValue(null)->comment('Добавочный'));
        $this->addColumn('contactPost', 'additional', $this->string(10)->defaultValue(null)->comment('Добавочный'));
        $this->addColumn('contactZak', 'additional', $this->string(10)->defaultValue(null)->comment('Добавочный'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('contactPod', 'additional');
        $this->dropColumn('contactPost', 'additional');
        $this->dropColumn('contactZak', 'additional');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180309_140351_addColumnAdditionalPhone cannot be reverted.\n";

        return false;
    }
    */
}
