<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\admin;

/**
 * Description of Zak
 *
 * @author Александр
 */
class Firm extends \app\models\BaseActiveRecord {

    private $contactModel = false;
    public static $formList = ['ООО', 'ИП', 'ЧП', 'ОАО', 'ЗАО', 'АО'];

    public function rules()
    {
        return [
            [['mainForm', 'typeOfPayment', 'delay', 'contract_number', 'update_time'],
                'integer'],
            [['credit'], 'number'],
            [['status', 'has_contract'], 'boolean'],
            ['status', 'default', 'value' => 1],
            [['mainName'], 'string'],
            [['mainName'], 'required', 'message' => 'Должно быть указано.']
        ];
    }

    public function init()
    {
        parent::init();
        if ( $this->isNewRecord ) $this->status = true;
    }

    public static function findName( int $id, $defVal = 'Не найден' )
    { // имя фирмы
//        return \yii\helpers\VarDumper::dumpAsString($id);
        $query = self::findOne( $id );
        if ( $query && isset( $query->mainName ) ) {
            return $query->mainName;
        } else {
            return $defVal;
        }
    }

    public function attributeLabels()
    {
        return \yii\helpers\ArrayHelper::merge( parent::attributeLabels(), [
                    'firm_id'           => 'Идентификатор',
                    'statusText'        => 'Статус',
                    'mainFormText'      => 'Форма',
                    'typeOfPaymentText' => 'Оплата',
                    'has_contractText'  => 'Договор',
                    'contact1Name'      => 'ФИО',
                    'phone'             => 'Телефон',
                    'mail'              => 'e-mail',
                    'woptype'           => 'Тип мат./раб.',
                    'wopInfoString'     => 'Тип мат./раб.',
                    'additional'        => 'Мобильный'
        ] );
    }

    /**
     * @return string
     */
    public function getWopInfoString()
    {
        if ( $this->formName() === 'Pod' || $this->formName() === 'Post' ) {
            $classN = 'app\models\admin\WOP' . $this->formName();
            if ( $model = $classN::find()->where( ['firm_id' => $this->firm_id] )->all() ) {
                $rVal = '';
                foreach ( $model as $el ) {
                    if ( $rVal ) $rVal .= ', ';
                    $rVal .= $el->nameText;
                }
                return $rVal;
            } else {
                return 'Нет';
            }
        } else {
            return 'Нет';
        }
    }

    /**
     * @return string
     */
    public function getWoptype()
    {
        if ( $this->formName() === 'Pod' || $this->formName() === 'Post' ) {
            $classN = 'app\models\admin\WOP' . $this->formName();
            if ( $model = $classN::find()->where( ['firm_id' => $this->firm_id] )->one() ) {
                return $model->nameText;
            } else {
                return 'Нет';
            }
        } else return 'Нет';
    }

    public function getHas_contractText()
    {
        if ( $this->has_contract ) return 'Да';
        else return 'Нет';
    }

    public function getStatusText()
    {
        if ( $this->status ) return 'Активен';
        else return 'Нет';
    }

    public function getMainFormText()
    {
        return self::$formList[$this->mainForm];
    }

    public function getTypeOfPaymentText()
    {
        if ( $this->typeOfPayment ) return 'без НДС';
        else return 'НДС';
    }

    private function getContactM()
    {
        if ( $this->contactModel === false ) {
            $dopT = substr( self::tableName(), 3 );
            $dopT = substr( $dopT, 0, strlen( $dopT ) - 2 );
            $dopT = 'app\models\admin\Contact' . strtoupper( substr( $dopT, 0, 1 ) ) . substr( $dopT, 1 );
            $this->contactModel = $dopT::find()->where( ['firm_id' => $this->firm_id] )->one();
        }
        return $this->contactModel;
    }

    public function getContact1Name()
    {
        $model = $this->getContactM();
        if ( $model ) return $model->name;
        else return 'нет';
    }

    public function getPhone()
    {
        $model = $this->getContactM();
        if ( $model ) return $model->phone;
        else return 'нет';
    }

    public function getAdditional()
    {
        $model = $this->getContactM();
        if ( $model ) return $model->additional;
        else return '';
    }

    public function getMail()
    {
        $model = $this->getContactM();
        if ( $model ) return $model->mail;
        else return 'нет';
    }

    public function beforeSave( $insert )
    {
        if ( !parent::beforeSave( $insert ) ) {
            return false;
        }
        $this->update_time = time();
        // ...custom code here...
        return true;
    }

}
