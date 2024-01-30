<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZAddedit
 *
 * @author Александр
 */
use Yii;
use app\models\zakaz\Zakaz;

class ZAddedit extends ZActionAEBase {

    public function run() {
        if ($this->postID) {

            if ($zak = Zakaz::findOne($this->postID)) {
                if (Yii::$app->user->identity->role !== 'admin' && Yii::$app->user->identity->id !== $zak->ourmanager_id && !(Yii::$app->user->identity->can('zakaz/zakazlist/editanyorder')) && (Yii::$app->user->identity->role === 'moder' && !Yii::$app->request->post('isDisainer') === 'true' )) {
                    return [
                        'status'    => 'error',
                        'errorText' => 'Запрещено редактировать чужую запись'
                    ];
                }
                return $this->saveProceed($zak);
            } else {
                return [
                    'status'    => 'error',
                    'errorText' => 'Заказ №' . $this->postID . ' не найден!'
                ];
            }
        } else {
            $zak = new Zakaz();
            $zak->stage = 0;
            $zak->division_of_work = 1;
            $zak->method_of_payment = 1;
            $zak->production_id = 0;
            $zak->dateofadmission = Yii::$app->formatter->asDate(time());
            $zak->ourmanager_id = Yii::$app->user->identity->id;
            return $this->saveProceed($zak);
        }
    }

}
