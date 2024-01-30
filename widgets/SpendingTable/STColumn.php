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
use yii\helpers\Html;
use yii\grid\DataColumn;
class STColumn extends DataColumn{
    public function init(){
        parent::init();
        $this->contentOptions=function ($model, $key, $index, $column){
            return [
                'data'=>[
                    'column-name'=>$column->attribute
                ]
            ];
        };
    }
    protected function renderDataCellContent ( $model, $key, $index )
    {
        return Html::input('text',null,$model[$this->attribute],[
            'role'=>'edit-cell',
            'data-type'=>$this->format,
            'class'=>'st-edit'
        ]);
    }
}
