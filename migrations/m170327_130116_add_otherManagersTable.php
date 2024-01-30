<?php

use yii\db\Migration;

class m170327_130116_add_otherManagersTable extends Migration
{
    public $tbls=[
        'Pod','Post','Zak'
    ];
    public function col($postFix){
        $idN='manager'.$postFix.'_id';
        $fKey='firm'.$postFix.'Id';
        return[
            $idN=>$this->primaryKey(),
            'name'=>$this->text()->notNull()->comment('Менеджер'),
            'phone'=>$this->text(20)->notNull()->comment('Телефон'),
            'mail'=>$this->text()->null()->comment('E-mail'),
            'grant'=>$this->boolean()->defaultValue(true)->comment('Допуск'),
            'firm_id'=>$this->integer()->notNull()->comment('К фирме')
        ];
    }
    public function up()
    {
        foreach ($this->tbls as $firms_name_postfix){
            $tN='manager'.$firms_name_postfix;
            $this->createTable($tN, $this->col($firms_name_postfix));
            $this->createIndex('fk-manager-'.$firms_name_postfix, $tN, 'firm_id');
            $this->addForeignKey(
                    'fk-manager-'.$firms_name_postfix,
                    $tN,
                    'firm_id',
                    'firm'.$firms_name_postfix,
                    'firm_id','CASCADE','CASCADE'
            );
        }
    }

    public function down()
    {
        foreach ($this->tbls as $firms_name_postfix){
            $tN='manager'.$firms_name_postfix;
            $this->dropForeignKey('fk-manager-'.$firms_name_postfix, $tN);
            $this->dropIndex('fk-manager-'.$firms_name_postfix, $tN);
            $this->dropTable($tN);
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
