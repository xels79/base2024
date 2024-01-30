<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of zakaz
 *
 * @author Александр
 * @property app\models\admin\ContactZak $_manager_query Контакт заказчика
 */

namespace app\models\zakaz;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use app\models\admin\Zak;
use app\models\admin\ContactZak;
use \yii\helpers\ArrayHelper;
use app\models\zakaz\ZakazMaterials;
use app\models\zakaz\ZakazPod;
use app\models\TblUser;

class Zakaz extends ZakazRules {

//    public $profit;
    public $files = null;
    public $tmpName = null;
    protected $_podryad = [];
    protected $_podtyadIsChange = false;
    protected $_materialsIsChange = false;
    protected $_materials = [];
    private $_podryad_total_coast = null;
    private $_podryad_total_coastF = null;
    private static $_Ourmanagername = [];
    public static $_stage = ['Согласование', 'Фотовывод', 'Дизайн', 'Подрядчик',
        'Шелкография', 'Тампопечать', 'Резка', 'Готов', 'Сдан', 'Ошибка'];
    public static $_stageD = [
        1 => 'Фотовывод',
        2 => 'Дизайн',
        6 => 'Резка',
        9 => 'Ошибка'
    ];

    public static function tableName() {
        return 'zakaz';
    }

    private $_manager_query = false;

	public function getUser(){
		return $this->hasOne(TblUser::className(),['id'=>'ourmanager_id']);
	}
	public function getPods(){
		return $this->hasMany(ZakazPod::className(),['zakaz_id'=>'id']);
	}
	public function getAllMaterials(){
		return $this->hasMany(ZakazMaterials::className(),['zakaz_id'=>'id']);
	}

    public function getInvoice_from_this_company_text() {
        $key = (int) $this->invoice_from_this_company ? $this->invoice_from_this_company : 0;
        if ($key) {
            if ($model = \app\models\admin\RekvizitOur::findOne($key)) {
                return $model->name;
            } else {
                return 'Не найден';
            }
        } else {
            return $this->method_of_payment == 0 ? 'Договорная' : ($this->method_of_payment == 2 ? 'В/З' : '?');
        }
    }

    public function getMethod_of_payment_text() {
        return ArrayHelper::getValue(['Договорная', '', 'В/З'], (int) $this->method_of_payment ? $this->method_of_payment : 0, '') . ((int) $this->method_of_payment === 1 ? ($this->account_number ? (' №' . $this->account_number) : '<b style="color:#F00;">не выставлен</b>') : '');
    }

    public function getDivision_of_work_text() {
        return ArrayHelper::getValue(['50/50', '100%', '0%'], (int) $this->division_of_work ? $this->division_of_work : 0, '');
    }

    public function getEmpt() {
        return '';
    }

    public function getStageText() {
        return self::$_stage[(int) $this->stage];
    }

    public function getFileCount() {
        if ($this->isNewRecord)
            return 0;
        $zipPath = \app\components\MyHelplers::zipPathToStore($zakazId);
        if (file_exists($zipPath)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === true) {
                $tmp = \app\components\MyHelplers::zipListById($this->id);
                return count($tmp['des']) + count($tmp['main']);
            } else
                return 0;
        }return 0;
    }

    public function getZak_idText() {
        if ($this->zak_id) {
            return Zak::findName($this->zak_id);
        } else {
            return 'нет';
        }
    }

    public function getManager_id_text() {
        if ($this->_manager_query === false) {
            $this->_manager_query = ContactZak::findOne((int) $this->manager_id);
        }
        if ($this->_manager_query) {
            return $this->_manager_query->name;
        } else {
            return 'Не найден';
        }
    }

    public function getManager_phone_text() {
        if ($this->_manager_query === false) {
            $this->_manager_query = ContactZak::findOne((int) $this->manager_id);
        }
        if ($this->_manager_query) {
            return $this->_manager_query->phone;
        } else {
            return 'Не найден';
        }
    }

    public function getManager_email_text() {
        if ($this->_manager_query === false) {
            $this->_manager_query = ContactZak::findOne((int) $this->manager_id);
        }
        if ($this->_manager_query) {
            return $this->_manager_query->mail;
        } else {
            return 'Не найден';
        }
    }

    public function getNumber_of_copiesText() {
        return $this->number_of_copies ? ((is_integer($this->number_of_copies) ? Yii::$app->formatter->asInteger($this->number_of_copies) : $this->number_of_copies)) : 'Не задано';
    }

    public function getNumber_of_copies1Text() {
        return $this->number_of_copies1 ? ((is_integer($this->number_of_copies1) ? Yii::$app->formatter->asInteger($this->number_of_copies1) : $this->number_of_copies1)) : '';
    }

    public function getDateofadmissionText() {
        return $this->dateofadmission ? Yii::$app->formatter->asDate($this->dateofadmission) : 'Не задано';
    }

    public function getDeadlineText() {
        return $this->deadline ? Yii::$app->formatter->asDate($this->deadline) : 'Не задано';
    }

//    public function getDate_of_receipt(){
//        return $this->date_of_receipt?Yii::$app->formatter->asDate($this->date_of_receipt):'';
//    }
    public function getTotal_coastText() {
        return Yii::$app->formatter->asInteger($this->total_coast ? $this->total_coast : 0);
    }

    public function getTotal_coastTextFB() {
        return Yii::$app->formatter->asInteger($this->total_coast ? $this->total_coast : 0);
    }

    public function getProfit() {
//        return 43.3;
        return $this->total_coast - $this->spending;
    }

    //$tmp= \app\models\tables\Productions::find()->select(['id','name'])->asArray()->all();
    public function setProfit($val) {

    }

    public function getOurmanagername() {
        if ($this->ourmanager_id) {
            if (array_key_exists((int) $this->ourmanager_id, self::$_Ourmanagername)) {
                return self::$_Ourmanagername[(int) $this->ourmanager_id];
            } else {
                if (Yii::$app->user->identity->id == $this->ourmanager_id) {
                    self::$_Ourmanagername[(int) $this->ourmanager_id] = Yii::$app->user->identity->realname;
                    return self::$_Ourmanagername[(int) $this->ourmanager_id];
                } else
                if ($model = \app\models\User::findOne((int) $this->ourmanager_id)) {
                    self::$_Ourmanagername[(int) $this->ourmanager_id] = $model->realname;
                    return self::$_Ourmanagername[(int) $this->ourmanager_id];
                } else
                    return 'Не найден';
            }
        }
        return 'Not set';
    }

    public function getTotal_spending() {
        return $this->spending + $this->spending2;
    }

    public function extraFields() {
        return ['profit', 'ourmanagername', 'podryad', 'materials', 'tmpName'];
    }

    public function beforeValidate() {
        if (!$this->isNewRecord) {
            if (count($this->_materials) < 2) {
                $this->date_of_receipt1 = null;
            }
            if (count($this->_materials) < 1) {
                $this->date_of_receipt = null;
            }
        }
        if ($this->dateofadmission) {
            $this->dateofadmission = Yii::$app->formatter->asDate($this->dateofadmission, 'php:d.m.Y');
        }
        if ($this->deadline) {
            $this->deadline = Yii::$app->formatter->asDate($this->deadline, 'php:d.m.Y');
        }
        Yii::debug([
            'date_of_receipt'  => $this->date_of_receipt,
            'date_of_receipt1' => $this->date_of_receipt1,
            'material_c'       => $this->_materials
                ], 'date_of_receipt before validate');

        return true;
    }

    protected function removByIdFormArray($arr, int $id) {
        $rVal = [];
        foreach ($arr as $el) {
            if ((int) $el->id !== $id)
                $rVal[] = $el;
        }
        return $rVal;
    }

    protected function beforeSaveCheckPodryad() {
        $cont = true;
        foreach ($this->_podryad as $key => $el) {
            Yii::trace($el, 'CheckPodryad_proceed');
            if (isset($el['id']) && $el['id']) {
                if (!$model = ZakazPod::findOne((int) $el['id']))
                    $model = new ZakazPod();
                unset($el['id']);
            } else {
                $model = new ZakazPod();
            }
            $model->attributes = $el;
            $model->zakaz_id = $this->id;
            if ($model->isMain === 'true' || $model->isMain == 1)
                $model->isMain = 1;
            else
                $model->isMain = 0;
            if (!$model->save()) {
                Yii::trace('Ошибка сохранения  №' . $key, 'CheckPodryad_proceed');
                Yii::trace($model->errors, 'CheckPodryad_proceed');
                $this->addError('podryad', 'Ошибка сохранения  №' . $key);
                $this->addError('podryad', $model->errors);
                $cont = false;
                \app\components\MyHelplers::log('Zakaz.php->afterSaveSave()->beforeSaveCheckPodryad() - ERRORS!', [
                    'subHeader' => 'Заказ №' . $this->id . ' проверка подрядчиков',
                    'data'      => [
                        'action'                 => Yii::$app->controller->id . '/' . Yii::$app->controller->action->id,
                        'user'                   => 'id:' . Yii::$app->user->identity->id . '; name:' . Yii::$app->user->identity->name,
                        'post'                   => Yii::$app->request->post(),
                        '_materialsIsChange'     => $this->_materialsIsChange,
                        '_podryad'               => $this->_podryad,
                        '_podtyadIsChange'       => $this->_podtyadIsChange,
                        '_materialsIsChange'     => $this->_materialsIsChange,
                        '_podtyadIsChange'       => $this->_podtyadIsChange,
                        '_realMaterilsFromTable' => ZakazMaterials::find()->where(['zakaz_id' => $this->id])->asArray()->all(),
                        '_realPodrFromTable'     => ZakazPod::find()->where(['zakaz_id' => $this->id])->asArray()->all(),
                        'errors'                 => $model->errors
                    ]
                ]);
            }
        }
        Yii::info(\yii\helpers\VarDumper::dumpAsString($this->_podryad), 'save-_podryad');
        return $cont;
    }

    protected function beforeSaveCheckMaterials() {
        $cont = true;
        foreach ($this->_materials as $key => $el) {
            if (isset($el['id']) && $el['id']) {
                if (!$model = ZakazMaterials::findOne($el['id'])) {
                    $model = new ZakazMaterials();
                }
                unset($el['id']);
            } else {
                $model = new ZakazMaterials();
            }
            $model->attributes = $el;
            $model->zakaz_id = $this->id;
            Yii::info(\yii\helpers\VarDumper::dumpAsString($model->attributes), 'save - material ' . $key);
            if (!$model->save()) {
                $this->addError('materials', 'Ошибка сохранения  №' . $key);
                $this->addError('materials', $model->errors);
                $cont = false;
                \app\components\MyHelplers::log('Zakaz.php->afterSaveSave()->beforeSaveCheckMaterials() - ERRORS!', [
                    'subHeader' => 'Заказ №' . $this->id . ' проверка материалов',
                    'data'      => [
                        'action'                 => Yii::$app->controller->id . '/' . Yii::$app->controller->action->id,
                        'user'                   => 'id:' . Yii::$app->user->identity->id . '; name:' . Yii::$app->user->identity->name,
                        'post'                   => Yii::$app->request->post(),
                        '_materialsIsChange'     => $this->_materialsIsChange,
                        '_podtyadIsChange'       => $this->_podtyadIsChange,
                        '_materials'             => $this->_materials,
                        '_realMaterilsFromTable' => ZakazMaterials::find()->where(['zakaz_id' => $this->id])->asArray()->all(),
                        '_realPodrFromTable'     => ZakazPod::find()->where(['zakaz_id' => $this->id])->asArray()->all(),
                        'errors'                 => $model->errors
                    ]
                ]);
            }
        }
//        foreach ($oldVal as $el) {
//            $el->delete();
//        }
        Yii::debug(\yii\helpers\VarDumper::dumpAsString($oldVal), 'save - material');
        return $cont;
    }

    public function beforeSave($insert) {
//        return true;
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $rVal = true;
        if ($this->dateofadmission) {
            $this->dateofadmission = Yii::$app->formatter->asDate($this->dateofadmission, 'php:Y-m-d');
        }
        if ($this->deadline) {
            $this->deadline = Yii::$app->formatter->asDate($this->deadline, 'php:Y-m-d');
        }
        if (!$this->isNewRecord) {
            if (count($this->_materials) < 2) {
                $this->date_of_receipt1 = null;
            }
            if (count($this->_materials) < 1) {
                $this->date_of_receipt = null;
            }
        }

        Yii::debug([
            'date_of_receipt'  => $this->date_of_receipt,
            'date_of_receipt1' => $this->date_of_receipt1,
                ], 'date_of_receipt - before save');
        if ($this->date_of_receipt) {
            $this->date_of_receipt = Yii::$app->formatter->asDate($this->date_of_receipt, 'php:Y-m-d');
        }
        if ($this->date_of_receipt1) {
            $this->date_of_receipt1 = Yii::$app->formatter->asDate($this->date_of_receipt1, 'php:Y-m-d');
        }
        if (!is_numeric($this->stage)) {
            $this->addError('stage', 'Должно быть числом');
            return false;
        }
        if (!is_numeric($this->division_of_work)) {
            $this->addError('division_of_work', 'Должно быть числом');
            return false;
        }
        if (!is_numeric($this->method_of_payment)) {
            $this->addError('method_of_payment', 'Должно быть числом');
            return false;
        }
        if (!$this->colors)
            $this->colors = [];
        Yii::info(\yii\helpers\VarDumper::dumpAsString($this->colors), 'save-colors');
        if (is_array($this->colors)) {
            $this->colors = Json::encode($this->colors);
            //$this->addError('colors','Должно быть массивом');
            //return false;
        };
        if (!$this->post_print)
            $this->post_print = '{}';
        if (is_array($this->post_print)) {
            $this->post_print = Json::encode($this->post_print);
        }
        $opl = (double) $this->getOplata();
//        $oplataOst=(double)$this->total_coast-opl;
        Yii::debug(['id' => $this->id, 'total_coast' => $this->total_coast, '$opl' => $opl], 'ttCoast');
        if ($this->re_print) {
            $this->oplata_status = 2;
        } else {
            if ($opl > (double) $this->total_coast) {
                $this->oplata_status = 3;
            } elseif ($opl > 0 && $opl < (double) $this->total_coast) {
                $this->oplata_status = 1;
            } elseif ($this->total_coast == $opl && $opl > 0 && $this->total_coast > 0) {
                $this->oplata_status = 2;
            } else {
                $this->oplata_status = 0;
            }
        }

        if ($this->method_of_payment == 1) {
            if (($this->account_number && !$this->invoice_from_this_company)) {
                $this->addError('invoice_from_this_company', 'Укажите фирму или уберите номер счёта');
                $rVal = false;
            }
        } else {
            if ($this->account_number) {
                $this->addError('account_number', 'Измените форму оплаты или очистите поле');
                $rVal = false;
            }
            if ($this->invoice_from_this_company) {
                $this->addError('invoice_from_this_company', 'Измените форму оплаты или укажите "не выбрана"');
                $rVal = false;
            }
            if (!$rVal)
                $this->addError('method_of_payment', 'Измените форму оплаты или очитите "№ счета" и "Фирма"');
        }
        if ($this->isNewRecord) {
            $tErr = ['speed', 'delivery', 'markup', 'bonus', 'transport', 'transport2'];
            foreach ($tErr as $k) {
                $kk = 'exec_' . $k . '_paid';
                $this->$kk = 0;
                if ($this->re_print) {
                    $kk = 'exec_' . $k . '_summ';
                    $this->$kk = 0;
                }
            }
        }
        //[['wages','percent1','percent2','percent3'],'number']
//        \yii\helpers\VarDumper::dump(['wages'=>$this->wages, 'percent1'=>$this->percent1, 'percent2'=>$this->percent2, 'percent3'=>$this->percent3],10,true);Yii::$app->end();
        if ($this->percent1 === '' || $this->percent2 === '' || $this->percent3 === '' || $this->percent1 === null || $this->percent2 === null || $this->percent3 === null) {
            if ($tUser = \app\models\TblUser::findOne((int) $this->ourmanager_id)) {
//                $this->wages=$tUser->wages;
                $this->percent1 = $tUser->percent1;
                $this->percent2 = $tUser->percent2;
                $this->percent3 = $tUser->percent3;
            }
        }
        if (count($this->errors)) {
            return false;
        } else {
            if ($this->isNewRecord && $rVal) {
                \app\components\MyHelplers::log('Zakaz.php->beforeSave() - isOk:', [
                    'subHeader' => 'Новый заказ добовляется...',
                    'data'      => [
                        'action'                 => Yii::$app->controller->id . '/' . Yii::$app->controller->action->id,
                        'user'                   => 'id:' . Yii::$app->user->identity->id . '; name:' . Yii::$app->user->identity->name,
                        'post'                   => Yii::$app->request->post(),
                        '_materialsIsChange'     => $this->_materialsIsChange,
                        '_podtyadIsChange'       => $this->_podtyadIsChange,
                        '_materialsIsChange'     => $this->_materialsIsChange,
                        '_podtyadIsChange'       => $this->_podtyadIsChange,
                        '_realMaterilsFromTable' => ZakazMaterials::find()->where(['zakaz_id' => $this->id])->asArray()->all(),
                        '_realPodrFromTable'     => ZakazPod::find()->where(['zakaz_id' => $this->id])->asArray()->all(),
                    ]
                ]);
            }
            return $rVal;
        }
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $_realMaterilsFromTable=ZakazMaterials::find()->where(['zakaz_id' => $this->id])->asArray()->all();
        if ($this->_materialsIsChange === true){
            $this->beforeSaveCheckMaterials();
            
        }
        if ($this->_podtyadIsChange === true)
            $this->beforeSaveCheckPodryad();
        \app\components\MyHelplers::log('Zakaz.php->afterSave():', [
            'subHeader' => $insert ? ('Заказ №' . $this->id . ' добавлен') : ('Заказ №' . $this->id . ' обновлён'),
            'data'      => [
                'action'                 => Yii::$app->controller->id . '/' . Yii::$app->controller->action->id,
                'user'                   => 'id:' . Yii::$app->user->identity->id . '; name:' . Yii::$app->user->identity->name,
                'post'                   => Yii::$app->request->post(),
                '_materialsIsChange'     => $this->_materialsIsChange,
                '_podtyadIsChange'       => $this->_podtyadIsChange,
                '_materials'             => $this->_materials,
                '_podryad'               => $this->_podryad,
                '_realMaterilsFromTable' => $_realMaterilsFromTable,
                '_realPodrFromTable'     => ZakazPod::find()->where(['zakaz_id' => $this->id])->asArray()->all(),
            ]
        ]);
    }

    public function getWorktypes_id_text() {
        if ($model = \app\models\tables\Worktypes::findOne($this->worktypes_id)) {
            return $model->name;
        } else {
            return '';
        }
    }

    public function getPodryad() {
        if ($this->isNewRecord)
            return [];
        else {
            $tmp = ZakazPod::find()
                    ->where(['zakaz_id' => $this->id])
                    ->orderBy('isMain DESC')
                    ->all();
            $rVal = [];
            foreach ($tmp as $el) {
                $rVal[] = $el->toArray();
            }
            return $rVal;
        }
    }

    private $_getPodryadBugalter = null;

    public function getPodryadBugalter() {
        if ($this->_getPodryadBugalter === null) {
            $query = ZakazPod::find()
                    ->where(['zakaz_id' => $this->id])
                    ->andWhere('zakaz_pod.payment>0')
                    ->with('pod')
                    ->orderBy('isMain DESC')
                    ->asArray();
            if (isset(Yii::$app->controller->filters) && array_key_exists('podryad_residue_list', Yii::$app->controller->filters) && Yii::$app->controller->filters['podryad_residue_list'][0]) {
                $query->andWhere('zakaz_pod.payment>zakaz_pod.paid');
            }
            $this->_getPodryadBugalter = $query->all();
//            $this->_getPodryadBugalter=ZakazPod::find()
//                ->where(['zakaz_id'=>$this->id])
//                ->andWhere('zakaz_pod.payment>0')
////                ->andWhere('zakaz_pod.payment>zakaz_pod.paid')
//                ->with('pod')
//                ->orderBy('isMain DESC')
//                ->asArray()
//                ->all();
        }
        Yii::debug($this->_getPodryadBugalter, 'getPodryad');
        return $this->_getPodryadBugalter;
    }

    private function podryadGetListByKey($key, $asInteger = true) {
        $tmp = $this->getPodryadBugalter();
        $rVal = [];
        Yii::debug($key, 'podryadfilter');
        foreach ($tmp as $el) {
            if (!array_key_exists('zakaz_pod.zakaz_id.`zakaz.id`.pod_id', Yii::$app->controller->filters ? Yii::$app->controller->filters : [
                            ] ) || (array_key_exists('zakaz_pod.zakaz_id.`zakaz.id`.pod_id', Yii::$app->controller->filters ? Yii::$app->controller->filters : [
                            ] ) && in_array($el['pod']['firm_id'], Yii::$app->controller->filters['zakaz_pod.zakaz_id.`zakaz.id`.pod_id']))) {
                if (!is_array($key)) {
                    $rVal[$el['id']] = $asInteger ? Yii::$app->formatter->asInteger($el[$key]) : $el[$key];
                } else {
                    $rVal[$el['id']] = ['el' => $el];
                    foreach ($key as $mKey => $subKey) {
                        $val = ($subKey === 'firm_name' ? $el['pod']['mainName'] : ($asInteger ? Yii::$app->formatter->asInteger($el[$subKey]) : $el[$subKey]));
                        if (is_numeric($mKey)) {
                            $rVal[$el['id']][$subKey] = $val;
                        } else {
                            $rVal[$el['id']][$mKey] = $val;
                        }
                    }
                }
            }
        }
        return $rVal;
    }

    public function getPodryad_residue_list() {
        $rVal = [];
        foreach ($this->getPodryadBugalter() as $el) {
            $rVal[$el['id']] = $el['payment'] - $el['paid'];
        }
        return $rVal;
    }

    public function getPodryad_total_coast($useFilter = true) {
        if ($useFilter) {
            if ($this->_podryad_total_coastF === null) {
                $opt = ['zakaz_id' => $this->id];
                if (array_key_exists('zakaz_pod.zakaz_id.`zakaz.id`.pod_id', Yii::$app->controller->filters ? Yii::$app->controller->filters : [
                                ] )) {
                    $opt['pod_id'] = Yii::$app->controller->filters['zakaz_pod.zakaz_id.`zakaz.id`.pod_id'];
                }
                $this->_podryad_total_coastF = (int) round(ZakazPod::find()->where($opt)->sum('payment'), 0);
                if (!$this->_podryad_total_coastF)
                    $this->_podryad_total_coastF = '';
            }
            return $this->_podryad_total_coastF;
        } else {
            if ($this->_podryad_total_coast === null) {
                $opt = ['zakaz_id' => $this->id];
                $this->_podryad_total_coast = (int) round(ZakazPod::find()->where($opt)->sum('payment'), 0);
                if (!$this->_podryad_total_coast)
                    $this->_podryad_total_coast = '';
            }
            return $this->_podryad_total_coast;
        }
    }

    public function getPodryad_total_coast_list() {
        return $this->podryadGetListByKey(['value'       => 'payment', 'profit_type' => 'payment',
                    'firm_name', 'paid']);
    }

    public function getPodryad_paied_list() {
        return $this->podryadGetListByKey('paid');
    }

    public function getPodryad_name_list() {
        return array_map(function($el) {
            return $el ? $el['mainName'] : 'не найден';
        }, $this->podryadGetListByKey('pod', false));
    }

    public function getOther_spends_summ() {
        $rVal = 0;
        $arr = ['speed', 'delivery', 'markup', 'bonus', 'transport', 'transport2'];
        foreach ($arr as $key) {
            $tmp = 'exec_' . $key . '_paid';
            if (($val = $this['exec_' . $key . '_payment']) && $val != 0 && (($key === 'transport2' && $this->exec_transport) || ($key !== 'transport2' && $this['exec_' . $key])) && (!array_key_exists('other_spends_list', Yii::$app->controller->filters ? Yii::$app->controller->filters : [
                            ] ) || in_array('exec_' . ($key !== 'transport2' ? $key : 'transport'), Yii::$app->controller->filters['other_spends_list']))
            ) {
                $rVal += $val;
            }
        }
        return $rVal ? Yii::$app->formatter->asInteger($rVal) : '';
    }

    public function getOther_spends_list_col() {
        $rVal = [];
        $arr = ['speed', 'delivery', 'markup', 'bonus', 'transport', 'transport2'];
        foreach ($arr as $key) {
            $tmp = 'exec_' . $key . '_paid';
            if (($val = $this['exec_' . $key . '_payment']) && $val != 0 && (($key === 'transport2' && $this->exec_transport) || ($key !== 'transport2' && $this['exec_' . $key])))
                $rVal[$tmp] = $this->attributeLabels()[$tmp];
        }
        return $rVal;
    }

    public function getOther_spends_list() {
        $rVal = [];
        $arr = ['speed', 'delivery', 'markup', 'bonus', 'transport', 'transport2'];
        foreach ($arr as $key) {
            $tmp = 'exec_' . $key . '_paid';
            if (($val = $this['exec_' . $key . '_payment']) && $val != 0 && (($key === 'transport2' && $this->exec_transport) || ($key !== 'transport2' && $this['exec_' . $key])) && (!array_key_exists('other_spends_list', Yii::$app->controller->filters ? Yii::$app->controller->filters : [
                            ] ) || in_array('exec_' . ($key !== 'transport2' ? $key : 'transport'), Yii::$app->controller->filters['other_spends_list']))
            )
                $rVal[$tmp] = [
                    'value'     => (int) $val,
                    'firm_name' => $this->attributeLabels()[$tmp],
                    'paid'      => $this[$tmp]
                ];
        }
        return $rVal;
    }

    public function getCalulateProfitText() {
        $rVal = $this->getCalulateProfit();
        return $rVal ? Yii::$app->formatter->asInteger($rVal) : '';
    }

    public function getCalulateProfit() {
        if ($this->isNewRecord)
            return 0;
        else {
            $arr = ['speed', 'delivery', 'markup', 'bonus', 'transport', 'transport2'];
            $rVal = $this->total_coast;
            $rVal -= $this->getMaterial_total_coast(false);
            $tmp = $this->getPodryad_total_coast(false);
            if ($tmp)
                $rVal -= $tmp;
            foreach ($arr as $key) {
                if ($val = $this['exec_' . $key . '_payment'])
                    $rVal -= (int) $val;
            }
            return round($rVal, 0, PHP_ROUND_HALF_DOWN);
        }
    }

    public function setPodryad($val) {
        Yii::debug($val, 'setPodryad');
        $this->_podryad = is_array($val) ? $val : [];
        $this->_podtyadIsChange = true;
    }

    public function getMaterials() {
        if ($this->isNewRecord)
            return [];
        else
            return ZakazMaterials::find()->where(['zakaz_id' => $this->id])->asArray([
                        'supplierTypeText'])->all();
    }

    public function setMaterials($val) {
        Yii::trace(\yii\helpers\VarDumper::dumpAsString($val), 'material');
        $this->_materialsIsChange = true;
        if (is_array($val))
            $this->_materials = $val;
        else
            $this->_materials = Json::decode($val);
    }

    public function setMaterials_to_erase($val) {
        $val = is_array($val) ? $val : Json::decode($val);
        ZakazMaterials::deleteAll([
            'zakaz_id' => $this->id,
            'id'       => $val
        ]);
    }

    public function getMaterials_to_erase($val) {
        return [];
    }

    public function setPodryad_to_erase($val) {
        $val = is_array($val) ? $val : Json::decode($val);
        ZakazPod::deleteAll([
            'zakaz_id' => $this->id,
            'id'       => $val
        ]);
    }

    public function getPodryad_to_erase($val) {
        return [];
    }

    private $_oplata = null;

    public function getOplata() {
        if ($this->_oplata === null) {
            $this->_oplata = round(ZakazOplata::find()->where(['zakaz_id' => (int) $this->id])->sum('summ'));
        }
        return $this->_oplata;
    }

    public function getOplataText() {
        $rVal = $this->getOplata();
        return $rVal ? Yii::$app->formatter->asInteger($rVal) : '';
    }

    public function getOplata_status_text() {
        return ArrayHelper::getValue(['<b style="color:#f00;">не оплачен</b>', 'Предоплата',
                    '<span style="color:#00aa00;">Оплачен</span>', 'Переплата'], (int) $this->oplata_status, '');
    }

    public function getOpl() {
        return $this->hasMany(ZakazOplata::className(), ['zakaz_id' => 'id']);
    }

    public function beforeDelete() {
        if (!parent::beforeDelete()) {
            return false;
        }
        ZakazOplata::deleteAll(['zakaz_id' => (int) $this->id]);
        ZakazMaterials::deleteAll(['zakaz_id' => (int) $this->id]);
        ZakazPod::deleteAll(['zakaz_id' => (int) $this->id]);
        return true;
    }

    public function getRowCountNoteHtml() {

    }

    public function getNoteHtml() {
        $rVal = '';
        for ($i = 0; $i < strlen($this->note); $i++) {
            if ($this->note[$i] === "\n") {
                $rVal .= '<br>';
            } else {
                $rVal .= $this->note[$i];
            }
        }
        return Yii::$app->formatter->asHtml($rVal);
    }

    public function getPechiatnik() {
        return $this->hasOne(\app\models\tables\Pechiatnik::className(), ['z_id' => 'id']);
    }
	
	
}
