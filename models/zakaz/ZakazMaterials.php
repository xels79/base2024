<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\zakaz;

/**
 * Description of ZakazMaterials
 *
 * @author Александр
 * 
 */
use yii\db\ActiveRecord;

class ZakazMaterials extends ActiveRecord{
    public static $supplierTypeBaseText=['Заказчик','Испол-ль','Наш'];
    public static function tableName() {
        return 'zakaz_materials';
    }
    
    public function rules() {
        return [
            [['type_id', 'mat_id', 'zakaz_id','firm_id','supplierType','count'], 'integer'],
            [['coast'],'double'],
            [['paid'],'boolean'],
            [['type_id', 'mat_id', 'zakaz_id','firm_id','supplierType'], 'required'],
            [['coast','count'],'default','value'=>0],
            ['paid', 'default', 'value' => 0],
        ];
    }
    public function getSupplierTypeText(){
        return self::$supplierTypeBaseText[$this->supplierType];
    }
    public function getZakaz(){
        return $this->hasOne(Zakaz::className(), ['id'=>'zakaz_id']);
    }
}
