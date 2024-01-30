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
class ShippingPost extends \app\models\BaseActiveRecord{
    public static function tableName() {
        return 'shippingPost';
    }
    
    public function rules(){
        return [
            [['firm_id'],'integer'],
            [['summ1','summ2','summ3','expense_to','expense_from'],'number'],
            [['is_self_transport','is_in_office','is_in_production','is_in_shop'],'boolean']
        ];
    }
    
}
