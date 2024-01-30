<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\helpers\Html;
use yii\i18n\Formatter;
/**
 * Description of FreeSpace
 *
 * @author Александр
 */
class FreeSpace extends BaseWidget{
    public $folders=[];
    public $itemOptions=[];
    public $minSpace=3; //В гигобайтах
    public function init(){
        parent::init();
        $this->minSpace*=1073741824;
        $this->containerTag='ul';
        Html::addCssClass($this->options, 'list-inline');
    }
    public function preFolders(){
        foreach ($this->folders as $key=>$el){
            if (isset($el['path'])){
                $this->folders[$key]['free']=file_exists($el['path'])?disk_free_space($el['path']):0;
            }
        }
    }
    public function renderFolders(){
//        $butt=Html::button('button',Html::tag('span',null,['class'=>'glyphicon glyphicon-hdd','title'=>'Дисковое пространство']),[
//            
//        ]);
        
        $this->content.=Html::tag('li',Html::tag('span',null,[
            'class'=>'glyphicon glyphicon-hdd',
            ]).Html::tag('span',null,[
            'class'=>'glyphicon glyphicon-chevron-right',
            'data-toggle'=>"mCollapse",
            'data-target'=>'#'.$this->id.'_info'
            ]),['class'=>'head','title'=>'Дисковое пространство']);
        //$this->content.Html::beginTag('div',['id'=>'demo','class'=>'collapse']);
        $this->content.=Html::beginTag('li');
        $this->content.=Html::beginTag('ul',['id'=>$this->id.'_info','class'=>'list-inline mCollapse']);
        foreach ($this->folders as $el){
            if (isset($el['label'])){
                $label=$el['label'];
            }else{
                $label=isset($el['path'])?$el['path']:false;
            }
            $low=$el['free']<$this->minSpace;
            $bageOpt=[
                'class'=>'badge'.($low?' badgeAlert':' badgeOk')
            ];
            if ($low) $bageOpt['title']='Мало свободного места';
            $free=isset($el['free'])?Html::tag('span',\yii::$app->formatter->asShortSize($el['free'],2),$bageOpt):'';
            $this->content.=Html::tag('li',$label.$free,$this->itemOptions);
        }
        $this->content.=Html::endTag('ul');
        $this->content.=Html::endTag('li');
    }

    public function run(){
        $this->preFolders();
//        $this->content.=\yii\helpers\VarDumper::dumpAsString($this->folders,10,true);
        $this->renderFolders();
        return parent::run();
    }
}
