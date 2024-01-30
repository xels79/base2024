<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\zakaz;

/**
 * Description of ZakazConstants
 *
 * @author Александр
 */
use yii\db\ActiveRecord;

class ZakazConstants extends ActiveRecord {

    public static $formats = [
        'a0'  => [841, 1189],
        'a1'  => [594, 841],
        'a2'  => [420, 594],
        'a3'  => [297, 420],
        'a4'  => [210, 297],
        'a5'  => [148, 210],
        'a6'  => [105, 148],
        'a7'  => [74, 105],
        'a8'  => [52, 74],
        'a9'  => [37, 52],
        'a10' => [26, 37],
        'b0'  => [1000, 1414],
        'b1'  => [707, 1000],
        'b2'  => [500, 707],
        'b3'  => [353, 500],
        'b4'  => [250, 353],
        'b5'  => [176, 250],
        'b6'  => [125, 176],
        'b7'  => [88, 125],
        'b8'  => [62, 88],
        'b9'  => [44, 62],
        'b10' => [31, 44],
        'c0'  => [917, 1297],
        'c1'  => [648, 917],
        'c2'  => [458, 648],
        'c3'  => [324, 458],
        'c4'  => [229, 324],
        'c5'  => [162, 229],
        'c6'  => [114, 162],
        'c7'  => [81, 114],
        'c8'  => [57, 81],
        'c9'  => [40, 57],
        'c10' => [28, 40],
        'sr3' => [320, 450],
        'e65' => [110, 220],
        'c65' => [114, 229]
    ];

    public function formatStringProceed($f) {
        $tmp = explode('*', $f);
        if (count($tmp) === 2) {
            return $f;
        } else {
            $f = strtolower($f);
            if (array_key_exists($f, self::$formats)) {
                return self::$formats[$f][0] . '*' . self::$formats[$f][1];
            } else {
                return $f;
            }
        }
    }

}
