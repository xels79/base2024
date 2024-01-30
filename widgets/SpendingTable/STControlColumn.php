<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\SpendingTable;

/**
 * Description of STColumn
 *
 * @author Алесандр
 */
use yii\grid\DataColumn;
use yii\helpers\Html;

class STControlColumn extends DataColumn{
    protected function renderDataCellContent ( $model, $key, $index )
    {
        return Html::a(Html::tag('span','',[
            'class'=>'glyphicon glyphicon-trash'
        ]),'#',[
            'class'=>'btn',
            'role'=>'st-remove'
        ]);
    }
    /**
     * Renders the footer cell.
     */
    public function renderFooterCellContent()
    {
        return Html::a(Html::tag('span','',[
            'class'=>'glyphicon glyphicon-plus'
        ]),'#',[
            'class'=>'btn',
            'role'=>'st-add'
        ]);
    }

}
