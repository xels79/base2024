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
class AddressPost extends Address{
    public static function tableName() {
        return 'addressPost';
    }
}
