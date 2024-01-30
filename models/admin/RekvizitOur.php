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

class RekvizitOur extends RekvizitBase {

    public static $formList = ['ООО', 'ИП', 'ЧП', 'ОАО', 'ЗАО', 'АО'];
    public static $taxList = ['НДС - 18%', 'НДС - 15%', 'НДС - 6%', 'фиксированный'];
    public $imgSignatureCEO;
    public $imgSignatureAccountant;
    public $imgStamp;

    public static function tableName() {
        return 'rekvizitOur';
    }

    public function getFormText() {
        return $this::$formList[$this->form];
    }

    public function rules() {
        return ArrayHelper::merge(parent::rules(), [
                    [['tax_system_id', 'tax_id', 'firm_id', 'update_time'], 'integer'],
                    [['incorporationDocuments'], 'string'],
                    [['imgSignatureCEO', 'imgSignatureAccountant', 'imgStamp'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, gif'],
        ]);
    }

    public function attributeLabels() {
        return ArrayHelper::merge(parent::attributeLabels(), [
                    'signatureCEO'        => 'Подпись Ген. директора',
                    'signatureAccountant' => 'Подпись бухгалтера',
                    'stamp'               => 'Печать'
        ]);
    }

    public function beforeDelete() {
        if (parent::beforeDelete()) {
            $attr = [
                'signatureCEO',
                'signatureAccountant',
                'stamp'
            ];
            foreach ($attr as $val) {
                if ($this->$val) {
                    if (file_exists($this->$val)) {
                        unlink($this->$val);
                        $this->$val = null;
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->update_time = time();
        // ...custom code here...
        return true;
    }

}
