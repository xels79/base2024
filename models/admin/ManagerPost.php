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
 */
use app\models\admin\Manager;
class ManagerPost extends Manager{
    public static function tableName() {
        return 'managerPost';
    }
}
