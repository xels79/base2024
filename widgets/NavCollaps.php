<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
/**
 * Description of NavCollaps
 *
 * @author Александр
 */
class NavCollaps extends \yii\bootstrap\Collapse{
    public function renderItem($header, $item, $index)
    {
        $isActive=false;
        if (array_key_exists('content', $item)) {
            $id = $this->options['id'] . '-collapse' . $index;
            $options = ArrayHelper::getValue($item, 'contentOptions', []);
            $options['id'] = $id;
            Html::addCssClass($options, ['widget' => 'panel-collapse', 'collapse' => 'collapse']);
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            if ($encodeLabel) {
                $header = Html::encode($header);
            }
            $headerToggle = Html::a($header, '#' . $id, [
                    'class' => 'collapse-toggle',
                    'data-toggle' => 'collapse',
                    'data-parent' => '#' . $this->options['id']
                ]) . "\n";
            $header = Html::tag('h4', $headerToggle, ['class' => 'panel-title']);
            if (is_string($item['content']) || is_numeric($item['content']) || is_object($item['content'])) {
                $content = Html::tag('div', $item['content'], ['class' => 'panel-body']) . "\n";
            } elseif (is_array($item['content'])) {
                $content = MNav::widget([
                    'items'=>$item['content'],
                    'options'=>['class' => 'nav nav-bar colaps-nav'],
                    'activateItems'=>true,
                    'activateParents'=>true,
                    'encodeLabels'=>false
                ]) . "\n";
//                $content = Html::tag('div', $content, ['class' => 'panel-body']);
                if ($isActive=MNav::getWasActive()){
                    Html::addCssClass($options, 'in');
                }
                if (isset($item['footer'])) {
                    $content .= Html::tag('div', $item['footer'], ['class' => 'panel-footer']) . "\n";
                }
            } else {
                throw new InvalidConfigException('The "content" option should be a string, array or object.');
            }
            
        } else {
            throw new InvalidConfigException('The "content" option is required.');
        }
        $group = [];
        $group[] = Html::tag('div', $header, ['class' => 'panel-heading']);
        $group[] = Html::tag('div', $content, $options);
        return implode("\n", $group);
    }
}
