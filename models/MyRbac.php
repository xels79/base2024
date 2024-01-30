<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Description of MyRbac
 *
 * @author alex
 * @property string $name
 * @property string $value
 * @property string $engname
 * @property int $lastupdate
 */
class MyRbac extends ActiveRecord {

    public function rules() {
        return [
            [['name', 'value'], 'required'],
            [['name', 'value', 'engname'], 'string'],
            [['lastupdate'], 'number']
        ];
    }

    public static function tableName() {
        return 'rbac';
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->lastupdate = time();
            return true;
        } else
            return false;
    }

}
