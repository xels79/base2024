<?php

use yii\db\Migration;

class m170401_095007_shippingZak extends Migration
{
    public $tName='shippingZak';
    public function up()
    {
        $this->createTable($this->tName, [
            'shippingId'=>$this->primaryKey(),
            'type_id'=>$this->integer()->defaultValue(0)->comment('Обязательная доставка'),
            'is_transport_company'=>$this->boolean()->defaultValue(true)->comment('Транспортная компания'),
            'summ1'=>$this->float()->null()->comment('Сумма доставки'),
            'summ2'=>$this->float()->null()->comment('Сумма доставки'),
            'sity'=>$this->text()->null()->comment('Город'),
            'is_to_office'=>$this->boolean()->defaultValue(false)->comment('в офис'),
            'is_to_production'=>$this->boolean()->defaultValue(true)->comment('на производство'),
            'is_to_shop'=>$this->boolean()->defaultValue(true)->comment('в магазин'),
            'firm_id'=>$this->integer()->notNull()->comment('К фирме')
        ]);
        $this->createIndex('fk-'.$this->tName, $this->tName, 'firm_id');
        $this->addForeignKey(
                'fk-'.$this->tName,
                $this->tName,
                'firm_id',
                'firmZak',
                'firm_id','CASCADE','CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-'.$this->tName, $this->tName);
        $this->dropIndex('fk-'.$this->tName, $this->tName);
        $this->dropTable($this->tName);
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