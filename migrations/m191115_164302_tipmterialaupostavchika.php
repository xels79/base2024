<?php

use yii\db\Migration;

/**
 * Class m191115_164302_tipmterialaupostavchika
 */
class m191115_164302_tipmterialaupostavchika extends Migration
{
    public function safeUp()
    {
        $this->addColumn('workOrproductType', 'category2', $this->integer()->defaultValue(0)->comment('Категория 2'));
        $this->addColumn('workOrproductType', 'update_time', $this->integer()->defaultValue(0)->comment('Последнее обновление'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('workOrproductType', 'category2');
        $this->dropColumn('workOrproductType', 'update_time');
        return true;
    }
}
