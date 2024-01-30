<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ZarplataIndex
 *
 * @author Александр
 * @inheritDoc
 * @property int $cYear Текущий год
 * @property int $cMonth Текущий месяц
 */

namespace app\controllers\ZarplataActions;

use Yii;
use yii\base\Action;
use app\models\ZarplatMoth;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use app\models\admin\ManagersOur;
use app\models\Zarplata;

class ZarplataIndex extends Action {

    private $realYear = null;
    private $cYear = null;
    private $cMonth = null;

    public function init() {
        parent::init();
        $this->controller->layout = 'main_2.php';
        $dt = new \DateTime('now');
        $this->realYear = (int) $dt->format('Y');
        if (!$this->cYear = (int) Yii::$app->request->post('year', Yii::$app->request->get('year')))
            $this->cYear = $this->realYear;
        if (!$this->cMonth = Yii::$app->request->post('cMonth', null))
            $this->cMonth = (int) $dt->format('m');
        if (!Yii::$app->user->isGuest) {
            $url = Yii::$app->user->identity->can('zarplata/changevalue') ? Url::to(['/zarplata/changevalue']) : "";
            $urlRM = Yii::$app->user->identity->can('zarplata/removemonth') ? Url::to(['/zarplata/removemonth']) : "";
            $urlRY = Yii::$app->user->identity->can('zarplata/removeyear') ? Url::to(['/zarplata/removeyear']) : "";
            $urlAddM = Yii::$app->user->identity->can('zarplata/addmonth') ? Url::to(['/zarplata/addmonth']) : "";
        } else {
            $url = "";
            $urlRM = "";
            $urlRY = "";
            $urlAddM = "";
        }
        $options = \yii\helpers\Json::encode([
                    'actionRemovMonthUrl' => $urlRM,
                    'actionRemovYearUrl'  => $urlRY,
                    'actionAddMonth'      => $urlAddM,
                    'addMonthMenu'        => $this->getOtherMonths()
        ]);
        $this->controller->view->registerAssetBundle('app\assets\AppAssetZarplata');
        $this->controller->view->registerJs("$('[role=\"data-input\"]').zarplataActions({actionUrl:\"$url\"})", \yii\web\View::POS_READY);
        $this->controller->view->registerJs("$('[data-toggle=\"tab\"]').zarplataTabsActions($options)", \yii\web\View::POS_READY);
    }

    public function getRealYear() {
        return $this->realYear;
    }

    public function getCurrentMonth() {
        return (int) $this->cMonth;
    }

    public function getCurrentYaer() {
        return (int) $this->cYear;
    }

    /*
     *
     * Список месяцев по году
     * @inheritDoc
     *
     * @return array
     */

    protected function allMonthByYearAsTabsItems(int $year = null, $idHtml = null) {
        $year = $year ? $year : $this->cYear;
        $rVal = $idHtml ? '' : [];
        $temp = ZarplatMoth::find()->where([
                    'year' => $year])->with('zarplata')->groupBy('month')->indexBy('month')->all();
        foreach (array_keys($temp) as $key) {
            $tmp = [];
            if ((int) $temp[$key]->month >= (int) $this->cMonth) {
                $this->addOthers($temp[$key]);
                $this->addPercents($temp[$key]);
            }
            foreach ($temp[$key]['zarplata'] as $el) {
                if (mb_strpos($el['name'], '%') > -1) {
                    $tmp[4][] = $el;
                } else {
                    $tmp[$el['payment_id']][] = $el;
                }
            }
            //$rVal[$key]['zarplata'] = $tmp;
            if ($idHtml) {
                $rVal .= $this->controller->renderPartial('_header', [
                    'zarplata' => $tmp,
                    'month_id' => $temp[$key]['id'],
                    'idHtml'   => $idHtml,
                ]);
            } else {
                $rVal[] = [
                    'label'         => \Yii::t('monthtoname', $key),
                    'content'       => $this->controller->renderPartial('_header', [
                        'zarplata'   => $tmp,
                        'month_id'   => $temp[$key]['id'],
                        'day_count'  => $temp[$key]['day_count'],
                        'monthName'  => \Yii::t('monthtoname', $key),
                        'curentYear' => $this->cYear,
                    ]),
                    'headerOptions' => ['data-month_num' => $key, 'data-month_id' => $temp[$key]['id']]
                ];
            }
        }
        return $rVal;
    }

    /*
     * Список за все года
     *
     */

    private function allYear() {
        return ZarplatMoth::find()->groupBy('year')->asArray()->all();
    }

    protected function getAlertMessage() {
		$lastMonthInBase = ZarplatMoth::find()->where([
                    'year'  => $this->cYear,
                    //'month' => $this->cMonth
                ])->max('month');
        $rVal = Html::tag('h3', 'Необходимо добавить месяц "'.\Yii::t('monthtoname', $lastMonthInBase+1).'"');
        $rVal .= Html::tag('div',
                        Html::tag('span', 'Рабочие дни', [
                            'class' => 'input-group-addon'])
                        . Html::input('text', 'dayCount', 20, [
                            'class' => 'form-control',
                            'id'    => 'zarplata-day-count'])
                        . Html::tag('span', 'дней', [
                            'class' => 'input-group-addon'])
                        , [
                    'class' => 'input-group',
                    'style' => [
                        'width' => '300px']]);
        $rVal .= Html::tag('div', Html::a('Добавить', Url::to([
                                    'addmonth']), [
                            'class'    => 'btn btn-danger',
                            'id'       => 'addMonthButton',
                            'disabled' => 'disabled']) .
                        Html::a('Закрыть', '#', [
                            'class'        => 'btn btn-default',
                            'data-dismiss' => 'alert']), [
                    'class' => 'btn-group']);
        return $rVal;
    }

    private function alert() {
        if ($curMonth = ZarplatMoth::find()->where([
                    'year'  => $this->cYear,
                    'month' => $this->cMonth
                ])->one() || ($this->cYear !== $this->realYear)) {
            return '';
        } else {
            return Alert::widget([
                        'options' => [
                            'class' => 'alert alert-danger fade in'
                        ],
                        'body'    => $this->alertMessage
            ]);
        }
    }

    protected function addPercents(ZarplatMoth $month) {
        $mans = ManagersOur::find()
                        ->where(['or', ['payment_id' => 4], ['hasPercents' => 1]])
                        ->andWhere(['not', ['status_id' => 3]])
                        ->asArray()->all();
        foreach ($mans as $el) {
            $zp = new Zarplata();
            $zp->name = $el['name'] . ($el['payment_id'] != 4 ? '%' : '');
            if (!$tmp = Zarplata::find()->where(['month_id' => $month->id, 'name' => $zp->name, 'payment_id' => $el['payment_id']])->one()) {
                $zp->payment_id = $el['payment_id'];
                $zp->month_id = $month->id;
                $zp->normal = $el['payment_id'] != 4?0:(double) $el['normal'];
                $zp->wages = $el['payment_id'] != 4?0:(double) $el['wages'];
                $zp->procats = $el['piecework'];
                $zp->employed = $el['employed'] == 1;
                $zp->save();
                if (!$zp->save()) {
                    $errors[] = $zp->errors;
                }
            } else {
                Yii::debug($tmp->toArray(), 'zarplata');
            }
        }
        return $mans;
    }

    protected function addOthers(ZarplatMoth $month) {
        $mans = ManagersOur::find()
                        ->where(['not', ['payment_id' => 4]])
                        ->andWhere(['not', ['status_id' => 3]])
                        ->asArray()->all();
        $errors = [];
        foreach ($mans as $el) {
            $zp = new Zarplata();
            $zp->name = $el['name'];
            if (!$tmp = Zarplata::find()->where(['month_id' => $month->id, 'name' => $zp->name, 'payment_id' => $el['payment_id']])->one()) {
                $zp->payment_id = $el['payment_id'];
                $zp->month_id = $month->id;
                $zp->wages = (double) $el['wages'];
                if ($el['payment_id'] == 1) {
                    $zp->normal = (double) ($el['wages'] / $month->day_count);
                } else {
                    $zp->normal = (double) $el['normal'];
                }
                $zp->procats = $el['piecework'];
                $zp->employed = $el['employed'] == 1;
                if (!$zp->save()) {
                    $errors[] = $zp->errors;
                }
            } else {
                Yii::debug($tmp->toArray(), 'zarplata');
            }
        }
        return ['$errors' => $errors, 'mans' => $mans];
    }

    /*
     * Находит отсутствуещие месяца в году
     * Следующие после текущего
     */

    private function getOtherMonths() {
        $rVal = [];
		$lastMonthInBase = ZarplatMoth::find()->where([
                    'year'  => $this->cYear,
                ])->max('month');
        $months = ZarplatMoth::find()
                        ->where(['year' => $this->cYear])
                        ->andWhere(['>', 'month', $lastMonthInBase+1])
                        ->indexBy('month')
                        ->asArray()->all();
        for ($i = $lastMonthInBase+1; $i < 13; $i++) {
            if (!array_key_exists($i, $months)) {
                $rVal[] = [
                    'month'     => $i,
                    'monthName' => \Yii::t('monthtoname', $i),
                    'year'      => $this->cYear
                ];
            }
        }
        return $rVal;
    }

    public function run() {
        return $this->controller->render('index', [
                    'year'       => $this->cYear,
                    'alert'      => $this->alert(),
                    'monthName'  => \Yii::t('monthtoname', $this->cMonth),
                    'items'      => $this->allMonthByYearAsTabsItems(),
                    'otheMonths' => $this->getOtherMonths()
        ]);
    }

}
