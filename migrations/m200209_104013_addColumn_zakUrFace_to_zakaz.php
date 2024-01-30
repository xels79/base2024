<?php

use yii\db\Migration;

/**
 * Class m200209_104013_addColumn_zakUrFace_to_zakaz
 */
class m200209_104013_addColumn_zakUrFace_to_zakaz extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('zakaz', 'zakUrFace', $this->integer()->null()->comment('Юр.лицо заказчика'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('zakaz', 'zakUrFace');
        return true;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m200209_104013_addColumn_zakUrFace_to_zakaz cannot be reverted.\n";

      return false;
      }
     */
}
