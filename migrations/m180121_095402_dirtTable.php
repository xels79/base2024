<?php

use yii\db\Migration;

/**
 * Class m180121_095402_dirtTable
 */
class m180121_095402_dirtTable extends Migration
{
    public $tblName='zakaz_dirt';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable($this->tblName, [
            'id'=>$this->primaryKey()->comment('Индекс'),
            'dateofadmission'=>$this->date()->notNull()->comment('Дата приёма'),
            'zak_id'=>$this->integer()->notNull()->comment('Идентификатор фирмы заказчика'),
            'manager_id'=>$this->integer()->notNull()->comment('Идентификатор менеджера заказчика'),
            'ourmanager_id'=>$this->integer()->notNull()->comment('Идентификатор нашего менеджера'),
            'production_id'=>$this->integer()->notNull()->comment('Идентификатор подукции'),
            'worktypes_id'=>$this->integer()->notNull()->comment('Идентификатор способа печати'),
            'name'=>$this->text()->notNull()->comment('Наименование'),
            'total_coast'=>$this->money()->notNull()->comment('Общая стоимость'),
            'other_date'=>$this->text()->notNull()->comment('Остальные данные JSON')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->tblName);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180121_095402_dirtTable cannot be reverted.\n";

        return false;
    }
    */
}
