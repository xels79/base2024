<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$a_l=$model->attributeLabels();
$yn=['Нет','Да'];
?>
<span style="display: none"><?=($index+1)?></span>
<div class="dialog-list form-shipping shipping-pod shipping-pod-view">
    <div class="d-first">
        <?php if ($model->is_self_transport===0):?>
        <div>
            <div class="form-group"><label class="control-label"><?=$a_l['expense_to']?></label><span class="form-control"><?=$model->expense_to?></span></div>
            <div class="form-group"><label class="control-label"><?=$a_l['expense_from']?></label><span class="form-control"><?=$model->expense_from?></span></div>
        </div>
        <div>
            <div class="form-group"><label class="control-label">сумма доставки</label><span class="form-control"><?=$model->summ1?></span></div>
            <div class="form-group"><label class="control-label">сумма доставки</label><span class="form-control"><?=$model->summ2?></span></div>
            <div class="form-group"><label class="control-label"><?=$a_l['summ3']?></label><span class="form-control"><?=$model->summ3?></span></div>
        </div>
        <?php else:?>
            <div class="form-group"><label class="control-label"><?=$a_l['is_self_transport']?></label><span class="form-control"><?=$yn[$model->is_self_transport]?></span></div>
        <?php       endif;?>
        <div>
            <div class="form-group"><label class="control-label"><?=$a_l['is_in_office']?></label><span class="<?=$model->is_in_office?'bg-success':'bg-danger'?>"><?=$yn[$model->is_in_office]?></span></div>
            <div class="form-group"><label class="control-label"><?=$a_l['is_in_production']?></label><span class="<?=$model->is_in_production?'bg-success':'bg-danger'?>"><?=$yn[$model->is_in_production]?></span></div>
            <div class="form-group"><label class="control-label"><?=$a_l['is_in_shop']?></label><span class="<?=$model->is_in_shop?'bg-success':'bg-danger'?>"><?=$yn[$model->is_in_shop]?></span></div>
        </div>

    </div>
    
</div>