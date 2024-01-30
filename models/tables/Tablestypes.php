<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * This is the model class for table "materials_on_firms".
 * 
 * @property int $id
 * @property string $name Название типа материала
 * @property string $struct Структура материала
 * @property string $translitName Название в транслитерации
 * @property string $structlist Временная переменная для сохранения структуры
 * 
 */

namespace app\models\tables;

/**
 * Description of TablesSmall
 *
 * @author Александр
 */
use yii\db\ActiveRecord;
use app\components\MyHelplers;
use yii\behaviors\TimestampBehavior;
class Tablestypes extends \app\models\BaseActiveRecord{
    public $structlist;
    public function translit($s) {
        return MyHelplers::translit($s);
    }
    public static function tableName() {
        return 'materialtypes';
    }
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)){
            $tmp= \yii\helpers\Json::decode($this->structlist);
//            array_push($tmp,'Склад');
            if (count($tmp)>1){
                $this->struct= \yii\helpers\Json::encode($tmp);
            }else{
                $this->addError('structlist','Должнобыть задано минимум две позиции');
                return false;
            }
            if (!$this->translitName){
                $this->translitName=$this->translit($this->name);
                $tmp= \yii\helpers\Json::decode($this->structlist);
            }
            if (mb_strlen($this->name)){
                $this->name= mb_strtoupper(mb_substr($this->name, 0,1)). mb_strtolower(mb_substr($this->name, 1));
            }
            return true;
        }else
            return false;
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['update_time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_time'],
                ],
                // если вместо метки времени UNIX используется datetime:
                // 'value' => new Expression('NOW()'),
            ],
        ];
    }
    public function rules() {
        return [
            [['id','update_time'],'integer'],
            [['name','struct','structlist','translitName'],'string'],
            [['name'],'required','message'=>'Должно быть указано.'],
            [['struct','structlist'],'default','value'=>'{}'],
            [['name'],'unique']
        ];
    }
}
