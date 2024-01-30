<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\siteActions;

use Yii;
use yii\base\Action;
use app\models\tables\Worktypes;
use app\models\zakaz\Zakaz;
use yii\helpers\ArrayHelper;
use app\components\MyHelplers;

/**
 * Description of SStatistic
 *
 * @author Александр
 */
class SStatistic extends Action {

    protected $managers = [];
    protected $managers_exists = null;
    protected $periud_from = null;
    protected $periud_to = null;
    protected $periud = null;
    protected static $_managers_exists1 = null;
    protected static $_managers_exists2 = null;
    protected static $_prepareWorks = null;


    protected function qItemInit(&$el,&$works=null){
		$rVal=[
				'manger_name'=>$el['user']['realname'],
				'ourmanager_id'=>$el['ourmanager_id'],
				'wages'=>$el['user']['wages'],
				'total1'=>0,
				'materials'=>0,
				'msterial_spend'=>0,
				'pod'=>0,
				'pod_profit'=>0,
				'asterion'=>0,
				'asterion_profit'=>0,
				'other_total'=>0,
				'other_spend'=>0,
				'total1_profit'=>0,
				'pod_percent'=>0,
				'asterion_percent'=>0,
				'wages_percent'=>0,
				'other_percent'=>0,
				'w_total'=>0,
				'w_spend'=>0
			];
                if ($works){
                    foreach ($works as $work) {
                            $rVal["w_total" . MyHelplers::translit($work['name'], true)]=0;
                            $rVal['w_total' . MyHelplers::translit($work['name'], true) . '_spend']=0;
                    }
                }
		$rVal['w_other_total']=0;
		$rVal['w_other_spend']=0;
		return $rVal;
	}
	protected function qItemProceed(&$qEl, &$el, &$works=null, &$worksIds=null){
		$qEl['total1']+=(double)$el['total_coast'];
		$pCoast=0;
		$pPayment=0;
		foreach ($el['pods'] as $pel){
			$pCoast+=$pel['coast'];
			$pPayment+= $pel['payment'];
			if ((int)$pel['pod_id']==1){
				$qEl['asterion']+=(double)$pel['coast'];
				$qEl['asterion_profit']+=(double)$pel['coast'] - (double)$pel['payment'];
				$qEl['asterion_percent']+=((double)$pel['coast'] - (double)$pel['payment'])/100*$el['percent1'];
			}
                        if ($works){
                            foreach ($works as $work) {
                                    if ((int)$pel['workType']==(int)$work['id']){
                                            $qEl["w_total" . MyHelplers::translit($work['name'], true)]+=(double)$pel['coast'];
                                            $qEl['w_total' . MyHelplers::translit($work['name'], true) . '_spend']+=(double)$pel['payment'];
                                    }
                            }
                        }
                        if ($worksIds){
                            if (!in_array((int)$pel['workType'],$worksIds)){
                                    $qEl['w_other_total']+=(double)$pel['coast'];
                                    $qEl['w_other_spend']+=(double)$pel['payment'];
                            }
                        }
			if ((int)$pel['pod_id']<>1){
				$qEl['pod']+=(double)$pel['coast'];
				$qEl['pod_profit']+=(double)$pel['coast'] - (double)$pel['payment'];
				$qEl['pod_percent']+=((double)$pel['coast']-(double)$pel['payment'])/100*$el['percent2'];
				$qEl['wages_percent']+=((double)$pel['coast']-(double)$pel['payment'])/100*$el['percent2'];
			}else{
				$qEl['wages_percent']+=((double)$pel['coast']-(double)$pel['payment'])/100*$el['percent1'];
			}

		}
		$addCoastComerc=false;
		foreach($el['allMaterials'] as $elMat){
			if ((int)$elMat['supplierType']==2){
				if ((int)$el['re_print']>0){
					$addCoastComerc=true;
				}
				$qEl['msterial_spend']+=(double)$elMat['count']*(double)$elMat['coast'];
			}
		}
		if ($addCoastComerc){
			$qEl['materials']+=(double)$el['material_coast_comerc'];
		}
		$qEl['other_total']+=(double)$el['exec_speed_summ']
						+(double)$el['exec_markup_summ']
						+(double)$el['exec_bonus_summ']
						+(double)$el['exec_delivery_summ']+(double)$el['exec_transport_summ']+(double)$el['exec_transport2_summ'];
		$qEl['other_spend']+=(double)$el['exec_speed_payment']
						+(double)$el['exec_markup_payment']
						+(double)$el['exec_bonus_payment']
						+(double)$el['exec_delivery_payment']
						+(double)$el['exec_transport_payment']
						+(double)$el['exec_transport2_payment'];
		$qEl['total1_profit']+=(double)$el['total_coast']-(double)$el['spending']-(double)$el['spending2'];
		$qEl['other_percent']+=((double)$el['exec_speed_payment']+(double)$el['exec_markup_payment']+(double)$el['exec_bonus_payment']
            + (double)$el['exec_delivery_payment']+(double)$el['exec_transport_payment']+(double)$el['exec_transport2_payment']-(double)$el['exec_speed_payment']+(double)$el['exec_markup_payment']+(double)$el['exec_bonus_payment']
            + (double)$el['exec_delivery_payment']+(double)$el['exec_transport_payment']+(double)$el['exec_transport2_payment'])/100*(double)$el['percent3'];

		$qEl['w_total']+=(double)$pCoast;
		$qEl['payment']+=(double)$pPayment;

	}
	protected  function queryProceed(&$query, &$works, &$worksIds){
		$query->asArray();
		$dt=$query->all();
		$answ=[];
		$keys=[];
		$cnt=0;
		foreach ($dt as $el){
			if (!isset($keys[(int)$el['user']['id']])){
				$keys[(int)$el['user']['id']]=count($answ);
				$answ[$keys[(int)$el['user']['id']]]=$this->qItemInit($el, $works);
			}
			$this->qItemProceed($answ[$keys[(int)$el['user']['id']]], $el, $works, $worksIds);
			$cnt++;
			//$answ[(int)$el['user']['id']]['element']=$el;
		}
		//$answ['Всего']=$cnt;
                Yii::debug([
                    'periud_from'=>$this->periud_from,
                    'periud_to'=>$this->periud_to,
                    'curInterval'=>$curInterval,
                    'count'=>$cnt,
                    'answ'=>$answ
                ],'newQuery'.++self::$_newQueryCount);
		return $answ;
	}
        private static $_newQueryCount=0;
	protected  function newQuery($works, $worksIds){
        $query = Zakaz::find()
					->with('allMaterials')
					->with('pods')
					->with('user')
//                ->leftJoin('zakaz_materials', 'zakaz.id=zakaz_materials.zakaz_id')
//                ->leftJoin('zakaz_pod', 'zakaz.id=zakaz_pod.zakaz_id')
//                ->leftJoin('tbl_user', 'zakaz.ourmanager_id=tbl_user.id')
//                ->where([
                    //'re_print' => 0])
                ->andWhere(['not', ['stage' => 0]]);
//                ->select($this->statisticSelect($works, $worksIds));
        if (count($this->managers)) {
            $query->andWhere([
                'ourmanager_id' => array_keys($this->managers)]);
        } else {
            $query->andWhere([
                'ourmanager_id' => ArrayHelper::getColumn($this->managers_exists, 'id')]);
        }
        if ($this->periud_from !== null && $this->periud_to !== null) {
            $query->andWhere([
                'and',
                [
                    '>=',
                    'dateofadmission',
                    $this->periud_from],
                [
                    '<=',
                    'dateofadmission',
                    $this->periud_to]]);
            $inend = new \DateTime($this->periud_to);
            $istart = new \DateTime($this->periud_from);
            $diff = $istart->diff($inend);
            $curInterval = $diff->format('%R%f дней');
        } else {
            $curInterval = null;
        }
        //$query->orderBy('ourmanager_id');
        //$query->groupBy('ourmanager_id');
            return $this->queryProceed($query, $works, $worksIds);
	}
    protected function statisticSelect($works, $worksIds): array {
        $select = [
            'tbl_user.realname as manger_name',
            'zakaz.ourmanager_id as ourmanager_id',
			'SUM(DISTINCT zakaz.id) as zCnt',
            'SUM(DISTINCT zakaz.total_coast) as total1',
            'SUM(DISTINCT IF (zakaz_materials.supplierType=2,zakaz.material_coast_comerc,0)) as materials',
            'sum(DISTINCT IF (zakaz_materials.supplierType=2,zakaz_materials.count*zakaz_materials.coast,0)) as msterial_spend',
            'SUM(IF(zakaz_pod.pod_id<>1,zakaz_pod.coast,0)) as pod',
            'SUM(IF(zakaz_pod.pod_id<>1,zakaz_pod.coast-zakaz_pod.payment,0)) as pod_profit',
            'SUM(IF(zakaz_pod.pod_id=1,zakaz_pod.coast,0)) as asterion',
            'SUM(IF(zakaz_pod.pod_id=1,zakaz_pod.coast-zakaz_pod.payment,0)) as asterion_profit',
            'SUM(DISTINCT (zakaz.exec_speed_summ+zakaz.exec_markup_summ+zakaz.exec_bonus_summ+'
            . 'zakaz.exec_delivery_summ+zakaz.exec_transport_summ+zakaz.exec_transport2_summ)) as other_total',
            'SUM(DISTINCT ((zakaz.exec_speed_payment+zakaz.exec_markup_payment+zakaz.exec_bonus_payment+'
            . 'zakaz.exec_delivery_payment+zakaz.exec_transport_payment+zakaz.exec_transport2_payment))) as other_spend',
            'SUM(DISTINCT (zakaz.total_coast-zakaz.spending-zakaz.spending2)) as total1_profit',
//Зп
            'tbl_user.wages as wages',
//Проценты:
            '(SUM(IF(zakaz_pod.pod_id<>1,(zakaz_pod.coast-zakaz_pod.payment)/100*zakaz.percent2,0))) as pod_percent',
            'SUM(IF(zakaz_pod.pod_id=1,(zakaz_pod.coast-zakaz_pod.payment)/100*zakaz.percent1,0)) as asterion_percent',
            'SUM(IF(zakaz_pod.pod_id<>1, (zakaz_pod.coast-zakaz_pod.payment)/100*zakaz.percent2, (zakaz_pod.coast-zakaz_pod.payment)/100*zakaz.percent1)) as wages_percent',
            'SUM(DISTINCT (zakaz.exec_speed_payment+zakaz.exec_markup_payment+zakaz.exec_bonus_payment+'
            . 'zakaz.exec_delivery_payment+zakaz.exec_transport_payment+zakaz.exec_transport2_payment-zakaz.exec_speed_payment+zakaz.exec_markup_payment+zakaz.exec_bonus_payment+'
            . 'zakaz.exec_delivery_payment+zakaz.exec_transport_payment+zakaz.exec_transport2_payment)/100*zakaz.percent3) as other_percent',
        ];

        foreach ($works as $work) {
//Здесь убрал 'DISTINCT' в двух след. строчках
            $select[] = "SUM(IF(zakaz_pod.workType=" . $work['id'] . ", zakaz_pod.coast,0)) as w_total" . MyHelplers::translit($work['name'], true);
            $select[] = 'SUM(IF(zakaz_pod.workType=' . $work['id'] . ', zakaz_pod.payment,0)) as w_total' . MyHelplers::translit($work['name'], true) . '_spend';
        }
        $tmpIn = '';
        foreach ($worksIds as $id) {
            if ($tmpIn)
                $tmpIn .= ',';
            $tmpIn .= $id;
        }
//Здесь убрал 'DISTINCT' в двух след. строчках
        $select[] = "SUM(IF(NOT(zakaz_pod.workType IN (" . $tmpIn . ")), zakaz_pod.coast,0)) as w_other_total";
        $select[] = "SUM(IF(NOT(zakaz_pod.workType IN (" . $tmpIn . ")), zakaz_pod.payment,0)) as w_other_spend";
        $select[] = "SUM(zakaz_pod.coast) as w_total";
        $select[] = "SUM(zakaz_pod.payment) as w_spend";
        return $select;
    }

    protected function statisticCreatePeriodArray(): array {
        $nw = new \DateTime('now');
        $rVal = [
            'dd'  => [
            ],
            'but' => [
        ]];
        $dt = new \DateTime('now');
        $d = (int) $nw->format('d') - 1;
        $dt->sub(new \DateInterval('P2M' . ($d) . 'D'));
        $rVal['but']['3M'] = [
            'from' => $dt->format('Y-m-d'),
            'to'   => $nw->format('Y-m-d')];
        $dt = new \DateTime('now');
        $dt->sub(new \DateInterval('P5M' . ($d) . 'D'));
        $rVal['but']['6M'] = [
            'from' => $dt->format('Y-m-d'),
            'to'   => $nw->format('Y-m-d')];
        $dt = new \DateTime('now');
        $dt->sub(new \DateInterval('P1Y' . ($d) . 'D'));
        $rVal['but']['12M'] = [
            'from' => $dt->format('Y-m-d'),
            'to'   => $nw->format('Y-m-d')];
        for ($i = 1; $i < 13; $i++) {
            $fr = new \DateTime('now');
            $to = new \DateTime('now');
            $d = (int) $to->format('d') - 1;
            $fr->sub(new \DateInterval('P' . (13 - $i) . 'M' . $d . 'D'));
            $to->sub(new \DateInterval('P' . (12 - $i) . 'M' . ($d + 1) . 'D'));
            $rVal['dd'][] = [
                'from' => $fr->format('Y-m-d'),
                'to'   => $to->format('Y-m-d')];
        }
        $fr = new \DateTime('now');
        $to = new \DateTime('now');
        $d = (int) $to->format('d') - 1;
        $fr->sub(new \DateInterval('P' . $d . 'D'));
        $rVal['dd'][] = [
            'from' => $fr->format('Y-m-d'),
            'to'   => $to->format('Y-m-d')];
        return $rVal;
    }

    protected function creatAllStatisticTable(&$query): void {
        if (count($query)) {
            $tmp = $query[0];
            for ($i = 1; $i < count($query); $i++) {
                foreach ($query[$i] as $key => $val) {
                    if ($key !== 'manger_name' && $key !== 'ourmanager_id')
                        $tmp[$key] += $val;
                }
            }
            $tmp['manger_name'] = 'Все';
            $tmp['ourmanager_id'] = -1;
            $query[] = $tmp;
        }
    }

    protected function statisticIntervalText(): string {
        $dt = new \DateTime($this->periud_from, new \DateTimeZone('Europe/Moscow'));
        switch ($this->periud) {
            case -1:
                return 'За всё время';
            case 0:
                return \Yii::t('app', $dt->format('F')) . ' ' . $dt->format('Y');
            case 1:
                return '3 месяца';
            case 2:
                return '6 месяцев';
            case 3:
                return '12 месяцев';
            default:
                return '';
        }
    }

    protected function prepareManager($periud, $periud_from, $periud_to, $def = null): void {
        if (Yii::$app->request->isPost) {
            $this->periud = Yii::$app->request->post('periud', $periud, -1);
            $this->periud_from = Yii::$app->request->post('periud_from', $periud_from);
            $this->periud_to = Yii::$app->request->post('periud_to', $periud_to);
            $this->managers = (array) Yii::$app->request->post('managers', $def);
        } else {
            $this->managers = (array) Yii::$app->request->get('managers', $def);
        }
    }

    protected function prepareManager_Exists(): void {
        if (Yii::$app->user->identity->role === 'moder') {
            if (self::$_managers_exists1){
                $this->managers_exists = self::$_managers_exists1;
            }else{
                $this->managers_exists = \app\models\TblUser::find()
                                ->select('id,realname')
                                ->where([
                                    '<',
                                    'utype',
                                    3])
                                ->andWhere([
                                    'id' => Yii::$app->user->identity->id])
                                ->orderBy('id')
                                ->asArray()->all();
                self::$_managers_exists1 = $this->managers_exists;
            }
            $this->managers = [
                (int) Yii::$app->user->identity->id => 'on'];
        } else {
            if (self::$_managers_exists2){
                $this->managers_exists = self::$_managers_exists2;
            }else{
                $this->managers_exists = \app\models\TblUser::find()->select('id,realname')->where([
                                    '<',
                                    'utype',
                                    3])
                                ->orderBy('id')
                                ->asArray()->all();
                self::$_managers_exists2 = $this->managers_exists;
            }
        }
    }

    protected function prepareWorks(): array {
        if (static::$_prepareWorks){
            return static::$_prepareWorks;
        }else{
            static::$_prepareWorks = Worktypes::find()
                            ->filterWhere([
                                'like',
                                'name',
                                'Шелкография'])
                            ->orFilterWhere([
                                'like',
                                'name',
                                'Тампопечать'])
                            ->orFilterWhere([
                                'like',
                                'name',
                                'Офсет'])
                            ->orFilterWhere([
                                'like',
                                'name',
                                'Цифра'])
                            ->orFilterWhere([
                                'like',
                                'name',
                                'Уф-лак'])
                            ->orderBy([
                                'name' => SORT_DESC])
                            ->select([
                                'id',
                                'name'])->asArray()->all();
            return static::$_prepareWorks;
        }
    }

    protected function prepareQuery(array $works, array $worksIds): object {
        $query = Zakaz::find()
                ->leftJoin('zakaz_materials', 'zakaz.id=zakaz_materials.zakaz_id')
                ->leftJoin('zakaz_pod', 'zakaz.id=zakaz_pod.zakaz_id')
                ->leftJoin('tbl_user', 'zakaz.ourmanager_id=tbl_user.id')
                ->where([
                    're_print' => 0])
                ->andWhere(['not', ['stage' => 0]])
                ->select($this->statisticSelect($works, $worksIds));
        if (count($this->managers)) {
            $query->andWhere([
                'ourmanager_id' => array_keys($this->managers)]);
        } else {
            $query->andWhere([
                'ourmanager_id' => ArrayHelper::getColumn($this->managers_exists, 'id')]);
        }
        if ($this->periud_from !== null && $this->periud_to !== null) {
            $query->andWhere([
                'and',
                [
                    '>=',
                    'dateofadmission',
                    $this->periud_from],
                [
                    '<=',
                    'dateofadmission',
                    $this->periud_to]]);
            $inend = new \DateTime($this->periud_to);
            $istart = new \DateTime($this->periud_from);
            $diff = $istart->diff($inend);
            $curInterval = $diff->format('%R%f дней');
        } else {
            $curInterval = null;
        }
        $query->orderBy('zakaz.ourmanager_id');
        $query->groupBy('zakaz.ourmanager_id');
        return $query;
    }

    public function run($periud = -1, $periud_from = null, $periud_to = null) {
        $this->controller->layout =  'main_2.php';
        $this->prepareManager($periud, $periud_from, $periud_to);
        $this->prepareManager_Exists();
        $works = $this->prepareWorks();
        $worksIds = ArrayHelper::getColumn($works, 'id');

//        \yii\helpers\VarDumper::dump($select,10,true);Yii::$app->end();

        //$query = $this->prepareQuery($works, $worksIds)->asArray()->all();
		$query = $this->newQuery($works, $worksIds);
        if (count($query) > 1)
            $this->creatAllStatisticTable($query);
        if (Yii::$app->request->isPost) {
            return $this->controller->renderPartial('statistic', [
                        'periud'           => (int) $this->periud,
                        'managers'         => $this->managers,
                        'managers_exists'  => $this->managers_exists,
                        'works'            => $works,
                        'test'             => $query,
                        'intervals'        => $this->statisticCreatePeriodArray(),
                        'current_interval' => $this->statisticIntervalText(),
                        'fromto'           => [
                            'from' => $this->periud_from,
                            'to'   => $this->periud_to,
                        ]
            ]);
        } else {
            return $this->controller->render('statistic', [
                        'periud'           => (int) $this->periud,
                        'managers'         => $this->managers,
                        'managers_exists'  => $this->managers_exists,
                        'works'            => $works,
                        'test'             => $query,
                        'current_interval' => $this->statisticIntervalText(),
                        'intervals'        => $this->statisticCreatePeriodArray(),
                        'fromto'           => [
                            'from' => $this->periud_from,
                            'to'   => $this->periud_to,
                        ]
            ]);
        }
    }

}
