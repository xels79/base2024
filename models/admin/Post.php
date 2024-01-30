<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\admin;

/**
 * Description of Zak
 *
 * @author Александр
 */
use app\models\tables\MaterialsOnFirms;
class Post extends Firm{
    public static function tableName() {
        return 'firmPost';
    }
    public function rules(){
        $rVal= parent::rules();
        $rVal[]=[['materials'],'string'];
        $rVal[]=[['materials'],'default','value'=>'{}'];
        $rVal[]=[['curency_type'],'string','length'=>[3,3]];
        return $rVal;
    }
    public function getMaterialParams(){
        return $this->hasMany(MaterialsOnFirms::className(), ['firm_id'=>'firm_id']);
    }
}
