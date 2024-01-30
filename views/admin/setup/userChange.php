<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="user-edit">
    <h3><?=(!($model->isNewRecord)?'Изменить параметры пользователя:':'Добавить пользователя:')?></h3>
    <?=$this->render('_userForm',['model'=>$model])?>
</div>