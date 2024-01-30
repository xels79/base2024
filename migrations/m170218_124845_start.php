<?php

use yii\db\Migration;

class m170218_124845_start extends Migration
{
    public $tbl=[['Zak','Реквизиты заказчика','Заказчик'],
                ['Pod','Реквизиты подрядчик','Подрядчик'],
                ['Post','Реквизиты поставщик','Поставщик'],
                ['Our','Реквизиты нашей фирмы']  //Название таблицы основной фирмы
                                                          //Обязательно последним
            ];
    private function allFims(){
        return[
            'firm_id'=> $this->primaryKey(),            
            'mainForm'=>$this->integer()->defaultValue(0)->comment('Фирма'),
            'mainName'=>$this->text()->notNull(),
            'status'=>$this->boolean()->defaultValue(1)->comment('Статус'),
            'typeOfPayment'=>$this->integer()->defaultValue(0)->comment('Оплата'),
            'credit'=>$this->text()->null()->comment('Кредит'),
            'delay'=>$this->integer()->defaultValue(0)->comment('Отсрочка')
        ];
    }

    private function createRekvizit(){
        $coloms=[
            'rekvizit_id'=>$this->primaryKey(),
            'form'=>$this->integer()->defaultValue(0)->comment('Форма'),
            'name'=>$this->text()->notNull()->comment('Название'),
            'inn'=>$this->string(16)->notNull()->comment('ИНН'),
            'kpp'=>$this->text()->null()->comment('КПП'),
            'address'=>$this->text()->null()->comment('Адрес'),
            'consignee'=>$this->text()->null()->comment('Грузополучатель'),
            'account'=>$this->text()->null()->comment('Расч. счёт'),
            'bik'=>$this->text()->null()->comment('БИК'),
            'bank'=>$this->text()->null()->comment('Банк'),
            'correspondentAccount'=>$this->text()->null()->comment('Кор. счёт'),
            'ogrn'=>$this->text()->null()->comment('ОГРН'),
            'okpo'=>$this->text()->null()->comment('ОКПО'),
            'okved'=>$this->text()->null()->comment('ОКВЭД'),
            'ceo'=>$this->text()->null()->comment('ФИО Ген. директора'),
            'okved'=>$this->text()->null()->comment('ОКВЭД'),
            'active'=>$this->boolean()->defaultValue(false)->comment('Активный'),
            'firm_id'=>$this->integer()->notNull()->comment('К фирме')
        ];
        foreach ($this->tbl as $el){
            if ($el[0]=='Our'){
                $this->createTable('rekvizit'.$el[0], yii\helpers\ArrayHelper::merge($coloms,[
                    'nameChiefAccountant'=>$this->text()->null()->comment('ФИО Гл. бухгалтера'),
                    'signatureCEO'=>$this->text()->null()->comment('Подпись Ген. директора'),
                    'signatureAccountant'=>$this->text()->null()->comment('Подпись бухгалтера'),
                    'stamp'=>$this->text()->null()->comment('Печать'),
                    'incorporationDocuments'=>$this->text()->null()->comment('Учредительные документы'),
                    'tax_system_id'=>$this->integer()->defaultValue(0)->comment('Система налогооблажения'),
                    'tax_id'=>$this->integer()->defaultValue(0)->comment('Налог')
                ]));
            }else
                $this->createTable('rekvizit'.$el[0], yii\helpers\ArrayHelper::merge($coloms,[
                    'passportSeries'=>$this->text()->null()->comment('Серия'),
                    'passportNumber'=>$this->text()->null()->comment('Номер'),
                    'passportGiven'=>$this->text()->null()->comment('Выдан'),
                    'passportGivenDate'=>$this->date()->null()->comment('Дата выдачи'),
                    'certificateSeries'=>$this->text()->null()->comment('Серия'),
                    'certificateNumber'=>$this->text()->null()->comment('Номер'),
                    'certificateGiven'=>$this->text()->null()->comment('Выдан'),
                    'certificateGivenDate'=>$this->date()->null()->comment('Дата выдачи'),                    
                ]));
            $this->addCommentOnTable('rekvizit'.$el[0], $el[1]);
            $this->createIndex('fk-firm'.$el[0], 'rekvizit'.$el[0], 'firm_id');
            $this->createIndex('fk-firm-unique'.$el[0], 'rekvizit'.$el[0], 'inn',true);
        }        
    }
    public function createMainFirm(){
        $tName='firmOur';
        $columns=[
            'firm_id'=>$this->primaryKey(),
            'mainName'=>$this->text()->notNull()->comment('Фирма'),
            'logo'=>$this->text()->null()->comment('Логотип'),
            'pic1'=>$this->text()->null()->comment('Рис1'),
            'pic2'=>$this->text()->null()->comment('Рис2'),
        ];
        $this->createTable($tName, $columns);
        $this->addForeignKey('fk-firmOur', 'rekvizitOur', 'firm_id', 'firmOur', 'firm_id','CASCADE','CASCADE');
    }
    public function createOtherFirm(){
        for ($i=0;$i<count($this->tbl)-1;$i++){
            $tmp=$this->allFims();
            $tmp['mainName']->comment($this->tbl[$i][2]);
            $this->createTable('firm'.$this->tbl[$i][0], $tmp);
            $this->addCommentOnTable('firm'.$this->tbl[$i][0], 'Фирмы '.mb_strtolower($this->tbl[$i][2]));
            $this->addForeignKey('fk-firm'.$this->tbl[$i][0],
                    'rekvizit'.$this->tbl[$i][0],
                    'firm_id',
                    'firm'.$this->tbl[$i][0],
                    'firm_id','CASCADE','CASCADE');
        }
    }
    public function createAddres(){
        $columns=[
            'address_id'=>$this->primaryKey(),
            'place_id'=>$this->integer()->defaultValue(0)->comment('Место'),
            'actualAddress'=>$this->text()->notNull()->comment('Фактический адрес'),
            'name'=>$this->text()->null()->comment('Ф.И.О'),
            'phone'=>$this->text()->null()->comment('Телефон'),
            'firm_id'=>$this->integer()->notNull()->comment('К фирме')
        ];
        foreach ($this->tbl as $el){
            $tName='address'.$el[0];
            $this->createTable($tName, $columns);
            $this->createIndex('fk-address'.$el[0], $tName, 'firm_id');
            $this->addForeignKey('fk-address'.$el[0], $tName, 'firm_id', 'firm'.$el[0], 'firm_id','CASCADE','CASCADE');
        }
    }
    public function createTblUser(){
        $this->createTable('tbl_user', [
            'id'=>$this->primaryKey()->comment('Индекс'),
            'username'=>$this->char(128)->notNull()->comment('Имя пользователя'),
            'password'=>$this->char(128)->notNull()->comment('Пароль'),
            'email'=>$this->char(128)->notNull()->comment('Почта'),
            'utype'=>$this->integer()->defaultValue(3)->comment('Тип пользователя'),
            'realname'=>$this->char(128)->notNull()->comment('Настоящие имя'),
            'update_time'=>$this->integer()->defaultValue(0)->comment('Последнее обновление'),
            'login_time'=>$this->integer()->defaultValue(0)->comment('Последнее вход'),
            'control_time'=>$this->integer()->defaultValue(0)->comment('Внутрений контроль'),
            'logout_time'=>$this->integer()->defaultValue(0)->comment('Последнее выход'),
            'last_zakaz'=>$this->integer()->defaultValue(0)->comment('Последнее заказ'),
            'wages'=>$this->decimal()->defaultValue(0)->comment('Оклад'),
            'percent1'=>$this->decimal()->defaultValue(0)->comment('№ Шелкуха'),
            'percent2'=>$this->decimal()->defaultValue(0)->comment('№ Подрядчик'),
            'percent3'=>$this->decimal()->defaultValue(0)->comment('№ доп'),

        ]);
        $model=new \app\models\TblUser();
        $model->username='admin';
        $model->passwordnew='admin';
        $model->utype=1;
        $model->email='admin@mail.ru';
        $model->realname='Administrator';
        $model->save();
    }
    public function up()
    {
        $this->createTblUser();
        $this->createRekvizit();
        $this->createMainFirm();
        $this->createOtherFirm();
        $this->createAddres();
    }

    public function down()
    {
        foreach ($this->tbl as $el){
            $this->dropForeignKey('fk-firm'.$el[0], 'rekvizit'.$el[0]);
            $this->dropIndex('fk-firm'.$el[0], 'rekvizit'.$el[0]);
            $this->dropIndex('fk-firm-unique'.$el[0], 'rekvizit'.$el[0]);
            $this->dropForeignKey('fk-address'.$el[0], 'address'.$el[0]);
            $this->dropIndex('fk-address'.$el[0], 'address'.$el[0]);
        }
        foreach ($this->tbl as $el){
            $this->dropTable('firm'.$el[0]);
            $this->dropTable('rekvizit'.$el[0]);
            $this->dropTable('address'.$el[0]);
        }
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
