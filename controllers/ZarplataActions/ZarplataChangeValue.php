<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\ZarplataActions;

use Yii;
use yii\base\Action;
use app\models\Zarplata;

/**
 * Description of ZarplataChangeValue
 *
 * @author Александр
 */
class ZarplataChangeValue extends ZarplataIndex {

    private $dataKey = "";
    private $value = null;
    private $z_id = 0;

    public function init()
    {
        parent::init();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->dataKey = (string) Yii::$app->request->post( 'dataKey', "" );
        $this->value = Yii::$app->request->post( 'value' );
        $this->z_id = (int) Yii::$app->request->post( 'id', 0 );
    }

    private function checkErrors()
    {
        if ( !$this->dataKey ) {
            return ['status' => 'error', 'errorText' => 'Не передан ключ'];
        } elseif ( !$this->z_id ) {
            return ['status' => 'error', 'errorText' => 'Не передан id'];
        } elseif ( $this->value === null ) {
            return ['status' => 'error', 'errorText' => 'Не передано значение'];
        } else {
            return null;
        }
    }

    public function run()
    {
        if ( $error = $this->checkErrors() ) {
            return $error;
        }
        if ( $model = Zarplata::findOne( (int) $this->z_id ) ) {
            $model->setAttribute( $this->dataKey, $this->value );
            if ( $model->update( $this->dataKey ) ) {
                return ['status' => 'ok', 'html' => trim( $this->allMonthByYearAsTabsItems( null, $this->z_id ) )];
            } else {
                return ['status'    => 'error', 'errorText' => "Запись #$this->z_id не удается сохранить!",
                    'errors'    => $model->errors];
            }
        } else {
            return ['status' => 'error', 'errorText' => "Запись #$this->z_id не найдена!"];
        }
    }

}
