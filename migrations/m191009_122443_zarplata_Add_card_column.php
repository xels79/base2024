<?php

use yii\db\Migration;

/**
 * Class m191009_122443_zarplata_Add_card_column
 */
class m191009_122443_zarplata_Add_card_column extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('zarplata', 'card1', $this->money()->defaultValue(0)->comment('Карта'));
        $this->addColumn('zarplata', 'card2', $this->money()->defaultValue(0)->comment('Карта'));
        $this->addColumn('zarplata', 'employed', $this->boolean()->defaultValue(0)->comment('Устроен'));
        $this->addColumn('zarplata', 'comment', $this->text()->null()->comment('Пояснения'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('zarplata', 'card1');
        $this->dropColumn('zarplata', 'card2');
        $this->dropColumn('zarplata', 'employed');
        $this->dropColumn('zarplata', 'comment');
        return true;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m191009_122443_zarplata_Add_card_column cannot be reverted.\n";

      return false;
      }
     */
}
