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
use app\models\admin\Contact;
class ContactZak extends Contact{
    public static function tableName() {
        return 'contactZak';
    }
    public function getFirmName(){
        $rVal='Не найден';
        if ($query=Zak::findOne((int)$this->firm_id)){
            return $query->mainName;
        }
        return $rVal;
    }
}
