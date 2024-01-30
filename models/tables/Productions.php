<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 *
 *
 *
 */

namespace app\models\tables;

/**
 * This is the model class for table "production".
 *
 * @author Александр
 * @property string $category2 Строка с JSON выбором
 * @property int $update_time Время последнего обновления
 */
class Productions extends TablesSmall {

    const category2values = [
        1 => 'визитки',
        2 => 'сам',
        3 => 'конструк.',
        4 => 'вырубка',
        5 => 'бум.пакет'
    ];

    public static function tableName() {
        return 'production';
    }

    public function getCatText() {
        return ['Листовая', 'Пакеты п/э', 'Сувенирка', 'Уф-лак', 'Пустая','Термоподъём'][$this->category];
    }

    public function getCat2Text() {
        $rVal = '';
        $tmp = \yii\helpers\Json::decode($this->category2);
        if (count(array_keys($tmp)) === count(array_keys(self::category2values))) {
            return 'Все';
        } else {
//            return \yii\helpers\VarDumper::dumpAsString(array_keys($tmp));
//            $dmp=[];
            foreach (array_keys($tmp) as $key) {
                if ($rVal !== '') {
                    $rVal .= ', ';
                    $rVal .= self::category2values[((int) $key) + 1];
                } else {
                    $rVal = mb_strtoupper(mb_substr(self::category2values[((int) $key) + 1], 0, 1)) . mb_substr(self::category2values[((int) $key) + 1], 1);
                }
//                $dmp[]=['k'=>(int)$key-1,'v'=>self::category2values[((int)$key)-1],'rVal'=>$rVal];
            }
//            return \yii\helpers\VarDumper::dumpAsString($dmp);
            return $rVal;
        }
    }

    public function rules() {
        return \yii\helpers\ArrayHelper::merge(parent::rules(), [
                    [['update_time'], 'integer'],
                    [['category2'], 'string'],
                    [['category2'], 'default', 'value' => '{}'], //"0":"визитки","1":"сам","2":"конструк.","3":"вырубка","4":"бум.пакет"
        ]);
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
