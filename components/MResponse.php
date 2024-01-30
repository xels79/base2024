<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MRequest
 *
 * @author Александер
 */
namespace app\components;
use yii\web\Response;
class MResponse extends Response{
    public function init(){
        parent::init();
        self::$httpStatuses[403]='Доступ запрещён';
        self::$httpStatuses[404]='Страничка не найдена';
    }
}
