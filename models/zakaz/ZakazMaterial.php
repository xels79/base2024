<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\zakaz;

/**
 * Description of ZakazBumaga
 *
 * @author Александр
 */
use Yii;
use app\components\MyHelplers;
use app\models\Options;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\tables\Postprint;
use app\models\tables\Tablestypes;
abstract class ZakazMaterial extends ZakazPodryadview{
    private $_options=null;
    private $_pColor=null;
    private $_mateRialsDetails=null;
    private $_skipIsDelivery_date=false;
    private $ufLackVar=['Нет','1+0','1+1','0+1'];
    private function getCacheOptionKey(){
        return MyHelplers::hashString(Yii::$app->user->identity->id.'technicalsOpt'.Yii::$app->id);
    }
    private function optionsDefault($matStructure){
        return [
                'values'=>[],
                'availabel'=>$matStructure?ArrayHelper::index($matStructure, 'translitName'):[],
                'availabelMaterOther'=>[
                    'material_count'=>'Количество',
                    'uf_lak'=>'Уф-лак'
                ],
                'availabelZakaz'=>[
                    'format_printing_block'=>'Формат печатного блока',
                    'num_of_printing_block'=>'Кол-во печатных блоков',
                ]
        ];
    }
    private function getOptionsModel(){
        if (!$model=Options::find()->where([
            'optionid'=>'tecnikal_1'
        ])->one()){
            $model=new Options();
            $model->userid=(int)Yii::$app->user->identity->id;
            $model->optionid='tecnikal_1';
            if ($tmp= Tablestypes::find()->asArray()->all()){
                $options=$this->optionsDefault($tmp);
            }else{
                $options=$this->optionsDefault();
            }
            $model->options=$options;
        }
        return $model;
    }
     protected function getOptions($caching=true){
         if ($this->_options) return $this->_options;
        if ($caching)
            if ($val=Yii::$app->cache->get($this->getCacheOptionKey())){
                Yii::trace('Options: востановлены из кэша.','model-Zakaz');
                return $val;
            }
        Yii::trace('Options: сгенерировано.','Zakaz-list');
        $model=$this->getOptionsModel();
        Yii::$app->cache->set($this->getCacheOptionKey(),$model->options);
        $this->_options=$model->options;
        return $model->options;
    }
    private function createNotSetHelpText($matName=''){
        return 'Не настроен '.Html::a(Html::tag('span',null,['class'=>'glyphicon glyphicon-question-sign help-question']), '#', [
            'data-toggle'=>"popover",
            'title'=>"Колонка не настроена",
            'data-content'=>'<p>Зайдите на страничку '
                            .'<a href="'
                            .Url::to(['/site/tables']).'" target="_blank">'
                            .'<em>"Таблицы"</em></a>.<p>Нажмите кнопку <em>"Настройка технички"</em> и настройте колонки технички'
                            .' для материала <em>"'.$matName.'"</em></p>'
        ]);
    }
    private function createUfNotSetHelpText(){
        return 'Не настроен '.Html::a(Html::tag('span',null,['class'=>'glyphicon glyphicon-question-sign help-question']), '#', [
            'data-toggle'=>"popover",
            'title'=>"Колонка не настроена",
            'data-content'=>'<p>Зайдите на страничку '
                            .'<a href="'
                            .Url::to(['/site/tables']).'" target="_blank">'
                            .'<em>"Таблицы"</em></a>.<p>Нажмите кнопку <em>"Постпечать"</em> и добавте тип'
                            .' <em>"Уф-лак"</em>.</p>'
                            .'<p>Тогда добавив "Уф-лак" в заказ, на вкладка <em>"Параметры изделия"</em>'
                            .' раздел <em>"Постпечать"</em>, информацию можно увидеть здесь.</p>'
        ]);
    }
    public function getHeaderName($id=0){
        if (!$this->_materialAll) $this->getMaterialAll();
        return isset($this->_materialAll[$id])?$this->_materialAll[$id]['name']:'Мат. не задан';
    }
    public function getHasUfLak(){
        return $this->post_print_uf_lak!=0;
    }
    public function getHasThermalLift(){
        return $this->post_print_thermal_lift!=0;
    }
    public function getUfLakText(){
        return $this->ufLackVar[(int)$this->post_print_uf_lak];
    }
    public function getThermalLiftText(){
        return $this->ufLackVar[(int)$this->post_print_thermal_lift];
    }
    /*
     * Проверяет наш ли материал
     */
    public function isOurMaterial($id=0){
        if (!$this->_materialAll) $this->getMaterialAll();
        if (isset($this->_materialAll[$id])){
            return $this->_materialAll[$id]['supplierType']==2;
        }else{
            return false;
        }
    }
    public function materialColumn($name,$id=0){
        if (!$this->_materialAll) $this->getMaterialAll();
        Yii::debug( \yii\helpers\VarDumper::dumpAsString($this->_materialAll[$id]),'materialColumn');
        if (isset($this->_materialAll[$id])){
            if ($name==='Количество'){
                return $this->_materialAll[$id]['count'];
            }elseif($name==='Чей'){
                return $this->_materialAll[$id]['supplierTypeText'];//supplierTypeText
            }elseif(isset($this->_materialAll[$id]['struct'])){
                if (isset($this->_materialAll[$id]['struct'][$name])){
                    return $this->_materialAll[$id]['struct'][$name];
                }elseif ($name==='Размер' && isset($this->_materialAll[$id]['struct']['Размер листа'])){
                    return $this->_materialAll[$id]['struct']['Размер листа'];
                }elseif ($name==='Размер листа' && isset($this->_materialAll[$id]['struct']['Размер'])){
                    return $this->_materialAll[$id]['struct']['Размер листа'];
                }else
                    return "'$name': нет";
            }
        }else{
            return 'Матер. нет';
        }
    }
    public function getHeaderTecnikalCol($colNum=0){
        $opt=$this->getOptions();
        if ($colNum>5) $colNum=5;
        if (!$this->_materialAll) $this->getMaterialAll();
        $tmpOpt=$this->getOptions();
        if (isset($this->_materialAll[0])){
            if (isset($tmpOpt['values'][$this->_materialAll[0]['tranlitName']])&& array_key_exists($colNum, $tmpOpt['values'][$this->_materialAll[0]['tranlitName']])){
                //return \yii\helpers\VarDumper::dumpAsString($this->_materialAll[0]['structBase'],10,true);
                $key=$tmpOpt['values'][$this->_materialAll[0]['tranlitName']][$colNum];
                $key= is_numeric($key)?(int)$key:$key;
                if (is_integer($key))
                    return $this->_materialAll[0]['structBase'][$key];
                else{
                    if (array_key_exists($key, $tmpOpt['availabelMaterOther'])){
                        return $tmpOpt['availabelMaterOther'][$key];
                    }elseif(array_key_exists($key, $tmpOpt['availabelZakaz'])){
                        return $tmpOpt['availabelZakaz'][$key];
                    }else{
                        return $this->createNotSetHelpText($this->_materialAll[0]['name']);
                        //return \yii\helpers\VarDumper::dumpAsString($tmpOpt['values'][$this->_materialAll[0]['tranlitName']],10,true);
                    }
                }
            }else
                return $this->createNotSetHelpText($this->_materialAll[0]['name']);
        }else
            return 'Мат. не задан';
    }
    
    public function getHeaderTecnikalCol2(){
        return $this->getHeaderTecnikalCol(1);
    }
    public function getHeaderTecnikalCol3(){
        return $this->getHeaderTecnikalCol(2);
    }
    public function getHeaderTecnikalCol4(){
        return $this->getHeaderTecnikalCol(3);
    }
    public function getHeaderTecnikalCol5(){
        return $this->getHeaderTecnikalCol(4);
    }
    public function getHeaderTecnikalCol6(){
        return $this->getHeaderTecnikalCol(5);
    }
    public function getIsMaterialSet(){
        if (!$this->_materialAll) $this->getMaterialAll();
        return count($this->_materialAll);
    }
    private function tecnikalCol($colN=0){
        $rVal='';
        if (!$this->_materialAll) $this->getMaterialAll();
        $tmpOpt=$this->getOptions();
        if (isset($this->_materialAll[0])){
            if (is_array($tmpOpt['values'])&& array_key_exists($this->_materialAll[0]['tranlitName'],$tmpOpt['values']) && array_key_exists($colN, $tmpOpt['values'][$this->_materialAll[0]['tranlitName']])){
                //return \yii\helpers\VarDumper::dumpAsString($this->_materialAll[0]['structBase'],10,true);
                $key=$tmpOpt['values'][$this->_materialAll[0]['tranlitName']][$colN];
                $key= is_numeric($key)?(int)$key:$key;
                if (is_integer($key)){
                    return $this->_materialAll[0]['struct'][$this->_materialAll[0]['structBase'][$key]];
                }elseif(array_key_exists($key, $tmpOpt['availabelMaterOther'])){
                    if ($key==='material_count'){
                        return $this->_materialAll[0]['count'];
                    }elseif($key==='uf_lak'){
                        $tmp= \yii\helpers\Json::decode($this->post_print);
                        if (count(array_keys($tmp))){
                            if ($tmpModel= Postprint::find()->where(['name'=>'Уф-лак'])->one()){
                                if (array_key_exists($tmpModel->id, $tmp)){
                                    return $tmp[$tmpModel->id];
                                }else{
                                    return 'не задан';
                                }
                            }else{
                                return $this->createUfNotSetHelpText();
                            }
                        }else
                            return 'не задан';
                    }else{
                        return 'Хз!? error';
                    }
                }elseif(array_key_exists($tmpOpt['values'][$this->_materialAll[0]['tranlitName']][$colN], $tmpOpt['availabelZakaz'])){
                    return $this[$tmpOpt['values'][$this->_materialAll[0]['tranlitName']][$colN]];
                }else{
                    return $this->createNotSetHelpText($this->_materialAll[0]['name']);
                }
            }else{
                return $this->createNotSetHelpText($this->_materialAll[0]['name']);
            }
            
        }
        return 'Матер. не задан';
        
    }
    public function getTecnikalCol1(){
        return $this->tecnikalCol();
    }
    public function getTecnikalCol2(){
        return $this->tecnikalCol(1);
    }
    public function getTecnikalCol3(){
        return $this->tecnikalCol(2);
    }
    public function getTecnikalCol4(){
        return $this->tecnikalCol(3);
    }
    public function getTecnikalCol5(){
        return $this->tecnikalCol(4);
    }
    public function getTecnikalCol6(){
        return $this->tecnikalCol(5);
    }
    public function getMaterialCount(){
        if (!$this->_materialAll) $this->getMaterialAll();
        if (isset($this->_materialAll[0])){
            return $this->_materialAll[0]['count'];
        }else{
            return 'Мат. не зад.';
        }
    }
    public function getPostPrint(){
        if ($this->_pColor) return $this->_pColor;
        $this->_pColor= \yii\helpers\Json::decode($this->colors);
        return $this->_pColor;
    }
    private function createColorCount($key){
        $tmp=$this->getPostPrint();
        $cnt= array_key_exists($key, $tmp)?$tmp[$key]:-1;
        if ($cnt<0) return '&nbsp';
        return ($cnt==1?'CMYK':($cnt==12?'CMYK+1':($cnt?($cnt-1):'0')));        
    }
    public function getColorFaceCount(){
        return $this->createColorCount('face_id');
    }
    public function getColorBackCount(){
        return $this->createColorCount('back_id');
    }
    public function getColorSideCount(){
        return $this->createColorCount('clips_id');
    }
    public function getColorClipsCount(){
        return $this->createColorCount('side_id');
    }
    public function colorString($arr){
        $rVal='';
        if (is_array($arr))
            foreach ($arr as $el)
                $rVal.=$el?($rVal?(', '.$el):$el):'';
        return $rVal;
    }
    private function textForSuvenir($colorCount,$key,$defText){
        $tmp=$this->getPostPrint();
        if ($colorCount){
            if (isset($tmp[$key]) && $tmp[$key])
                return $tmp[$key];
            else
                return $defText;
        }else
            return '&nbsp';        
    }
    public function getColorFaceText(){
        return $this->textForSuvenir($this->getColorFaceCount(),'face_id_txt','Тело, с права');
    }
    public function getColorBackText(){
        return $this->textForSuvenir($this->getColorBackCount(),'back_id_txt','Тело, с лева');
    }
    public function getColorClipsText(){
        return $this->textForSuvenir($this->getColorClipsCount(),'side_id_txt','Клипса');
    }
    public function getColorSideText(){
        return $this->textForSuvenir($this->getColorSideCount(),'clips_id_txt','');
    }
    private function createColorString($key){
        $tmp=$this->getPostPrint();
        if ($this->getColorFaceCount()&&isset($tmp[$key]))
            return $this->colorString($tmp[$key]);
        else
            return '&nbsp';
    }
    public function getColorFaceString(){
        return $this->createColorString('face_values');
    }
    public function getColorBackString(){
        return $this->createColorString('back_values');
    }
    public function getColorClipsString(){
        return $this->createColorString('side_values');
    }
    public function getColorSideString(){
        return $this->createColorString('clips_values');
    }

    public function getPostPrintRawsNew(){
        $rVal='';
        $tmp= \yii\helpers\Json::decode($this->post_print);
        $tmpKeys= array_keys($tmp);
        if (count($tmpKeys)){
            $query= Postprint::find()->where(['id'=>$tmpKeys])->asArray()->all();
            $cnt=count($query);
            if ($cnt){
                $itNum=0;
                foreach ($query as $el){
                    $itNum++;
                    $opt=$itNum==$cnt?['class'=>'btt']:[];
                    $txt=$tmp[$el['id']]['info'];//.Html::tag('span','Выполнить к '.\Yii::$app->formatter->asDate($tmp[$el['id']]['date']),['class'=>'gr']);
                    $dtTxt=$tmp[$el['id']]['date'];
                    if (!$rVal)
                        $rVal.=Html::tag('tr',Html::tag('th','Постпечать:',['rowspan'=>$cnt,'class'=>'btt']).Html::tag('td',$el['name'],['colspan'=>3]).Html::tag('td',Html::tag ('div',$txt,['class'=>'ramka']),['colspan'=>5])
                                                                                                                                                       .Html::tag('td','Дата:').Html::tag('td',Html::tag ('div',$dtTxt,['class'=>'ramka'])),$opt);
                    else
                        $rVal.=Html::tag('tr',Html::tag('td',$el['name'],['colspan'=>3]).Html::tag('td',Html::tag ('div',$txt,['class'=>'ramka']),['colspan'=>5])
                                                                                        .Html::tag('td','Дата:').Html::tag('td',Html::tag ('div',$dtTxt,['class'=>'ramka'])),$opt);
                }
            }
        }
        return $rVal;
    }
    public function getPostPrintRaws(){
        $rVal='';
        $tmp= \yii\helpers\Json::decode($this->post_print);
        $tmpKeys= array_keys($tmp);
        if (count($tmpKeys)){
            $query= Postprint::find()->where(['id'=>$tmpKeys])->asArray()->all();
            if (count($query)){
                foreach ($query as $el){
                    $tr='<tr>';
                    $tr.='<th colspan="2">'.$el['name'].'</th>';
                    $tr.='<td colspan="7">'.$tmp[$el['id']].'</td>';
                    $rVal.=$tr.'</tr>';
                }
            }
        }
        return $rVal;
    }
    public function getSizeViewRow(){
        if (!$this->_materialAll) $this->getMaterialAll();
        if (isset($this->_materialAll[0])&&isset($this->_materialAll[0]['struct'])){
            $key='Размер';
            if (!isset($this->_materialAll[0]['struct'][$key])) $key='Формат';
            if (isset($this->_materialAll[0]['struct'][$key])){
                //$rVal= yii\helpers\VarDumper::dumpAsString($this->_materialAll[0]['struct'],10,true);
                $tmpStr= mb_strtolower(str_replace(' ', '', $this->_materialAll[0]['struct'][$key]));
                $tmpStr=str_replace ('х', '*', $tmpStr);
                $tmpStr=str_replace ('x', '*', $tmpStr);
                if (isset(self::$formats[$tmpStr])){
                    $tmpArr=self::$formats[$tmpStr];
                }else{
                    $tmpArr= explode('*', $tmpStr);
                    if (count($tmpArr)>1){
                        $tmpArr= array_slice($tmpArr, 0,2);
                        if ($tmpArr[0]>$tmpArr[1]) $tmpArr= array_reverse ($tmpArr);
                        $tmpArr[0]=(int)$tmpArr[0];
                        $tmpArr[1]=(int)$tmpArr[1];
                    }else
                        return '';
                }
                $rVal='<table class="size-view">';
                $rVal.='<tr><td></td><td>'.$tmpArr[1].'</td><td></td><td>'.$tmpArr[1].'</td></tr>';
                $rVal.='<tr><td>'.$tmpArr[0].'</td><td><div class="rectangle"></div></td><td>'.$tmpArr[0].'</td><td><div class="rectangle"></div></td></tr>';
                $rVal.='</table>';
                return Html::tag('tr',Html::tag('td',$rVal,['colspan'=>9]));
            }
        }
        return '';
    }
    public function getDDownRawWithFiles(){
        $rVal='';// yii\helpers\VarDumper::dumpAsString(MyHelplers::zipListById($this->id),10,true);
        $tmp=MyHelplers::zipListById($this->id);
        if (count($tmp['main'])){
            $rVal.= '<div class="dropdown">'
                    .'<button class="btn btn-default dropdown-toggle" type="button" id="DDownRawWithFiles1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">'
                    .'Выберете файл'
                    .'<span class="caret"></span>'
                    .'</button>'
                    .'<ul class="dropdown-menu" aria-labelledby="DDownRawWithFiles1">';
            foreach ($tmp['main'] as $key=>$val){
                $tmpV= MyHelplers::zipPublishFile((int)$key, $this->id);
                if ($tmpV['status']==='ok'){
                    $rVal.= '<li><a href="#" data-url="'.Html::encode($tmpV['url']).'" data-ext="'.$tmpV['ext'].'">'.$tmpV['basename'].'</a></li>';
                }
            }
            $rVal.='</ul>';
            //$rVal.='<tr><td colspan="9"><div class="tImg-view"></div></td></tr>';
            return Html::tag('tr',Html::tag('td',$rVal,['colspan'=>9,'class'=>'hidden-print'])).'<tr><td colspan="9"><div class="tImg-view"></div></td></tr>';
        }else
            return Html::tag('tr',Html::tag('td','Файлов нет',['colspan'=>9]));
    }
    
    public function skipNotIsNullDeliveryDate(){
        $this->_skipIsDelivery_date=true;
    }
    private function getMaterialDetails(){
        if ($this->_mateRialsDetails) return $this->_mateRialsDetails;
        if (!$this->_materialAll) $this->getMaterialAll(2,$this->_skipIsDelivery_date);
        $tmpOpt=$this->getOptions()['values'];
        $this->_mateRialsDetails=[];
        foreach ($this->_materialAll as $el){
            Yii::debug($el,'MaterialDetails');
            $val=[
                'count'=>$el['count'],
                'name'=>$el['name'],
                'postName'=>$el['postav']['name'],
                'firm_id'=>(int)$el['firm_id'],
                'delivery_date'=>$el['delivery_date'],
                'order_date'=>$el['order_date'],
                'supplierType'=>(int)$el['supplierType'],
                'info'=>'',
                'id'=>(int)$el['id'],
                'total_coast'=>(double)$el['count']*$el['coast'],
                'paid'=>$el['paid']!=0
                ];
            if (isset($tmpOpt[$el['tranlitName']])){
                foreach ($tmpOpt[$el['tranlitName']] as $v){
                    if (is_numeric($v)){
                        if (isset($el['structBase'][(int)$v])){
                            if ($val['info']) $val['info'].=', ';
                            $val['info'].=$el['struct'][$el['structBase'][(int)$v]];
                        }
                    }
                }
            }
            $this->_mateRialsDetails[]=$val;
        }
        return $this->_mateRialsDetails;
    }


    public function getMaterial_total_coast($useFilter=true){
        if (!$this->_mateRialsDetails) $this->getMaterialDetails ();
        $rVal=0;
        foreach ($this->_mateRialsDetails as $el){
            if (!$useFilter || !array_key_exists('zakaz_materials.zakaz_id.`zakaz.id`.firm_id', Yii::$app->controller->filters?Yii::$app->controller->filters:[]) || in_array($el['firm_id'], Yii::$app->controller->filters['zakaz_materials.zakaz_id.`zakaz.id`.firm_id'])!==false){
                $rVal+=$el['total_coast'];
            }
        }
        return round($rVal);

    }
    private $_hasFilter=null;
    private function _loadWhithFilter(&$target,&$el,$key,$val=null,$formater=null){
        if ($this->_hasFilter===null){
            if (isset(Yii::$app->controller->filters)){
                if (array_key_exists('material_residue_list', Yii::$app->controller->filters) && Yii::$app->controller->filters['material_residue_list'][0]){
                    $this->_hasFilter=function($v1){
                        return $v1['total_coast']>$v2['paid'];
                    };
                }elseif(array_key_exists('zakaz_materials.zakaz_id.`zakaz.id`.firm_id', Yii::$app->controller->filters)){
                    $this->_hasFilter=function($v1){
                        Yii::debug(\yii\helpers\VarDumper::dumpAsString(Yii::$app->controller->filters['zakaz_materials.zakaz_id.`zakaz.id`.firm_id']),'filters');
                        Yii::debug(\yii\helpers\VarDumper::dumpAsString($v1),'filters');
                        Yii::debug(\yii\helpers\VarDumper::dumpAsString(in_array($v1['firm_id'], Yii::$app->controller->filters['zakaz_materials.zakaz_id.`zakaz.id`.firm_id'])),'filters');
                        return in_array($v1['firm_id'], Yii::$app->controller->filters['zakaz_materials.zakaz_id.`zakaz.id`.firm_id']);
                    };
                }
            }else
                $this->_hasFilter=false;
        }
        if (is_callable($this->_hasFilter)){
            if (call_user_func($this->_hasFilter,$el))
                if ($val!==null || $key===null)
                    $target[$el['id']]=$val;
                elseif (is_string($key))
                    $target[$el['id']]=$el[$key];
                elseif (is_array($key)){
                    $target[$el['id']]=[];
                    foreach ($key as $mKey=>$subKey){
                        if (is_numeric($mKey))
                            $target[$el['id']][$subKey]=$el[$subKey];
                        else{
                            if ($mKey==='value'&& is_callable($formater)) {
                                $target[$el['id']][$mKey]=call_user_func($formater,$el[$subKey]);
                            }else{
                                $target[$el['id']][$mKey]=$el[$subKey];
                            }
                        }
                    }
                }
        }else{
                if ($val!==null || $key===null)
                    $target[$el['id']]=$val;
                elseif (is_string($key))
                    $target[$el['id']]=$el[$key];
                elseif (is_array($key)){
                    $target[$el['id']]=[];
                    foreach ($key as $mKey=>$subKey){
                        if (is_numeric($mKey))
                            $target[$el['id']][$subKey]=$el[$subKey];
                        else{
                            if ($mKey==='value'&& is_callable($formater)) {
                                $target[$el['id']][$mKey]=call_user_func($formater,$el[$subKey]);
                            }else{
                                $target[$el['id']][$mKey]=$el[$subKey];
                            }
                        }
                    }
                }
        }
    }
    private function _loadByKey($key,$val=null,$formater=null){
        if (!$this->_mateRialsDetails) $this->getMaterialDetails ();
        $rVal=[];
        foreach ($this->_mateRialsDetails as $el){
            $this->_loadWhithFilter($rVal,$el,$key,$val,$formater);
        }
        return $rVal;
    }
    public function getMaterial_firms(){
        return $this->_loadByKey('postName');
    }
    public function getMaterial_total_coast_list(){
        return $this->_loadByKey(['value'=>'total_coast','paid','firm_name'=>'postName'],null,function($val){
            return Yii::$app->formatter->asDecimal($val);
        });
    }
    public function getMaterial_residue_list(){
        return $this->_loadByKey(null,(double)$el['total_coast']-(double)($el['paid']?$el['paid']:0));
        if (!$this->_mateRialsDetails) $this->getMaterialDetails ();
        $rVal=[];
        foreach ($this->_mateRialsDetails as $el){
            $this->_loadWhithFilter($rVal,$el,null,(double)$el['total_coast']-(double)($el['paid']?$el['paid']:0));
        }
        return $rVal;
    }

    public function getMaterial_count_list(){
        return $this->_loadByKey('count');
    }
    public function getMaterial_post_list(){
        return $this->_loadByKey('postName');
    }
    /*
     * @array
     */
    public function getMaterial_paied_list(){
        if (!$this->_mateRialsDetails) $this->getMaterialDetails ();
        $rVal=[];
        foreach ($this->_mateRialsDetails as $el){
            $this->_loadWhithFilter($rVal,$el,null,$el['paid']?$el['paid']:0);
//            $rVal[$el['id']]=$el['paid']?$el['paid']:0;
        }
        return $rVal;
        
    }
    public function getMaterial_name_list(){
        return $this->_loadByKey('name');
    }
    public function getMaterial_info_list(){
        return $this->_loadByKey('info');
    }
    public function getMaterial_info_name(){
        if (!$this->_materialAll) $this->getMaterialAll(2,$this->_skipIsDelivery_date);
        $rVal=[];
        foreach ($this->_materialAll as $el){
            $rVal[]=isset($el['struct']['Название'])?$el['struct']['Название']:'';
        }
        return $rVal;
    }
    public function getMaterial_info_color(){
        if (!$this->_materialAll) $this->getMaterialAll(2,$this->_skipIsDelivery_date);
        $rVal=[];
        foreach ($this->_materialAll as $el){
            $rVal[]=isset($el['struct']['Цвет'])?$el['struct']['Цвет']:'';
        }
        return $rVal;
    }
    public function getMaterial_info_format(){
        if (!$this->_materialAll) $this->getMaterialAll(2,$this->_skipIsDelivery_date);
        $rVal=[];
        foreach ($this->_materialAll as $el){
            $rVal[]=isset($el['struct']['Формат'])?$el['struct']['Формат']:(isset($el['struct']['Размер'])?$el['struct']['Размер']:'');
        }
        return $rVal;
    }
    public function getMaterial_info_razmerlista(){
        if (!$this->_materialAll) $this->getMaterialAll(2,$this->_skipIsDelivery_date);
        $rVal=[];
        foreach ($this->_materialAll as $el){
            $rVal[]=isset($el['struct']['Размер листа'])?$el['struct']['Размер листа']:'';
        }
        return $rVal;
    }
    public function getMaterial_info_density(){
        if (!$this->_materialAll) $this->getMaterialAll(2,$this->_skipIsDelivery_date);
        $rVal=[];
        foreach ($this->_materialAll as $el){
            $rVal[]=isset($el['struct']['Плотность'])?$el['struct']['Плотность']:'';
        }
        return $rVal;
    }
    public function getMaterial_ordered(){
        if (!$this->_mateRialsDetails) $this->getMaterialDetails ();
        $rVal=[];
        foreach ($this->_mateRialsDetails as $el){
            $rVal[$el['id']]=$el['order_date'];
        }
        return $rVal;
    }
    public function getMaterial_delivery(){
        if (!$this->_mateRialsDetails) $this->getMaterialDetails ();
        $rVal=[];
        foreach ($this->_mateRialsDetails as $el){
            $rVal[$el['id']]=$el['delivery_date'];
        }
//        return $this->_materialAll;
        return $rVal;
    }
}
