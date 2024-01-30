<?php

use yii\db\Migration;

class m171006_142010_rbac_addColl extends Migration
{
    public $tblName='rbac';
    public function safeUp()
    {
        $this->addColumn($this->tblName, 'engname', $this->text()->notNull()->comment('Транслит'));
    }

    public function safeDown()
    {
        $this->dropColumn($this->tblName, 'engname');
        return true;
    }

}
