<?php

use yii\db\Migration;

class m170718_124226_addPostIdTo_ZakazMaterila extends Migration
{
    public $tblName='zakaz';
    public $materialsTblName;
    public function init(){
        parent::init();
        $this->materialsTblName= $this->tblName.'_materials';
    }
    public function safeUp()
    {
        $this->addColumn($this->materialsTblName, 'firm_id', $this->integer()->notNull()->comment('Идентификатор фирмы поставщика'));
        $this->addColumn($this->materialsTblName, 'count', $this->integer()->notNull()->comment('Количество'));//supplierType
        $this->addColumn($this->materialsTblName, 'coast', $this->double()->notNull()->comment('Стоимость')->defaultValue(0));
        $this->addColumn($this->materialsTblName, 'supplierType', $this->integer()->notNull()->comment('Тип поставщика'));//supplierType
    }

    public function safeDown()
    {
        $this->dropColumn($this->materialsTblName, 'firm_id');
        $this->dropColumn($this->materialsTblName, 'coast');
        $this->dropColumn($this->materialsTblName, 'count');
        $this->dropColumn($this->materialsTblName, 'supplierType');
        return true;
    }
}
