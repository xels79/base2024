<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets\SpendingTable;

/**
 * Description of SpendingTable
 *
 * @author Алесандр
 */
use Yii;
use yii\helpers\Html;
use yii\base\Widget;
use yii\helpers\VarDumper;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use app\widgets\SpendingTable\STColumn;
use app\widgets\SpendingTable\STTotalColumn;
use app\widgets\SpendingTable\STControlColumn;
use app\widgets\SpendingTable\assets\SpendingTableAsset;

class SpendingTable extends GridView{
    public $options=[];
    public $modelName='';
    public $keyColumnName='id';
    public $dateColumnName='date';
    public $dataProviderOptions=[];
    public $date='';
    public $columnsTypes=[];
    public $action='';
    public $actionAdd='';
    public $actionRemove='';
    public $jsVarName='';
    private $formName='';
    private $_dateFrom;
    private $_dateTo;
    private const defaultSpends=[
        'Ветошь',
        'Клише',
        'Инжиниринг',
        'Канц товары',
        'Ладинг',
        'Менделеев',
        'Мусор',
        'Серикол',
        'Топ-пресс',
        'Эзапринт',
        'Экспресс сервис',
    ];
    public function init(){
        SpendingTableAsset::register($this->view);
        if (!$this->modelName){
            throw new InvalidArgumentException('SpendingTable::init() - Должен быть указан класс модели "modelName"');
        }
        if (!$this->date){
            throw new InvalidArgumentException('SpendingTable::init() - Должна быть указана дата "date"');
        }
        if (!$this->action){
            throw new InvalidArgumentException('SpendingTable::init() - Должн быть указан параметр "action"');
        }
        $fr=new \DateTime((new \DateTime($this->date))->format('Y').'-'.(new \DateTime($this->date))->format('M').'-01');
        $fn = new \DateTime($fr->format('Y-m-d'));
        $fn->add(new \DateInterval('P1M'));
        $this->_dateFrom=$fr->format('Y-m-d');
        $this->_dateTo=$fn->format('Y-m-d');
        $this->prepareDataProvider();
        $this->columns=$this->prepareColums();
        $this->showFooter=true;
        parent::init();
    }
    private function prepareColums(){
        $columns=[['class' => \yii\grid\SerialColumn::className()]];
        try{
            $model=new $this->modelName;
        }catch(\ErrorException $ex){
            throw new InvalidConfigException('SpendingTable::init()::prepareColums() : '.$ex-getMessage());
        }
        $this->formName=$model->formName();
        $numFormats=['integer', 'currency', 'decimal', 'duration'];
        $total=[];
        foreach ($this->columnsTypes as $key=>$format){
            if (in_array($format, $numFormats)){
                $total[$key]=$this->computeSumm($key);
            }
        }
        //$total=$this->computeSumm('coast');
        foreach ($model->attributes() as $attr){
            $col=[
                'class'=>STColumn::className(),
                'attribute'=>$attr,
                'format'=>'text'
            ];
            if ($attr!==$this->keyColumnName && $attr!==$this->dateColumnName){
                if (array_key_exists($attr, $this->columnsTypes)){
                    if (in_array($this->columnsTypes[$attr], $numFormats))
                    {
                        $col['class']=STTotalColumn::className();
                        $col['total']=$total[$attr];
                        if ($attr==='coast'){
                            $col['footerOptions']=['class'=>'st-spend-in-monthtd'];
                        }
                        $col['month']=(new \DateTime($this->date))->format('M');
                    }
                    $col['format']=$this->columnsTypes[$attr];
                }
                $columns[]=$col;
            }
        }
        $columns[]=['class' => STControlColumn::className()];
        return $columns;
    }
    private function computeSumm($colName)
    {
        $query=null;
        try{
            $query=$this->modelName::find();
        }catch(\ErrorException $ex){
            throw new InvalidConfigException('SpendingTable::init()::computeSumm() : '.$ex-getMessage());
        }
        $query->where(['>=', $this->dateColumnName, $this->_dateFrom])
              ->andWhere([ '<', $this->dateColumnName, $this->_dateTo]);
        $query->select('SUM(`'.$colName.'`) as total');
        $answer=$query->asArray()->all();
        if (count($answer)){
            return $answer[0]['total'];
        }else{
            return 0;
        }
    }
    private function registerScript(){
        $this->view->registerJs('new stones.widgets.SpendingTable({'
                . 'widgetId:"' . 'ST'.$this->id . '",'
                . 'action:"'.$this->action.'",'
                . 'keyColumnName:"'.$this->keyColumnName.'",'
                . 'dataFormName:"'.$this->formName.'",'
                . 'actionAdd:"'.$this->actionAdd.'",'
                . 'actionRemove:"'.$this->actionRemove.'",'
                . 'date:"'.$this->_dateFrom.'",'
                . ($this->jsVarName?('toUpdateControllerVarName:"'.$this->jsVarName.'"'):'')
                . '});',yii\web\View::POS_READY, $key = 'STJs'.$this->id);
    }
    private function checkDefault(){
        $query=null;
        try{
            $query=$this->modelName::find();
        }catch(\ErrorException $ex){
            throw new InvalidConfigException('SpendingTable::init()::prepareDataProvider() : '.$ex-getMessage());
        }
        $cnt = $query->where(['>=', $this->dateColumnName, $this->_dateFrom])
              ->andWhere([ '<', $this->dateColumnName, $this->_dateTo])
              ->count();
        $query=$this->modelName::find();
        $query->where(['>=', $this->dateColumnName, $this->_dateFrom])
              ->andWhere([ '<', $this->dateColumnName, $this->_dateTo])
              ->andWhere(['name'=>self::defaultSpends])
              ->select('name');
        $answ=$query->asArray()->all();
        $tmp= array_diff(self::defaultSpends, ArrayHelper::getColumn($answ, 'name'));
        if (count($tmp) && !$cnt){
            foreach ($tmp as $name){
                $model=new $this->modelName();
                $model->date=$this->_dateFrom;
                $model->name=$name;
                $model->save();
            }
        }
    }
    private function prepareDataProvider(){
        $this->checkDefault();
        $query=null;
        try{
            $query=$this->modelName::find();
        }catch(\ErrorException $ex){
            throw new InvalidConfigException('SpendingTable::init()::prepareDataProvider() : '.$ex-getMessage());
        }
        $query->where(['>=', $this->dateColumnName, $this->_dateFrom])
              ->andWhere([ '<', $this->dateColumnName, $this->_dateTo]);
        $options= array_merge([
            'pagination' => [
                'pageParam'=>'stPage'.$this->id,
                'pageSize' => 20
            ],
        ],$this->dataProviderOptions);
        $options['query']=$query;
        $this->dataProvider=new ActiveDataProvider($options);
    }
    public function run(){
        $this->registerScript();
        $optBack=$this->options;
        $this->options=['id'=>$optBack['id']];
        ob_start();
        Pjax::begin([
            'id'=>'stPjax'.$this->id,
            'timeout'=>7000,
        ]);
        parent::run();
        Pjax::end();
        $gW=ob_get_clean();
        $this->options=$optBack;
        $this->options['id']='ST'.$this->id;
        $optons= array_merge([
            'class'=>'st st-spends',
            'data-date'=>$this->date
        ],$this->options);
        return Html::tag('div',$gW,$optons);
    }
}
