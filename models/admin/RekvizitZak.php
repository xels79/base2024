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


class RekvizitZak extends Rekvizit{
    public static function tableName() {
        return 'rekvizitZak';
    }
}