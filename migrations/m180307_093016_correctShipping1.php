<?php

use yii\db\Migration;

/**
 * Class m180307_093016_correctShipping1
 */
class m180307_093016_correctShipping1 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        //'type_id'=>$this->integer()->defaultValue(0)->comment('Обязательная доставка'),
        //'is_transport_company'=>$this->boolean()->defaultValue(true)->comment('Транспортная компания'),
        //'sity'=>$this->text()->null()->comment('Город'),
        $this->addColumn('shippingPod', 'type_id', $this->integer()->defaultValue(0)->comment('Обязательная доставка'));
        $this->addColumn('shippingPost', 'type_id', $this->integer()->defaultValue(0)->comment('Обязательная доставка'));
        
        $this->addColumn('shippingPod', 'is_transport_company', $this->boolean()->defaultValue(true)->comment('Транспортная компания'));
        $this->addColumn('shippingPost', 'is_transport_company', $this->boolean()->defaultValue(true)->comment('Транспортная компания'));
        
        $this->addColumn('shippingPod', 'sity', $this->text()->null()->comment('Город'));
        $this->addColumn('shippingPost', 'sity', $this->text()->null()->comment('Город'));
        
        $this->renameColumn('shippingPod', 'is_in_office', 'is_to_office');
        $this->renameColumn('shippingPod', 'is_in_production', 'is_to_production');
        $this->renameColumn('shippingPod', 'is_in_shop', 'is_to_shop');
        
        $this->renameColumn('shippingPost', 'is_in_office', 'is_to_office');
        $this->renameColumn('shippingPost', 'is_in_production', 'is_to_production');
        $this->renameColumn('shippingPost', 'is_in_shop', 'is_to_shop');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "Миграция: 'm180307_093016_correctShipping1' не может быть отменена\nВ связи с изменением названий колонок!\n";
        return false;
    }

}
