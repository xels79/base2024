<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\admin;

/**
 * Description of MainpageController
 *
 * @author Александр
 */
use Yii;
use app\controllers\AjaxController;
use app\models\admin\Zak;
use app\models\admin\Pod;
use app\models\admin\Post;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class MainpageController extends AjaxController {

    const pageSize = 10;

    public function init() {
        parent::init();
        $this->viewPath = '@app/views/admin/setup';
    }

    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'ajaxlist_zak'    => ['post'],
                    'ajaxlist_pod'    => ['post'],
                    'ajaxlist_post'   => ['post'],
                    'ajaxlist_zakone' => ['post']
                ],
            ],
        ];
    }

    protected function getProviders($firm, $postFix, $page = 1) {
        $classN = 'app\\models\\admin\Rekvizit' . $postFix;
        $provaiderRekvizit = new ActiveDataProvider([
            'query'      => $classN::find()->where(['firm_id' => $firm->firm_id]),
            'pagination' => [
                'pageSize' => self::pageSize,
                'page'     => $page - 1
            ]
        ]);
        $classN = 'app\\models\\admin\Address' . $postFix;
        $provaiderAdresses = new ActiveDataProvider([
            'query'      => $classN::find()->where(['firm_id' => $firm->firm_id]),
            'pagination' => [
                'pageSize' => self::pageSize,
                'page'     => $page - 1
            ]
        ]);
        $classN = 'app\\models\\admin\Manager' . $postFix;
        $provaiderManagers = new ActiveDataProvider([
            'query'      => $classN::find()
                    ->where(['firm_id' => $firm->firm_id]),
            'pagination' => [
                'pageSize' => self::pageSize,
                'page'     => $page - 1
            ]
        ]);
        $classN = 'app\\models\\admin\Contact' . $postFix;
        $provaiderContacts = new ActiveDataProvider([
            'query'      => $classN::find()
                    ->where(['firm_id' => $firm->firm_id]),
            'pagination' => [
                'pageSize' => self::pageSize,
                'page'     => $page - 1
            ]
        ]);
        $classN = 'app\\models\\admin\Shipping' . $postFix;
        $provaiderShipping = new ActiveDataProvider([
            'query'      => $classN::find()
                    ->where(['firm_id' => $firm->firm_id]),
            'pagination' => [
                'pageSize' => self::pageSize,
                'page'     => $page - 1
            ]
        ]);
        if ($postFix === 'Post' || $postFix === 'Pod') {
            $classN = 'app\\models\\admin\WOP' . $postFix;
            $provaiderWOP = new ActiveDataProvider([
                'query'      => $classN::find()
                        ->where(['firm_id' => $firm->firm_id]),
                'pagination' => [
                    'pageSize' => self::pageSize,
                    'page'     => $page - 1
                ]
            ]);
        } else {
            $provaiderWOP = NULL;
        }
        //provaiderContacts
        return [
            'provaiderRekvizit' => $provaiderRekvizit,
            'provaiderAdresses' => $provaiderAdresses,
            'provaiderManagers' => $provaiderManagers,
            'provaiderContacts' => $provaiderContacts,
            'provaiderShipping' => $provaiderShipping,
            'provaiderWOP'      => $provaiderWOP,
            'provaiderEmployee' => false
        ];
    }

    protected function _changeSF($req, $id, $classN = 'Zak', $formFile = 'firmourList') {
//        return ['status'=>'error','errorText'=>'id:'.$id,'post'=>$_POST,'get'=>$_GET];
        if (!$page = Yii::$app->request->post('page'))
            $page = Yii::$app->request->get('page', 1);
//        return ['status'=>'error','errorText'=>'page:'.$page,'post'=>$_POST,'get'=>$_GET,'classN'=>$classN];
        if (!$id) {
            return ['status' => 'error', 'errorText' => 'id записи не передан', 'post' => $_POST, 'get' => $_GET];
        } else {
            $fullName = 'app\\models\\admin\\' . $classN; //\Yii::getAlias('@app/models/admin/'.$classN);
            $model = $fullName::findOne((int) $id);
            if ($model) {
                if ($model->load(Yii::$app->request->post(), $classN)) {
                    if ($model->save() && (($classN !== 'OurFirm' && $classN !== 'RekvizitOur' && $classN !== 'ManagersOur') || $model->saveFiles())) {
                        Yii::$app->cache->flush();
                        return ['status' => 'saved', 'modelName' => $model->formName(), 'errors' => $model->errors, 'files' => $_FILES, 'post' => $_POST];
                    } else {
                        return [
                            'status'    => 'error',
                            'modelName' => $model->formName(),
                            'errors'    => $model->errors,
                            'errorText' => 'Ошибка сохранения',
                        ];
                    }
                } else {
                    return ['status'    => 'form', 'html'      => $this->renderPartial(
                                $formFile,
                                \yii\helpers\ArrayHelper::merge(['model' => $model, 'otherRequestParam' => $req], $this->getProviders($model, $classN, $page))
                        ), 'modelName' => $model->formName(), 'errors'    => $model->errors, 'files'     => $_FILES, 'post'      => $_POST];
                }
            } else
                return ['status' => 'error', 'errorText' => "Запись №$id не найдена", 'post' => $_POST, 'get' => $_GET, 'classN' => $classN];
        }
    }

    private function processActionMain(array $req = null, $page = 1, $classN = 'Zak') {
        $this->viewPath = '@app/views/admin/customer';
        if (!$req)
            $req = Yii::$app->request->post('req', 1);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id', null);
        if ($id === null)
            $id = Yii::$app->request->get('id', 0);
        //return ['status'=>'error','errorText'=>'id:'.$id,'post'=>$_POST,'get'=>$_GET];
        if ($req && isset($req['name'])) {
            $tmp = explode('-', $req['name']);
//            return ['tmp'=>$tmp];
            if ($req['name'] == 'change-Zak' || $req['name'] == 'change-Pod' || $req['name'] == 'change-Post') {
                return $this->_changeSF($req, $id, $tmp[1]);
            }
            switch ($tmp[0]) {
                case 'add':
                    if (count($tmp) < 3)
                        return $this->_addSF($req, $tmp[1]);
                    else
                        return $this->_addSF($req, $tmp[1], '_' . $tmp[0] . $tmp[2]);
                    break;
                case 'change':
                    if (count($tmp) < 3)
                        return parent::_changeSF($req, $id, $tmp[1]);
                    else
                        return parent::_changeSF($req, $id, $tmp[1], '_add' . $tmp[2]);
                    break;
                case 'validate':
                    return $this->_validateSF($req, $id, $tmp[1]);
                    break;
                case 'view':
                    if (count($tmp) < 3)
                        return $this->_viewSF($req, $id, $tmp[1]);
                    else
                        return $this->_viewSF($req, $id, $tmp[1], '_' . $tmp[0] . $tmp[2]);
                    break;
                case 'remove':
                    return $this->_removeSF($req, $id, $tmp[1]);
                    break;
            }
        } else {
            return $this->_changeSF($req, $id, $classN);
        }
    }

    public function actionAjaxlist_zakone(array $req = null, $page = 1, $classN = 'Zak') {
        return $this->processActionMain($req, $page, $classN);
    }

    public function actionAjaxlist_zak($req = null, $page = 1) {
        if (!$req)
            $req = Yii::$app->request->post('req', 1);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($req && isset($req['name'])) {
            if ($req['name'] == 'add-Zak')
                return $this->_addSF($req, 'Zak', '@app/views/admin/customer/_addFirm');
            if ($req['name'] == 'change-Zak') {
                return $this->runAction('ajaxlist_zakone');
            }
            if ($req['name'] == 'change-main-Zak')
                return $this->_changeSF($req, Yii::$app->request->post('id'), 'Zak', '@app/views/admin/customer/_addFirm');
            if ($req['name'] == 'view-Zak')
                return $this->_viewSF($req, Yii::$app->request->post('id'), 'Zak', '_viewFirm');
            if ($req['name'] == 'remove-Zak')
                return $this->_removeSF($req, Yii::$app->request->post('id'), 'AddressOur');
            if ($req['name'] == 'remove'){
                $tmpId=Yii::$app->request->post('id');
                $zList= \app\models\zakaz\Zakaz::find()->where(['zak_id'=>$tmpId])->select(['id'])->asArray()->all();
                if (!count($zList)){
                    //return ['status'=>'error','errorText'=>'Пока не надо никого удалять '];
                    return $this->_removeSF($req, $tmpId, 'Zak');
                }else{
                    return $this->returnErrorFindInZakaz('Заказчик','id',$zList);
                }
            }
        }
        $provaider = new ActiveDataProvider([
            'query'      => Zak::find(),
            'pagination' => [
                'pageSize' => self::pageSize,
                'page'     => $page - 1
            ]
        ]);
        $this->viewPath = '@app/views/admin/customer';
        return ['html'   => $this->renderPartial('customerList', [
                'dataProvider' => $provaider,
            ]), 'status' => 'ok', 'files'  => $_FILES, 'post'   => $_POST];
    }

    public function actionAjaxlist_post($req = null, $page = 1) {
//        $woptype=Yii::$app->request->post('woptype');
        if (!$req)
            $req = Yii::$app->request->post('req', 1);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($req && isset($req['name'])) {
            if ($req['name'] == 'add-Post')
                //Yii::$app->cache->flush();
                return $this->_addSF($req, 'Post', '@app/views/admin/customer/_addFirm');
            if ($req['name'] == 'change-Post') {
                //Yii::$app->cache->flush();
                return $this->runAction('ajaxlist_zakone', [
                            'req'    => $req,
                            'page'   => $page,
                            'classN' => 'Post'
                ]);
            }
            if ($req['name'] == 'change-main-Post')
                return $this->_changeSF($req, Yii::$app->request->post('id'), 'Post', '@app/views/admin/customer/_addFirm');
            if ($req['name'] == 'view-Post')
                return $this->_viewSF($req, Yii::$app->request->post('id'), 'Post', '_viewFirm');
            if ($req['name'] == 'remove-Post')
                return $this->_removeSF($req, Yii::$app->request->post('id'), 'Post');
            if ($req['name'] == 'remove'){
                $tmpId=Yii::$app->request->post('id');
                $zList= \app\models\zakaz\ZakazMaterials::find()
                        ->where(['firm_id'=>$tmpId])
                        ->select(['zakaz_id'])
                        ->distinct()
                        ->orderBy('zakaz_id')
                        ->asArray()
                        ->all();
                if (!count($zList)){
                    //return ['status'=>'error','errorText'=>'Пока не надо никого удалять '];
                    return $this->_removeSF($req, Yii::$app->request->post('id'), 'Post');
                }else{
                    return $this->returnErrorFindInZakaz('Поставщик','zakaz_id',$zList);
                }
            }
        }
//        if ($woptype){
//            $query=Post::find()
//                   ->leftJoin('WOPPost','firmPost.firm_id=WOPPost.firm_id', ['WOPPost.referensId'=>$woptype]);
//        }else{
//            $query=Post::find();
//        }
//        Yii::debug($woptype, 'woptype');
        $provaider = new ActiveDataProvider([
            'query'      => Post::find(),
            'pagination' => [
                'pageSize' => self::pageSize,
                'page'     => $page - 1
            ]
        ]);
        $this->viewPath = '@app/views/admin/customer';
        return ['html'   => $this->renderPartial('customerList', [
                'dataProvider' => $provaider,
            ]), 'status' => 'ok', 'files'  => $_FILES, 'post'   => $_POST];
    }

    public function actionAjaxlist_pod($req = null, $page = 1) {
        if (!$req)
            $req = Yii::$app->request->post('req', 1);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($req && isset($req['name'])) {
            if ($req['name'] == 'add-Pod')
                return $this->_addSF($req, 'Pod', '@app/views/admin/customer/_addFirm');
            if ($req['name'] == 'change-Pod') {
                return $this->runAction('ajaxlist_zakone', [
                            'req'    => $req,
                            'page'   => $page,
                            'classN' => 'Pod'
                ]);
            }
            if ($req['name'] == 'view-Pod')
                return $this->_viewSF($req, Yii::$app->request->post('id'), 'Pod', '_viewFirm');
            if ($req['name'] == 'change-main-Pod')
                return $this->_changeSF($req, Yii::$app->request->post('id'), 'Pod', '@app/views/admin/customer/_addFirm');
            if ($req['name'] == 'remove-Pod')
                return $this->_removeSF($req, Yii::$app->request->post('id'), 'Pod');
            if ($req['name'] == 'remove'){
                $tmpId=Yii::$app->request->post('id');
                $zList= \app\models\zakaz\ZakazPod::find()
                        ->where(['pod_id'=>$tmpId])
                        ->select(['zakaz_id'])
                        ->distinct()
                        ->orderBy('zakaz_id')
                        ->asArray()
                        ->all();
                if (!count($zList)){
                    //return ['status'=>'error','errorText'=>'Пока не надо никого удалять '];
                    return $this->_removeSF($req, Yii::$app->request->post('id'), 'Pod');
                }else{
                    return $this->returnErrorFindInZakaz('Подрядчик','zakaz_id',$zList);
               }
                
            }
        }
        $provaider = new ActiveDataProvider([
            'query'      => Pod::find(),
            'pagination' => [
                'pageSize' => self::pageSize,
                'page'     => $page - 1
            ]
        ]);
        $this->viewPath = '@app/views/admin/customer';
        return ['html'   => $this->renderPartial('customerList', [
                'dataProvider' => $provaider,
            ]), 'status' => 'ok', 'files'  => $_FILES, 'post'   => $_POST];
    }
    private function returnErrorFindInZakaz($firmType,$colName,&$zList){
        return ['status'=>'error','errorText'=>"Не возможно удалить.<br>$firmType используется "
                . 'в '.(count($zList)>1?'заказах':'заказе')
                .':<br/>№ '.implode('<br/>№ ', ArrayHelper::getColumn($zList, $colName))];
    }

}
