<?php

use yii\db\Migration;

/**
 * Class m190115_183741_addColBlockZapasToZakaz
 */
class m190115_183741_addColBlockZapasToZakaz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'block_zapas', $this->integer()->defaultValue(0)->comment('Блок запас в ручн.'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'block_zapas');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190115_183741_addColBlockZapasToZakaz cannot be reverted.\n";

        return false;
    }
    */
}
