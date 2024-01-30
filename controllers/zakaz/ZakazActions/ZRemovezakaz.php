<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZRemovezakaz
 *
 * @author Александр
 */
use Yii;
use yii\helpers\FileHelper;
use app\components\MyHelplers;
use yii\helpers\Json;
use app\models\zakaz\Zakaz;
use app\models\zakaz\ZakazDirt;
class ZRemovezakaz extends ZActionFileWorks{
    public function run(){
        if ($this->postID){
            if ($model= Zakaz::findOne($this->postID)){
                //deleteIndex
                $zipPath= MyHelplers::zipPathToStore($model->id);
                $list=MyHelplers::zipListById($model->id);
                $fRemoved=0;
                $dRemoved=0;
                $rVal=['status'=>'ok'];
                if (file_exists($zipPath)){
                    $zip=new \ZipArchive();
                    if ($zip->open($zipPath)===true){
                        $rVal['list']=$list;
                        $rVal['removeN']=[];
                        foreach (array_keys($list['des']) as $key){
                            $rVal['removeN'][]=$key;
                            $zip->deleteIndex($key);
                            $fRemoved++;
                        }//$list['main']
                        foreach (array_keys($list['main']) as $key){
                            $rVal['removeN'][]=$key;
                            $zip->deleteIndex($key);
                            $fRemoved++;
                        }
                        $cnt=$zip->numFiles;
                        $zip->close();
                        if (!$cnt) unlink ($zipPath);
                        $model->delete();
                        $tmpP=Yii::getAlias('@temp/'.MyHelplers::translit(Yii::$app->user->identity->realname));
                        $tmpDirList=[];
                        if (file_exists($tmpP)){
                            $tmpDirList= MyHelplers::myscandir($tmpP);
                            foreach ($tmpDirList as $name){
                                if (is_dir($tmpP.'/'.$name)){
                                    if (file_exists($tmpP.'/'.$name.'/content/content.json')){
                                        $zak=Json::decode(file_get_contents($tmpP.'/'.$name.'/content/content.json'));
                                        if ($zak['id']&&(int)$zak['id']===(int)$model->id){
                                            $dRemoved++;
                                            FileHelper::removeDirectory($tmpP.'/'.$name);
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        return ['status'=>'error','errorText'=>'Невозможно открыть zip архив "'.$zipPath.'"!'];
                    }
                }else{
                    $model->delete();
                }
                if ($tmpMenu=$this->controller->showMessage(true)){
                    $rVal['label']=$tmpMenu['label'];
                    $rVal['menu']= \yii\bootstrap\Dropdown::widget(['items'=>$tmpMenu['items'],'options'=>['class'=>'nav-mess-main']]);
                    
                }
                $rVal['zipRemoved']=$fRemoved;
                $rVal['tempDirRemoved']=$dRemoved;
                return $rVal;
            }else{
                return ['status'=>'error','errorText'=>'Заказ №'.$this->postID.' не найден!'];
            }
        }elseif ($dirtId=Yii::$app->request->post('dirtId')){
            if ($dirEl=ZakazDirt::findOne((int) $dirtId)){
                $dirEl->delete();
                return ['status'=>'ok'];
            }else
                return ['status'=>'error',"Запись №$dirtId не найдена в черновиках"];
        }else{
            return ['status'=>'error','errorText'=>'Не передан номер заказа!'];
        }
    }
}
