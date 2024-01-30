<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\Glyphicon;
use Yii;
use yii\helpers\Html;
use app\widgets\BaseWidgetBootstrap;

/**
 * Description of AddGlyphiconButton
 *
 * @author Александр
 */
class GroupGlyphiconButton   extends BaseWidgetBootstrap{
    //public $color='success'; //bootstrap class
    public $size='sm';         //bootstrap size
    public $changeButton=false;
    public $removeButton=false;
    public $tableId=-1;
    public function init(){
        parent::init();
        if ($this->changeButton && !is_array($this->changeButton)) $this->changeButton=[];
        if ($this->removeButton && !is_array($this->removeButton)) $this->removeButton=[];
        Html::addCssClass($this->options,'btn-group');
        if (!$this->template) $this->template='{change}{remove}';
        if ($this->tableId>=0){
            if (is_array($this->changeButton)) $this->changeButton['tableId']=$this->tableId;
            $this->removeButton['tableId']=$this->tableId;
        }
        if (is_array($this->changeButton)) $this->changeButton['role']='change';
        $this->removeButton['role']='remove';
    }
    protected function renderChange(){
        $this->content['change']=is_array($this->changeButton)?ChangeGlyphiconButton::widget(['size'=>$this->size,'options'=>$this->changeButton,'tooltip'=>$this->tooltip]):'';
    }
    protected function renderRemove(){
        $this->content['remove']=is_array($this->removeButton)?RemoveGlyphiconButton::widget(['size'=>$this->size,'options'=>$this->removeButton,'tooltip'=>$this->tooltip]):'';
    }
    public function run(){
        $this->renderChange();
        $this->renderRemove();
        return parent::run();
    }
}
