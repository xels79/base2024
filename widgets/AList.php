<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Description of AList
 *
 * @author Александер
 */
class AList extends BaseWidget{
    public $itemOptions=[];
    public $items=[];
    
    private function renderBadge($it){
        $opt=['class'=>'badge'];
        if (isset($it['badgeOptions'])){
            if (isset($it['badgeOptions']['class'])){
                Html::addCssClass($opt,$it['badgeOptions']['class']);
                unset($it['badgeOptions']['class']);
            }
            $opt=array_merge($opt,$it['badgeOptions']);
        }
        return isset($it['badge'])?Html::tag('span',$it['badge'],$opt):'';
    }
    private function renderItem(&$it){
        $opt=array_merge(['href'=>'#'],$this->itemOptions);
        if (is_array($it)){
            if (isset($it['url'])){
                if (is_array($it['url']))
                    $opt['href']=Url::to($it['url']);
                else
                    $opt['href']=$it['url'];
            }
            if (isset($it['options'])){
                if (isset($it['options']['class'])){
                    Html::addCssClass($opt,$it['options']['class']);
                    unset($it['options']['class']);
                }
                $opt=array_merge($opt,$it['options']);
            }
            return Html::tag('a',(isset($it['label'])?$it['label']:'').$this->renderBadge($it),$opt);
        }else{
            return Html::tag('a',$it,$opt);
        }
    }
    private function renderItems(){
        foreach ($this->items as $it){
            $this->content.=$this->renderItem($it);
        }
    }
    public function run(){
        $this->renderItems();
        return parent::run();
    }
}
