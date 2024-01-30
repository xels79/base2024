<?php

use yii\db\Migration;

class m170621_141701_tableZakaz extends Migration
{
    public $tblName='zakaz';
    public $materialsTblName;
    public $podTblName;
    public function init(){
        parent::init();
        $this->materialsTblName= $this->tblName.'_materials';
        $this->podTblName=$this->tblName.'_pod';
    }
    private function getMatCol(){
        $refColName=$this->tblName.'_id';
        return [
            'id'=>$this->primaryKey(),
            'type_id'=>$this->integer()->notNull()->comment('Идентификатор типа материала'),
            'mat_id'=>$this->integer()->notNull()->comment('Идентификатор непосредственно материала'),
            "$refColName"=>$this->integer()->notNull()->comment('Сноска на заказ')
        ];
    }
    private function getPodCol(){
        $refColName=$this->tblName.'_id';
        return [
            'id'=>$this->primaryKey(),
            'pod_id'=>$this->integer()->null()->comment('Идентификатор фирмы'),
            'manager_id'=>$this->integer()->null()->comment('Идентификатор менеджера'),
            'coast'=>$this->money()->defaultValue(0)->comment('Стоимость'),
            'payment'=>$this->money()->defaultValue(0)->comment('Выплата'),
            "$refColName"=>$this->integer()->notNull()->comment('Сноска на заказ')
        ];
    }
    private function getZakazCol(){
        return [
            'id'=>$this->primaryKey(),
            'dateofadmission'=>$this->date()->notNull()->comment('Дата приёма'),
            'deadline'=>$this->date()->notNull()->comment('Срок сдачи'),
            'zak_id'=>$this->integer()->notNull()->comment('Идентификатор фирмы заказчика'),
            'manager_id'=>$this->integer()->notNull()->comment('Идентификатор менеджера заказчика'),
            'ourmanager_id'=>$this->integer()->notNull()->comment('Идентификатор нашего менеджера'),
            'production_id'=>$this->integer()->notNull()->comment('Идентификатор подукции'),
            'worktypes_id'=>$this->integer()->notNull()->comment('Идентификатор способа печати'),
            'name'=>$this->text()->notNull()->comment('Наименование'),
            'number_of_copies'=> $this->string(14)->notNull()->comment('Тираж'),
            'stage'=> $this->char()->defaultValue(0)->comment('Этапы работы'),
            'division_of_work'=>$this->char()->defaultValue(0)->comment('Работа (50/50 ...)'),
            'attention'=> $this->text()->null()->comment('Особое внимание'),
            'method_of_payment'=> $this->char()->defaultValue(0)->comment('Форма оплаты'),
            'account_number'=>$this->text()->null()->comment('Номер счета'),
            'total_coast'=>$this->money()->notNull()->comment('Общая стоимость'),
            'spending'=>$this->money()->notNull()->comment('Затраты'),
            'product_size'=> $this->string(12)->null()->comment('Размер готового изделия'),
            'format_printing_block'=> $this->string(12)->null()->comment('Формат печатного блока'),
            'num_of_products_in_block'=>$this->integer()->null()->comment('Кол-во изделий в блоке'),
            'num_of_printing_block'=>$this->integer()->null()->comment('Кол-во печатных блоков'),
            'colors'=> $this->text()->notNull()->comment('Цветность'),
            'post_print'=> $this->text()->notNull()->comment('Постпечатка'),
            'note'=>$this->text()->null()->comment('Примечания')
        ];
    }
    public function safeUp()
    {
        $refColName=$this->tblName.'_id';
        $this->createTable($this->tblName, $this->getZakazCol());
        $this->createTable($this->materialsTblName, $this->getMatCol());
        $this->createTable($this->podTblName, $this->getPodCol());
        $this->createIndex('fk_'.$this->materialsTblName.'_'.$refColName, $this->materialsTblName, $refColName);
        $this->createIndex('fk_'.$this->podTblName.'_'.$refColName, $this->podTblName, $refColName);
        $this->addForeignKey('fk_'.$this->materialsTblName.'_'.$refColName,
                            $this->materialsTblName,
                            $refColName,
                            $this->tblName,
                            'id','CASCADE','CASCADE');
        $this->addForeignKey('fk_'.$this->podTblName.'_'.$refColName,
                            $this->podTblName,
                            $refColName,
                            $this->tblName,
                            'id','CASCADE','CASCADE');
        $this->addCommentOnTable($this->materialsTblName, 'Материалы используемые в заказах');
        $this->addCommentOnTable($this->podTblName, 'Подрядчики используемые в заказах');
    }

    public function safeDown()
    {
        $refColName=$this->tblName.'_id';
        $this->dropForeignKey('fk_'.$this->materialsTblName.'_'.$refColName, $this->materialsTblName);
        $this->dropForeignKey('fk_'.$this->podTblName.'_'.$refColName, $this->podTblName);
        $this->dropIndex('fk_'.$this->materialsTblName.'_'.$refColName, $this->materialsTblName);
        $this->dropIndex('fk_'.$this->podTblName.'_'.$refColName, $this->podTblName);
        $this->dropTable($this->materialsTblName);
        $this->dropTable($this->podTblName);
        $this->dropTable($this->tblName);
        return true;
    }
}
