<?php

use yii\db\Migration;

/**
 * Class m190827_152028_pechatniki
 */
class m190827_152028_pechatniki extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('pechiatnik', [
            'id'=>$this->primaryKey(),
            'm_id'=>$this->integer()->notNull()->comment('ИД Печатника'),
            'z_id'=>$this->integer()->notNull()->comment('ИД Заказа'),
            'z_time'=>$this->char(5)->comment('Время'),
            'z_date'=>$this->date()->comment('Дата'),
            'ready'=>$this->date()->null()->comment('Готов дата'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('pechiatnik');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190827_152028_pechatniki cannot be reverted.\n";

        return false;
    }
    */
}
