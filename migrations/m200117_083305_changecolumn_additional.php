<?php

use yii\db\Migration;

/**
 * Class m200117_083305_changecolumn_additional
 */
class m200117_083305_changecolumn_additional extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn( 'contactPod', 'additional', $this->string( 16 )->defaultValue( null )->comment( 'Мобильный' ) );
        $this->alterColumn( 'contactPost', 'additional', $this->string( 16 )->defaultValue( null )->comment( 'Мобильный' ) );
        $this->alterColumn( 'contactZak', 'additional', $this->string( 16 )->defaultValue( null )->comment( 'Мобильный' ) );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "Изменения не произведены!\nНо ничего в том смысла нет.";

        return true;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m200117_083305_changecolumn_additional cannot be reverted.\n";

      return false;
      }
     */
}
