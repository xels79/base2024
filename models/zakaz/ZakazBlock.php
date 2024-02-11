<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\zakaz;

/**
 * Description of ZakazBlock
 *
 * @author Александр
 *
 * @method string blockInfo() Возвращает таблицу block для технички
 */
use Yii;
use yii\helpers\Json;
use yii\helpers\Html;

abstract class ZakazBlock extends ZakazMaterial {

    private static $_production_idText_cach = [
    ];

    private function findProductionOrCache() {
        if (array_key_exists($this->production_id, self::$_production_idText_cach)) {
            return self::$_production_idText_cach[$this->production_id];
        } else {
            if ($model = \app\models\tables\Productions::find()->where([
                        'id' => $this->production_id])->one()) {
                self::$_production_idText_cach[$this->production_id] = [
                    'name'      => $model->name,
                    'category'  => $model->category,
                    'category2' => $model->canGetProperty('category2') ? Json::decode($model->category2) : [
                ]];
                return self::$_production_idText_cach[$this->production_id];
            } else {
                return null;
            }
        }
    }

    public function getProductCategory() {
        if ($this->production_id) {
            $prod = $this->findProductionOrCache();
            if ($prod)
                return !$prod['category'] ? 0 : (int) $prod['category'];
            else
                return 0;
        } else {
            return 0;
        }
    }

    public function getProductCategory2() {
        if ($this->production_id) {
            $prod = $this->findProductionOrCache();
            if ($prod && isset($prod['category2']))
                return $prod['category2'];
            else
                return [
                ];
        } else {
            return [
            ];
        }
    }

    public function getProduction_idText() {
        if ($this->production_id) {
            $prod = $this->findProductionOrCache();
            if ($prod)
                return $prod['name'];
            else
                return 'Не найден';
        } else {
            return 'Не задано';
        }
    }

    public function swapFormat($val) {
        $tmp = explode('*', $val);
        if (count($tmp) === 2) {
            return $tmp[1] . '*' . $tmp[0];
        } else {
            return $val;
        }
    }

    /**
     * @return string Возвращает таблицу block для технички
     */
    public function blockInfo($id = 0) {
        //Вар "a":
        $key = $id ? ('' . $id) : '';
        $tdWP = [
            'style' => [
                'width' => '90px']];
        $formatTmp = explode('*', $this["format_printing_block$key"]);
        if (count($formatTmp) < 2) {
            $formatTmp = [0, 0];
        }
        $blockOpt = Json::decode($this["material_block_format$key"] ? $this["material_block_format$key"] : '{"block":{}}');
        $swapFormat = false;
        if (isset($blockOpt['block']) && is_array($blockOpt['block']) && array_key_exists('var', $blockOpt['block']) && $blockOpt['block']['var'] == 2 && (int) $formatTmp[0] < (int) $formatTmp[1]) {
            $swapFormat = true;
        }
//        if (is_array($blockOpt['block']) && array_key_exists('blockWidth', $blockOpt['block']) && array_key_exists('blockHeight', $blockOpt['block']) && $blockOpt['block']['blockWidth'] < $blockOpt['block']['blockHeight']) {
//            $swapFormat = true;
//        }
        switch ($this->getProductCategory()) {
            case 0:
                $rVal = Html::tag('tr', Html::tag('th', Html::tag('div', Html::tag('span', 'Блок')), [
                                    'rowspan' => 5]));
                $rVal .= Html::tag('tr', Html::tag('th', 'Блоков с листа:') . Html::tag('td', Html::tag('p', $this["blocks_per_sheet$key"]), $tdWP));
                $rVal .= Html::tag('tr', Html::tag('th', 'Формат блоков:') . Html::tag('td', Html::tag('p', $swapFormat ? $this->swapFormat($this["format_printing_block$key"]) : $this["format_printing_block$key"]), $tdWP));
                $rVal .= Html::tag('tr', Html::tag('th', 'Штук в блоке:') . Html::tag('td', Html::tag('p', $this["num_of_products_in_block$key"]), $tdWP));
                $rVal .= Html::tag('tr', Html::tag('th', 'Кол-во блоков:') . Html::tag('td', Html::tag('p', $this["num_of_printing_block$key"]), $tdWP));
                return Html::tag('table', Html::tag('tbody', $rVal), [
                            'class' => 'table block']);
                break;
            case 1:
                $rVal = Html::tag('tr', Html::tag('th', Html::tag('div', Html::tag('span', 'Блок')), [
                                    'rowspan' => 5]));
                $rVal .= Html::tag('tr', Html::tag('th', 'Цвет:') . Html::tag('td', Html::tag('p', $this->materialColumn('Цвет', $id)), $tdWP));
                $rVal .= Html::tag('tr', Html::tag('th', 'Формат блоков:') . Html::tag('td', Html::tag('p', $this["format_printing_block$key"]), $tdWP));
                $rVal .= Html::tag('tr', Html::tag('th', 'Запас:') . Html::tag('td', Html::tag('p', $this["block_zapas$key"]), $tdWP));
                $rVal .= Html::tag('tr', Html::tag('th', 'Кол-во блоков:') . Html::tag('td', Html::tag('p', $this["num_of_printing_block$key"]), $tdWP));
                return Html::tag('table', Html::tag('tbody', $rVal), [
                            'class' => 'table block']);
                break;
            case 2:
                $rVal = Html::tag('tr', Html::tag('th', Html::tag('div', Html::tag('span', 'Блок')), [
                                    'rowspan' => 5]));
                $rVal .= Html::tag('tr', Html::tag('th', 'Состав:') . Html::tag('td', Html::tag('p', $this->materialColumn('Cостав')), $tdWP));
                $rVal .= Html::tag('tr', Html::tag('th', 'Мест нанесения:') . Html::tag('td', Html::tag('p', $this->place_of_application_block), $tdWP));
                $rVal .= Html::tag('tr', Html::tag('th', 'Запас:') . Html::tag('td', Html::tag('p', $this["block_zapas$key"]), $tdWP));
                $rVal .= Html::tag('tr', Html::tag('th', 'Кол-во блоков:') . Html::tag('td', Html::tag('p', $this["num_of_printing_block$key"]), $tdWP));
                return Html::tag('table', Html::tag('tbody', $rVal), [
                            'class' => 'table block']);
                break;
            case 3:
            case 5:
                $dt = Json::decode($this->colors);
                $rVal = Html::tag('tr', Html::tag('th', Html::tag('div', Html::tag('span', 'Блок')), [
                                    'rowspan' => $dt['lam1_id'] === '1' ? 4 : 2]));
                if ($dt['lam1_id'] === '1') {
                    $rVal .= Html::tag('tr', Html::tag('th', 'Ламинация:')
                                    . Html::tag('td', Html::tag('p', 'Да'))
                                    . Html::tag('td', Html::tag('p', [
                                                '1+0',
                                                '1+1',
                                                '0+1'][$dt['lam2_id']])));
                    $rVal .= Html::tag('tr', Html::tag('th', '') . Html::tag('td', Html::tag('p', $dt['lam_text']), [
                                        'colspan' => 2]));
                }
                $rVal .= Html::tag('tr', Html::tag('th', 'Способ печати:') . Html::tag('td', Html::tag('p', [
                                            'Нет',
                                            'Цифра',
                                            'Офсет',
                                            'Шёлкография'][$dt['medtodofprint_id']]), [
                                    'colspan' => 2]));
                return Html::tag('table', Html::tag('tbody', $rVal), [
                            'class' => 'table block']);
                break;
            default:
                return 'Not set';
        }
    }

    public function raskroyLista($id = 0) {
        $key = $id ? ('' . $id) : '';
        $blockOpt = Json::decode($this["material_block_format$key"] ? $this["material_block_format$key"] : '{"block":{}}');
        $reskeaOptimal = false;
        if ($this->getProductCategory() === 0) {
//            if (is_array($blockOpt['block']) && array_key_exists('var', $blockOpt['block']) && $blockOpt['block']['var'] == 2 && (int) $formatTmp[0] < (int) $formatTmp[1]) {
//                $formatTmp = $this->swapFormat($this["format_printing_block$key"]);
//            } else {
//                $formatTmp = $this["format_printing_block$key"];
//            }
            $formatTmp = $this["format_printing_block$key"];
            $format = explode('*', $formatTmp);
            if (count($format) < 2)
                $format = [0, 0];
            $liffSizeTmp = $this->formatStringProceed($this->materialColumn('Размер листа', $id));
            if (isset($blockOpt['block']) && is_array($blockOpt['block']) && array_key_exists('var', $blockOpt['block'])) {
                if ($blockOpt['block']['var'] == 2) {
                    //if ((int) $format[0] < (int) $format[1]) {
                    $formatTmp = $this->swapFormat($formatTmp);
                    //}
                } elseif ($blockOpt['block']['var'] == 3) {
                    $reskeaOptimal = true;
                    return $formatTmp . '&nbsp&nbsp(оптимально)';
                }
            }
            $liffSizes = explode('*', $liffSizeTmp);
            $format = explode('*', $formatTmp);
            if (count($liffSizes) !== 2 || count($format) !== 2)
                return '???';
            $hor = floor($liffSizes[0] / $format[0]);
            $vert = floor($liffSizes[1] / $format[1]);

            if (!$hor || !$vert) {
                $hor = floor($liffSizes[0] / $format[1]);
                $vert = floor($liffSizes[1] / $format[0]);
                if (!$hor || !$vert) {
                    return 'Проверте формат листа и блока';
                } else {
                    $dirtH = floor((int) $liffSizes[0] / $hor);
                    $dirtV = floor((int) $liffSizes[1] / $vert);
                    return $dirtV . '*' . $dirtH . ($reskeaOptimal ? '&nbsp&nbsp(оптимально)' : '');
                }
            } else {
                $dirtH = floor((int) $liffSizes[0] / $hor);
                $dirtV = floor((int) $liffSizes[1] / $vert);
//                return \yii\helpers\VarDumper::dumpAsString([
//                            '$hor'                               => $hor,
//                            '$vert'                              => $vert,
//                            '$liffSizes'                         => $liffSizes,
//                            '$format'                            => $format,
//                            '$this["format_printing_block$key"]' => $this["format_printing_block$key"],
//                            '$formatTmp'                         => $formatTmp,
//                            '$blockOpt'                          => $blockOpt
//                                ], 10, true);
                return $dirtH . '*' . $dirtV . ($reskeaOptimal ? '&nbsp&nbsp(оптимально)' : '');
            }
//            return \yii\helpers\VarDumper::dumpAsString( [
//                        '$hor'               => $hor,
//                        '$vert'              => $vert,
//                        '$liffSizes'         => $liffSizes,
//                        '$format'            => $format,
//                        '$blockOpt["block"]' => $blockOpt['block']
//                            ], 10, true );
        } else {
            return '???';
        }
    }

}
