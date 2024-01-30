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
use app\widgets\SpendingTable\assets\SpendingTableAsset;
use app\models\Zarplata;
use app\models\StaticSpends;
use yii\widgets\DetailView;
use app\widgets\SpendingToYourself\SpendingToYourself;

class MainSpendingTable extends Widget{
    public $options=['class'=>'st st-main'];
    public $date='';
    public $action='';
    public $jsVarName='';
    private $_dateFrom;
    private $dataProvider;
    public function init(){
        parent::init();
        SpendingTableAsset::register($this->view);
        if (!$this->date){
            throw new InvalidArgumentException('SpendingTable::init() - Должна быть указана дата "date"');
        }
        if (!$this->action){
            throw new InvalidArgumentException('SpendingTable::init() - Должн быть указан параметр "action"');
        }
        $this->_dateFrom=new \DateTime((new \DateTime($this->date))->format('Y').'-'.(new \DateTime($this->date))->format('M').'-01');
        if (!$this->jsVarName) $this->jsVarName='stJs'.$this->id;
    }
    private function registerScript($formName,$id){
        $this->view->registerJs('stones.widgets.'.$this->jsVarName.'=new stones.widgets.MainSpendingTable({'
                . 'widgetId:"' . $this->id . '",'
                . 'action:"'.$this->action.'",'
                . 'keyColumnName:"id",'
                . 'keyColumnValue:"'.$id.'",'
                . 'dataFormName:"'.$formName.'",'
                . '});',yii\web\View::POS_READY, $key = 'STJs'.$this->id);
    }
    public function run(){
        if (!$model=StaticSpends::findOne(['date'=>$this->_dateFrom->format('Y-m-d')])){
            $model=new StaticSpends();
            $pred=(new \DateTime($this->_dateFrom->format('Y-m-d')))->sub(new \DateInterval('P1M'));
            if ($model2=StaticSpends::findOne(['date'=>$pred->format('Y-m-d')])){
                $model->arsiRent=$model2->arsiRent;
                $model->officeRental=$model2->officeRental;
                $model->nalogZPAST=$model2->nalogZPAST;
                $model->nalogZPAsterio=$model2->nalogZPAsterio;
                $model->cuttingIntFFighting=$mode2->cuttingIntFFighting;
                $model->bank1=$model2->bank1;
                $model->bank2=$model2->bank2;
                $model->bank3=$model2->bank3;
                $model->bank4=$model2->bank4;
            }
            $model->date=$this->_dateFrom->format('Y-m-d');
            $model->save();
        }
        $this->registerScript($model->formName(),$model->id);
        $rVal=DetailView::widget([
            'model' => $model,
            'template'=>function($attribute, $index, $widget){
                $stopArr=[
                    'date'=>[],
                    'percent'=>['class'=>'st-percent'],
                    'zp'=>['class'=>'st-zp'],
                    'spends'=>['class'=>'st-spend-in-month'],
                    'total'=>['class'=>'st-total-month']
                ];
                $label=$attribute['label'];
                $trOpt=[];
                $tdOpt=[];
                if (array_key_exists($attribute['attribute'],$stopArr)){
                    $val=Html::tag('span',Yii::$app->formatter->format($attribute['value'],$attribute['format']));
                    $trOpt=$stopArr[$attribute['attribute']];
                    $m=(new \DateTime($widget->model->date))->format('M');
                    if ($attribute['attribute']==='spends'){
                        $tdOpt["data-tabs-$m-total"]='coast';
                    }
                    if ($attribute['attribute']==='total'){
                        $tdOpt["data-tabs-$m-total"]='total';
                    }
                }else{
                    
                    $val=Html::input('text',null,$attribute['value'],[
                        'class'=>'st-edit',
                        'role'=>'st-'.$this->id.'-save',
                        'data-column-name'=>$attribute['attribute']
                    ]);
                }
                return Html::tag('tr',"<th>$label</th>".Html::tag('td',$val,$tdOpt),$trOpt);
            },
            'attributes' => [
                'zp:currency',
                'percent:currency',
                'spends:currency',
                'officeRental:currency',
                'arsiRent:currency',
                'cuttingIntFFighting:currency',
                'utilityBills:currency',
                'nalogZPAsterio:currency',
                'nalogZPAST:currency',
                'ndsAsterio:currency',
                'sixPercentAST:currency',
                'bank1:currency',
                'bank2:currency',
                'bank3:currency',
                'bank4:currency',
                'forCars:currency',
                'cashWithdrawal:currencyl',
                'rezerved1:currency',
                'rezerved2:currency',
                'total:currency'
            ],
        ]);
        return Html::tag('div',$rVal.SpendingToYourself::widget([
            'date'=>$this->date,
            'modelName'=>'app\models\Spendingtoyourselftable',
            'dataColumnName'=>'summ',
            'actionUrl'=> \yii\helpers\Url::to(['/site/saveyuospend'])
        ]),array_merge($this->options,[
            'id'=>$this->id,
            'data-date'=>$this->date
        ]));
    }
}
