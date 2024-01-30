<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

namespace app\models\tables;

/**
 * Description of TablesSmall
 *
 * @author Александр
 * @property int $id Идентификатор
 * @property int $category Идентификатор категории
 * @property string $name Название
 */
class TablesSmall extends \app\models\BaseActiveRecord{
    static $categoryText=['А','Б','В'];
    public function rules() {
        return [
            [['id','category'],'integer'],
            [['name'],'string'],
            [['name'],'required','message'=>'Должно быть указано.'],
            ['name','unique']
        ];
    }
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)){
            if (mb_strlen($this->name)&&isset($this->name)){
                $this->name= mb_strtoupper(mb_substr($this->name, 0,1)). mb_strtolower(mb_substr($this->name, 1));
            }
            return true;
        }else{
            return false;
        }
    }
}
