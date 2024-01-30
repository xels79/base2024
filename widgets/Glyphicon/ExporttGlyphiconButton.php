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
class ExporttGlyphiconButton   extends BaseWidgetBootstrap{
    public $title='Удалить';
    //public $color='success'; //bootstrap class
    public $size='sm';         //bootstrap size
    public function init(){
        $this->color=self::color_danger;
        $this->containerTag='button';
        parent::init();
        if (!array_key_exists('title', $this->options))
                $this->options['title']=$this->title;
        Html::addCssClass($this->options,['btn','btn-'.$this->color,'btn-'.$this->size]);
    }
    public function run(){
        $this->content=ExporttGlyphicon::widget();
        return parent::run();
    }
}
