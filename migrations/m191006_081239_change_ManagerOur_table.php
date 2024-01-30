<?php

use yii\db\Migration;

/**
 * Class m191006_081239_change_ManagerOur_table
 */
class m191006_081239_change_ManagerOur_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('managerOur', 'hasPercents', $this->boolean()->defaultValue(false)->comment('% от прибыли'));
        $this->addColumn('managerOur', 'piecework', $this->char()->defaultValue(0)->comment('Сделка'));
        $this->addColumn('managerOur', 'ourfirm_profit', $this->integer()->defaultValue(0)->comment('от нашей фирмы'));
        $this->addColumn('managerOur', 'material_profit', $this->integer()->defaultValue(0)->comment('материал'));
        $this->addCommentOnColumn('managerOur', 'profit', 'подрядчик');
        $this->addCommentOnColumn('managerOur', 'superprofit', 'сверхприбыль');
        $this->addCommentOnColumn('managerOur', 'wages', 'Зарплата/Оклад');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('managerOur', 'hasPercents');
        $this->dropColumn('managerOur', 'piecework');
        $this->dropColumn('managerOur', 'ourfirm_profit');
        $this->dropColumn('managerOur', 'material_profit');
        $this->addCommentOnColumn('managerOur', 'profit', 'прибыль');
        $this->addCommentOnColumn('managerOur', 'superprofit', '% сверхприбыль');
        $this->addCommentOnColumn('managerOur', 'wages', 'Зарплата');
        return true;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m191006_081239_change_ManagerOur_table cannot be reverted.\n";

      return false;
      }
     */
}
