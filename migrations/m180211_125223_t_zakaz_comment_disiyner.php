<?php

use yii\db\Migration;

/**
 * Class m180211_125223_t_zakaz_comment_disiyner
 */
class m180211_125223_t_zakaz_comment_disiyner extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('zakaz', 'design_comment', $this->text()->null()->comment('Коментарий дизайнера'));
        $this->addColumn('zakaz', 'proizv_comment', $this->text()->null()->comment('Коментарий производство'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('zakaz', 'design_comment');
        $this->dropColumn('zakaz', 'proizv_comment');
        return true;
    }
}
