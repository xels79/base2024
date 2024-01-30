<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use app\widgets\Keyboard;
use yii\helpers\Url;
$keybordOptions=['class'=>'btn btn-default btn-xs'];
$showKeyb=true;//($this->context->action->id!=='index'&&$this->context->id!='site'&&$this->context->id!='admin/setup'&&$this->context->action->id!=='tables');
$keySz=30;
//$breadcrumbs=$this->params['breadcrumbs'];
$breadcrumbs=$this->context->createBreadcrumbs();
//yii\helpers\VarDumper::dump($breadcrumbs,10,true);Yii::$app->end();return;
$brcPos=$breadcrumbs['pos'];
$max=isset($breadcrumbs['values'])?(count($breadcrumbs['values'])-1):0;
//yii\helpers\VarDumper::dump($breadcrumbs['values'][$brcPos-1]['get'],10,true);
//echo '<br>';
//yii\helpers\VarDumper::dump(array_merge([$breadcrumbs['values'][$brcPos-1][0],'bcrpos'=>($brcPos-1)],$breadcrumbs['values'][$brcPos-1]['get']),10,true);
//Yii::$app->end();
?>
   
    <table class="table a-page-header hidden-print">
        <tr>
            <td<?=!$showKeyb?' style="width:0px;"':''?> class="hidden-xs">
                  <?=$showKeyb?(Html::tag('div',Keyboard::widget([
                      'buttonOptions'=>$keybordOptions,
                  ])).Html::tag('div',isset($this->params['inputForKeyb'])?$this->params['inputForKeyb']:'')):''?>
            </td>
            <td>
                <?=Html::tag('span',$this->title,['class'=>'header'])?>
            </td>
            <td>
                <div class="nav-buttons">
                    <?=Html::a('Главная',Yii::$app->homeUrl,['class'=>'btn btn-xs btn-main-font-red center-block'])?>
                </div>
                <div class="nav-buttons">
                    <span class="center-block">
                        <a href="#"><img onclick="window.location.reload(true)" src="<?=$this->assetManager->publish('@app/web/css/pic/Strelka_obnovit.png')[1]?>" width="<?=$keySz?>" height="<?=$keySz?>" /></a>
                    <?=Html::a(Html::img($this->assetManager->publish('@app/web/css/pic/Strelka_nazad'.($brcPos<1||!isset($breadcrumbs['values'][$brcPos-1])?'_disabled':'').'.png')[1], [
                        'width'=>$keySz,
                        'height'=>$keySz
                    ]), $brcPos&&isset($breadcrumbs['values'][$brcPos-1])>0?Url::to(array_merge([$breadcrumbs['values'][$brcPos-1][0],'bcrpos'=>($brcPos-1)],$breadcrumbs['values'][$brcPos-1]['get'])):'#', [
                        'disabled'=>$brcPos<1||!isset($breadcrumbs['values'][$brcPos-1]),
                        'title'=>$brcPos>0&&isset($breadcrumbs['values'][$brcPos-1])?$breadcrumbs['values'][$brcPos-1][1]:'Перехода нет'
                    ])?>
                    <?=Html::a(Html::img($this->assetManager->publish('@app/web/css/pic/Strelka_vpered'.($brcPos>-1&&$brcPos>=$max||!isset($breadcrumbs['values'][$brcPos+1])?'_disabled':'').'.png')[1], [
                        'width'=>$keySz,
                        'height'=>$keySz
                    ]), $brcPos>-1&&$brcPos<$max&&isset($breadcrumbs['values'][$brcPos+1])?Url::to(array_merge($breadcrumbs['values'][$brcPos+1]['get'],[$breadcrumbs['values'][$brcPos+1][0],'bcrpos'=>($brcPos+1)])):'#', [
                        'disabled'=>$brcPos>-1&&$brcPos>=$max||!isset($breadcrumbs['values'][$brcPos+1]),
                        'title'=>($brcPos>-1&&$brcPos<$max&&isset($breadcrumbs['values'][$brcPos+1])?$breadcrumbs['values'][$brcPos+1][1]:'Перехода нет')
                    ])?>
                    </span>
                </div>
            </td>
        </tr>
    </table>
