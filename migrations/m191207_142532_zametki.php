<?php

use yii\db\Migration;

/**
 * Class m191207_142532_zametki
 */
class m191207_142532_zametki extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('zametki', [
            'id'          => $this->primaryKey(),
            'tabId'       => $this->integer()->notNull()->comment('Сноска на вкладку'),
            'name'        => $this->string(120)->notNull()->comment('Названи заметки'),
            'content'     => $this->text()->null()->comment('Содержимое'),
            'add_time'    => $this->integer()->null()->comment('Время создания'),
            'update_time' => $this->integer()->null()->comment('Время обновления'),
            'size'        => $this->integer()->null()->comment('Время создания'),
        ]);
        $this->addCommentOnTable('zametki', 'Заметки');
        $this->createTable('zametkiTabs', [
            'id'       => $this->primaryKey(),
            'name'     => $this->string(120)->null()->comment('Название колонки'),
            'add_time' => $this->integer()->comment('Время создания')
        ]);
        $this->addCommentOnTable('zametkiTabs', 'Заметки название колонок');
        $this->createIndex('tabIdRef', 'zametki', 'tabId');
        $this->addForeignKey('tabIdRef', 'zametki', 'tabId', 'zametkiTabs', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropForeignKey('tabIdRef', 'zametki');
        $this->dropIndex('tabIdRef', 'zametki');
        $this->dropTable('zametkiTabs');
        $this->dropTable('zametki');
        return true;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m191207_142532_zametki cannot be reverted.\n";

      return false;
      }
     */
}
