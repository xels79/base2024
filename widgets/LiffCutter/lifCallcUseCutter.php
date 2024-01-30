<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of liCallcUseCutter
 *
 * @author Александр
 */
namespace app\widgets\LiffCutter;
use lifCalc2;
class lifCallcUseCutter extends lifCalc2{

    public function run(){
        //$swp=false;
        if ($this->initError) return parent::run();
        if ($this->hLif>$this->wLif){
            $this->swapVar ($this->hLif, $this->wLif);
            //$this->swapVar ($this->hLifN, $this->wLifN);
          //  $swp=true;
        }
        $out='';
        $tmp=$this->calcCount($this->wLif, $this->hLif, $this->wLifN, $this->hLifN);
        $out.=$this->drawTables($tmp);
        
        $cutArea=$tmp['cutArea']+$tmp['cutW']*$tmp['h'];//+isset($tmp['tbl2'])?$tmp['tbl2']['cutArea']:0;
        //echo 'cutArea'.$cutArea;exit;
        if ($this->mm)
            $this->outPut['data']['lifSizes']=['w'=>$tmp['w'],'h'=>$tmp['h'],'wn'=>$tmp['wn'],'hn'=>$tmp['hn']];
        else
            $this->outPut['data']['lifSizes']=['w'=>round($tmp['w']/10,1),'h'=>round($tmp['h']/10,1),'wn'=>round($tmp['wn']/10,1),'hn'=>round($tmp['hn']/10,1)];
        $this->outPut['data']['tabels']=$out;
        $this->outPut['data']['pcs']=$tmp['cnt'];
        $this->outPut['data']['style']=$this->creatStyleStr($tmp);
        $this->outPut['data']['cutAreaPercent']=round($cutArea/$this->lifArea*100);
        return lifCalcBase::run();
    }
}
