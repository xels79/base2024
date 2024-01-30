<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\admin;

/**
 * Description of ManagersOur
 *
 * @author Александр
 * @property int $managerZak_id Идентификатор
 * @property string $name Имя
 * @property string $phone Телефон
 * @property string $mail E-mail
 * @property integer $grant Допуск
 * @property integer $firm_id Идентификатор фирмы
 * 
 * @property string $grantText Текстовое значени допуска
 */
class Manager extends \app\models\BaseActiveRecord{
    public function rules() {
        return [
            [['firm_id'],'integer'],
            [['phone','name'],'string'],
            [['phone','name'],'required','message'=>'Должно быть указано.'],
            [['grant'],'boolean'],
            [['mail'],'email']
        ];
    }

    public function attributeLabels() {
        return \yii\helpers\ArrayHelper::merge(parent::attributeLabels(),[
            'grantText'=>'Допуск'
        ]);
    }    
    public function getGrantText(){
        return $this->grant?'Да':'Нет';
    }
}
