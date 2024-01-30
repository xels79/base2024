<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use Yii;
use yii\helpers\Html;
use yii\base\Widget;
use yii\helpers\VarDumper;
/**
 * Description of BaseWidget
 *
 * @author Александр
 */
class BaseWidget extends Widget{
    public $options=[];
    public $containerTag='div';
    public $template=false;
    public $log=false;
    protected $content;
    
    public function init(){
        parent::init();
        $this->options=array_merge([
            'id'=>$this->id
        ], $this->options);
        $this->content='';
    }
    protected function renderByTemplate(){
            if (!$this->template){
                $rVal='';
                if (is_array($this->content)){
                    foreach ($this->content as $value)
                        $rVal+=$value;
                    return $rVal;            
                }else{
                    return $this->content;
                }
        }else{
            return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) {
                $name = $matches[1];
                if($this->log) Yii::trace('renderByTemplate : "'.$name. '"','BaseWidget');
                if (isset($this->content[$name])){
                        if($this->log) Yii::trace('value : "'.VarDumper::dumpAsString($this->content[$name]). '"','BaseWidget');
                        return $this->content[$name];
                    }
                else {
                    if($this->log) Yii::trace('value: "empty"','BaseWidget');

                    return '';
                }
            }, $this->template);
        }
    }
    protected function getCounter(){
        return parent::$counter;
    }
    protected function getAutoIdPrefix(){
        return parent::$autoIdPrefix;
    }

    public function run(){       
        if($this->log) Yii::trace('run','BaseWidget');
        if (is_array($this->content)){
            $rVal= Html::tag($this->containerTag,$this->renderByTemplate(),  $this->options);;
        }else
            $rVal= Html::tag($this->containerTag,$this->content,  $this->options);
        if($this->log) Yii::trace('ret: '.$rVal,'BaseWidget');
        return $rVal;
    }
}
