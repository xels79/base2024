<?php

use yii\db\Migration;

class m170327_120917_add_contract_field_toFirms extends Migration
{
    public $tbls=[
        'Pod','Post','Zak'
    ];
    public function up()
    {
        foreach ($this->tbls as $firms_name_postfix){
            $tblName='firm'.$firms_name_postfix;
            $this->addColumn($tblName, 'has_contract', $this->boolean()->defaultValue(false)->comment('Договор'));
            $this->addColumn($tblName, 'contract_number', $this->text()->null()->comment('№ договора'));
        }
    }

    public function down()
    {
        foreach ($this->tbls as $firms_name_postfix){
            $tblName='firm'.$firms_name_postfix;
            $this->dropColumn($tblName, 'has_contract');
            $this->dropColumn($tblName, 'contract_number');
        }
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
