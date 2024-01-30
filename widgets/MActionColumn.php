<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use Yii;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
/**
 * Description of MActionColumn
 *
 * @author Александер
 */
class MActionColumn extends ActionColumn {
    public $template=false;
    public $modelKeyToConfirm=false;
    public $checkUserIdColName=false;
    public $otherParam=false;
    public $confirm='Вы уверенны что хотите удалить запись ?'; // {info} = $model[$modelKeyToConfirm]
    public $copyConfirm=false;
    public function init(){
        parent::init();
        $controllerId=Yii::$app->controller->id;
        $ident=\Yii::$app->user->identity;
        $this->visibleButtons=[
            'change' => $ident->can($controllerId.'/change'),
            'details' => $ident->can($controllerId.'/details'),
            'remove' => $ident->can($controllerId.'/remove'),
            'copy' => $ident->can($controllerId.'/copy'),
            'deschange' => $ident->can($controllerId.'/deschange')&&!\Yii::$app->user->can('change'),
        ];
        if (!$this->template)
            $this->template='{details} {change} {remove}';
    }
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Подробности'),
                    'aria-label' => Yii::t('yii', 'View'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Изменить'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Удалить'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', preg_replace('/\\{([\info\/]+)\\}/',$this->modelKeyToConfirm?$model[$this->modelKeyToConfirm]:'',$this->confirm)),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['details'])) {
            $this->buttons['details'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Подробности'),
                    'aria-label' => Yii::t('yii', 'Details'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['copy'])) {
            $this->buttons['copy'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Копировать'),
                    'aria-label' => Yii::t('yii', 'Copy'),
//                    'data-pjax' => '0',
//                    'data-method' => 'get',
                ], $this->buttonOptions);
                if ($this->copyConfirm) $options['data-confirm']=Yii::t('yii', preg_replace('/\\{([\info\/]+)\\}/',$this->modelKeyToConfirm?$model[$this->modelKeyToConfirm]:'',$this->copyConfirm));
                return Html::a('<span class="glyphicon glyphicon-file"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['change'])) {
            $this->buttons['change'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Изменить'),
                    'aria-label' => Yii::t('yii', 'Change'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                $rVal=Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                if (!$this->checkUserIdColName){
                    return $rVal;
                }else{
                    if ($model[$this->checkUserIdColName]===Yii::$app->user->identity->id){
                        return $rVal;
                    }else{
                        return '';
                    }
                }
            };
        }
        if (!isset($this->buttons['deschange'])) {
            $this->buttons['deschange'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Изменить'),
                    'aria-label' => Yii::t('yii', 'Deschange'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['remove'])) {
            $this->buttons['remove'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Удалить'),
                    'aria-label' => Yii::t('yii', 'Remove'),
                    'data-confirm' => Yii::t('yii', preg_replace('/\\{([\info\/]+)\\}/',$this->modelKeyToConfirm?$model[$this->modelKeyToConfirm]:'',$this->confirm)),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
            };
        }
    }
    public function createUrl($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index, $this);
        } else {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            if (is_array($this->otherParam)){
                $params=ArrayHelper::merge($params,$this->otherParam);
            }
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;
            return Url::toRoute($params);
        }
    }
}
