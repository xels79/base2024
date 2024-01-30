<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\bootstrap\Modal;
use yii\base\Widget;
use yii\base\UnknownPropertyException;
use yii\base\InvalidConfigException;

/**
 * Description of AddresEdit
 *
 * @author Александр
 */
class AddresEdit extends Widget{
    public $model;
    public $attribute;
    
    public function init(){
        parent::init();
        if (!$this->model){
            throw new InvalidConfigException('Не задана модель!');
        }
        if (!$this->attribute){
            throw new InvalidConfigException('Не задан атрибут модели!');
        }        
    }
    public function run(){
        if ($this->model[$this->attribute]){
            echo $this->model[$this->attribute];
        }else{
            Modal::begin([
                    'header' => '<h2>Hello world</h2>',
    'toggleButton' => ['label' => 'click me'],
            ]);
            echo 'kuku';
            Modal::end();
        }
    }
}
