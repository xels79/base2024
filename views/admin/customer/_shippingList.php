<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$a_l=$model->attributeLabels();
$yn=['Нет','Да'];
?>
<div class="dialog-list form-shipping">
    <div class="d-first">
        <div>
            <div class="form-group"><label class="control-label"><?=$a_l['typeText']?></label><span class="form-control"><?=$model->typeText?></span></div>
            <div class="form-group"><label class="control-label"><?=$a_l['summ1']?></label><span class="form-control"><?=$model->summ1?></span></div>
        </div>
        <div>
            <div class="form-group"><label class="control-label"><?=$a_l['is_transport_company']?></label><span class="form-control"><?=$yn[$model->is_transport_company]?></span></div>
            <div class="form-group"><label class="control-label"><?=$a_l['sity']?></label><span class="form-control"><?=$model->sity?></span></div>
            <div class="form-group"><label class="control-label"><?=$a_l['summ2']?></label><span class="form-control"><?=$model->summ2?></span></div>
        </div>
    </div>
    <div class="d-sec">
        <span>Доставка</span>
        <div class="form-group"><label class="control-label"><?=$a_l['is_to_office']?></label><span class="form-control"><?=$yn[$model->is_to_office]?></span></div>
        <div class="form-group"><label class="control-label"><?=$a_l['is_to_production']?></label><span class="form-control"><?=$yn[$model->is_to_production]?></span></div>
        <div class="form-group"><label class="control-label"><?=$a_l['is_to_shop']?></label><span class="form-control"><?=$yn[$model->is_to_shop]?></span></div>
    </div>
</div>