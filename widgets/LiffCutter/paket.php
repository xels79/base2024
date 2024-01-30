<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paket
 *
 * @author Александер
 */
namespace app\widgets\LiffCutter;
const e='см';
const p='%';
class Paket {
    private $w,$h,$z,$error,$lifW,$lifH,$bottom,$hLifN,$horSzText,$colSpan;
    //put your code here
    function __construct($lifW,$lifH) {
        $this->scr=$screen;
        $this->error=0;
        $this->lifW=$lifW;
        $this->lifH=$lifH;
        $this->colSpan=4;
        if (isset($_POST['data']['wP']))$this->w=$_POST['data']['wP'];else $this->w=300;
        if (isset($_POST['data']['hP']))$this->h=$_POST['data']['hP'];else $this->h=400;
        if (isset($_POST['data']['zP']))$this->z=$_POST['data']['zP'];else $this->z=100;
        $this->bottom=round(($this->z*2)/3);
        $this->hLifN=$this->h+4+$this->bottom;
    }
    function errorSt(){
        return 'Необходимо уменьшить какой-нибудь размер!';
    }
    private function calcHorSz(){

        $rVal=0;
        $nW=2+$this->w+$this->z+$this->w+$this->z;
        if ($nW<$this->lifW){
            $tmp=round((2+$this->w*2+$this->z*2)/100);
            $rVal=[2*$tmp.p,$this->w*$tmp.p,$this->z*$tmp.p,$this->w*$tmp.p,$this->z*$tmp.p];
            $this->horSzText=['2',$this->w,$this->z,$this->w,$this->z];
        }elseif($nW<$this->lifW*2){
            $tmp=round((2+$this->w+$this->z)/100);
            $rVal=[2*$tmp.p,$this->w*$tmp.p,$this->z*$tmp.p];
            $this->colSpan=2;
            $this->horSzText=['2',$this->w,$this->z];
        }else{
            $this->error='Ширина требуемого листа привышает разумные пределы!';
        }
        return $rVal;
    }
    function run(){
        $tmp=$this->calcHorSz();
        $rVal= ['HorSzText'=>$this->horSzText,
                'HorSize0'=>$tmp[0],
                'bottom'=>$this->bottom,
                'VertSz'=>$this->h.e,
                'Colspan'=>$this->colSpan,
                'ColspanBott'=>$this->colSpan+1,
                'Error'=>$this->error
               ];
        $lW=array_sum($rVal['HorSzText']);
        $rVal['HorSzText']=array_map(function($val){
            return $val.e;
        }, $rVal['HorSzText']);
        $rVal['wLifN']=$lW;
        $rVal['hLifN']=$this->hLifN;
        $rVal['tableH']=round(620/(($lW+5)/$this->hLifN));
       // if ($rVal['tableH']>500)$rVal['tableH']=500;
        if (!$this->error){
            if ($this->hLifN>$this->lifH&&$this->hLifN>$this->lifW){
                $rVal['Error']=$this->errorSt();
            }elseif($this->hLifN>$this->lifH&&$lW>$this->lifH) $rVal['Error']=$this->errorSt();
        }
        return $rVal;
    }
}
