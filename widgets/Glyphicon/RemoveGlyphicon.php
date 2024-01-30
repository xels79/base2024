<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\Glyphicon;
use Yii;
use yii\helpers\Html;
use app\widgets\BaseWidget;
/**
 * Description of AddGlyphicon
 *
 * @author Александр
 */
class RemoveGlyphicon  extends BaseWidget{
    public function init(){
        parent::init();
        $this->containerTag='span';
        Html::addCssClass($this->options,'glyphicon glyphicon-remove-sign');
    }
}
