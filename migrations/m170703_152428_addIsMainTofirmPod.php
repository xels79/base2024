<?php

use yii\db\Migration;

class m170703_152428_addIsMainTofirmPod extends Migration
{
    public $tblName='zakaz_pod';
    public function safeUp()
    {
        $this->addColumn($this->tblName, 'isMain', $this->boolean()->defaultValue(false));
        $this->addCommentOnColumn($this->tblName, 'isMain', 'Основной подрядчик');
        $this->addColumn($this->tblName, 'workType', $this->integer()->defaultValue(0));
        $this->addCommentOnColumn($this->tblName, 'workType', 'Вид работ');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tblName, 'isMain');
        $this->dropColumn($this->tblName, 'workType');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170703_152428_addIsMainTofirmPod cannot be reverted.\n";

        return false;
    }
    */
}
