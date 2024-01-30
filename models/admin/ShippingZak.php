<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\admin;

/**
 * Description of ShippingZak
 *
 * @author Александр
 */
class ShippingZak extends \app\models\BaseActiveRecord{
    public static $typeList=[
        'Да','Нет','Договорная'
    ];
    public static function tableName() {
        return 'shippingZak';
    }
    
    public function getTypeText(){
        return self::$typeList[$this->type_id];
    }
    public function rules(){
        return [
            [['sity'],'string'],
            [['type_id','firm_id'],'integer'],
            [['summ1','summ2'],'number'],
            [['is_transport_company','is_to_office','is_to_production','is_to_shop'],'boolean']
        ];
    }

    public function attributeLabels() {
        return \yii\helpers\ArrayHelper::merge(parent::attributeLabels(),[
            'typeText'=>'Обязательная доставка'
        ]);
    }
    
}
