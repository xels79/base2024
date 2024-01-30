<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\data\ArrayDataProvider;
//use yii\widgets\ListView;
/**
 * Description of FileList
 *
 * @author Александр
 */
class FileListDropdown extends _FileBase{
    public $size=false;
    public $formControlName=false;
    public $afterClickFunction="false";
    public function init(){
        parent::init();
        if (!$this->formControlName) $this->formControlName=$this->id.'-fCFLDD';
        if (!$this->template) $this->template='{heading}{body}';
        if (!$this->dir) $this->dir=\Yii::getAlias('@file').'/'. \app\models\Zakaz::createZakazFolderName($this->zakazId);
        //Html::addCssClass($this->options, ['dropdown']);
        $this->subFolder=$this->isInputFiles?self::inputFileFolderName:self::outputFileFolderName;
    }
    protected function renderFiles(){
        if (is_array($this->fileList)){
            $this->bodyContent=  \yii\helpers\VarDumper::dumpAsString($this->fileList, 10,true);
            $this->bodyContent=\app\widgets\ActiveDropdown::widget([
//            'selected'=>isset($values['description'])?$values['description']:false,
            'formControlID'=>$this->id.'-fcFLDD',
            'formControlName'=>$this->formControlName,
            'items'=>$this->fileList,
            'menuId'=>$this->id.'-fDD',
            'placeholder'=>'Выберите',
            'afterClickFunction'=>$this->afterClickFunction,
            'menuPullR'=>'pull-left',
            'size'=>  $this->size
        ]);

        }elseif($this->notFolderMess===''){
            $this->bodyContent=$this->notFolderMess;
        }
    }
    protected function renderHeading(){
        //if (!isset($this->headerOptions['class'])) $this->headerOptions['class']=[];
       // Html::addCssClass($this->headerOptions, ['panel-heading']);
        $this->content['heading']='';
    }
    protected function renderBody(){
        //if (!isset($this->bodyOptions['class'])) $this->bodyOptions['class']=[];
        //Html::addCssClass($this->bodyOptions, ['panel-body']);
        $this->content['body']=$this->bodyContent;
    }
}
