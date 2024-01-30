<?php

use yii\db\Migration;

/**
 * Class m200315_090723_addOptCoastToMaterials
 */
class m200315_090723_addOptCoastToMaterials extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        //materials_on_firms
        $this->addColumn('materials_on_firms', 'optfrom', $this->integer()->defaultValue(0)->comment('Опт количество'));
        $this->addColumn('materials_on_firms', 'optcoast', $this->double()->defaultValue(0)->comment('Опт стоимость'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('materials_on_firms', 'optfrom');
        $this->dropColumn('materials_on_firms', 'optcoast');
        return true;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m200315_090723_addOptCoastToMaterials cannot be reverted.\n";

      return false;
      }
     */
}
