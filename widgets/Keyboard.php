<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii;
use yii\base\Widget;
use yii\helpers\Html;
/**
 * Description of Keyboard
 *
 * @author Александр
 */
class Keyboard extends Widget{
    public $wraps=[['tagName'=>'td']];// Обёртки
                                    //[
                                    // [tagName=>'таг',options=>'опции']
                                    //]
    public $tagName='button';
    public $buttonOptions=['data-label'=>'keyboard'];
    public $options=['class'=>'keyboard'];
    public function codeToUtf8($code) {
    $code = (int) $code;
 
    if ($code < 0) {
        throw new RangeException("Negative value was passed");
    }
    # 0-------
    elseif ($code <= 0x7F) {
        return chr($code);
    }
    # 110----- 10------
    elseif ($code <= 0x7FF) {
        return chr($code >> 6 | 0xC0)
            . chr($code & 0x3F | 0x80)
        ;
    }
    # 1110---- 10------ 10------
    elseif ($code <= 0xFFFF) {
        return chr($code >> 12 | 0xE0)
            . chr($code >> 6 & 0x3F | 0x80)
            . chr($code & 0x3F | 0x80)
        ;
    }
    # 11110--- 10------ 10------ 10------
    elseif ($code <= 0x1FFFFF) {
        return chr($code >> 18 | 0xF0)
            . chr($code >> 12 & 0x3F | 0x80)
            . chr($code >> 6 & 0x3F | 0x80)
            . chr($code & 0x3F | 0x80)
        ;
    }
    # 111110-- 10------ 10------ 10------ 10------
    elseif ($code <= 0x3FFFFFF) {
        return chr($code >> 24 | 0xF8)
            . chr($code >> 18 & 0x3F | 0x80)
            . chr($code >> 12 & 0x3F | 0x80)
            . chr($code >> 6 & 0x3F | 0x80)
            . chr($code & 0x3F | 0x80)
        ;
    }
    # 1111110- 10------ 10------ 10------ 10------ 10------
    elseif ($code <= 0x7FFFFFFF) {
        return chr($code >> 30 | 0xFC)
            . chr($code >> 24 & 0x3F | 0x80)
            . chr($code >> 18 & 0x3F | 0x80)
            . chr($code >> 12 & 0x3F | 0x80)
            . chr($code >> 6 & 0x3F | 0x80)
            . chr($code & 0x3F | 0x80)
        ;
    }
    else {
        throw new RangeException("Invalid character code");
    }
}
 
/**
 * Получение кода символа Юникода
 *
 * @param string $utf8Char
 * Символ в кодировке UTF-8. Если в строке содержится больше одного символа
 * UTF-8, то учитывается только первый.
 *
 * @return int
 * Код символа из Юникода.
 *
 * @throws InvalidArgumentException
 */
    public function utf8ToCode($utf8Char) {
    $utf8Char = (string) $utf8Char;
 
    if ("" == $utf8Char) {
        throw new InvalidArgumentException("Empty string is not valid character");
    }
 
    # [a, b, c, d, e, f]
    $bytes = array_map('ord', str_split(substr($utf8Char, 0, 6), 1));
 
    # a, [b, c, d, e, f]
    $first = array_shift($bytes);
 
    # 0-------
    if ($first <= 0x7F) {
        return $first;
    }
    # 110----- 10------
    elseif ($first >= 0xC0 && $first <= 0xDF) {
        $tail = 1;
    }
    # 1110---- 10------ 10------
    elseif ($first >= 0xE0 && $first <= 0xEF) {
        $tail = 2;
    }
    # 11110--- 10------ 10------ 10------
    elseif ($first >= 0xF0 && $first <= 0xF7) {
        $tail = 3;
    }
    # 111110-- 10------ 10------ 10------ 10------
    elseif ($first >= 0xF8 && $first <= 0xFB) {
        $tail = 4;
    }
    # 1111110- 10------ 10------ 10------ 10------ 10------
    elseif ($first >= 0xFC && $first <= 0xFD) {
        $tail = 5;
    }
    else {
        throw new InvalidArgumentException("First byte is not valid");
    }
 
    if (count($bytes) < $tail) {
        throw new InvalidArgumentException("Corrupted character: $tail tail bytes required");
    }
 
    $code = ($first & (0x3F >> $tail)) << ($tail * 6);
 
    $tails = array_slice($bytes, 0, $tail);
    foreach ($tails as $i => $byte) {
        $code |= ($byte & 0x3F) << (($tail - 1 - $i) * 6);
    }
 
    return $code;
}
    public function renderRow($s,$e,$conver=true){
        $rVal='';
        $opt=['data-label'=>'keyboard','class'=>'btn btn-default btn-xs'];
        for ($i=$this->utf8ToCode($s);$i<=$this->utf8ToCode($e);$i++){
            $tmp=Html::button($this->codeToUtf8($i),$opt);
            foreach ($this->wraps as $wrap){
                if (!isset($wrap['options'])) $wrap['options']=[];
                $tmp=Html::tag($wrap['tagName'],$tmp,$wrap['options']);
            }
            $rVal.=$tmp;
        }
        return $rVal;
    }
    public function run(){
        $this->view->registerJsFile(\Yii::$app->assetManager->publish('@app/widgets/Keyboard/keyboard.js')[1],['depends'=>['yii\web\JqueryAsset','yii\jui\JuiAsset']]);
        $rVal=Html::tag('tr',$this->renderRow('0','9'));
        $rVal.=Html::tag('tr',$this->renderRow('А','П'));
        $rVal.=Html::tag('tr',$this->renderRow('Р','Я'));
        return Html::tag('table',$rVal,$this->options);
    }
}
