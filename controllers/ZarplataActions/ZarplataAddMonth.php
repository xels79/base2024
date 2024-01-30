<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\ZarplataActions;

/**
 * Description of ZarplataAddMonth
 *
 * @author Александр
 */
use \Yii;
use app\models\ZarplatMoth;
use app\models\Zarplata;
use app\models\admin\ManagersOur;

class ZarplataAddMonth extends ZarplataIndex {

    private $dayCount = null;

    public function init() {
        parent::init();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->dayCount = Yii::$app->request->post('dayCount');
    }

    private function addMonth() {
		$lastInBaseMonth = ZarplatMoth::find()->where([
                    'year'  => $this->currentYaer,
                    //'month' => $this->cMonth
                ])->max('month');

        $model = new ZarplatMoth();
        $model->month = $lastInBaseMonth+1;
        $model->year = $this->currentYaer;
        $model->day_count = $this->dayCount;
        if (!$model->save()) {//
            return [
                'status'    => 'error',
                'errorText' => 'Ошибка сохранения (' . $model->getFirstErrors()[array_keys($model->getFirstErrors())[0]] . ')'
            ];
        } else {
            //$this->addPechatniks($model);
            return [
                'addPercents' => $this->addPercents($model),
                'addOthers'   => $this->addOthers($model),
                'status'      => 'ok'];
        }
    }

    public function run() {
        if ($this->dayCount < 5 || $this->dayCount > 29) {
            return [
                'status'    => 'error',
                'errorText' => 'Не верное значени количества рабочих дней (' . $this->dayCount . ')'
            ];
        } else {
            return $this->addMonth();
        }
    }

}
