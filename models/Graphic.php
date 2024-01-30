<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\base\Model;

/**
 * Description of Graphic
 *
 * @author Алесандр
 */
class Graphic extends Model{
    public $year=null;
    public $columnType='column';
    private $_formName='';
    private $_setDefaultYear=false;
    public function init(){
        parent::init();
        if ($this->_setDefaultYear && !$year){
            $this->year=$this->getCurrenntYear();
        }
    }
    public function attributeLabels()
    {
        return [
            'year' => 'Год',
        ];
    }
    public function rules()
    {
        return [
            [['columnType'],'string'],
            [['year'], 'integer'],
            [['columnType'],'default','value'=>'column'],
            [['year'],'default','value'=>0],
            [['year'],function ($attribute, $params, $validator){
                if ($this->$attribute>0){
                    if ($this->$attribute<2020){
                        $validator->addError($this, $attribute, 'Значение {value}г. недопустимо для {attribute}.');
                    }elseif($this->$attribute>(int)(new \DateTime('now'))->format('Y')){
                        $validator->addError($this, $attribute, 'Значение {value}г. недопустимо для {attribute}.');
                    }
                }
            }]
        ];
    }
    public function getCurrenntYear(){
        return (int)(new \DateTime('now'))->format('Y');
    }
    public function getYearList(){
        $last=(int)(new \DateTime('now'))->format('Y');
        $rVal=[];
        for ($i=2020;$i<=$last;$i++){
            $rVal[$i]=$i.'г';
        }
        return $rVal;
    }
    public function getColumnTypesList(){
        return [
            'column'=>'Колонки',
            'line'=>'Линии',
            'stepLine'=>'Линии шаг',
            'spline'=>'Кривые',
            'area'=>'Линии с заливкой',
            'splineArea'=>'Кривые с заливкой'
        ];
    }
    public function formName ( ){
        return $this->_formName?$this->_formName:parent::formName();
    }
    public function setSetDefaultYear($val){
        if ($val) 
            $this->_setDefaultYear=true;
        else
            $this->_setDefaultYear=false;
    }
    public function setFormName($val){
        $this->_formName=$val;
    }
}
