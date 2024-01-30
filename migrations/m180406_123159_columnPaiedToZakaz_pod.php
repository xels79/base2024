<?php

use yii\db\Migration;

/**
 * Class m180406_123159_columnPaiedToZakaz_pod
 */
class m180406_123159_columnPaiedToZakaz_pod extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz_pod', 'paid', $this->double()->defaultValue(0)->comment('Выплачено'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz_pod', 'paid');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180406_123159_columnPaiedToZakaz_pod cannot be reverted.\n";

        return false;
    }
    */
}
