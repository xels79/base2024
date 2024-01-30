<?php

use yii\db\Migration;

class m170601_145227_addMaterialColumnToFirmPost extends Migration
{
    public function up()
    {
        $this->addColumn('firmPost', 'materials', $this->text());
        $this->addCommentOnColumn('firmPost', 'materials', 'Список материалов');
    }

    public function down()
    {
        $this->dropColumn('firmPost', 'materials');
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
