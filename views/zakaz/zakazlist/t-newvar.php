<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of t-newvar
 *
 * @author Александр
 *
 * @var $this yii\web\View
 * @var $model app\models\zakaz\Zakaz
 */
use yii\helpers\Html;
use app\components\MyHelplers;
use app\widgets\ActiveDropdown;

//echo \yii\helpers\VarDumper::dumpAsString(MyHelplers::zipListById($model->id?$model->id:0), 10, true).'<br>';
//$files=MyHelplers::zipListById($model->id?$model->id:0);
//$main=[];$des=[];
//foreach ($files['main'] as $key=>$val){
//    $main[]=['label'=>$val,'value'=>$key];
//}
//foreach ($files['des'] as $key=>$val){
//    $des[]=['label'=>$val,'value'=>$key];
//}
?>
<table id="table-left-tekhnikal" class="table table2<?= $model->productCategory ? ' table2-small-block' : '' ?>">
    <tbody>
        <tr class="btt bold">
            <td colspan="2" class="rt">Заказ №:</td>
            <td colspan="3"><div class="ramka"><?= $model->id ?></div></td>
            <td class="rt">Заказчик:</td>
            <td colspan="3"><div class="ramka"><?= $model->Zak_idText ?></div></td>
            <td colspan="2"><?= $model->is_express ? Html::tag( 'div', 'СРОЧНО', [
            'id' => 'is_expressZ' . $model->id, 'class' => 'ramka t-danger bold md'] ) : '' ?></td>
        </tr>
        <tr class="btt bold">
            <td>Срок сдачи:</td>
            <td colspan="4"><div class="ramka gr"><?= $model->deadlineText ?></div></td>
            <td>Время:</td>
            <td><div class="ramka<?= $model->deadline_time && $model->deadline_time !== '00:00' ? ' gr' : '' ?>"><?= $model->deadline_time && $model->deadline_time !== '00:00' ? $model->deadline_time : '' ?></div></td>
            <td>Менеджер:</td>
            <td colspan="2"><div class="ramka gr"><?= $model->ourmanagername ?></div></td>
            <td></td>
        </tr>
        <tr class="bold">
            <th rowspan="2" class="btt">Продукция:</th>
            <td colspan="4"><div class="ramka"><i><?= $model->production_idText ?></i></div></td>
            <td class="rt sm">Наименование:</td>
            <td colspan="3"><div class="ramka"><?= $model->name ?></div></td>
            <td colspan="2"></td>
        </tr>
        <tr class="btt bold">
            <td colspan="4">Размер готового изделия:</td>
            <td colspan="1"><div class="ramka gr"><?= $model->product_size ?></div></td>
            <td colspan="2" class="md"><?php if ( $model->re_print ): ?><div class="ramka t-danger">Перепечатка №<?= $model->re_print ?></div><?php endif; ?></td>
            <td class="md sm">Тираж:</td>
            <td class="tirage"><div class="ramka gr"><?= $model->number_of_copies ?></div><?php if ( $model->number_of_copies1 ): ?><span>и</span><div class="ramka gr"><?= $model->number_of_copies1 ?></div><?php endif; ?></td>
            <td></td>
        </tr>
<?= $this->render( 't-var-' . $model->productCategory, ['model' => $model] ); ?>
        <tr class="bold">
            <th rowspan="2" class="btt">Произ-во:</th>
            <td class="sm">Плёнка:</td>
            <td><div class="ramka" style="width: 40px">&nbsp;</div></td>
            <td class="sm">Матрица:</td>
            <td><div class="ramka" style="width: 40px">&nbsp;</div></td>
            <td class="sm">№ сито:</td>
            <td><div class="ramka" style="width: 100px">&nbsp;</div></td>
            <td class="sm">Материал:</td>
            <td><div class="ramka" style="width: 40px">&nbsp;</div></td>
            <td class="sm">Краска:</td>
            <td><div class="ramka" style="width: 40px">&nbsp;</div></td>
        </tr>
        <tr class="btt bold sm">
            <td>Печатник:</td>
            <td colspan="3"><div class="ramka">&nbsp;</div></td>
            <td colspan="2">Факт. готовых блоков:</td>
            <td colspan="2"><div class="ramka">&nbsp;</div></td>
            <td>Время печати:</td>
            <td><div class="ramka">&nbsp;</div></td>
        </tr>
        <tr>
            <td colspan="11" id="view-files<?= $model->id ?>">
            </td>
        </tr>
    </tbody>
</table>
