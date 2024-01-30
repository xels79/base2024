<?php

use yii\db\Migration;

/**
 * Class m190926_200904_zp_table
 * Добовляем таблицу по зарплате
 * И редактируем колонки в таблице 'managerOur'
 *
 */
class m190926_200904_zp_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->alterColumn('managerOur', 'normal', $this->money()->null()->comment('руб/час'));
        $this->createTable('zarplat_moth', [
            'id'        => $this->primaryKey(),
            'month'     => $this->smallInteger()->notNull()->comment("Месяц"),
            'year'      => $this->smallInteger()->notNull()->comment('Год'),
            'day_count' => $this->smallInteger()->notNull()->comment('Количество рабочих дней')
        ]);
        $this->createTable('zarplata', [
            'id'         => $this->primaryKey(),
            'name'       => $this->string()->notNull()->comment('Имя'),
            'post'       => $this->integer()->notNull()->comment('Должность'),
            'month_id'   => $this->integer()->notNull()->comment('Дата'),
            'wages'      => $this->money()->null()->comment('В месяц'),
            'normal'     => $this->money()->null()->comment('руб/час'),
            'hours'      => $this->smallInteger()->defaultValue(0),
            'prize'      => $this->money()->null()->comment('Премия'),
            'prepayment' => $this->money()->null()->comment('22 число'),
            'payment1'   => $this->money()->null()->comment('Выдано'),
            'payment2'   => $this->money()->null()->comment('Выдано'),
            'payment3'   => $this->money()->null()->comment('Выдано'),
        ]);
        $this->createIndex('fk-zarplata', 'zarplata', 'month_id');
        $this->addForeignKey('fk-zarplata', 'zarplata', 'month_id', 'zarplat_moth', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->alterColumn('managerOur', 'normal', $this->money()->null()->comment('Норма'));
        $this->dropForeignKey('fk-zarplata', 'zarplata');
        $this->dropIndex('fk-zarplata', 'zarplata');
        $this->dropTable('zarplata');
        $this->dropTable('zarplat_moth');
        return true;
    }

}
