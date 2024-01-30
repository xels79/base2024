<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;
use yii\helpers\Json;
/**
 * Description of Options
 *
 * @author Александр
 */
class Options extends ActiveRecord{
    private $options=null;
    public static function tableName() {
        return 'options';
    }
    public function rules()
    {
        return [
            [['userid','optionid'], 'required'],
            [['userid','id'], 'integer'],
            [['value'],'string'],
            [['optionid'], 'string', 'max' => 64],
            [['value'],'default','value'=>'{}']
        ];
    }
    public function getOptions(){
        if (is_array($this->options)) return $this->options;
        $this->options=Json::decode($this->value);
        return $this->options;
    }
    public function setOptions(array $arr){
        $this->options=$arr;
        $this->value=Json::encode($arr);
    }
}
