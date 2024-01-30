<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use Yii;
/**
 * Description of Nav
 *
 * @author Александр
 */
class MNav extends Nav{
   static private $wasActive=false;
   public function init(){
       parent::init();
       self::$wasActive=false;
   }
   public static function getWasActive(){
       return self::$wasActive;
   }
   public function renderItem($item)
    {
        $tmpActive=false;
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item,false);
        }
        if (empty($items)) {
            $items = '';
        } else {
            $linkOptions['data-toggle'] = 'dropdown';
            Html::addCssClass($options, ['widget' => 'dropdown']);
            Html::addCssClass($linkOptions, ['widget' => 'dropdown-toggle']);
            if ($this->dropDownCaret !== '') {
                $label .= ' ' . $this->dropDownCaret;
            }
            if (is_array($items)) {

                $items = $this->isChildActive($items, $tmpActive);
                if ($tmpActive){
                    Html::addCssClass($options, 'open');
//                    Html::addCssClass($options, 'active');
                    unset ($linkOptions['data-toggle']);
                    $active='';
                    self::$wasActive=true;
                }
                $items = $this->renderDropdown($items, $item);
            }
        }
        if ($active&&!$tmpActive) {
            unset ($linkOptions['data-toggle']);
            Html::addCssClass($options, 'open');
            Html::addCssClass($options, 'active');
            self::$wasActive=true;
        }
        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items, $options);
    }
    protected function isItemActive($item,$dep=true)
    {
        if (!$this->activateItems) {
            return false;
        }
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }
            return true;
        }
        if (isset($item['items'])&&$dep){
            $chk=false;
            foreach ($item['items'] as $it){
                if ($chk=$this->isItemActive($it)) break;
            }
            if ($chk) return true;
        }
        return false;
    }
//    protected function isChildActive($items, &$active)
//    {
//        foreach ($items as $i => $child) {
//            if (ArrayHelper::remove($items[$i], 'active', false) || $this->isItemActive($child)) {
//                Html::addCssClass($items[$i]['options'], 'active');
//                //if (is_array($items[$i]['items'])) $items[$i]['items']['active']=true;
//                if ($this->activateParents) {
//                    $active = true;
//                }
//            }
//        }
//        return $items;
//    }

}
