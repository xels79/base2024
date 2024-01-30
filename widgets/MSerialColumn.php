<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\grid\SerialColumn;
use yii\helpers\Html;
/**
 * Description of MSerialColumn
 *
 * @author Александр
 */
class MSerialColumn extends SerialColumn{
    protected function renderDataCellContent($model, $key, $index){
        $val=parent::renderDataCellContent($model, $key, $index);
        return Html::tag('span',Html::tag('a',$val,['name'=>$key]),['class'=>'GDA']);
    }
}
