<?php

use yii\db\Migration;
use app\models\tables\Productions;
use yii\helpers\Console;

/**
 * Class m190312_095436_changeCategory2ColunType
 */
class m190312_095436_changeCategory2ColunType extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('production', 'category2', $this->text()->null());
        $query= Productions::find()->all();
        $cnt=count($query);
        $pos=0;
        $txt='Обнуленние записей в `production` ';
        Console::startProgress($pos,$cnt,$txt);
        foreach ($query as $model){
            Console::updateProgress($pos,$cnt,$txt);
            $model->category2='{}';
            $model->update(true,['category2']);
        }
        Console::endProgress($pos,$cnt,$txt);
        echo "\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190312_095436_changeCategory2ColunType cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190312_095436_changeCategory2ColunType cannot be reverted.\n";

        return false;
    }
    */
}
