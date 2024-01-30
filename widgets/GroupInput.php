<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use Yii;
use app\widgets\BaseWidget;
use yii\helpers\Html;

/**
 * Description of bootstrapField
 *
 * @author Александр
 */
class GroupInput extends BaseWidget{
    public $addon=false;
    public $button=false;
    public $value;
    public $inputOptions=[];
    public $inputName='';
  
    public function init(){
        parent::init();
        if (isset($this->inputOptions['class']))
            $this->inputOptions['class'].=' form-control';
        else
            $this->inputOptions['class']='form-control';
        if (isset($this->options['class']))
            $this->options['class']='input-group '.$this->options['class'];
        else
            $this->options['class']='input-group';
        if ($this->addon){
            if (!isset($this->addon['options']))
                $this->addon['options']=[];
            if (isset($this->addon['options']['class']))
                $this->addon['options']['class'].=' input-group-addon';
            else
                $this->addon['options']['class']='input-group-addon';
            if (!isset($this->addon['content']))
                $this->addon['content']='@';
        }
        if ($this->button){
            if (!isset($this->button['options']))
                $this->button['options']=[];
            if (!isset($this->button['options']['class']))
//                $this->button['options']['class'].='btn btn-default';
//            else
                $this->button['options']['class']='btn btn-default';
            if (!isset($this->button['content']))
                $this->button['content']='button';
        }
        $this->content=[];
        if (!$this->template) $this->template='{addon}{input}{button}';
    }
    private function DrawAddOn(){
        if($this->log) Yii::trace('addon:"'.$this->addon['content'].'"','GroupInput');
        $this->content['addon']=HTML::tag('span',$this->addon['content'],$this->addon['options']);
    }
    private function DrawButton(){
        if($this->log) Yii::trace ('button "'. $this->button['content'].'"','GroupInput');
        $this->content['button']=HTML::tag('span',
                HTML::button($this->button['content'],  $this->button['options']),
                ['class'=>'input-group-btn']
            );
    }
    private function DrawInput(){
        if($this->log) Yii::trace ('input "'.$this->value .'"','GroupInput');
        $this->content['input']=HTML::input('text',$this->inputName,$this->value,$this->inputOptions);
    }
    private function DrawHelpBlock(){
        $this->content['help']=HTML::tag('div','',['class'=>'help-block']);
    }

    public function run(){
         if($this->log) Yii::trace('run template:"'.$this->template.'"','GroupInput');
        if ($this->addon) $this->DrawAddOn ();
        if ($this->button) $this->DrawButton ();
        $this->DrawInput();
        $this->DrawHelpBlock();
        return parent::run();
    }
}
