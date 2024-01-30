<?php

use yii\db\Migration;

class m170328_144258_create_t_contacts extends Migration
{
    public $tbls=[
        'Pod','Post','Zak'
    ];
    public $tPerf='contact';
    public function col($postFix){
        $idN=$this->tPerf.$postFix.'_id';
        $fKey='firm'.$postFix.'Id';
        return[
            $idN=>$this->primaryKey(),
            'post_id'=>$this->smallInteger()->defaultValue(0)->comment('Должность'),
            'name'=>$this->text()->notNull()->comment('Ф.И.О'),
            'phone'=>$this->text()->notNull()->comment('Телефон'),
            'mail'=>$this->text()->null()->comment('E-mail'),
            'status_id'=>$this->smallInteger()->defaultValue(0)->comment('Статус'),
            'comment'=>$this->text()->null()->comment('Коментарий'),
            'firm_id'=>$this->integer()->notNull()->comment('К фирме')
        ];
    }
    public function up()
    {
        foreach ($this->tbls as $firms_name_postfix){
            $tN=$this->tPerf.$firms_name_postfix;
            $this->createTable($tN, $this->col($firms_name_postfix));
            $this->createIndex('fk-'.$this->tPerf.'-'.$firms_name_postfix, $tN, 'firm_id');
            $this->addForeignKey(
                    'fk-'.$this->tPerf.'-'.$firms_name_postfix,
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
            $tN=$this->tPerf.$firms_name_postfix;
            $this->dropForeignKey('fk-'.$this->tPerf.'-'.$firms_name_postfix, $tN);
            $this->dropIndex('fk-'.$this->tPerf.'-'.$firms_name_postfix, $tN);
            $this->dropTable($tN);
        }        
        return true;
    }
}