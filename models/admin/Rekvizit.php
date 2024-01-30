<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\admin;

/**
 * Description of RekvizitOur
 *
 * @author Александр
 */
use \yii\helpers\ArrayHelper;
use yii;

class Rekvizit extends \app\models\BaseActiveRecord {

    public static $formList = ['ООО', 'ИП', 'ЧП', 'ОАО', 'ЗАО', 'АО'];
    public static $taxList = ['НДС - 18%', 'НДС - 15%', 'НДС - 6%', 'фиксированный'];

    public function getFormText() {
        return $this::$formList[$this->form];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->passportGivenDate)
                $this->passportGivenDate = Yii::$app->formatter->asDate($this->passportGivenDate, 'php:Y-m-d');
            return true;
        } else {
            return false;
        }
    }

    public function rules() {
        return ArrayHelper::merge(parent::rules(), [
                    [['form', 'kpp', 'correspondentAccount', 'okpo', 'ogrn', 'firm_id', 'inn', 'bik', 'passportSeries', 'passportNumber'], 'integer'],
                    [['name', 'address', 'consignee', 'account', 'bank', 'okved', 'ceo', 'okved', 'passportGiven'], 'string'],
                    [['inn'], 'string', 'length' => [10, 10]],
                    [['passportSeries'], 'string', 'length' => [4, 4]],
                    [['passportNumber'], 'string', 'length' => [6, 6]],
                    [['passportGivenDate'], 'date'],
                    [['correspondentAccount', 'account'], 'string', 'length' => [20, 20]],
                    [['bik', 'kpp'], 'string', 'length' => [9, 9]],
                    [['name', 'inn'], 'required', 'message' => 'Должно быть указано.'],
                    [['inn'], 'unique']
        ]);
    }

}
