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
class Zak extends Firm{
    public static function tableName() {
        return 'firmZak';
    }
    public function rules(){
        $rVal= parent::rules();
        $rVal[]=[['blackList'],'string'];
        $rVal[]=[['blackList'],'default','value'=>null];
        return $rVal;
    }
    public function attributeLabels() {
        return \yii\helpers\ArrayHelper::merge(parent::attributeLabels(),[
            'blackList'=>'Чёрный список'
        ]);
    }
}
