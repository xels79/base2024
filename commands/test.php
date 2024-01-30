<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\commands;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
/**
 * Description of MetodsortController
 *Проверка
 * @author Александр
 */
class MetodsortController extends Controller{
    public $recursive='true';//weeqweqw
    protected $sub=[];//sdds
    public $test2='r';
    	function test2(){
	}
        
    public function beforeAction($action)
    {
        // your custom code here, if you want the code to run before action filters,
        // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl

        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->recursive= strtolower($this->recursive)=='false'?false:true;
        // other custom code here}{{{
        

        return true; // or false to not run the action
        /*}}{}{{{{}
         * 
         */
    }
    public function actionIndex($path=''){
        if (!$path){
            echo Console::ansiFormat('Ошибка:',[Console::FG_RED])." не указан путь.\n";
            return;
        }
        if (($newpath= realpath($path))!==false){;
            echo 'Path: ';
            echo \yii\helpers\VarDumper::dumpAsString($newpath)."\n";
            echo 'isRecursive:';
            echo \yii\helpers\VarDumper::dumpAsString($this->recursive)."\n";        
            $this->proceedPath($path,$path);
        }else{
            echo Console::ansiFormat('Ошибка:',[Console::FG_RED])." файл '$path' - не найден!\n";
        }
        
    }
    protected function subFolderStr(){
        $rVal='';
        foreach ($this->sub as $el){
            if ($rVal)$rVal.='/';
            $rVal.=$el;
        }
        return $rVal;
    }
    protected function mFSTR($str,$addOnStr,$param=[]){
        $tab='';
        for ($i=0;$i<count($this->sub);$i++) $tab.=" ";
        echo $tab.Console::ansiFormat($str,$param).$addOnStr."\n";
    }
    protected function findStr(&$content,$needle,$pos,$len){
        $hasSlash=false;
        $hasBigComment=false;
        $hasSmallComment=false;
        if ($pos<$len){
            if (!($needleLen=mb_strlen($needle))){
                return false;
            }
            $needlePos=0;
            while ($pos<$len && $needlePos<$needleLen){
                //echo $content[$pos];
                if ($content[$pos]==='/'  && !$hasBigComment  && !$hasSmallComment){
                    if ($pos+1<$len && $content[$pos+1]==='/'){ 
                        $hasSmallComment=true;
                        $pos+=2;
                    }elseif($pos+1<$len && $content[$pos+1]==='*'){
                        $hasBigComment=true;
                        $pos+=2;
                    }else $pos++;
                }elseif($hasBigComment && $content[$pos]==='/'){
                    if($pos>0 && $content[$pos-1]==='*'){
                        $hasBigComment=false;
                        $pos++;
                    }else $pos++;
                }elseif($hasSmallComment && $content[$pos]==="\n"){
                        $hasSmallComment=false;
                        $pos++;
                }elseif ($content[$pos]===$needle[0]){
                    for ($needlePos=0;$needlePos<$needleLen && $pos+$needlePos<$len && $content[$pos+$needlePos]===$needle[$needlePos];$needlePos++);
                    if ($needlePos!==$needleLen) $pos++;
                }else{
                    $pos++;
                }
            }
            if ($needlePos===$needleLen)
                return $pos;
            else
                return false;
        } else {
            return false;
        }
    }
    protected function findClassName(&$content,&$header,&$className){
        $len= mb_strlen($content);
        if (($pos= $this->findStr($content, 'class', 0, $len))===false){
            $this->mFSTR('Ошибка: ', ' класс не найден!',[Console::FG_RED]);
            Yii::$app->end();
        }
        $pos+= mb_strlen('class');
        while ($content[$pos]===' ' && $pos<$len){
            $pos++;
        }
        while ($content[$pos]!==' ' && $pos<$len){
            $className.=$content[$pos++];
        }
        while ($content[$pos]!=='{' && $pos<$len){
            $pos++;
        }
        $header= substr($content, 0, ++$pos);
        return $pos++;
    }
    protected function proceedFile($path,&$fileInfo){
        $header='';
        $className='';
        $prop=[];
        $metods=[];
        $this->mFSTR('Файл: ', $fileInfo['filename'].'.'.$fileInfo['extension'],[Console::BOLD]);
        if (($content=file_get_contents ($path))===false){
            $this->mFSTR('Ошибка чтения: ', $fileInfo['filename'].'.'.$fileInfo['extension'],[Console::FG_RED]);
            Yii::$app->end();
        }
        $len= mb_strlen($content);
//        for ($i=0;$i<$len;$i++){
//            echo $content[$i];
//        }
        $pos=$this->findClassName($content, $header, $className);
        $this->mFSTR("Заголовок:\n", '',[Console::FG_BLUE]);
        echo $header."\n";
        $this->mFSTR('Класс: ', "'$className'",[Console::FG_GREEN]);
    }
    protected function proceedDir($path){
        $this->mFSTR('Каталог: ', $this->subFolderStr().' - начало',[Console::BOLD, Console::FG_CYAN]);
        if (($dir= opendir($path))===false){
            $this->mFSTR('Ошибка чтения', " коталога: '$path'",[Console::FG_RED]);
            Yii::$app->end();
        }else{
            while (false !== ($entry = readdir($dir))) {
                if ($entry!=='.' && $entry!=='..')
                    $this->proceedPath($path.'/'.$entry,$entry);
            }
        }
        $this->mFSTR('Каталог: ', $this->subFolderStr().' - конец',[Console::BOLD, Console::FG_CYAN]);
    }
    protected function proceedPath($path,$entryName=''){
        if (!file_exists($path)){
            echo Console::ansiFormat('Ошибка:',[Console::FG_RED])." файл '$path' - не найден!\n";
            Yii::$app->end();
        }
        if (is_dir($path)){
            if ($this->recursive || !$this->sub){
                array_push($this->sub, $entryName);
                $this->proceedDir($path);
                array_pop($this->sub);
            }
        }else{
            $fileInfo= pathinfo($path);
            if ($fileInfo['extension']==='php'){
                $this->proceedFile($path, $fileInfo);
            }
        }
        
    }
    public function options($actionID) {
        return [
            'index'=>['recursive']
        ][$actionID];
    }
}
