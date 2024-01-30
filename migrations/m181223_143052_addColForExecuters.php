<?php

use yii\db\Migration;

/**
 * Class m181223_143052_addColForExecuters
 */
class m181223_143052_addColForExecuters extends Migration
{
    /**
     * {@inheritdoc}
     */
    private $dopN=['_summ','_payment'];
    private $dopC=[' сумма',' выплаты'];
    public function addBlock($baseName,$comm, $addBool=true){
        if ($addBool)
            $this->addColumn('zakaz', $baseName, $this->boolean()->defaultValue(0)->comment($comm));
        for ($i=0;$i<count($this->dopN);$i++){
            $this->addColumn('zakaz', $baseName.$this->dopN[$i], $this->money()->defaultValue(0)->comment($comm.$this->dopC[$i]));
        }
    }
    public function safeUp()
    {
        $this->addBlock('exec_speed','Срочность');
        $this->addBlock('exec_markup','Наценка');
        $this->addBlock('exec_bonus','Бонус');
        $this->addBlock('exec_delivery','Доставка');
        $this->addBlock('exec_transport','Транспорт');
        $this->addBlock('exec_transport2','Транспорт2', false);
    }
    public function removeBloc($baseName, $addBool=true){
        if ($addBool) $this->dropColumn ('zakaz', $baseName);
        for ($i=0;$i<count($this->dopN);$i++){
            $this->dropColumn ('zakaz', $baseName.$this->dopN[$i]);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removeBloc('exec_speed');
        $this->removeBloc('exec_markup');
        $this->removeBloc('exec_bonus');
        $this->removeBloc('exec_delivery');
        $this->removeBloc('exec_transport');
        $this->removeBloc('exec_transport2', false);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181223_143052_addColForExecuters cannot be reverted.\n";

        return false;
    }
    */
}
