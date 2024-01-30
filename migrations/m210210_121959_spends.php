<?php

use yii\db\Migration;

/**
 * Class m210210_121959_spends
 */
class m210210_121959_spends extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('spends',[
            'id'=>$this->primaryKey(),
            'date'=>$this->date()->notNull()->comment('Дата'),
            'name'=>$this->char(32)->notNull()->comment('Название'),
            'coast'=>$this->float()->defaultValue(0)->comment('Стоимость')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m210210_121959_spends cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210210_121959_spends cannot be reverted.\n";

        return false;
    }
    */
}
