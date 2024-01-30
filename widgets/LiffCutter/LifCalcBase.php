<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of lifCalcBase
 *
 * @author Александр
 */

namespace app\widgets\LiffCutter;

use yii;

class LifCalcBase {

    //put your code here
    public $outPut = ['info'  => '', 'error' => false,
//                    'data'=>['pcs'=>0]
    ];
    public $wLif = 0;
    public $hLif = 0;
    public $wLifN = 0;
    public $hLifN = 0;
    public $lifAreaN = 0;
    public $lifArea = 0;
    public $cnt = 0;
    public $cutArea = 0;
    public $pW = 0;
    public $sqr = 0;
    public $hv = false;
    public $mm = true;
    public $initError = false;
    public $useAllSpace = true;   //Использовать обрезки нещадно.

    //public $equity=-1;
    public function __construct( $opt = [] )
    {
        if ( isset( $opt['wLif'] ) ) (int) $this->wLif = $opt['wLif'];
        if ( isset( $opt['hLif'] ) ) (int) $this->hLif = $opt['hLif'];
        if ( isset( $opt['wLifN'] ) ) (int) $this->wLifN = $opt['wLifN'];
        if ( isset( $opt['hLifN'] ) ) (int) $this->hLifN = $opt['hLifN'];
        if ( isset( $opt['pW'] ) ) (int) $this->pW = $opt['pW'];
        if ( isset( $opt['mm'] ) ) (bool) $this->mm = $opt['mm'];
        if ( !($this->wLif && $this->wLifN && $this->hLif && $this->hLifN && $this->pW) ) {
            if ( $data = Yii::$app->request->post( 'data' ) ) {
                if ( isset( $data['wLif'] ) ) (int) $this->wLif = $data['wLif'];
                if ( isset( $data['wLifN'] ) )
                        (int) $this->wLifN = $data['wLifN'];
                if ( isset( $data['hLif'] ) ) (int) $this->hLif = $data['hLif'];
                if ( isset( $data['hLifN'] ) )
                        (int) $this->hLifN = $data['hLifN'];
                if ( isset( $data['pW'] ) && $this->wLif )
                        $this->pW = (int) $data['pW'];
                if ( isset( $data['mm'] ) ) $this->mm = (bool) $data['mm'];
                //echo $this->sqr;exit;
                $this->outPut['error'] = ($this->wLif && $this->wLifN &&
                        $this->hLif && $this->hLifN &&
                        $this->pW) ? false : 'Данные не приняты.';
                if ( !$this->outPut['error'] ) {
                    $this->lifArea = $this->hLif * $this->wLif;
                    $this->lifAreaN = $this->hLifN * $this->wLifN;
                }
            } else {
                $this->outPut['error'] = [
                    'Ошибка передачи',
                    'opt'  => $opt,
                    'fact' => [$this->wLif, $this->wLifN, $this->hLif, $this->hLifN,
                        $this->pW, $this->mm]
                ]; //'Ошибка передачи';
                $this->initError = true;
            }
        } else {
            if ( !$this->outPut['error'] ) {
                $this->lifArea = $this->hLif * $this->wLif;
                $this->lifAreaN = $this->hLifN * $this->wLifN;
            }
        }
        if ( ($this->wLifN > $this->wLif) && (($this->wLifN > $this->hLif) || ($this->hLifN > $this->wLif)) ||
                ($this->hLifN > $this->hLif) && (($this->hLifN > $this->wLif) || ($this->wLifN > $this->hLif)) ) {
            $this->outPut['error'] = 'Размер блока привышает размер листа';
            $this->initError = true;
        }
        \Yii::trace( [
            'wLif'  => $this->wLif,
            'hLif'  => $this->hLif,
            'wLifN' => $this->wLifN,
            'hLifN' => $this->hLifN,
            'pw'    => $this->pW,
            'mm'    => $this->mm,
            'opt'   => $opt,
            'data'  => $data
                ], 'lifCalk' );
    }

    public function table( $w, $h, $wn, $hn, $t1WCnt, $c = 'hor' )
    {
        $hCnt = floor( $h / $hn );
        $wCnt = floor( $w / $wn );
        if ( ($wCnt * $wn) >= ($t1WCnt * $wn) ) {
            $rVal = ['wCnt' => $t1WCnt, 'hCnt' => $hCnt];
            $rVal['cnt'] = $t1WCnt * $hCnt;
            $rVal['cutH'] = ['h' => $h - $hCnt * $hn, 'w' => $wn * $t1WCnt];
            $rVal['usefulW'] = $wn * $t1WCnt; //+$t1WCnt;
            $rVal['usefulH'] = $hn * $hCnt;
            //if ($rVal['usefulH']>0) $rVal['usefulH']-=$hCnt;
            $rVal['cellClass'] = $c;
            $rVal['cutArea'] = $rVal['cutH']['h'] * $rVal['cutH']['w'];
            $rVal['hn'] = $hn;
            $rVal['wn'] = $wn;
            if ( $rVal['wCnt'] * $rVal['wn'] > $w || $rVal['hCnt'] * $rVal['hn'] > $h ) {
                echo 'OOOpsssaaa!!';
                exit;
            }
            return $rVal;
        } else {
            return 0;
        }
    }

    public function swapVar( &$v1, &$v2 )
    {
        $tmp = $v2;
        $v2 = $v1;
        $v1 = $tmp;
    }

    public function two_tabel( $w, $h, $wn, $hn, $t1WCnt )
    {
        $hCnt = floor( $h / $hn );
        $wCnt = floor( $w / $wn );
        $rVal['tbl1'] = $this->table( $w, $h, $wn, $hn, $t1WCnt );
        $rVal['cnt'] = $rVal['tbl1']['cnt'];
        $rVal['cutW'] = $w - $t1WCnt * $wn;
        $rVal['cutArea'] = 0;
        if ( $this->useAllSpace ) {
            if ( $t1WCnt < $wCnt ) {
                $tmp = $this->table( $w - $t1WCnt * $wn, $h, $hn, $wn, floor( ($w - $t1WCnt * $wn) / $hn ), 'vert' );
                if ( $tmp ) {
                    //$this->useAllSpace=false Вторая таблица отключена
                    $rVal['tbl2'] = $tmp;
                    $rVal['cnt'] += $rVal['tbl2']['cnt'];
                    $rVal['cutW'] = ($w - $t1WCnt * $wn) - floor( ($w - $t1WCnt * $wn) / $hn ) * $hn;
                    $rVal['cutArea'] += $rVal['tbl2']['cutArea'];
                    $rVal['cutArea'] += $rVal['cutW'];
                    $rVal['cutArea'] += $rVal['cutW'] * (($tmp['hCnt'] * $wn) + $tmp['cutH']['h']);
                    if ( $tmp['wCnt'] * $tmp['wn'] > $w || $tmp['hCnt'] * $tmp['hn'] > $h ) {
                        echo 'OOOpsss!!';
                        exit;
                    }
                    unset( $tmp );
                } else {
                    echo 'OOOpsss!!';
                    exit;
                    return 0;
                }
            } else {
                $rVal['cutArea'] += $rVal['tbl1']['cutArea'];
            }
        } else {
            $rVal['cutArea'] += $rVal['tbl1']['cutArea'];
        }
        if ( $rVal['tbl1']['wCnt'] * $rVal['tbl1']['wn'] > $w || $rVal['tbl1']['hCnt'] * $rVal['tbl1']['hn'] > $h ) {
            echo 'OOOpsssaaa!!';
            exit;
        }
        return $rVal;
    }

    public function drawTabel( &$dt, &$tbl, $tNum, &$rVal, $sqr, &$cnt )
    {
        $rVal .= '<div id="kTabel' . $tNum . '" class="kTabel" style="max-height:' . floor( ($tbl['hCnt'] * $tbl['hn'] + $tbl['cutH']['h']) * $sqr ) . 'px;">';
        for ( $y = 0; $y < $tbl['hCnt']; $y++ ) {
            $rVal .= '<div class="kTRaw" style="max-height:' .
                    ($tbl['cellClass'] == 'hor' ? $dt['hn'] * $sqr : ($dt['wn'] * $sqr)) . 'px;">';
            for ( $x = 0; $x < $tbl['wCnt']; $x++ ) {
                $rVal .= '<div id="kTD" class="' . $tbl['cellClass'] . '"';
                //echo 'sqr'.$sqr;exit;
                if ( $sqr * $tbl['hn'] > 16 && $sqr * $tbl['wn'] > (strlen( '' . ($cnt + 1) ) * 14) ) {
                    $rVal .= '>' . ++$cnt;
                } else {
                    //$cnt++;
                    $rVal .= 'data-title="' . ++$cnt . '">';
                }
                $rVal .= '</div>';
            }
            $rVal .= '</div>';
        }
        if ( $tbl['cutH']['h'] > 0 ) {
            $rVal .= '<div id="kCutH' . $tNum . '" class="kCutW"';
            $dop = '';
            if ( $sqr * $tbl['cutH']['w'] > (strlen( '' . $tbl['cutH']['w'] ) * 14) && $sqr * $tbl['cutH']['h'] > 60 ) {
                if ( $this->mm ) {
                    $dop .= '' . $tbl['cutH']['h'];
                } else {
                    $dop .= '' . round( $tbl['cutH']['h'] / 10, 1 );
                }
                if ( $sqr * $tbl['cutH']['w'] > ((strlen( '' . $tbl['cutH']['w'] ) + 2) * 14) ) {
                    $dop = '&uarr;<br>' . $dop;
                    if ( $sqr * $tbl['cutH']['w'] > ((strlen( '' . $tbl['cutH']['w'] ) + 4) * 14) ) {
                        if ( $this->mm ) $dop .= 'мм';
                        else $dop .= 'см';
                    }
                    $dop .= '<br>&darr;';
                } else
                        $rVal .= ' data-title="<p>&uarr;</p><p><span>Обрез ' . ($this->mm ? $tbl['cutH']['h'] . 'мм' : round( $tbl['cutH']['h'] / 10, 1 ) . 'см') . '</span></p><p>&darr;</p>"';
            } else
                    $rVal .= ' data-title="<p>&uarr;</p><p><span>Обрез ' . ($this->mm ? $tbl['cutH']['h'] . 'мм' : round( $tbl['cutH']['h'] / 10, 1 ) . 'см') . '</span></p><p>&darr;</p>"';
            $rVal .= '>';
            if ( $dop !== '' ) $rVal .= '<p>' . $dop . '</p>';
            $rVal .= '</div>';
        }
        $rVal .= '</div>';
    }

    public function drawTables( &$dt )
    {
        $rVal = '';

        $usefulW = $dt['tbl1']['usefulW'];
        if ( isset( $dt['tbl2'] ) ) {
            $usefulW += $dt['tbl2']['usefulW'];
            $usefulW += isset( $dt['tbl2']['cutW'] ) ? $dt['tbl2']['cutW'] : 0;
        } else {

            $usefulW += isset( $dt['tbl1']['cutW'] ) ? $dt['tbl1']['cutW'] : 0;
        }
//        if ( $this->useAllSpace ) {
//            $sqr = ($this->pW - $dt['cutW']) / $usefulW; //!!!!!!!!!!!Две таблицы
//        } else {
//            $sqr = $this->pW / $this->wLif;
//        }
        $sqr = $this->pW / $this->wLif;
        $tbl = $dt['tbl1'];
        $tNum = 1;
        $cnt = 0;
        $stop = 0;
        $mh = floor( $dt['tbl1']['usefulH'] * $sqr ); //+$dt['tbl1']['cutH']['h'];
        $mw = floor( $usefulW * $sqr );
        $rVal .= '<div class="kCut" >'; //style="max-height:'.$mh.'px !important;">';// max-width:'.$mw.'px;min-width:'.$mw.'px;">';
        while ($tbl && $stop < 2) {
            $this->drawTabel( $dt, $tbl, $tNum, $rVal, $sqr, $cnt );
            $tNum++;
            $tbl = $this->useAllSpace ? (isset( $dt['tbl2'] ) ? $dt['tbl2'] : 0) : 0; //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!Вторая таблица отключена
            $stop++;
        }
        if ( $dt['cutW'] ) {
            $rVal .= '<div id="kCutW" class="kCutW"';
            $dop = '';
            if ( $sqr * $dt['cutW'] > (strlen( '' . $dt['cutW'] ) * 14) ) {
                $dop .= $this->mm ? '' . $dt['cutW'] : '' . round( '' . $dt['cutW'] / 10, 1 );
                if ( $sqr * $dt['cutW'] > ((strlen( '' . $dt['cutW'] ) + 2) * 14) ) {
                    $dop = '&larr;' . $dop;
                    if ( $sqr * $dt['cutW'] > ((strlen( '' . $dt['cutW'] ) + 4) * 14) ) {
                        $dop .= $this->mm ? 'мм' : 'см';
                    }
                    $dop .= '&rarr;';
                } else
                        $rVal .= ' data-title="<p></p><p>&larr; Обрез ' . ($this->mm ? $dt['cutW'] . 'мм' : round( $dt['cutW'] / 10, 1 ) . 'см') . '&rarr;</p><p></p>"';
            } else
                    $rVal .= ' data-title="<p></p><p>&larr; Обрез ' . ($this->mm ? $dt['cutW'] . 'мм' : round( $dt['cutW'] / 10, 1 ) . 'см') . '&rarr;</p><p></p>"';
            $rVal .= '>';
            if ( $dop !== '' ) $rVal .= '<p>' . $dop . '</p>';
            $rVal .= '</div>';
        }
        $rVal .= '</div>';
        $this->sqr = $sqr;

        return $rVal;
    }

    public function creatStyleStr( &$dt )
    {
        $this->sqr = abs( $this->sqr );
        $px = 'px;';
        $pmgt = ' p{ margin-top:';
        $minw = 'min-width:';
        $maxw = 'max-width:';
        $minh = 'min-height:';
        $maxh = 'max-height:';
        $rVal = ' .hor{ ' . $minw . abs( floor( $dt['wn'] * $this->sqr - 2 ) ) . $px . $maxw . abs( floor( $dt['wn'] * $this->sqr - 1 ) ) . $px;
        $rVal .= $minh . abs( floor( $dt['hn'] * $this->sqr ) ) . $px . $maxh . abs( floor( $dt['hn'] * $this->sqr - 1 ) ) . $px . '}';

        $rVal .= ' .vert{ ' . $minw . abs( floor( $dt['hn'] * $this->sqr - 2 ) ) . $px . $maxw . abs( floor( $dt['hn'] * $this->sqr - 1 ) ) . $px;
        $rVal .= $minh . abs( floor( $dt['wn'] * $this->sqr ) ) . $px . $maxh . abs( floor( $dt['wn'] * $this->sqr - 1 ) ) . $px . '}';
        $rVal .= ' #kCutW{ ' . 'width:' . abs( floor( $dt['cutW'] * $this->sqr ) ) . $px . $maxw . floor( $dt['cutW'] * $this->sqr ) . $px;
        $rVal .= 'height:' . floor( $this->hLif * $this->sqr ) . 'px}';
        $rVal .= ' #kCutW' . $pmgt . (floor( $this->hLif * $this->sqr / 2 ) - 16) . 'px}';

        $rVal .= ' #kCutH1{ ' . $minh . floor( ($dt['tbl1']['cutH']['h'] * $this->sqr) - 1 ) . $px;
        $rVal .= 'height:' . floor( ($dt['tbl1']['cutH']['h'] * $this->sqr) - 1 ) . $px;
        $rVal .= 'width:100%}';
        $rVal .= ' #kCutH1' . $pmgt . floor( ($dt['tbl1']['cutH']['h'] * $this->sqr) / 2 - 36 ) . $px . '}';
//             $this->stop($dt,'creatStyleStr');
        if ( isset( $dt['tbl2'] ) ) {
            $rVal .= ' #kCutH2{ ' . $minh . floor( ($dt['tbl2']['cutH']['h'] * $this->sqr - 1 ) ) . $px;
            $rVal .= 'height:' . floor( ($dt['tbl2']['cutH']['h'] * $this->sqr - 1 ) ) . $px;
            $rVal .= 'width:100%}';
            $rVal .= ' #kCutH2' . $pmgt . floor( ($dt['tbl2']['cutH']['h'] * $this->sqr) / 2 - 36 ) . $px . '}';
        }
        return $rVal;
    }

    public function info( $val )
    {
        $this->outPut['info'] = $val;
    }

    public function error( $val )
    {
        $this->outPut['error'] = $val;
    }

    public function stop( $out, $err = 'error' )
    {
        echo json_encode( [
            'error' => $err,
            'data'  => $out
        ] );
        ob_flush();
        exit( 0 );
    }

    public function run()
    {
        return $this->outPut;
    }

}
