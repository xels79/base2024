<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\admin;

/**
 * Description of Address
 *
 * @author Александр
 */
class AddressOur extends \app\models\BaseActiveRecord{
    public static $places_list=[
        'Офис',
        'Склад',
        'Магазин',
        'Производство'
    ];
    
    public static function tableName() {
        return 'addressOur';
    }
    public function attributeLabels() {
        return \yii\helpers\ArrayHelper::merge(parent::attributeLabels(),[
            'placeText'=>'Место'
        ]);
    }
    public function getPlaceText(){
        return self::$places_list[$this->place_id];
    }
    public function rules() {
        return [
            [['place_id','firm_id'],'integer'],
            [['actualAddress','name','phone'],'string'],
            [['actualAddress','name','phone','firm_id'],'required','message'=>'Должно быть указано.']
        ];
    }
}
