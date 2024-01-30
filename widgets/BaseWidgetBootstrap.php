<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use app\widgets\BaseWidget;
/**
 * Description of BaseWidgetBootstrap
 *
 * @author Александр
 */
class BaseWidgetBootstrap extends BaseWidget {
    const color_default='default';
    const color_success='success';
    const color_info='info';
    const color_warning='warning';
    const color_danger='danger';
    
    public $color='default';
    public $tooltip=false;
    public function run(){
        if ($this->tooltip&&isset($this->options['title'])){
            if (!isset($this->options['data-toggle'])){
                $this->options['data-toggle']='tooltip';
            }
        }
        return parent::run();
    }
}
