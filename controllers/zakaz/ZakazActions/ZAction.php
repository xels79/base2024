<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ZList
 *
 * @author Александр
 */
namespace app\controllers\zakaz\ZakazActions;
use Yii;
use app\components\MyHelplers;
use app\models\tables\DependTables;
use yii\base\Action;
use yii\helpers\FileHelper;
use app\models\admin\Post;
class ZAction extends Action{
    //put your code here
    public $postID=null;
    public function init() {
        parent::init();
        $this->postID=Yii::$app->request->post('id',null);
    }
    protected function _materialInfoByContent(&$mat,&$val){
        return MyHelplers::materialInfoByContent($mat, $val);
    }
    protected function materialInfoByContent($val){
        if ($mat= \app\models\tables\Tablestypes::findOne($val['type_id'])){
            return \yii\helpers\ArrayHelper::merge(['status'=>'ok'], $this->_materialInfoByContent($mat,$val));
        }else{
            return['status'=>'error','errorText'=>'Getfullmaterialinfo: Материал не найден!'];
        }
    }
    protected function createForSource($arr){
        $rVal=[];
        //Yii::debug($arr,'createForSource');
        foreach ($arr as $key=>$el){
            if (is_array($el)){
                if (array_key_exists('category', $el) && array_key_exists('category2', $el)){
                    $rVal[]=['label'=>$el['name'],'value'=>(int)$el['id'],'category'=>(int)$el['category'],'category2'=> \yii\helpers\Json::decode($el['category2'])];
                }elseif (array_key_exists('category', $el)){
                    $rVal[]=['label'=>$el['name'],'value'=>(int)$el['id'],'category'=>(int)$el['category']];
                }else{
                    $rVal[]=['label'=>$el['name'],'value'=>(int)$el['id']];
                }
            }else{
                $rVal[]=['label'=>$el,'value'=>(int)$key];
            }
        }
        return $rVal;
    }
    protected function pathToRemoveFileStored($tmpName){
        $tempSubF=$this->createTempPathAndCheckFolderExist($tmpName).'/content';
        if (!file_exists($tempSubF)) FileHelper::createDirectory ($tempSubF);
        return $tempSubF.'/toremove.json';        
    }
    protected function toRemoveFileStored($tmpName){
        $tempSubF=$this->pathToRemoveFileStored($tmpName);
        if (file_exists($tempSubF)){
            $tmp=file_get_contents($tempSubF);
            $val= \yii\helpers\Json::decode($tmp);
        }else{
            $val=[];
        }
        return $val;
    }
    protected function createTempPathAndCheckFolderExist($tmpName){
        if (!file_exists(Yii::getAlias('@temp'))) FileHelper::createDirectory(Yii::getAlias('@temp'));
        $tempSubF= Yii::getAlias('@temp/'.MyHelplers::translit(Yii::$app->user->identity->realname));
        if (!file_exists($tempSubF)) FileHelper::createDirectory($tempSubF);
        $tempSubF.='/'.$tmpName;
        if (!file_exists($tempSubF)) FileHelper::createDirectory($tempSubF);
        if (!file_exists($tempSubF.'/files')) FileHelper::createDirectory($tempSubF.'/files');
        if (!file_exists($tempSubF.'/content')) FileHelper::createDirectory($tempSubF.'/content');
        return $tempSubF; 
    }

}
