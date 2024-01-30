<?php

use yii\db\Migration;
use app\models\tables\Worktypes;
/**
 * Class m190411_093547_addWorkPackUnpack
 */
class m190411_093547_addWorkPackUnpack extends Migration
{
    /**
     * {@inheritdoc}
     */
    private function addV($value){
        if (!$q= Worktypes::findOne(['name'=>$value])){
            $m=new Worktypes();
            $m->name=$value;
            $m->category=3;
            if (!$m->save()){
                echo 'Ошибка добавления вида работы "'.$value.'"'."\n";
                foreach($m->errors as $k=>$v){
                    echo " '$k'=>'$v'\n";
                }
                return false;
            }else{
                echo "Добавлена - '$value'\n";
            }
        }else{
            echo "'$value' - уже существует.\n";
        }
        return true;
    }
    public function safeUp()
    {
        echo "Добаляем стандартные работы:\n";
        if (!$this->addV('Распаковка')) return false;
        if (!$this->addV('Упаковка')) return false;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "Удаляем стандартные работы:\n";
        echo "Удалены: ".Worktypes::deleteAll(['in','name',['Распаковка','Упаковка']])." елемент(а(ов)).\n";
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_093547_addWorkPackUnpack cannot be reverted.\n";

        return false;
    }
    */
}
