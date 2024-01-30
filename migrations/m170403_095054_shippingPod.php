<?php

use yii\db\Migration;

class m170403_095054_shippingPod extends Migration
{
    public $tName='shippingPod';
    public function up()
    {
        $this->createTable($this->tName, [
            'shippingId'=>$this->primaryKey(),
            'is_self_transport'=>$this->boolean()->defaultValue(false)->comment('Самовывоз'),
            'expense_to'=>$this->float()->null()->comment('до'),
            'expense_from'=>$this->float()->null()->comment('от'),
            'summ1'=>$this->float()->null()->comment('Сумма доставки до'),
            'summ2'=>$this->float()->null()->comment('Сумма доставки от'),
            'summ3'=>$this->float()->null()->comment('срочная'),
            'is_in_office'=>$this->boolean()->defaultValue(false)->comment('в офис'),
            'is_in_production'=>$this->boolean()->defaultValue(true)->comment('на производство'),
            'is_in_shop'=>$this->boolean()->defaultValue(true)->comment('в магазин'),
            'firm_id'=>$this->integer()->notNull()->comment('К фирме')
        ]);
        $this->createIndex('fk-'.$this->tName, $this->tName, 'firm_id');
        $this->addForeignKey(
                'fk-'.$this->tName,
                $this->tName,
                'firm_id',
                'firmPod',
                'firm_id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-'.$this->tName, $this->tName);
        $this->dropIndex('fk-'.$this->tName, $this->tName);
        $this->dropTable($this->tName);
        return true;
    }

}
