<?php

use yii\db\Migration;

class m170307_122733_step2 extends Migration
{
    public $tbl=[['Zak','Реквизиты заказчика','Заказчик'],
                ['Pod','Реквизиты подрядчик','Подрядчик'],
                ['Post','Реквизиты поставщик','Поставщик'],
                ['Our','Реквизиты нашей фирмы']  //Название таблицы основной фирмы
                                                          //Обязательно последним
            ];
//    public function createContacts(){
//        $columns=[
//            'contacts_id'=>$this->primaryKey(),
//            'post_id'=>$this->integer()->defaultValue(0)->comment('Должность'),
//            'name'=>$this->text()->notNull()->comment('Ф.И.О'),
//            'phone'=>$this->text()->null()->comment('Телефон'),
//            'email'=>$this->text()->null()->comment('E-mail'),
//            'status_id'=>$this->integer()->notNull()->comment('Статус'),
//            'comment'=>$this->text()->null()->comment('Комментарий'),
//            'firm_id'=>$this->integer()->notNull()->comment('К фирме')
//        ];
//        $this->createTable('contacts', $columns);
//        foreach ($this->tbl as $el){
//            if ($el[0]!='Our'){
//                $this->createIndex('fk-contacts'.$el[0], 'contacts', 'firm_id');
//                $this->addForeignKey('fk-contacts'.$el[0], 'contacts', 'firm_id', 'firm'.$el[0], 'firm_id');
//            }
//        }
//
//    }

    public function up()
    {
        $columns=[
            'managerOur_id'=>$this->primaryKey(),
            'name'=>$this->text()->notNull()->comment('Ф.И.О'),
            'phone1'=>$this->text()->null()->comment('Телефон'),
            'phone2'=>$this->text()->null()->comment('Телефон'),
            'address'=>$this->text()->null()->comment('Адрес'),
            'inn'=>$this->string(16)->notNull()->comment('ИНН'),
            'snils'=>$this->text()->notNull()->comment('Снилс'),
            'passport_series'=>$this->text()->null()->comment('Серия'),
            'passport_number'=>$this->text()->null()->comment('Номер'),
            'passport_given'=>$this->text()->null()->comment('Выдан'),
            'passport_given_date'=>$this->date()->null()->comment('Дата выдачи'),
            'birthday'=>$this->date()->null()->comment('День рождения'),
            'registration'=>$this->text()->null()->comment('Регистрация'),
            'post'=>$this->text()->notnull()->comment('Должность'),
            'employed'=>$this->boolean()->defaultValue(true)->comment('Оформление'),
            'payment_id'=>$this->integer()->defaultValue(0)->comment('Оплата'),
            'wages'=>$this->money()->null()->comment('Зарплата'),
            'profit'=>$this->integer()->defaultValue(10)->comment('% прибыль'),
            'superprofit'=>$this->integer()->defaultValue(50)->comment('% сверхприбыль'),
            'normal'=>$this->money()->null()->comment('Норма'),
            'recycling_rate'=>$this->decimal(2,0)->defaultValue((double)1.1)->comment('коэф. переработки'),
            'status_id'=>$this->integer()->defaultValue(0)->comment('Статус'),
            'foto'=>$this->text()->null()->comment('Фото'),
            'firm_id'=>$this->integer()->notNull()->comment('К фирме')
        ];
        $this->createTable('managerOur', $columns);
        $this->createIndex('fk-managerOur', 'managerOur', 'firm_id');
        $this->addForeignKey('fk-managerOur', 'managerOur', 'firm_id', 'firmOur', 'firm_id','CASCADE','CASCADE');
        $this->createIndex('fk-mangers-inn-unique', 'managerOur', 'inn',true);
    }

    public function down()
    {
        $this->dropForeignKey('fk-managerOur','managerOur');
        $this->dropIndex('fk-managerOur', 'managerOur');
        $this->dropIndex('fk-mangers-inn-unique', 'managerOur');
        $this->dropTable('managerOur');
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
