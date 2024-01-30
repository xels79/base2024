<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OurFirm
 *
 * @author Александр
 */
namespace app\models\admin;
class OurFirm extends \app\models\BaseActiveRecord{
    public $logoFile;
    public $pic1File;
    public $pic2File;
    public $logoremove;
    public $pic1remove;
    public $pic2remove;
    public static function tableName() {
        return 'firmOur';
    }
    public function saveFilesAttr(){
        return[
            'logoFile'=>'logo',
            'pic1File'=>'pic1',
            'pic2File'=>'pic2'
        ];
    }
    public function rules() {
        return [
            [['mainName','logoremove','pic1remove','pic2remove'],'string'],
            [['mainName'],'required','message'=>'Должно быть указано название.'],
            [['logoFile','pic1File','pic2File'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, gif'],
        ];
    }
}
