<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of MaterialController
 *
 * @author Александр
 */
use Yii;
use app\controllers\ControllerMain;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\MyHelplers;
use yii\filters\VerbFilter;
use app\models\tables\Tablestypes;
use app\models\tables\DependTables;
use \app\models\tables\DependTable;
use app\models\tables\MaterialsOnFirms;
use app\models\zakaz\ZakazMaterials;
use \yii\helpers\Html;

class MaterialController extends TablesController {

    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list'                     => ['post'],
                    'validate'                 => ['post'],
                    'addedit'                  => ['post'],
                    'remove'                   => ['post'],
                    'subtableslist'            => ['post'],
                    'subtablegetlist'          => ['post'],
                    'subtabladdedit'           => ['post'],
                    'getsuppliers'             => ['post'],
                    'subtableremove'           => ['post'],
                    'subtablegetallnamesfordd' => ['post'],
                ],
            ],
        ];
    }

    public function actionGetsuppliers() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $materialId = (int) Yii::$app->request->post('materialId'); //id в конечной таблице
        $parentId = (int) Yii::$app->request->post('parentId'); //id в типа материала
        $changeStateToFirmId = (int) Yii::$app->request->post('changeStateToFirmId'); //id фирмы для изменения статуса
        $coast = Yii::$app->request->post('coast', null); //Стоимость материала
        $optfrom = Yii::$app->request->post('optfrom', null); //Со скольки начинается опт
        $optcoast = Yii::$app->request->post('optcoast', null); //Стоимость опта
        $rCoast = Yii::$app->request->post('rCoast', null); //Стоимость по прайсу
        $cache=Yii::$app->cache;
        if (!$id = Yii::$app->request->post('id')) {
////??????  
            $_uTime=0;
            //Отключил кэш 13.05.2021
            $proceedCache=!$changeStateToFirmId && $coast===null && $coast===null && $optcoast===null && $rCoast===null;
            $_cacheKey=$this->generateCacheKey('subTimeKey'.$materialId.$parentId);
            if ($proceedCache){
                $_cacheDt=$cache->get($_cacheKey);
                if (is_array($_cacheDt)){
                    $_uTime=(int)\app\models\admin\Post::find()->max('`update_time`');
                    if ((int)$_cacheDt['_uTime']!=$_uTime){
                        $cache->set($_cacheKey,$_cacheDt,100000);
                        return array_merge($_cacheDt['_data'],[
                            'fromcache'=>true,
                        ]);
                    }
                }
            }else{
                $cache->delete($_cacheKey); 
            }
            $category = null; //sYii::$app->request->post( 'category' );
            if ($category !== null) {
                /*
                  $query = \app\models\tables\WorkOrproductType::find();
                  $query->rightJoin( 'WOPPost', 'WOPPost.referensId=workOrproductType.id' );
                  $query->rightJoin( 'firmPost', 'WOPPost.firm_id=firmPost.firm_id' );
                  $query->where( ['workOrproductType.category' => (int) $category] );
                  //                $query->orWhere(['category2' => (int) $category]);
                 */
                $query = \app\models\admin\Post::find()->orderBy('mainName');
                $query->leftJoin('WOPPost', 'WOPPost.firm_id=firmPost.firm_id');
                $query->leftJoin('workOrproductType', 'WOPPost.referensId=workOrproductType.id');
                $query->where(['workOrproductType.category' => (int) $category]);
                $query->select([
                    'firmPost.*',
                    'WOPPost.firm_id as WOPPost_firm_id',
                    'WOPPost.referensId as WOPPost_referensId',
                    'WOPPost.WOPid as WOPPost_WOPid',
                    'workOrproductType.*'
                ]);
            } else {
                $query = \app\models\admin\Post::find()->orderBy('mainName');
            }
            $query = \app\models\admin\Post::find()->orderBy('mainName');
            $firms = $query->asArray()->all();
            Yii::debug(['$firms' => $firms, '$category' => $category, '$query' => $query->sql], 'actionGetsuppliers');
            $rVal = ['source' => [['label' => 'Нет', 'value' => -1]], 'status' => 'ok',
                'post'   => $_POST];
            if ($changeStateToFirmId === -1 && $coast === 'no') {
                $rVal['unChecked'] = MaterialsOnFirms::deleteAll(['firm_id' => ArrayHelper::getColumn($firms, 'firm_id', [
                            ]), 'm_id'    => (int) $materialId, 'm_type'  => (int) $parentId]);
            }
            foreach ($firms as $el) {
                $toAdd = ['label' => $el['mainName'], 'value' => (int) $el['firm_id']];
                if ($pos = MaterialsOnFirms::find()->where(['firm_id' => (int) $el['firm_id'],
                            'm_id'    => (int) $materialId, 'm_type'  => (int) $parentId])->one()) {
                    $toAdd['coast']    = $pos['coast'];
                    $toAdd['optfrom']  =$pos['optfrom'];
                    $toAdd['optcoast'] =$pos['optcoast'];
                    $toAdd['rCoast']   = $pos['recomendetcoast'];
                    if ($changeStateToFirmId === (int) $el['firm_id']) {
                        if ($coast == 'no') {
                            $pos->delete();
                        } else {
                            $toUpdate=['coast','update'];
                            $pos->coast = (double) $coast;
                            if ($optfrom!==null){
                                $pos->optfrom=(int) $optfrom;
                                $toUpdate[]='optfrom';
                            }
                            if ($optcoast!==null){
                                $pos->optcoast=(double) $optcoast;
                                $toUpdate[]='optcoast';
                            }
                            if ($rCoast !== null){
                                $pos->recomendetcoast=(double) $rCoast;
                                $toUpdate[]='recomendetcoast';
                            }
                            $pos->update(true,$toUpdate);
                        }
                    } else {
                        $toAdd['selected'] = true;
                    }
                } else {
                    if ($changeStateToFirmId === (int) $el['firm_id']) {
                        $pos = new MaterialsOnFirms();
                        $pos->firm_id = $changeStateToFirmId;
                        $pos->m_id = $materialId;
                        $pos->m_type = $parentId;
                        if (!$pos->save()) {
                            return [
                                'staus'     => 'error',
                                'errorText' => 'Ошибка сохранения статуса материала',
                                'errors'    => $pos->errors
                            ];
                        }
                        $toAdd['selected'] = true;
                    }
                }
                $toAdd['curency_type'] = $el['curency_type'];
                $toAdd['curency_coast'] = $el['curency_type'] === 'RUB' ? 1 : round($this->currencies[$el['curency_type']], 2);
                $rVal['source'][] = $toAdd;
                $rVal['el'][] = [
                    '$el'                  => $el,
                    '$changeStateToFirmId' => $changeStateToFirmId,
                    $changeStateToFirmId === (int) $el['firm_id']];
            }
            if ($proceedCache){ 
                $cache->set($_cacheKey,[
                    '_uTime'=>$_uTime,
                    '_data'=>$rVal
                ],100000);
            }
            return $rVal;
        } else {
//            if ($query=MaterialsOnFirms::find()->where(['firm_id'=>$id])->asArray()->all()){
            //$cache=Yii::$app->cache;
            $_tmpTime=(int)MaterialsOnFirms::find()->max('`update`');
            $_fC=$cache->get($cache->buildKey($this->generateCacheKey('getsuppliersIDTime').$id));
            if (!($_time=(int) $cache->get($cache->buildKey($this->generateCacheKey('getsuppliersIDTime').$id))) || $_time!=$_tmpTime){
                $_time=$_tmpTime;
                $doRequestAnyWay=true;
            }else{
                $doRequestAnyWay=false;
            }
            $fromCache=true;
            if ($doRequestAnyWay
            || !($rVal=$cache->get($cache->buildKey($this->generateCacheKey('getsuppliersIDRVal').$id)))
            || !($rValOpt=$cache->get($cache->buildKey($this->generateCacheKey('getsuppliersIDRValOpt').$id)))){
                $query = MaterialsOnFirms::find()->where(['firm_id' => $id])->with('firm')->asArray()->all();
                $rVal = [];
                $rValOpt = [];
                $fromCache=false;
                foreach ($query as $el) {
                    $coast = $el['firm']['curency_type'] === 'RUB' ? $el['coast'] : round((double) $el['coast'] * round($this->currencies[$el['firm']['curency_type']], 2), 2);
                    $optCoast = $el['firm']['curency_type'] === 'RUB' ? $el['optcoast'] : round((double) $el['optcoast'] * round($this->currencies[$el['firm']['curency_type']], 2), 2);
                    $optFrom = $el['optfrom'];
                    if (array_key_exists($el['m_type'], $rVal)) {
                        $rVal[$el['m_type']][$el['m_id']] = $coast;
                        $rValOpt[$el['m_type']][$el['m_id']] = ['optcoast' => $optCoast, 'optfrom' => $optFrom, 'rCoast'=>$el['recomendetcoast']];
                    } else {
                        $rVal[$el['m_type']] = [$el['m_id'] => $coast];
                        $rValOpt[$el['m_type']] = [$el['m_id'] => ['optcoast' => $optCoast, 'optfrom' => $optFrom, 'rCoast'=>$el['recomendetcoast']]];
                    }
                }
            }
            $cache->set($cache->buildKey($this->generateCacheKey('getsuppliersIDTime').$id),$_time,100000);
            $cache->set($cache->buildKey($this->generateCacheKey('getsuppliersIDRVal').$id),$rVal,100000);
            $cache->set($cache->buildKey($this->generateCacheKey('getsuppliersIDRValOpt').$id),$rValOpt,100000);
            return ['status'       => 'ok',
                '$_tmpTime'=>$_tmpTime,
                //'$_time'=>$_time,
                '$fromCache'=>$fromCache,
                '$_fC'=>$_fC,
                //'cache_key_getsuppliersIDTime'=>\Yii::$app->cache->buildKey($this->generateCacheKey('getsuppliersIDTime').$id),
                //'$doRequestAnyWay'=>$doRequestAnyWay,
                'materials'    => \yii\helpers\Json::encode($rVal),
                'materialsOpt' => $rValOpt,
                '$query'       => $query
                ];
//            }else{
//                return ['status'=>'error','errorText'=>'Фирма с id='.$id.' не найдена.'];
//            }
        }
    }

    public function actionGetsuppliersOld() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $materialId = Yii::$app->request->post('materialId'); //id в конечной таблице
        $parentId = Yii::$app->request->post('parentId'); //id в типа материала
        $changeStateToFirmId = Yii::$app->request->post('changeStateToFirmId'); //id фирмы для изменения статуса
        if ($materialId && $parentId) {
            $select = ['firm_id', 'mainName', 'materials'];
        } else {
            $select = ['firm_id', 'mainName'];
        }
        if (!$id = Yii::$app->request->post('id')) {
            $query = \app\models\admin\Post::find()->select($select)->asArray()->all();
            $rVal = ['source' => [['label' => 'Нет', 'value' => -1]], 'status' => 'ok',
                'post'   => $_POST];
            foreach ($query as $el) {
                $toAdd = ['label' => $el['mainName'], 'value' => $el['firm_id']];
                $tmp = isset($el['materials']) ? \yii\helpers\Json::decode($el['materials']) : false;
                if (is_array($tmp)) {
                    //$toAdd['process']='yes';
                    if ($changeStateToFirmId == $el['firm_id']) {
                        if (array_key_exists($parentId, $tmp)) {
                            if (($pos = array_search($materialId, $tmp[$parentId])) !== false) {
                                unset($tmp[$parentId][$pos]);
                                if (!count($tmp[$parentId]))
                                    unset($tmp[$parentId]);
                            } else {
                                $tmp[$parentId][] = $materialId;
                            }
                        } else
                            $tmp[$parentId] = [$materialId];
                        if ($model = \app\models\admin\Post::findOne($changeStateToFirmId)) {
                            $model->materials = json_encode($tmp);
                            $model->save();
                        }
                        //$toAdd['process']='yes';
                    }
                    if (array_key_exists($parentId, $tmp) && in_array($materialId, $tmp[$parentId])) {
                        $toAdd['selected'] = true;
                    }
                }
                $rVal['source'][] = $toAdd;
//                $rVal['el']=$el;
//                $rVal['tmp']=$tmp;
            }
            return $rVal;
        } else {
            if ($model = \app\models\admin\Post::findOne($id)) {
                return ['status' => 'ok', 'materials' => $model->materials];
            } else {
                return ['status' => 'error', 'errorText' => 'Фирма с id=' . $id . ' не найдена.'];
            }
        }
    }

    private function createHtmlReportToRemove($values) {
        $html = '';
        foreach ($values as $el) {
            $html .= Html::tag('li', '№' . $el['id'] . ' ' . $el['name'], ['class' => 'list-group-item',
                        'style' => 'padding:2px 10px;font-size:0.7em;']);
        }
        return Html::tag('p', 'Материал используется в ' . count($values) . ' ' . MyHelplers::endingNums(count($values), [
                            'заказе', 'заказах', 'заказах']) . ':')
                . Html::tag('ul', $html, ['class' => 'list-group', 'style' => [
                        'max-height' => '140px',
                        'overflow-y' => 'auto'
            ]])
                . Html::tag('p', 'Всё равно продолжить удаление?');
    }

    public function actionRemove($classN = null, $tName = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($classN === null)
            $classN = Yii::$app->request->post('formName');
        if (!$classN) {
            return ['status' => 'error', 'errorText' => 'Не передано имя формы (formName)'];
        }
        $fullName = 'app\\models\\tables\\' . $classN;
        if (!$id = Yii::$app->request->post('id', false)) {
            $model = new $fullName();
        } else {
            if (!$model = $fullName::findOne((int) $id)) {
                return ['status'    => 'error', 'errorText' => 'Запись ' . $id . ' не найдена',
                    'id'        => $id];
            }
            if ($classN === 'Tablestypes') {
                if (Yii::$app->request->post('removeAnyWay')) {
                    ZakazMaterials::deleteAll(['type_id' => (int) $id]);
                    MaterialsOnFirms::deleteAll(['m_type' => (int) $id]);
                } else {
                    $query = ZakazMaterials::find()
                            ->where(['type_id' => (int) $id])
                            ->leftJoin('zakaz', 'zakaz.id=zakaz_materials.zakaz_id')
                            ->select('zakaz.name as name,zakaz.id as id')
                            ->asArray();
                    /*
                    if ($fId=(int)Yii::$app->request->post('firm_id',0)){
                        $query->andWhere([
                            'firm_id'=>$fId
                        ]);
                    }
                     * 
                     */
                    $tmp=$query->all();
                    if (count($tmp)) {
                        return ['status' => 'check', 'checkHTML' => $this->createHtmlReportToRemove($tmp)];
                    } else {
                        //return ['status' => 'check', 'checkHTML' => $this->createHtmlReportToRemove($tmp)];
                        if ($fId){
                            MaterialsOnFirms::deleteAll(['m_type' => (int) $id]);
                        }else{
                            MaterialsOnFirms::deleteAll(['m_type' => (int) $id]);
                        }
                    }
                }
            }
        }
        $tmp = DependTables::getDependsTable($model->translitName, $model->struct);
//        $struct= \yii\helpers\Json::encode($model->struct);
//        return['status'=>'error','struct'=>$tmp];
        $connection = new \yii\db\Connection([
            'dsn'      => 'mysql:host=localhost;dbname=u0931523_base_asterion',
            'username' => 'u0931523_b_aster',
            'password' => '0A9o6I7z',
            'charset'  => 'utf8',
        ]);
        $transaction = $connection->beginTransaction();
        try {
            for ($i = count($tmp) - 1; $i > -1; $i--) {
                if (in_array($tmp[$i]['fullname'], Yii::$app->db->schema->tableNames))
                    $connection->createCommand()->dropTable($tmp[$i]['fullname'])->execute();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return ['status' => 'error', 'errorText' => $e->message];
        }
        $model->delete();
        return ['status' => 'ok'];
        return ['status' => 'error', 'errorText' => 'Удаление невозможно'];
    }

    public function actionSubtableslist() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cache=Yii::$app->cache;
        $id = (int) Yii::$app->request->post('id', null);
        $_k=$this->generateCacheKey('actionSubtableslist'.$id);
        if ($id === null) {
            return ['status' => 'error', 'errorText' => 'Не передан первичный ключ (id)'];
        }
        $_cData=$cache->get($_k);
        $_cTime = (int) Tablestypes::find()->max('`update_time`');
        if (!is_array($_cData) || $cData['time']!=$cTime){
            if (!$model = Tablestypes::findOne((int) $id)) {
                return ['status'    => 'error', 'errorText' => 'Запись ' . $id . ' не найдена',
                    'id'        => $id];
            }
            $tmp = DependTables::getDependsTable($model->translitName, $model->struct);

            $rVal = [
                'status' => 'ok',
                'tables' => [],
                'struct' => $model->struct
            ];
            foreach ($tmp as $el) {
                $rVal['tables'][] = [
                    'rusname'  => $el['rusname'],
                    'fullname' => $el['fullname'],
                ];
            }
            $_cData=[
                'time'=>$_cTime,
                'data'=>$rVal
            ];
        }else{
            $rVal=array_merge($_cData['data'],['fromCache'=>true]);
        }
        $cache->set($_k,$_cData);
        return $rVal;
    }

    public function actionSubtablegetallnamesfordd() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //fr();
        if ($tableName = Yii::$app->request->post('formName')) {
            $tmp = \app\models\tables\DependTable::createObject($tableName)
                    ->find()
                    ->select('name')
                    ->distinct()
                    ->all();
            return [
                'status' => 'ok',
                'source' => count($tmp) ? ArrayHelper::getColumn($tmp, 'name') : [
                ]
            ];
        } else {
            return ['status' => 'error', 'errorText' => 'Не передано имя таблицы (formName)'];
        }
    }

    public function actionSubtablegetlist() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $zakmaterial = Yii::$app->request->post('zakmaterial');
        if ($tableName = Yii::$app->request->post('formName')) {
            if (($refer = Yii::$app->request->post(DependTables::$reference)) && !$zakmaterial) {
                $query = \app\models\tables\DependTable::createObject($tableName);
                return ['status' => 'ok', 'result' => $query->find()->where([DependTables::$reference => $refer])->all()];
            } else {
                if (!$zakmaterial || !is_array($zakmaterial) || count($zakmaterial) == 0)
                    return ['status' => 'error', 'errorText' => 'Не переданны параметры'];
                else {
                    $matId = array_keys($zakmaterial)[0];
                    if ($materialStruct = Tablestypes::findOne($matId)) {
                        $tblTrancelitN = DependTables::dependsTablesNamesFromRus(explode(DependTables::$delimmer, $tableName)[2],
                                        \yii\helpers\Json::decode($materialStruct->struct));
                        $prev = false;
                        $query = null;
                        $cnt = count($tblTrancelitN);
                        $orderBy = [];
                        foreach ($tblTrancelitN as $el) {
                            $cnt--;
                            if ($prev) {
                                $query->leftJoin($el, (string) ('`' . $el . '`.`' . DependTables::$reference . '`=`' . $prev . '`.`' . DependTables::$pKey . '`'));
                                Yii::debug('LeftJoin(' . $el . ',' . (string) ('`' . $el . '`.`' . DependTables::$reference . '`=`' . $prev . '`.`' . DependTables::$pKey . '`') . ')', 'material');
                                $prev = $el;
                            }
                            if ($el === $tableName && !$prev) {
                                $query = DependTable::createObject($el)
                                        ->find();
//                                if ($cnt) //Проверим что последняя таблица и добавим article
                                $query->select(['id'   => (string) ('`' . $el . '`.`' . DependTables::$pKey . '`'),
                                    'name' => (string) ('`' . $el . '`.`name`')]);
                                Yii::debug('select', 'material');
                                Yii::debug(['id'   => (string) ('`' . $el . '`.`' . DependTables::$pKey . '`'),
                                    'name' => (string) ('`' . $el . '`.`name`')], 'material');
                                $orderBy[(string) ('`' . $el . '`.`name`')] = 'asc';
//                                else
//                                    $query->select(['id'=>$el.'.'.DependTables::$pKey,'name'=>$el.'.name','article'=>$el.'.article']);
                                $prev = $el;
                                $tmpKey = (string) ('`' . $el . '`.`' . DependTables::$reference . '`');
                                Yii::debug('where', 'material');
                                Yii::debug(["$tmpKey" => $refer], 'material');
                                if ($refer)
                                    $query->where(["$tmpKey" => $refer]);
                            }
                        }
                        $query->orderBy($orderBy);
                        $tmpKey = '`' . $prev . '`.`' . DependTables::$pKey . '`';
                        if ($refer)
                            $query->andWhere(["$tmpKey" => $zakmaterial[$matId]]);
                        else
                            $query->where(["$tmpKey" => $zakmaterial[$matId]]);
                        Yii::debug($refer ? 'andWhere' : 'where', 'material');
                        Yii::debug(["$tmpKey" => $zakmaterial[$matId]], 'material');
                        return ['status' => 'ok', 'result' => $query->distinct()->asArray()->all()];
                    } else {
                        return ['status' => 'error', 'errorText' => 'Материал с id=' . array_keys($zakmaterial)[0] . ' не найден!'];
                    }
                }
            }
        } else {
            return ['status' => 'error', 'errorText' => 'Не передано имя таблицы (formName)'];
        }
    }

    public function actionSubtabladdedit() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$tName = Yii::$app->request->post('formName')) {
            return ['status' => 'error', 'errorText' => 'Не передано имя формы (formName)'];
        }
        return parent::actionAddedit('DependTable', strtolower($tName));
    }

    private function stRmove(&$tList, $posInList, $ref_id, int &$rmObj, int &$rmInTbl) {

        $referensColName = DependTables::$reference;
        $query = DependTable::createObject($tList[$posInList]['fullname'])->findAll([
            "$referensColName" => $ref_id]); //->where(["$referensColName"=>$ref_id])->all();
        $rmInTbl++;
        foreach ($query as $el) {
            if ($posInList < count($tList) - 1) {
                $this->stRmove($tList, $posInList + 1, $el->id, $rmObj, $rmInTbl);
            }
            //if ($el->delete()) $rmObj++;
        }
        $rmObj += DependTable::createObject($tList[$posInList]['fullname'])->deleteAll([
            "$referensColName" => $ref_id]);
    }

    private function checkAllChains() {

        $qeryPost = \app\models\admin\Post::find()->all();
        foreach ($qeryPost as $el) {
            $tmpMatList = \yii\helpers\Json::decode($el->materials);
            $needToSave = false;
            if (count($tmpMatList)) {
                $tmpMKeys = array_keys($tmpMatList);
                for ($i = 0; $i < count($tmpMKeys); $i++) {
                    if ($materila = Tablestypes::findOne($tmpMKeys[$i])) {
                        $tmpstruct = \yii\helpers\Json::decode($materila->struct);
                        $tNames = DependTables::dependsTablesNamesFromRus($materila->translitName, $tmpstruct);
                        $matExist = ArrayHelper::getColumn(DependTable::createObject($tNames[count($tmpstruct) - 1])
                                                ->find()
                                                ->select('id')
                                                ->where(['id' => $tmpMatList[$tmpMKeys[$i]]])
                                                ->asArray()
                                                ->all(), 'id');
                        if (count($matExist) < count($tmpMatList[$tmpMKeys[$i]])) {
                            $toRemove = array_diff($tmpMatList[$tmpMKeys[$i]], $matExist);
                            $tmpMatList[$tmpMKeys[$i]] = array_diff($tmpMatList[$tmpMKeys[$i]], $toRemove);
                            $needToSave = true;
                        }
                    } else {
                        unset($tmpMatList[$tmpMKeys[$i]]);
                        $needToSave = true;
                    }
                }
            }
            if ($needToSave) {
                $el->materials = \yii\helpers\Json::encode($tmpMatList);
                $el->save();
            }
        }
    }

    private function findAllDepends(&$tList, &$tName, $id, $treeId, $toErase = false) {
        $rVal = [];
        $i = 0;
        $query = DependTable::createObject($tList[$i]['fullname'])->find(); //->where(['`'.$tList[$i].'`.`id`'=>$id]);
        $hasMatch = false;
        if ($tName === $tList[$i]['fullname']) {
            $query->where(['`' . $tList[$i]['fullname'] . '`.`id`' => $id]);
            $hasMatch = true;
        }
        if ($hasMatch && $tName !== $tList[$i]['fullname'] && $toErase) {
            //$select=['`'.$tList[$i]['fullname'].'`.`name` as `'.$tList[$i]['fullname'].'___name`'];
            $select[] = '`' . $tList[$i]['fullname'] . '`.`id` as `' . $tList[$i]['fullname'] . '___id`';
        }
        $prev = $tList[$i++]['fullname'];
        for (; $i < count($tList);) {
            $query->join('LEFT OUTER JOIN', $tList[$i]['fullname'], '`' . $tList[$i]['fullname'] . '`.`' . DependTables::$reference . '`=`' . $prev . '`.`' . DependTables::$pKey . '`');
            if ($tName === $tList[$i]['fullname']) {
                $query->where(['`' . $tList[$i]['fullname'] . '`.`id`' => $id]);
                $hasMatch = true;
            }
            if ($hasMatch && $tName !== $tList[$i]['fullname'] && $toErase) {
                //$select[]='`'.$tList[$i]['fullname'].'`.`name` as `'.$tList[$i]['fullname'].'___name`';
                $select[] = '`' . $tList[$i]['fullname'] . '`.`id` as `' . $tList[$i]['fullname'] . '___id`';
            }
            $prev = $tList[$i++]['fullname'];
        }
        $query->leftJoin('zakaz_materials', '`zakaz_materials`.`mat_id`=`' . $tList[$i - 1]['fullname'] . '`.`id` and `zakaz_materials`.`type_id`=' . $treeId);
        if (!$toErase) {
            $query->leftJoin('zakaz', 'zakaz.id=zakaz_materials.zakaz_id');
            $query->andWhere(['not', ['zakaz.id' => null]]);
            $select[] = 'zakaz.id as id';
            $select[] = 'zakaz.name as name';
            $query->select($select);
            $rVal['mInZakaz'] = $query->asArray()->all();
        } else {
            $query->leftJoin('materials_on_firms', '`materials_on_firms`.`m_id`=`' . $tList[$i - 1]['fullname'] . '`.`id` and `materials_on_firms`.`m_type`=' . $treeId);
            $select[] = 'materials_on_firms.id as materials_on_firms___id';
            $select[] = 'zakaz_materials.id as zakaz_materials___id';
            $query->select($select);
//            $rVal['on']='zakaz_materials.mat_id='.$tList[$i-1]['fullname'].'.id';
//            $rVal['$hasMatch']=$hasMatch;
//            $rVal['query']=$query;
//            $rVal['query_all']=$query->asArray()->all();
            $rVal['toErase'] = ['subTable'           => [], 'materials_on_firms' => [
                ], 'zakaz_materials'    => [
            ]];
            foreach ($query->asArray()->all() as $el) {
                foreach ($el as $key => $val) {
                    $tmp = explode('___', $key);
                    if (count($tmp) === 2 && $val) {
                        if ($tmp[0] === 'materials_on_firms') {
                            if (!in_array((int) $val, $rVal['toErase']['materials_on_firms']))
                                $rVal['toErase']['materials_on_firms'][] = (int) $val;
                        } elseif ($tmp[0] === 'zakaz_materials') {
                            if (!in_array((int) $val, $rVal['toErase']['zakaz_materials']))
                                $rVal['toErase']['zakaz_materials'][] = (int) $val;
                        } elseif (array_key_exists($tmp[0], $rVal['toErase']['subTable'])) {
                            if (!in_array((int) $val, $rVal['toErase']['subTable'][$tmp[0]]))
                                $rVal['toErase']['subTable'][$tmp[0]][] = (int) $val;
                        } else {
                            $rVal['toErase']['subTable'][$tmp[0]] = [(int) $val];
                        }
                    }
                }
            }
        }
        return $rVal;
    }

    public function actionSubtableremove() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$tName = Yii::$app->request->post('formName')) {
            return ['status' => 'error', 'errorText' => 'Не передано имя формы (formName)'];
        }
        if (!$treeId = Yii::$app->request->post('treeId')) {
            return ['status' => 'error', 'errorText' => 'Не передан идетификатор дерева (treeId)'];
        }
        if (!$matRusName = Yii::$app->request->post('matRusName')) {
            return ['status' => 'error', 'errorText' => 'Не передано название материала (matRusName)'];
        }
        if (!$id = Yii::$app->request->post('id', false)) {
            return ['status' => 'error', 'errorText' => 'Не передано идентификатор удаляемой (id)'];
        }
        if ($model = Tablestypes::find()->where(['name' => $matRusName])->one()) {
            $tmp = DependTables::getDependsTable($model->translitName, $model->struct);
            $checkZakaz = $this->findAllDepends($tmp, $tName, $id, $treeId);
            if (count($checkZakaz['mInZakaz']) && !Yii::$app->request->post('removeAnyWay')) {
                return ['status' => 'check', 'checkHTML' => $this->createHtmlReportToRemove($checkZakaz['mInZakaz'])];
            } else {
//                return ['status'=>'error','findAllDepends'=>$this->findAllDepends($tmp, $tName, $id, $treeId,true)];
                $toErase = $this->findAllDepends($tmp, $tName, $id, $treeId, true)['toErase'];
                if (count($toErase['materials_on_firms'])) {
                    MaterialsOnFirms::deleteAll(['id' => $toErase['materials_on_firms']]);
                }
                if (count($toErase['zakaz_materials'])) {
                    ZakazMaterials::deleteAll(['id' => $toErase['zakaz_materials']]);
                }
                foreach ($toErase['subTable'] as $key => $val) {
                    DependTable::createObject($key)->deleteAll(['id' => $val]);
                }
            }
            $doCheck = false;
            for ($i = 0; $i < count($tmp) && !$doCheck; $i++) {
                if ($doCheck = ($tmp[$i]['fullname'] === $tName)) {
                    if ($el = DependTable::createObject($tName)->findOne($id)) {
                        ; //->where(['id'=>$id])->one()){
                        $rmObj = 0;
                        $rmInTbl = 0;
                        if ($i < count($tmp) - 1) {
                            $this->stRmove($tmp, $i + 1, $id, $rmObj, $rmInTbl);
                        }
                        $rmObj += DependTable::createObject($tName)->deleteAll([
                            'id' => $id]);
                        $this->checkAllChains();
                        return ['status'    => 'ok', 'removedEl' => $rmObj, 'inTables'  => $rmInTbl,
                            'name'      => $el->toArray()];
                    } else {
                        return ['status'    => 'error', 'errorText' => 'Запись с id=' . $id . ' не найдена!',
                            'tmp'       => $tmp, 'post'      => $_POST];
                    }
                }
            }
            return ['status'    => 'error', 'errorText' => 'Ничего не выполнео, странненько.',
                'tmp'       => $tmp, 'post'      => $_POST];
            //return parent::actionRemove('DependTable',strtolower($tName));
        } else {
            return ['status' => 'error', 'errorText' => "Материал '$matRusName' не найден!"];
        }
    }

    public function actionAddedit($classN = null, $tName = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($classN === null)
            $classN = Yii::$app->request->post('formName');
//        return ['status'=>'error','errorText'=>'test','post'=>$_POST];
        if (!$classN) {
            return ['status' => 'error', 'errorText' => 'Не передано имя формы (formName)'];
        }
        $fullName = 'app\\models\\tables\\' . $classN;
        if (!$id = Yii::$app->request->post('id', false)) {
            $model = new $fullName();
        } else {
            if (!$model = $fullName::findOne((int) $id)) {
                return ['status'    => 'error', 'errorText' => 'Запись ' . $id . ' не найдена',
                    'id'        => $id];
            }
        }

        if ($model->load(Yii::$app->request->post(), $classN)) {
//            return ['status'=>'error','errorText'=>'test','post'=>$_POST,'model'=>$model,'fullname'=>$fullName];
            if ($model->save()) {
//                return ['status'=>'error','errorText'=>'test','post'=>$_POST];
                $check = new \app\models\tables\DependTables([
                    'mainName'    => $model->translitName,
                    'mainNameRus' => $model->name,
                    'struct'      => \yii\helpers\Json::decode($model->struct)
                ]);
//                Yii::$app->cache->flush();
                return [
                    'status'       => 'ok',
                    'item'         => [
                        'id'   => $model->id,
                        'name' => $model->name,
//                        'category'=>$model->catText
                    ],
                    'dependTables' => $check->checkAndAdd(),
                    'param'        => ['fullName'  => $fullName, 'post'      => $_POST,
                        'model'     => $model, 'className' => $classN]
                ];
            } else {
                return ['status' => 'error', 'errors' => $model->errors];
            }
        } else {
            if (!$model->isNewRecord && $classN === 'Tablestypes')
                return [
                    'status'    => 'error',
                    'errorText' => 'Нельзя изменить структуру материала <br>после сохранения!'
                ];
            else
                return [
                    'status'   => 'ok',
                    'formName' => $classN,
                    'prim'     => $model->id,
                    $classN    => $model->toArray(),
                ];
        }
    }

    public function actionList($classN = null, $tName = null, $where = null, $pageSize = 1800) {
        Yii::$app->response->format = Response::FORMAT_JSON;
//        return ['status'=>'ok','result'=>[],'count'=>0,'post'=>$_POST];
        if ($zakmaterial = Yii::$app->request->post('zakmaterial')) {
            if (is_array($zakmaterial))
                return parent::actionList($classN, $tName, ['id' => array_keys($zakmaterial)]);
            else
                return ['status' => 'ok', 'result' => [], 'count' => 0, 'post' => $_POST];
        } else
            return parent::actionList($classN, $tName, $where, $pageSize);
    }

}
