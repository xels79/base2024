<?php

use yii\db\Migration;

/**
 * Class m190130_131312_addOplataTable
 */
class m190130_131312_addOplataTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('zakaz_oplata', [
            'id'=>$this->primaryKey(),
            'zakaz_id'=> $this->integer()->notNull()->comment('К заказу'),
            'date'=> $this->date()->notNull()->comment('Дата'),
            'summ'=>$this->money()->notNull()->comment('Сумма')
        ]);
        $this->addCommentOnTable('zakaz_oplata', 'Оплаты по номерам заеазов');
        $this->createIndex('fk-zakaz_oplata', 'zakaz_oplata', 'zakaz_id');
        $this->addForeignKey('fk-zakaz_oplata',  'zakaz_oplata', 'zakaz_id', 'zakaz', 'id','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-zakaz_oplata',  'zakaz_oplata');
        $this->dropIndex('fk-zakaz_oplata', 'zakaz_oplata');
        $this->dropTable('zakaz_oplata');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190130_131312_addOplataTable cannot be reverted.\n";

        return false;
    }
    */
}
