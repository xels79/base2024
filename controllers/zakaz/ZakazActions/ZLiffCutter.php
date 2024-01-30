<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

use Yii;
use app\components\MyHelplers;
use app\widgets\LiffCutter\LifCalc2;
use app\controllers\zakaz\ZakazActions\ZBumFormats;

/**
 * Description of ZLiffCutter
 *
 * @author Александр
 */
class ZLiffCutter extends ZBumFormats {

    public $format_printing_block = null;
    public $pW = null;
    public $mm = null;

    public function init() {
        parent::init();
        if (!$this->hasError) {
            if (count($this->format_printing_block = ($this->format_printing_block ? $this->format_printing_block : $this->expl(Yii::$app->request->post('format_printing_block', '')))) < 2) {
                $this->errorContent = ['error'     => true, 'errorText' => 'Не правильный формат блока',
                    'errorHead' => 'Ошибка данных', 'tmp'       => $this->format_printing_block];
                $this->hasError = true;
                return;
            };
        } else
            return;
        $options = [
            'wLif'         => (int) ($this->bumformat[0] > $this->bumformat[1] ? $this->bumformat[0] : $this->bumformat[1]),
            'hLif'         => (int) ($this->bumformat[0] > $this->bumformat[1] ? $this->bumformat[1] : $this->bumformat[0]),
            'wLifN'        => (int) $this->format_printing_block[1], //(int) ($this->format_printing_block[0] > $this->format_printing_block[1] ? $this->format_printing_block[0] : $this->format_printing_block[1]),
            'hLifN'        => (int) $this->format_printing_block[0], //(int) ($this->format_printing_block[0] > $this->format_printing_block[1] ? $this->format_printing_block[1] : $this->format_printing_block[0]),
            'tmp'          => $this->bumformat,
            'formatString' => $this->bumName
        ];
        if ($this->pW !== null)
            $options['pW'] = $this->pW;
        if ($this->mm !== null)
            $options['mm'] = $this->mm;
        $lc = new LifCalc2($options);
        //$this->retValue=[$this->bumformat,(int)($this->format_printing_block[0]>$this->format_printing_block[1]?$this->format_printing_block[0]:$this->format_printing_block[1]),(int)($this->format_printing_block[0]>$this->format_printing_block[1]?$this->format_printing_block[1]:$this->format_printing_block[0]),$lc->run()];
        $this->retValue = \yii\helpers\ArrayHelper::merge($lc->run(), ['tmp'      => $this->format_printing_block,
                    'material' => $this->bumformat]);
    }

}
