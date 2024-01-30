<?php

use yii\db\Migration;

/**
 * Class m181217_150656_addForPostPrint
 */
class m181217_150656_addForPostPrint extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'post_print_call_to_print', $this->boolean()->defaultValue(false)->comment('Вызов на печать'));
        $this->addColumn('zakaz', 'post_print_agent_name', $this->string(64)->null()->comment('Вызов на печать имя'));
        $this->addColumn('zakaz', 'post_print_agent_phone', $this->string(24)->null()->comment('Вызов на печать телефон'));
        $this->addColumn('zakaz', 'post_print_uf_lak', $this->smallInteger()->defaultValue(0)->comment('Печать и резка Уф-лак'));
        $this->addColumn('zakaz', 'post_print_uf_lak_text', $this->text()->null()->comment('Печать и резка Уф-лак текст'));
        $this->addColumn('zakaz', 'post_print_thermal_lift', $this->smallInteger()->defaultValue(0)->comment('Печать и резка Термоподъем'));
        $this->addColumn('zakaz', 'post_print_thermal_lift_text', $this->text()->null()->comment('Печать и резка Термоподъем текст'));
        $this->addColumn('zakaz', 'post_print_rezka', $this->boolean()->defaultValue(false)->comment('Печать и резка Резка'));
        $this->addColumn('zakaz', 'post_print_rezka_text', $this->text()->null()->comment('Печать и резка Резка текст'));
        $this->addColumn('zakaz', 'post_print_color_fit', $this->boolean()->defaultValue(false)->comment('Печать и резка точность цветов'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'post_print_rezka_text');
        $this->dropColumn('zakaz', 'post_print_rezka');
        $this->dropColumn('zakaz', 'post_print_thermal_lift_text');
        $this->dropColumn('zakaz', 'post_print_thermal_lift');
        $this->dropColumn('zakaz', 'post_print_uf_lak_text');
        $this->dropColumn('zakaz', 'post_print_uf_lak');
        $this->dropColumn('zakaz', 'post_print_agent_phone');
        $this->dropColumn('zakaz', 'post_print_agent_name');
        $this->dropColumn('zakaz', 'post_print_call_to_print');
        $this->dropColumn('zakaz', 'post_print_color_fit');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181217_150656_addForPostPrint cannot be reverted.\n";

        return false;
    }
    */
}
