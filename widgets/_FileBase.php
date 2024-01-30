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
abstract class _FileBase extends BaseWidget{
    public $zakazId=null;
    public $isInputFiles=true;
    public $headerOptions=[];
    public $bodyOptions=[];
    public $headerContent='';
    public $dirNotFoundMess='Каталог не найден';
    public $isEmptyMess='Каталог пустой';
    public $notFolderMess='Указанный путь не является каталогом';
    public $dir=false;
    public $downloadAction=null;
    public $pageSize=4;
    public $showTotalSize=true;
    public $removeAction=false;
    public $shortHeader=false;
    protected $fileList=[];
    protected $totalSize=0;
    protected $bodyContent='';
    protected $subFolder;
    const inputFileFolderName='zakaz';
    const outputFileFolderName='designer';
    //const zakazClassName='app\models\Zakaz';
    private function createFileList(){        
        if (file_exists($this->dir)){
            $this->dir.='/'.$this->subFolder;
            if (file_exists($this->dir)){
                $tmp=scandir($this->dir);
                foreach($tmp as $fName){
                    if (!is_dir($this->dir.'/'.$fName)){
                        if ($this->downloadAction){
                            $size=filesize($this->dir.'/'.$fName);
                            $this->totalSize+=$size;
                            $ttl=  mb_strlen($fName)>($this->shortHeader?15:45)?$fName.' (':'';
                            $this->fileList[]=[
                                'fName'=>$fName,
                                'size'=>$size,
                                'html'=>Html::tag('a',$fName,[
                                    'href'=>Url::to([
                                        $this->downloadAction,
                                        'id'=>$this->zakazId,
                                        'fName'=>$fName,
                                        'isInputFiles'=>$this->isInputFiles
                                    ]),
                                    'download'=>true,
                                    'data-pjax'=>0,
                                    'title'=> $ttl.\yii::$app->formatter->asShortSize($size,2).($ttl?')':'')
                                ])
                                ];
                        }else{
                            $this->fileList[]=$fName;
                        }
                    }
                }
            }else{
                $this->bodyContent=$this->dirNotFoundMess.'('.$this->dir.')';
            }
        }else{
            $this->bodyContent=$this->dirNotFoundMess.'('.$this->dir.')';
        }
    }
    abstract protected function renderFiles();
    abstract protected function renderHeading();
    abstract protected function renderBody();
    public function run(){
        //$this->content.=$this->dir;
        $this->createFileList();
        $this->renderFiles();
        $this->renderHeading();
        $this->renderBody();
        return parent::run();
    }
}
