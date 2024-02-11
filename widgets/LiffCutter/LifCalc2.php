<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of lifCalc2
 *
 * @author Александр
 */
//use lifCalcInterface;

namespace app\widgets\LiffCutter;

use app\widgets\LiffCutter\LifCalcBase;

class LifCalc2 extends LifCalcBase {

    //put your code here

    public function __construct( $opt = [] )
    {
        parent::__construct( $opt );
        $this->info( 'Расчет без учета долевой' );
    }

    public function calcCount( $w, $h, $wn, $hn )
    {
        //echo $w.' '.$h.' '.$wn.' '.$hn;exit;
        //$bwn=$wn; $bhn=$hn ;
        $wCnt = floor( $w / $wn );
        $rVal = 0;
        $cnt = 0;
        $check = true;
        if ( $wCnt > 0 ) {
            while ($wCnt > 0 || $check) {
                if ( !$wCnt && $check ) {
                    $check = false;
                    //echo $wn.' '.$hn.'<br>';
//                    $this->swapVar($wn, $hn);
                    //echo $wn.' '.$hn.'<br>';exit;
                    $wCnt = floor( $w / $wn );
                    // echo '----Revers----<br>';
                }
                if ( !$rVal ) {
                    $rVal = $this->two_tabel( $w, $h, $wn, $hn, $wCnt );
//                    $this->stop($rVal,'calcCount:two_tabel !rVal');
//                    echo $ctn++.'<br>';
//                    print_r($rVal);
                } else {
                    $tmp = $this->two_tabel( $w, $h, $wn, $hn, $wCnt );
//                    $this->stop($tmp,'calcCount:two_tabel rVal');
//                    echo '<br>'.$ctn++.'<br>';
//                    print_r($tmp);
                    if ( $tmp['cnt'] > $rVal['cnt'] ||
                            ($tmp['cnt'] == $rVal['cnt'] && $tmp['cutArea'] < $rVal['cutArea']) )
                            $rVal = $tmp;
                    unset( $tmp );
                }
                //echo '----<br>';
                if ( $wn == $hn ) {
                    $check = false;
                    $wCnt = 0;
                } else {
                    $wCnt--;
                }
            }
            $rVal['wn'] = $rVal['tbl1']['wn']; //$wn;
            $rVal['hn'] = $rVal['tbl1']['hn']; //$hn;
            $rVal['w'] = $w;
            $rVal['h'] = $h;
            if ( isset( $rVal['tbl2'] ) ) {
                if ( $rVal['tbl2']['usefulH'] > $rVal['tbl1']['usefulH'] ) {
                    $this->swapVar( $rVal['tbl1'], $rVal['tbl2'] );
                }
            }
            $rVal['tbl1']['usefulH'] += $rVal['tbl1']['cutH']['h'];
        }
        return $rVal;
    }

    private function computeStep( $wLif, $hLif, $wLifN, $hLifN, $useAllSpace )
    {
        $rVal = [];
        $this->useAllSpace = $useAllSpace;
        $rVal['tmp1'] = $this->calcCount( $wLif, $hLif, $wLifN, $hLifN );
        $rVal['tabels'] = $this->drawTables( $rVal['tmp1'] );

        $cutArea = $rVal['tmp1']['cutArea'] + $rVal['tmp1']['cutW'] * $rVal['tmp1']['h'] + (isset( $rVal['tmp1']['tbl2'] ) ? $rVal['tmp1']['tbl2']['cutArea'] : 0);
        if ( $this->mm )
                $rVal['lifSizes'] = ['w'  => $rVal['tmp1']['w'], 'h'  => $rVal['tmp1']['h'],
                'wn' => $rVal['tmp1']['wn'], 'hn' => $rVal['tmp1']['hn']];
        else
                $rVal['lifSizes'] = ['w'  => round( $rVal['tmp1']['w'] / 10, 1 ),
                'h'  => round( $rVal['tmp1']['h'] / 10, 1 ), 'wn' => round( $rVal['tmp1']['wn'] / 10, 1 ),
                'hn' => round( $rVal['tmp1']['hn'] / 10, 1 )];
        $rVal['pcs'] = $rVal['tmp1']['cnt'];
        $rVal['style'] = $this->creatStyleStr( $rVal['tmp1'] );
        $rVal['cutAreaPercent'] = $this->lifArea ? round( $cutArea / $this->lifArea * 100 ) : 0;
        return $rVal;
    }

    public function run()
    {
        if ( $this->initError ) return parent::run();
        $this->useAllSpace = false;
        $this->outPut['data1'] = $this->computeStep( $this->wLif, $this->hLif, $this->wLifN, $this->hLifN, false );
        $this->outPut['data2'] = $this->computeStep( $this->wLif, $this->hLif, $this->hLifN, $this->wLifN, false );
        $tmp1 = $this->computeStep( $this->wLif, $this->hLif, $this->wLifN, $this->hLifN, true ); //Резать остатки
        $tmp2 = $this->computeStep( $this->wLif, $this->hLif, $this->hLifN, $this->wLifN, true ); //Резать остатки
        if ( $tmp1['pcs'] < $tmp2['pcs'] || ($tmp1['pcs'] === $tmp2['pcs'] && $tmp1['tmp1']['cutArea'] > $tmp2['tmp1']['cutArea']) ) {
            $tmp1 = $tmp2;
        }
        $this->pW = $this->pW * ($this->hLif / $this->wLif);
        //$this->pW = $this->pW - $this->pW / 100 * 40;
        if ( $this->wLif < 720 ) {
            $tmp2 = $this->computeStep( $this->hLif, $this->wLif, $this->wLifN, $this->hLifN, true ); //Резать остатки;
            if ( $tmp1['pcs'] < $tmp2['pcs'] || ($tmp1['pcs'] === $tmp2['pcs'] && $tmp1['tmp1']['cutArea'] > $tmp2['tmp1']['cutArea']) ) {
                $tmp1 = $tmp2;
            }
            $tmp2 = $this->computeStep( $this->hLif, $this->wLif, $this->hLifN, $this->wLifN, true ); //Резать остатки;
            if ( $tmp1['pcs'] < $tmp2['pcs'] || ($tmp1['pcs'] === $tmp2['pcs'] && $tmp1['tmp1']['cutArea'] > $tmp2['tmp1']['cutArea']) ) {
                $tmp1 = $tmp2;
            }
        }
        $this->outPut['data3'] = $tmp1;

        return parent::run();
    }

}
