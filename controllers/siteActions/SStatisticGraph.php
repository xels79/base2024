<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\siteActions;

/**
 * Description of SStatisticGraph
 *
 * @author Александр
 */
use Yii;
use yii\helpers\ArrayHelper;
use app\models\zakaz\Zakaz;
use yii\data\ArrayDataProvider;
use app\models\Zarplata;
use app\models\Spends;
use app\models\StaticSpends;

class SStatisticGraph extends SSEditSpends {
    
    private function proceedZakazSumm($year='now'){
        $rVal = [];
        for ($i = 1; $i < 13; $i++) {
            $fr = new \DateTime($year);
            $fr = new \DateTime($fr->format('Y') . '-' . ($i < 10 ? ('0' . $i) : $i) . '-01');
            $fn = new \DateTime($fr->format('Y-m-d'));
            $fn->add(new \DateInterval('P1M'));
            $fn->sub(new \DateInterval('P1D'));
            $this->periud = 0;
            $this->periud_from = $fr->format('Y-m-d');
            $this->periud_to = $fn->format('Y-m-d');
            if (Yii::$app->request->isPost) {
                $this->managers = (array) Yii::$app->request->post('managers', [
                            (int) Yii::$app->user->identity->id => 'on']);
            } else {
                $this->managers = (array) Yii::$app->request->get('managers', [
                            (int) Yii::$app->user->identity->id => 'on']);
            }

            $this->prepareManager_Exists();
            $works = $this->prepareWorks();
            $worksIds = ArrayHelper::getColumn($works, 'id');
            $query = $this->newQuery($works, $worksIds);
            $rVal[] = [
                'query' => $query,
                'from'  => $fr->format('Y-m-d'),
                'to'    => $fn->format('Y-m-d')
            ];
        }
        return $rVal;
    }
    public function run($periud = -1, $periud_from = null, $periud_to = null) {
        $this->controller->layout = 'main_2_withBunner.php';// 'main_2.php';
        if (Yii::$app->request->get('actionSpend')) return $this->runSpands();
        if (Yii::$app->request->isPost && !Yii::$app->request->isPjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (Yii::$app->request->post('mss')){
                $rv= \yii\helpers\Json::decode(\app\widgets\MonthSalarySchedule\MSSchedule::widget([
                    'id'=>Yii::$app->request->post('mss')
                ]));
            }else{
                $rv=[
                    'test'            => $this->proceedZakazSumm(),
                    'total'           => $this->creatTotal(),
                    'managers_exists' => $this->managers_exists,                
                ];
            }
            Yii::debug($rv,'graphics');
            return $rv;
        } else {
            $total2=null;
            $graphicModel=new \app\models\Graphic();
            $y='now';
            if (!$graphicModel->load(Yii::$app->request->post())){
                if ($graphicModel->load(Yii::$app->request->get()) && $graphicModel->validate() && $graphicModel->year){
                    $y=$graphicModel->year.'-01-01';
                }
            }elseif($graphicModel->validate() && $graphicModel->year){
                $y=$graphicModel->year.'-01-01';
            }
            $y2='now';
            $graphicModel2=new \app\models\Graphic(['formName'=>'S3selyer','setDefaultYear'=>true]);
            if (!$graphicModel2->load(Yii::$app->request->post())){
                if ($graphicModel2->load(Yii::$app->request->get()) && $graphicModel2->validate() && $graphicModel2->year){
                    $y2=$graphicModel2->year.'-01-01';
                }
            }elseif($graphicModel2->validate() && $graphicModel2->year){
                $y2=$graphicModel2->year.'-01-01';
            }
            $tmpD=$this->proceedZakazSumm($y);
            $rv=[
                'test'            => $this->proceedZakazSumm($y),
                'periud'          => (int) $this->periud,
                'managers'        => $this->managers,
                'managers_exists' => $this->managers_exists,
                'total'           => $this->creatTotal($y),
                'selectedYear'    => $graphicModel->year,
                'gModel'          => $graphicModel,
                'gModel2'         => $graphicModel2,
                'crossGraph'      => $this->computeCrossYaer($y2),
            ];                
            Yii::debug($rv,'graphics');
            return $this->controller->render('statisticgraph', $rv);
        }
    }

    private function creatTotal($year='now') {
        $rVal = [];
        for ($i = 1; $i < 13; $i++) {
            $fr = new \DateTime($year);
            $fr = new \DateTime($fr->format('Y') . '-' . ($i < 10 ? ('0' . $i) : $i) . '-01');
            $fn = new \DateTime($fr->format('Y-m-d'));
            //$oneDay = new \DateInterval('P1D');
            $fn->add(new \DateInterval('P1M'));
            $fn->sub(new \DateInterval('P1D'));
//            $this->prepareManager( $periud, $fr->format( 'Y-m-d' ), $fn->format( 'Y-m-d' ), [
//                (int) Yii::$app->user->identity->id => 'on'] );
            $this->periud = 0;
            $this->periud_from = $fr->format('Y-m-d');
            $this->periud_to = $fn->format('Y-m-d');
            if (Yii::$app->request->isPost) {
                $this->managers = (array) Yii::$app->request->post('managers', [
                            (int) Yii::$app->user->identity->id => 'on']);
            } else {
                $this->managers = (array) Yii::$app->request->get('managers', [
                            (int) Yii::$app->user->identity->id => 'on']);
            }

            $this->prepareManager_Exists();
            $this->managers = ArrayHelper::map($this->managers_exists, 'id', 'realname');
            $works = $this->prepareWorks();
            $worksIds = ArrayHelper::getColumn($works, 'id');
            //$query = $this->prepareQuery($works, $worksIds)->asArray()->all();
			$query = $this->newQuery($works, $worksIds);
			//echo '<p>'.\yii\helpers\VarDumper::dumpAsString($query,10,true).'</p>';
            //if ( count( $query ) > 1 ) $this->creatAllStatisticTable( $query );
            $rVal[] = [
                'query' => $query,
                'from'  => $fr->format('Y-m-d'),
                'to'    => $fn->format('Y-m-d')
            ];
        }
        return $rVal;
    }
    private function computeCrossYaer($year='now'){
        $tmp=[];$rVal=['items'=>[],'total'=>[]];
        $chkBoxNamePerfix='subTabChkBox';
        $_pjaxId=Yii::$app->request->post('_pjax',Yii::$app->request->get('_pjax'));
        $from=1;    //Первый месяц
        $to=13;     //Последний

        for ($i = $from; $i < $to; $i++) {
            $fr = new \DateTime($year);
            $fr = new \DateTime($fr->format('Y') . '-' . ($i < 10 ? ('0' . $i) : $i) . '-01');
            $fn = new \DateTime($fr->format('Y-m-d'));
            //$oneDay = new \DateInterval('P1D');
            $fn->add(new \DateInterval('P1M'));
            $fn->sub(new \DateInterval('P1D'));
            $chkBoxName=$chkBoxNamePerfix.$fr->format('M');
            $hideOtherMonth=Yii::$app->request->post($chkBoxName,Yii::$app->request->get($chkBoxName,'off'))==='on';
            $tmp[]=$this->computeCrossMonth($fr->format('Y-m-d'), $fn->format('Y-m-d'),$hideOtherMonth);
        }
        $i=$from=1;
        $fr = new \DateTime($year);
        $activeTab=(int)Yii::$app->request->get('Table1TabsTab',0);
        foreach ($tmp as $el){
            $fn = new \DateTime($fr->format('Y') . '-' . ($i < 10 ? ('0' . $i) : $i) . '-01');
            $formId='S3SubTab_Form_'.$fn->format('M');
            $chkBoxName=$chkBoxNamePerfix.$fn->format('M');
            $chkBoxVal=Yii::$app->request->post($chkBoxName,Yii::$app->request->get($chkBoxName,'off'));
            $rVal['items'][]=[
                'active'=>($i-1)===$activeTab,
                'label'=>Yii::$app->formatter->asDate($fn->format('Y-m-d'),'LLL'),
                'content'=>$this->controller->renderFile('@app/views/site/_statisticgraph3SubTab.php',[
                    'dataProvider'=>new ArrayDataProvider([
                        'sort' => [
                            'attributes'=>[
                                'zakazData',
                                'date'=>['default'=>SORT_DESC],
                                'zakaz_id' => ['default'=>SORT_DESC],
                            ],
                        ],
                        'allModels'=>$el['detail'],
                        'pagination' => [
                            'pageSize' => 15,
                            'pageParam'=>'Table1TabsTab'.$fn->format('M').'Page'
                        ],
                    ]),
                    'total'=>$el['total'],
                    'formId'=>$formId,
                    'pjaxId'=>'p_monts_'.$i,
                    'chkBoxName'=>$chkBoxName,
                    "$chkBoxName"=>$chkBoxVal,
                    'tabId'=>$tabId
                ])
            ];
            $i++;
            $rVal['total'][]=$el['total'];
        }
        return $rVal;
    }
    private function addToTotal(&$total, &$values){
        foreach (array_keys($total) as $key){
            $total[$key]+= array_key_exists($key, $values)?$values[$key]:0;
        }
    }
    private function computeCrossMonth($from,$to,$hideOtherMonth){
        $rVal=['total'=>[
            'summaZakaza'=>0,
            'summOplata'=>0,
            'predOlata'=>0,
            'spend'=>0,
            'profit'=>0,
            'totalProfit'=>0,
            'spends'=>StaticSpends::totalByDate($from),
            'cleanProfit'=>0
        ],'detail'=>[]];
        $zSelect=[
            'id','dateofadmission',
            'percent1','percent2','percent3',
            're_print','material_coast_comerc',
            'exec_speed_summ','exec_markup_summ','exec_bonus_summ',
            'exec_delivery_summ','exec_transport_summ','exec_transport2_summ',
            'exec_speed_payment','exec_markup_payment','exec_bonus_payment','exec_delivery_payment',
            'exec_transport_payment','exec_transport2_payment',
            'total_coast','spending','spending2'
        ];
        $curZOplata=\app\models\zakaz\ZakazOplata::find()
                
                ->where(['>=','date',$from])
                ->andWhere(['<=','date',$to])
                ->orderBy(['date' => SORT_ASC,'zakaz_id' => SORT_ASC]);
        if ($hideOtherMonth){
            $curZOplata->leftJoin('zakaz', 'zakaz.id=zakaz_oplata.zakaz_id')
                    ->select('zakaz_oplata.*')
                    ->andWhere(['>=','zakaz.dateofadmission',$from])
                    ->andWhere(['<','zakaz.dateofadmission',$to]);
        }
        $curZOplata=$curZOplata->asArray()->all();
        foreach ($curZOplata as $id=>$el){
            $zakaz = Zakaz::find()
                ->select($zSelect)
                ->with('allMaterials')
                ->with('pods')
                ->andWhere(['id'=>$el['zakaz_id']])
                ->asArray()
                ->one();
            $t=$this->qItemInit($zakaz);
            $this->qItemProceed($t,$zakaz);
            $tmp=\app\models\zakaz\ZakazOplata::find()
                    ->where(['zakaz_id'=>(int)$el['zakaz_id']])
                    ->andWhere(['<','date',$el['date']])
                    ->select('SUM(`summ`) as preOpl')
                    ->having('preOpl>0')
                    ->asArray()
                    ->all();
            $tProfit=(double)$t['total1_profit'];
            $zCoast=(double)$zakaz['total_coast'];
            $pOpl=(double)count($tmp)?$tmp[0]['preOpl']:0;
            $opl=(double)$el['summ'];
            $spend=$zCoast-$tProfit;
            $curProfit=(double)0;
            if ($pOpl+$opl>$spend){
                if ($pOpl>$apend){
                    $curProfit=$opl;
                }else{
                    $curProfit=$opl-$spend;
                }
            }
            $main=[
                'date'=>$el['date'],
                'zakaz_id'=>(int)$el['zakaz_id'],
                'summaZakaza'=>$zCoast,
                'summOplata'=>$opl,
                'predOlata'=>$pOpl,
                'spend'=>$spend,
                'profit'=>$curProfit,
                'totalProfit'=>$tProfit,
                'zakazData'=>$zakaz['dateofadmission']
            ];
            $this->addToTotal($rVal['total'],$main);
            $rVal['detail'][]=$main;
        }
        $rVal['total']['cleanProfit']=$rVal['total']['profit']-$rVal['total']['spends'];
        return $rVal;
    }
    private function computeOtherSpends($from){
        $dt=new \DateTime($from);
        $rVal= StaticSpends::totalByDate($from);
        return $rVal;
    }
}
