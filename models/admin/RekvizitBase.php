<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\admin;

/**
 * Description of RekvizitBase
 *
 * @author Александр
 */
class RekvizitBase extends \app\models\BaseActiveRecord{
    public $signatureCEOremove=null;
    public $signatureAccountantremove=null;
    public $stampremove=null;
    public function rules() {
        return [
            [['form','kpp', 'correspondentAccount', 'okpo', 'ogrn','firm_id','inn','bik'],'integer'],
            [['name',  'address', 'consignee', 'account', 'bank', 'okved', 'ceo', 'okved','stampremove','signatureCEOremove','signatureAccountantremove'],'string'],
            [['inn'],'string','length'=>[10,10]],
            [['correspondentAccount','account'],'string','length'=>[20,20]],
            [['bik','kpp'],'string','length'=>[9,9]],
            [['name','inn'],'required','message'=>'Должно быть указано.'],
            [['inn'],'unique']
        ];
    }

}
