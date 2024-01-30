<?php

use yii\db\Migration;

/**
 * Class m191006_130214_correct_zarplata_table
 */
class m191006_130214_correct_zarplata_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn( 'zarplata', 'post' );
        $this->addColumn( 'zarplata', 'payment_id', $this->integer()->notNull()->comment( 'тип зарплаты' ) );
        $this->addColumn( 'zarplata', 'procats', $this->integer()->defaultValue( 0 )->comment( 'Прокаты' ) ); //для СДЕЛКИ
        $this->addColumn( 'zarplata', 'minus', $this->money()->defaultValue( 0 )->comment( 'Вычеты' ) );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn( 'zarplata', 'payment_id' );
        $this->addColumn( 'zarplata', 'post', $this->integer()->notNull()->comment( 'Должность' ) );
        $this->dropColumn( 'zarplata', 'procats' );
        $this->dropColumn( 'zarplata', 'minus' );
        return true;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m191006_130214_correct_zarplata_table cannot be reverted.\n";

      return false;
      }
     */
}
