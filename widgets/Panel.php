<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use app\widgets\BaseWidgetBootstrap;
use yii\helpers\Html;
/**
 * Description of Panel
 *
 * @author Александр
 */
class Panel extends BaseWidgetBootstrap{
    public $panelContent=false;
    public $panelHeader=false;
    public $panelFooter=false;
    private $isHeaderRendered=false;
    public $isEcho=false;
    public function init(){
        parent::init();
        Html::addCssClass($this->options,'panel panel-'.$this->color);
    }
    protected function renderElement($cnt,$class){
        if (is_array($cnt)){
            if (!isset($cnt['options'])) $cnt['options']=[];
            Html::addCssClass($cnt['options'],$class);
            return Html::tag('div',isset($cnt['content'])?$cnt['content']:'',$cnt['options']);
        }elseif($cnt){
            return Html::tag('div',$cnt,['class'=>$class]);
        }else{
            return '';
        }
    }
    protected function renderHeader(){
        $rVal='';
        if (!$this->isHeaderRendered){
            $rVal=$this->panelHeader?$this->renderElement($this->panelHeader, 'panel-heading'):'';
            $this->isHeaderRendered=true;
        }
        $this->content.=$rVal;
    }
    public static function begin($config=[]){
        $rVal=parent::begin($config);
        ob_start();
        return $rVal;
    }
    public static function end(){
        if (!empty(static::$stack)) {
            $widget = array_pop(static::$stack);
            if (get_class($widget) === get_called_class()) {
                $widget->panelContent=ob_get_clean();
                echo $widget->run();
                return $widget;
            } else {
                throw new \yii\base\InvalidCallException('Expecting end() of ' . get_class($widget) . ', found ' . get_called_class());
            }
        } else {
            throw new \yii\base\InvalidCallException('Unexpected ' . get_called_class() . '::end() call. A matching begin() is not found.');
        }    }

    public function run(){
        $this->renderHeader();
        $this->content.=$this->panelContent?$this->renderElement($this->panelContent, 'panel-body'):$this->renderElement('', 'panel-body');
        $this->content.=$this->panelFooter?$this->renderElement($this->panelFooter, 'panel-footer'):'';
        return parent::run();
    }
}
