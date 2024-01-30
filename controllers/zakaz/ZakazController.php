<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ZakazController
 *
 * @author Александр
 */
namespace app\controllers\zakaz;
use Yii;
use app\controllers\ControllerMain;
use yii\filters\VerbFilter;
use app\models\admin\Zak;
use app\models\admin\ContactZak;
use app\models\zakaz\Zakaz;
use app\models\admin\Pod;
use app\models\admin\Post;
use \app\models\tables\DependTables;
use app\components\MyHelplers;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\helpers\Json;
use \app\controllers\ControllerTait;
class ZakazController extends Zakazbase{
    use ControllerTait;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'list'=>['post'],
                    'validate'=>['post'],
                    'addedit'=>['post'],
                    'remove'=>['post'],
                    'customerlist'=>['post'],
                    'getcustomermanager'=>['post'],
                    'smalltablelist'=>['post'],
                    'getpostrmanager'=>['post'],
                    'getpodinfo'=>['post'],
                    'getfullmaterialinfo'=>['post'],
                    'getpostinfo'=>['post'],
                    'fileupload'=>['post'],
                    'cansel'=>['post'],
                    'preaparefiletoreomove'=>['post'],
                    'getfile'=>['get','post'],
                    'storetemp'=>['post'],
                    'getstored'=>['post'],
                    'removestored'=>['post'],
                    'publishfile'=>['post'],
                    'removezakaz'=>['post'],
                    'liffCutter'=>['post'],
                    'paketcutter'=>['post'],
                    'getonematerinfo'=>['post'],
                    'copy'=>['post'],
                    'mailfile'=>['post'],
                    'mailGetZkazchikEmails'=>['post'],
                    'materialreset'=>['post'],
                    'savedraft'=>['post']
                ],
                
            ],
        ];
    }    
    public function actions() {
        return [
            'addedit'=>'app\controllers\zakaz\ZakazActions\ZAddedit',
            'savedraft'=>'app\controllers\zakaz\ZakazActions\ZDraft',
            'getfullmaterialinfo'=>'app\controllers\zakaz\ZakazActions\ZGetfullmaterialinfo',
            'fileupload'=>'app\controllers\zakaz\ZakazActions\ZFileUpload',
            'preaparefiletoreomove'=>'app\controllers\zakaz\ZakazActions\ZPreaparefiletoreomove',
            'publishfile'=>'app\controllers\zakaz\ZakazActions\ZPublishfile',
            'getstored'=>'app\controllers\zakaz\ZakazActions\ZGetstored',
            'getfile'=>'app\controllers\zakaz\ZakazActions\ZGetfile',
            'removestored'=>'app\controllers\zakaz\ZakazActions\ZRemovestored',
            'removezakaz'=>'app\controllers\zakaz\ZakazActions\ZRemovezakaz',
            'storetemp'=>'app\controllers\zakaz\ZakazActions\ZStoretemp',
            'customerlist'=>'app\controllers\zakaz\ZakazActions\ZCustomerlist',
            'smalltablelist'=>'app\controllers\zakaz\ZakazActions\ZSmalltablelist',
            'liffcutter'=>'app\controllers\zakaz\ZakazActions\ZLiffCutter',
            'paketcutter'=>'app\controllers\zakaz\ZakazActions\ZPaketCutter',
            'getonematerinfo'=>'app\controllers\zakaz\ZakazActions\ZBumFormats',
            'copy'=>'app\controllers\zakaz\ZakazActions\ZCopy',
            'mailfile'=>'app\controllers\zakaz\ZakazActions\ZMailFile',
            'mailGetZkazchikEmails'=>'app\controllers\zakaz\ZakazActions\ZMailGetZkazchikEmails',
            'materialreset'=>'app\controllers\zakaz\ZakazActions\ZMaterialReset'
        ];
    }
    public function actionGetpostrmanager(){
        if ($this->postID){
            if ($tmp= \app\models\admin\ContactPod::findOne($this->postID)){
                return ['status'=>'ok','value'=>$tmp->toArray()];
            }else
                return ['status'=>'error','errorText'=>'Менеджер с id='.$this->postID.' не найден!'];
        }else{
            return ['status'=>'error','errorText'=>'Не передан id менеджера'];
        }
    }
    public function actionGetpodinfo(){
        if ($this->postID){
            if ($tmp= \app\models\admin\Pod::findOne($this->postID)){
                return ['status'=>'ok','value'=>$tmp->toArray()];
            }else
                return ['status'=>'error','errorText'=>'Фирма с id='.$this->postID.' не найден!'];
        }else{
            return ['status'=>'error','errorText'=>'Не передан id фирмы'];
        }
    }
    public function actionGetpostinfo(){
        if ($this->postID){
            if ($tmp= Post::findOne($this->postID)){
                return ['status'=>'ok','value'=>$tmp->toArray()];
            }else
                return ['status'=>'error','errorText'=>'Фирма с id='.$this->postID.' не найден!'];
        }else{
            return ['status'=>'error','errorText'=>'Не передан id фирмы'];
        }
    }

    public function actionGetcustomermanager(){
        if ($this->postID){
            if ($tmp=ContactZak::findOne($this->postID)){
                return ['status'=>'ok','value'=>$tmp->toArray()];
            }else
                return ['status'=>'error','errorText'=>'Менеджер с id='.$this->postID.' не найден!'];
        }else{
            return ['status'=>'error','errorText'=>'Не передан id менеджера'];
        }
    }
    public function actionValidate(){
        if ($attrName=Yii::$app->request->post('attrName')){
            $model=new Zakaz;
            $model->$attrName=Yii::$app->request->post('value');
            if ($model->validate([$attrName])){
                return ['status'=>'ok','post'=>$_POST];
            }else{
                return ['status'=>'error','errors'=>$model->getErrors($attrName)];
            }
        }else{
            return ['status'=>'error','errorText'=>'Не переданны параметры'];
        }
        $model=new Zakaz;
        if ($model->load(Yii::$app->request->post())){
            return \yii\widgets\ActiveForm::validate($model);
        }else
            return '';
    }
    public function doCansel($tmpName){
        $tempSubF= Yii::getAlias('@temp/'.MyHelplers::translit(Yii::$app->user->identity->realname).'/'.$tmpName);
        if (file_exists($tempSubF)&& is_dir($tempSubF))
            FileHelper::removeDirectory($tempSubF);
    }
    public function actionCansel(){
        if ($tmpName=Yii::$app->request->post('tmpName')){
            $this->doCansel($tmpName);
            $label=false;
            if ($tmpMenu=$this->showMessage(true)){
                $label=$tmpMenu['label'];
                $tmpMenu= \yii\bootstrap\Dropdown::widget(['items'=>$tmpMenu['items'],'options'=>['class'=>'nav-mess-main']]);
            }
            return ['status'=>'ok','label'=>$label,'menu'=>$tmpMenu];
        }else{
            return ['status'=>'error','errorText'=>'Не переданно имя временного католога!'];
        }
    }
}
