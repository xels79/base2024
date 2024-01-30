<?php

use yii\db\Migration;

/**
 * Class m190304_111210_MatrialsOnFirms
 */
class m190304_111210_MatrialsOnFirms extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('materials_on_firms', [
            'id'=>$this->primaryKey(),
            'firm_id'=>$this->integer()->notNull()->comment('Сноска на фирму'),
            'm_type'=>$this->integer()->notNull()->comment('Сноска на тип материала'),
            'm_id'=>$this->integer()->notNull()->comment('Сноска на материал'),
            'coast'=>$this->double()->defaultValue(0)->notNull()->comment('Стоимость'),
            'update'=>$this->integer()->defaultValue(0)->comment('Посдеднее изменение'),
        ]);
        $this->addCommentOnTable('materials_on_firms', 'Связь материала с фирмой');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('materials_on_firms');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190304_111210_MatrialsOnFirms cannot be reverted.\n";

        return false;
    }
    */
}
