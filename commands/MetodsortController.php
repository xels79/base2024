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
    public $recursive='true';
    protected $sub=[];
    public function beforeAction($action)
    {
        // your custom code here, if you want the code to run before action filters,
        // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl

        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->recursive= strtolower($this->recursive)=='false'?false:true;
        // other custom code here

        return true; // or false to not run the action
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
            //$this->proceedPath($path,$path);
        }else{
            echo Console::ansiFormat('Ошибка:',[Console::FG_RED])." файл '$path' - не найден!\n";
        }
        $this->test($newpath);
    }
    private static $cnt=0;
    public static function hM($matches){
        self::$cnt++;
        echo "match(".self::$cnt."):".PHP_EOL;
        if (count($matches)){
            foreach($matches as $m){
                echo $m.PHP_EOL;
            }
        }        
    }
    protected function test($path){
        
        if ($str=file_get_contents($path)){
            mb_ereg_replace_callback('/[:graph:*:space:*]*/function',"app\commands\MetodsortController::hM",$str);
        }
        //mb_ereg_replace_callback
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
    protected function findStr(&$content,$needleArr,$pos,$len,$toNextSpace=false){
        $hasSlash=false;
        $hasBigComment=false;
        $hasSmallComment=false;
        $hasSimbol=false;
        $varFnd=false;
        $needleSelect=false;
        $needleArr= is_array($needleArr)?$needleArr:[$needleArr];
        $bk=$pos;
        if ($pos<$len){
            $needlePos=0;
            while ($pos<$len && !$varFnd){
//                echo $content[$pos];
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
                }elseif($hasSmallComment && ($content[$pos]==="\n")){
                        $hasSmallComment=false;
                        $pos++;
                }else{
                    for ($i=0;$i<count($needleArr)&&!$varFnd;$i++){
                        $needle=$needleArr[$i];
                        $needleLen=strlen($needle);
                        if ($content[$pos]===$needle[0]){
                            $varFnd=true;
                            for ($needlePos=0;$needlePos<$needleLen && $pos+$needlePos<$len && $varFnd;$needlePos++){
                                $varFnd=$content[$pos+$needlePos]===$needle[$needlePos];
                            }
                            if ($varFnd) $needleSelect=$i;
                        }
                    }
                    if(!$hasBigComment && !$hasSmallComment  && $toNextSpace && $content[$pos]!=="\n" && $content[$pos]!=="\t"){
                        if ($content[$pos]!==' '){
                            $hasSimbol=true;
                        }elseif($hasSimbol && !$varFnd){
                            echo '1. $hasSymbjl='.\yii\helpers\VarDumper::dumpAsString($hasSimbol).' - "'.substr ( $content,$bk,15)."\"\n";
                            echo '1. $needle: "'.\yii\helpers\VarDumper::dumpAsString($needleArr)."\" - not found  '".$content[$pos]."'\n";
                            return false;
                            }
                    }
                    if (!$varFnd)
                        $pos++;
                }
            }
            if ($varFnd){
                //echo "fnd\n";
                echo '   $needle: "'.\yii\helpers\VarDumper::dumpAsString($needleArr[$needleSelect])."\" - ok '".substr ( $content,$pos,20)."'\n";
                return ['pos'=>$pos,'itemNum'=>$needleSelect];
            }else{
                echo '$needle: "'.\yii\helpers\VarDumper::dumpAsString($needleArr)."\" - not found '".substr ( $content,$pos,15)."'\n";
                return false;
            }
        } else {
            return false;
        }
    }
    protected function findClassName(&$content,&$header,&$className){
        $len= strlen($content);
        if (($pos= $this->findStr($content, 'class', 0, $len))===false){
            $this->mFSTR('Ошибка: ', ' класс не найден!',[Console::FG_RED]);
            Yii::$app->end();
        }
        $pos['pos']+= strlen('class');
        while ($content[$pos['pos']]===' ' && $pos['pos']<$len){
            $pos['pos']++;
        }
        while ($content[$pos['pos']]!==' ' && $pos['pos']<$len){
            $className.=$content[$pos['pos']++];
        }
        while ($content[$pos['pos']]!=='{' && $pos['pos']<$len){
            $pos['pos']++;
        }
        $header= substr($content, 0, ++$pos['pos']);
        return $pos['pos'];
    }
    protected function findVarOrFunction(&$prop,&$metods,&$content,$type,$pos,$len){
//            echo "findVarOrFunction $type\n";
            if (($nextPos=$this->findStr($content, ['$','function'], $pos+ strlen($type), $len,true))!==false){
                if ($nextPos['itemNum']===0){
                    if (($endPos=$this->findStr($content, ';', $nextPos['pos'], $len))){
                        $tmpS= substr($content, $nextPos['pos']+1,$endPos['pos']-$nextPos['pos']-1);
                        $tmpV=explode('=',$tmpS);
                        if (count($tmpV)<2){
                            $metods[trim($tmpV[0])]=['type'=>$type,'value'=>''];
                        }else{
                            $metods[trim($tmpV[0])]=['type'=>$type,'value'=>trim($tmpV[1])];
                        }
                        return $pos+1;
                    }
                }else{
                    return $pos+1;$nextPos['pos']+strlen('funcion')-2;
                }
                return false;
            }else{
                return false;
            }
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
        $len= strlen($content);
//        for ($i=0;$i<$len;$i++){
//            echo $content[$i];
//        }
        $pos=$this->findClassName($content, $header, $className);
        $this->mFSTR('Класс: ', "'$className'",[Console::FG_GREEN]);
        $keyWords=['public','protected','private'];
//        while ($pos<$len){
//            if (($nextPos=$this->findStr($content, $keyWords, $pos, $len))!==false){
////                echo "public $pos, $nextPos\n";
//                if (($tmp=$this->findVarOrFunction($prop, $metods, $content,$keyWords[$nextPos['itemNum']] , $nextPos['pos'], $len))!==false){
//                    $pos+=$tmp;
//                }else{
//                    $pos++;
//                }
//            }else{
//                $pos++;
//            }
//        }
        
        $hasBigComment=false;
        $hasSmallComment=false;
        $brecked=[];
        $quote=0;
        $quote=2;
        function creatItm(){
            return ['name'=>'','type'=>false,'visibiliti'=>false,'header'=>'',$param=>'',$value=>false];
        };
        $item=creatItm();
        $tmp='';
        while ($pos<$len){
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
            }elseif($hasSmallComment && ($content[$pos]==="\n")){
                    $hasSmallComment=false;
                    $pos++;
            }else{
                if (!$brecked){
                    if ($quote || $quote2 || $brecked){
                        if ($content[$pos]==='\''){
                            if (!$quote2)
                                if ($quote)
                                    $quote=0;
                                else
                                    $quote=1;
                        }elseif ($content[$pos]==='"'){
                            if (!$quote)
                                if ($quote2)
                                    $quote2=0;
                                else
                                    $quote2=1;
                            
                        }elseif ((!$quote && !$quote2) && $content[$pos]==='{'||$content[$pos]==='[') {
                            array_push($brecked, $content[$pos]);
                        }elseif ((!$quote && !$quote2) && $content[$pos]==='}'||$content[$pos]===']') {
                            if ($brecked[count($brecked)-1]===$content[$pos])
                                array_pop($brecked);
                            else{
                                $this->mFSTR('Ошибка', " неверная скобка '$content[$pos]' - ". substr($content, $pos,15),[Console::FG_RED]);
                                Yii::$app->end();
                            }
                        }else{
                            $tmp.=$content[$pos];
                        }
                        $pos++;
                    }else{
                        if ($item['visibiliti']===false){
                            if ($content[$pos]!==' '){
                                if (substr($className, $pos, strlen('public'))==='public'){
                                    $item['visibiliti']='public';
                                }elseif (substr($className, $pos, strlen('protected'))==='protected'){
                                    $item['visibiliti']='protected';
                                }elseif (substr($className, $pos, strlen('private'))==='private'){
                                    $item['visibiliti']='private';
                                }else{
                                    $item['header'].=$content[$pos++];
                                }
                            }else{
                                $pos++;
                            }
                        }elseif ($item['type']===false){
                            if ($content[$pos]!==' '){
                                if ($content[$pos]==='$'){
                                    $tmp='';
                                    $item['type']='variable';
                                    $pos++;
                                }elseif(substr($className, $pos, strlen('function'))==='function'){
                                    $item['type']='function';
                                    $tmp='';
                                    $pos+=strlen('function');
                                }
                            }else{
                                $pos++;
                            }
                        }elseif($item['type']==='variable'){
                           if  ($item['type']['value']===false){
                                if ($content[$pos]!==' '&&$content[$pos]!=='='&&$content[$pos]!==';'&&$content[$pos]!=="\n"){
                                    $$item['type']['name'].=$content[$pos];
                                }elseif ($content[$pos]==='=') {
                                    $item['type']['value']='';
                                }elseif ($content[$pos]===';') {
                                    $prop[]=$item;
                                    $item=creatItm();
                                }
                                $pos++;
                           }else{
                               if ($content[$pos]!==' '&&$content[$pos]!==';'&&$content[$pos]!=="\n"){
                                   
                               }else{
                                   if ($content[$pos]===';'){
                                       $prop[]=$item;
                                       $item=creatItm();
                                   }elseif ($content[$pos]!=="'"){
                                       $item['type']['value']="'";
                                       $quote=1;
                                   }elseif($content[$pos]!=='"'){
                                       $item['type']['value']='"';
                                       $quote2=1;
                                   }elseif($content[$pos]!=='['){
                                       array_push($brecked, '[');
                                       $item['type']['value']='[';
                                   }else{
                                       $item['type']['value'].=$content[$pos];
                                   }
                               }
                               $pos++;
                           }
                        }
                    }
                }
            }
        }            
        echo 'M:'.\yii\helpers\VarDumper::dumpAsString($metods)."\n";
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
