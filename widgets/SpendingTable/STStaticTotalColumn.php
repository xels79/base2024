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
class STStaticTotalColumn extends DataColumn{
    public $total=0;
    protected function renderFooterCellContent()
    {
        return $this->grid->formatter->format($this->total, $this->format);
    }
    /**
     * Renders the footer cell.
     */
    public function renderFooterCell()
    {
        return Html::tag('td', $this->renderFooterCellContent(), array_merge($this->footerOptions,[
            'data-tabs-total'=>$this->attribute
        ]));
    }

}
