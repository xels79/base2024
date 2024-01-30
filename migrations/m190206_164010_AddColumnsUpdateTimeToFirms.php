<?php

use yii\db\Migration;

/**
 * Class m190206_164010_AddColumnsUpdateTimeToFirms
 */
class m190206_164010_AddColumnsUpdateTimeToFirms extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firmPod', 'update_time', $this->integer()->defaultValue(0)->comment('Последнее обновление'));
        $this->addColumn('firmPost', 'update_time', $this->integer()->defaultValue(0)->comment('Последнее обновление'));
        $this->addColumn('firmZak', 'update_time', $this->integer()->defaultValue(0)->comment('Последнее обновление'));
        $this->addColumn('production', 'update_time', $this->integer()->defaultValue(0)->comment('Последнее обновление'));
        $this->addColumn('rekvizitOur', 'update_time', $this->integer()->defaultValue(0)->comment('Последнее обновление'));
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firmPod', 'update_time');
        $this->dropColumn('firmPost', 'update_time');
        $this->dropColumn('firmZak', 'update_time');
        $this->dropColumn('production', 'update_time');
        $this->dropColumn('rekvizitOur', 'update_time');


        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190206_164010_AddColumnsUpdateTimeToFirms cannot be reverted.\n";

        return false;
    }
    */
}
