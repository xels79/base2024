<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;
use yii\grid\DataColumn;
/**
 * Description of SummColumn
 *
 * @author Алесандр
 */
class TotalColumn extends DataColumn{
//    private $total = 0;
    public $total=[];
//    public function getDataCellValue($model, $key, $index)
//    {
//        $value = parent::getDataCellValue($model, $key, $index);
//        $this->total += $value;
//        return $value;
//    }

    protected function renderFooterCellContent()
    {
        if (array_key_exists($this->attribute, $this->total)){
            return $this->grid->formatter->format($this->total[$this->attribute], $this->format);
            //return $this->grid->formatter->format($this->total, $this->format);
        }else{
            return '';
        }
    }
}
