<?php

use yii\db\Migration;

class m171008_160451_rbac_finelSetup extends Migration
{
    public $tblName='rbac';
    public function safeUp()
    {
        $this->addColumn($this->tblName, 'lastupdate', $this->integer()->comment('Время последнего обновления'));
        foreach (['admin'=>'Администратор','moder'=>'Менеджер','buhgalter'=>'Бухгалтер','dizayner'=>'Дизайнер','proizvodstvo'=>'Производство'] as $key=>$el){
            $model=new \app\models\MyRbac();
            $model->name=$el;
            $model->value='{"allow":{},"denied":{}}';
            $model->engname= $key;
            $model->save();
        }
    }

    public function safeDown()
    {
        $this->dropColumn($this->tblName, 'lastupdate');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171008_160451_rbac_finelSetup cannot be reverted.\n";

        return false;
    }
    */
}
