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

class STTotalColumn extends STColumn{
    public $total=0;
    public $month='';
    protected function renderFooterCellContent()
    {
        return $this->grid->formatter->format($this->total, $this->format);
    }
    /**
     * Renders the footer cell.
     */
    public function renderFooterCell()
    {
        $key=$this->month?"data-tabs-{$this->month}-total":"data-tabs-total";
        $opt=[];
        $opt[$key]=$this->attribute;
        return Html::tag('td', Html::tag('span',$this->renderFooterCellContent()), array_merge($this->footerOptions,$opt));
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
