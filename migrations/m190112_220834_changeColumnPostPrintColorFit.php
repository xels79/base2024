<?php

use yii\db\Migration;

/**
 * Class m190112_220834_changeColumnPostPrintColorFit
 */
class m190112_220834_changeColumnPostPrintColorFit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('zakaz', 'post_print_color_fit', $this->smallInteger()->defaultValue(0)->comment('Печать и резка точность цветов'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('zakaz', 'post_print_color_fit', $this->boolean()->defaultValue(false)->comment('Печать и резка точность цветов'));

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190112_220834_changeColumnPostPrintColorFit cannot be reverted.\n";

        return false;
    }
    */
}
