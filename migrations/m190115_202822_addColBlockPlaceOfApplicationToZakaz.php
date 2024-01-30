<?php

use yii\db\Migration;

/**
 * Class m190115_202822_addColBlockPlaceOfApplicationToZakaz
 */
class m190115_202822_addColBlockPlaceOfApplicationToZakaz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'place_of_application_block', $this->smallInteger()->defaultValue(0)->comment('Блок мест нанесения'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'place_of_application_block');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190115_202822_addColBlockPlaceOfApplicationToZakaz cannot be reverted.\n";

        return false;
    }
    */
}
