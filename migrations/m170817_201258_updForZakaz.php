<?php

use yii\db\Migration;

class m170817_201258_updForZakaz extends Migration
{
    public function up()
    {
        $this->addCommentOnColumn('zakaz', 'spending', 'Затраты исполнитель');
        $this->addColumn('zakaz', 'spending2', $this->money()->notNull()->comment('Затраты материал'));
        return true;
    }

    public function down()
    {
        $this->dropColumn('zakaz', 'spending2');
        return true;
    }
}
