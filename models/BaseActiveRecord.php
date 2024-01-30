<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use Yii;
use yii\web\UploadedFile;
use app\components\MyHelplers;
/**
 * Description of BaseActiveRecord
 *
 * @author Александр
 */
class BaseActiveRecord extends \yii\db\ActiveRecord {
    public static $tax_systemList=['общая (с НДС)','упрощенная (без НДС)'];
    public $attrL=null;
    protected function renderEl($options){
        $tag=$options['tag'];
        unset ($options['tag']);
        if (isset($options['content'])){
            $content=$options['content'];
            unset($options['content']);
        }else
            $content=null;
        return \yii\helpers\Html::tag($tag,$content,$options);
    }
    public function renderFileInput($fieldName,$options=[],$renderImg=true,$renderRemoveButton=true,$wrapImg=false){
        $options= \yii\helpers\ArrayHelper::merge([
            'containerOptions'=>[
                'tag'=>'div',
                'class'=>'dialog-control-group'
            ],
            'labelOptions'=>[
                'tag'=>'label',
                'class'=>'dialog-col-160',
                'content'=>$this->attributeLabels()[$fieldName]
            ],
            'inputOptions'=>[
                'id'=>'openFile',
                'tag'=>'button',
                'type'=>'button',
                'class'=>'btn btn-main',
                'content'=>'Выберите файл'
            ]
        ],$options);
        if (!isset($options['hiddenInputName'])) $options['hiddenInputName']=$fieldName;
        $options['containerOptions']['content']='';
        if (!isset($options['label']))
            $options['containerOptions']['content'].=$this->renderEl($options['labelOptions']);
        if (!isset($options['input']))
            $options['containerOptions']['content'].=$this->renderEl($options['inputOptions']);
        $options['containerOptions']['content'].= \yii\helpers\Html::input('file',$this->formName().'['.$options['hiddenInputName'].']',null,[
            'id'=> strtolower($this->formName().'-'.$options['hiddenInputName']),
            'style'=>'display:none;'
        ]);
        \Yii::trace('renderFileInput','Заначение renderImg=({renderImg})',['renderImg'=>$renderImg]);
        if ($renderImg){
            if ($this->$fieldName&& file_exists($this->$fieldName))
                $path=Yii::$app->assetManager->publish($this->$fieldName)[1];
            else 
                $path=Yii::$app->assetManager->publish('@app/web/pic/w-point.gif')[1];
            $opt=[];
            if ($renderImg!=='visible') $opt['style']=$this->$fieldName?'':'visibility:hidden';
            if ($wrapImg)
                $options['containerOptions']['content'].= \yii\helpers\Html::tag('div',\yii\helpers\Html::img($path,$opt),['class'=>'thumbnail']);
            else
                $options['containerOptions']['content'].= \yii\helpers\Html::img($path,$opt);
        }
        if ($renderRemoveButton){
            $opt=[
                'class'=>'glyphicon glyphicon-remove',
                'style'=>$this->$fieldName?'':'visibility:hidden',
                'role'=>'removepic',
                'data'=>['form-name'=>$this->formName(),'attr-name'=>$fieldName]
            ];
            if ($renderImg&&$renderImg!=='visible'){
                $opt['data']['hide-img']=true;
            }
            if ($this->isNewRecord) $opt['isnewrecord']='true';
            $options['containerOptions']['content'].= \yii\helpers\Html::tag('span',null,$opt);
        }
        return $this->renderEl($options['containerOptions']);
    }
    private function createPath($key){
        
        $f=$this->formName().'_'.$this->primaryKey.'_'.$this->$key->baseName . '.' . $this->$key->extension;
        $mainDir=\Yii::getAlias('@documents');
        MyHelplers::checkDirToExistAndCreate($mainDir);
        switch($key){
            case 'imgStamp':
                $sDir=\Yii::getAlias('@documents/stamps/');
                MyHelplers::checkDirToExistAndCreate($sDir);
                return $sDir.$f;
                break;
            case 'fotoFile':
                $sDir=\Yii::getAlias('@documents/fotos/');
                MyHelplers::checkDirToExistAndCreate($sDir);
                return $sDir.$f;
                break;
            case 'logoFile':
            case 'pic1File':
            case 'pic2File':
                $sDir=\Yii::getAlias('@documents/ourfirmspic/');
                MyHelplers::checkDirToExistAndCreate($sDir);
                return $sDir.$f;
                break;
            case 'imgSignatureCEO':
                $sDir=\Yii::getAlias('@documents/signatures/');
                MyHelplers::checkDirToExistAndCreate($sDir);
                return $sDir.$f;
                break;
        }
    }
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)){
            foreach ($this->saveFilesAttr() as $key=>$val){
                $fieldN=$val.'remove';
                if (isset($this->$fieldN)&&$this->$fieldN==='remove'){
                    if ($this[$val]&&file_exists($this->$val)){
                        unlink($this->$val);                      
                    }
                    $this->$val=null;  
                }
            }
            return true;
        }else
            return false;
    }
    public function saveFilesAttr(){
        return[
            'imgSignatureCEO'=>'signatureCEO',
            'imgSignatureAccountant'=>'signatureAccountant',
            'imgStamp'=>'stamp'
        ];
    }
    public function saveFiles(){
        $noError=true;
        $attr=$this->saveFilesAttr();
        $update=[];
        foreach ($attr as $key=>$val){
            if ($this->$key=UploadedFile::getInstance($this, $key)){
                if ($this->$val){
                    if (file_exists($this->$val)){
                        unlink($this->$val);
                        $this->$val=null;
                    }
                }
                $update[]=$val;
                $path=$this->createPath($key);
                if (!$this->$key->saveAs($path)){
                    $noError=false;
                    $this->addError($key,$this->$key->error);
                }else{
                    $needSave=true;
                    $this->$val=$path;
                }
            }
        }
        if (count($update))
            $this->update(false,$update);
        return $noError;
    }

    public function attributeLabels(){
        if ($this->attrL) return $this->attrL;
        foreach($this->getTableSchema($this->tableName())->columns as $col){
            $this->attrL[$col->name]=$col->comment;
        }
        return $this->attrL;
    }

}
