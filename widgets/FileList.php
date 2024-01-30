<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\widgets;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;
use yii\widgets\ListView;
/**
 * Description of FileList
 *
 * @author Александр
 */
class FileList extends _FileBase{

    public $maxButtonCount=10;
    public function init(){
        parent::init();
        if (!$this->template) $this->template='{heading}{body}';
        if (!$this->dir) realpath ($this->dir=\Yii::getAlias('@file').'/'. \app\models\Zakaz::createZakazFolderName($this->zakazId));
        //if (!isset($this->options['class'])) $this->options['class']=[];
        Html::addCssClass($this->options, ['panel','panel-default']);
        $this->subFolder=$this->isInputFiles?self::inputFileFolderName:self::outputFileFolderName;
    }
    protected function renderFiles(){
        if (is_array($this->fileList)){
            $dataprovider=new ArrayDataProvider([
                'allModels'=>$this->fileList,
                'pagination'=>[
                    'pageParam'=>'fPage',
                    'pageSize'=>$this->pageSize
                ]
            ]);
            $options=[
                'id'=>$this->id.'_LV',
                'pager'=>[
                    'maxButtonCount'=>$this->maxButtonCount
                ],
                'options'=>[
                    'tag'=>'ul',
                    'class'=>'list-group'
                ],
                'itemOptions'=>[
                    'tag'=>'li',
                    'class'=>'list-group-item'
                ],
                'summaryOptions'=>[
                    'tag'=>'li',
                    'class'=>'list-group-item list-group-item-heading'
                ],
                'dataProvider'=>$dataprovider,
                'itemView'=>function ($model, $key, $index, $widget){
                    $rVal=Html::tag('div',$model['html'],['class'=>'pull-left']);
                    if ($this->removeAction){
                        if (\yii::$app->user->can($this->removeAction)){
                            $rVal.=Html::tag('a',Html::tag('span',null,['class'=>'glyphicon glyphicon-remove']),[
                                'class'=>'pull-right btn btn-xs btn-danger',
                                'href'=>Url::to([$this->removeAction,'fName'=>$model['fName'],'id'=>$this->zakazId])
                            ]);
                        }
                    }
                    return $rVal;
                }
            ];
            if ($this->shortHeader){
                $options['summary']=false;
            }
            $this->bodyContent=ListView::widget($options);
        }elseif($this->bodyContent===''){
            $this->bodyContent=$this->notFolderMess;
        }
    }
    protected function renderHeading(){
        //if (!isset($this->headerOptions['class'])) $this->headerOptions['class']=[];
        Html::addCssClass($this->headerOptions, ['panel-heading']);
        if ($this->shortHeader){
            if (!isset($this->headerOptions['title']))
                $this->headerOptions['title']=$this->headerContent.' (общий размер: '.\yii::$app->formatter->asShortSize($this->totalSize,2).')';
        }
        $this->content['heading']=Html::tag('div',$this->headerContent.(!$this->shortHeader?($this->showTotalSize?('<span class="pull-right">('.(!$this->shortHeader?'общий размер: ':'').\yii::$app->formatter->asShortSize($this->totalSize,2).')</span>'):''):''),$this->headerOptions);
    }
    protected function renderBody(){
        //if (!isset($this->bodyOptions['class'])) $this->bodyOptions['class']=[];
        Html::addCssClass($this->bodyOptions, ['panel-body']);
        $this->content['body']=Html::tag('div',$this->bodyContent,$this->bodyOptions);
    }
}
