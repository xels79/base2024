<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\ZarplataActions;

/**
 * Description of ZarplataRemoveMonth
 *
 * @author Александр
 */
use Yii;
use yii\base\Action;
use app\models\ZarplatMoth;
use app\models\Zarplata;

class ZarplataRemoveMonth extends Action {

    public $month_id = 0;

    public function init() {
        parent::init();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->month_id = Yii::$app->request->post('month_id', 0);
    }

    public function run() {
        if ($month = ZarplatMoth::findOne($this->month_id)) {
            $month->delete();
            return ['status' => 'ok'];
        } else {
            return ['status' => 'error', 'errorText' => 'Месяц c id=' . $this->month_id . ' не найден'];
        }
    }

}
