<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\commands;

/**
 * Description of MiniJSHelpMain
 *
 * @author Александр
 */
use yii\console\Controller;
use yii\helpers\Console;

class MiniJSHelpMain extends Controller {

    protected $fileIn;
    protected $fileOut;
    private $f=[];
    private function findF(){
        $re = '/((private|protected|public)\s+function|function)\s*([^\(]+)\(([^\)]*)\)/ms';
        preg_match_all($re, $this->fileIn, $matches,  PREG_OFFSET_CAPTURE|PREG_SET_ORDER, 0);
        //var_dump($matches);
        $endPos= strlen($this->fileIn);
        $buffArr=[];
        foreach ($matches as $el){
            $tmp=[
                'visibility'=>$el[2][0],
                'params'=>$el[4][0],
                'content'=>''
            ];
            $hasEndMark=false;
            $hasStart=false;
            $brecked=0;
            $commStart=0;
            $hasComment=false;
            $quotation1=0;
            $quotation2=0;
            for ($i=$el[0][1]+strlen($el[0][0]);$i<$endPos && !$hasEndMark;$i++){
                $chr=$this->fileIn[$i];
                
                //Игнор коментарий
                if (!$quotation1 && !$quotation2){
                    if (!$hasComment){
                        if ($chr==="/" && $commStart<2){
                            $commStart++;
                            if ($commStart===2){
                                $hasComment=true;
                            }
                        }elseif($commStart===1){
                            if ($chr==='*'){
                                $hasComment=true;
                                $commStart=4;
                            }else{
                                $commStart=0;
                            }
                        }else{
                            $commStart=0;
                        }
                        //Игнор кавычек
                        if (!$quotation1 && !$quotation2){
                            if ($chr==="'")
                                !$quotation1=1;
                            elseif($chr==="\"")
                                !$quotation2=1;
                        }else{
                            if ($quotation1 && $chr==="'")
                                $quotation1=0;
                            elseif($quotation2 && $chr==="\"")
                                $quotation12=0;
                        }
                    }else{
                        if ($commStart>2){
                            if ($commStart===4){
                                if ($chr==='*') $commStart--;
                            }elseif($commStart===3 ){
                                if ($chr==='/'){
                                    $hasComment=false;
                                    $commStart=0;
                                }else{
                                    $commStart=4;
                                }
                            }
                        }elseif ($commStart===2 && ($chr==="\n" || $chr==="\r")){
                            $hasComment=false;
                            $commStart=0;
                        }
                    }
                }
                if ($chr==='}'&&!$hasComment && !$quotation1 && !$quotation2){
                    $tmp['content'].=$this->ansiFormat($brecked,Console::FG_GREEN);
                    $brecked--;
                }
                if ($hasStart&&!$brecked){
                    $hasEndMark=true;
                     $hasStart=false;
                }
                if ($brecked){
                    if ($hasStart){
                        if ($hasComment){
                            $tmp['content'].=$this->ansiFormat($chr,Console::BG_RED);
                        }else{
                            $tmp['content'].=$chr;//$this->ansiFormat($chr, Console::BG_BLACK);
                        }
                    }
                }
                if ($chr==='{'&&!$hasComment && !$quotation1 && !$quotation2){
                    $brecked++;
                    $hasStart=true;
                }
                //echo $chr;
            }
            $buffArr[$el[3][0]]=$tmp;
            
        }
        $keys= array_keys($buffArr);
        sort($keys);
        foreach ($keys as $key){
            $this->f[$key]=$buffArr[$key];
        }
        var_dump($this->f);
    }
    protected function _Run() {
        echo "\n";
        $this->findF();
        echo "\n";
        //fclose($this->fileIn);
        fclose($this->fileOut);
    }

}
