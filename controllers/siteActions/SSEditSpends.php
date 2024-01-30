<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\siteActions;
use Yii;

use app\models\Spends;
use app\models\StaticSpends;
/**
 * Description of SSEditSpends
 *
 * @author Алесандр
 */
class SSEditSpends extends SStatistic {
    private function saveSpand(){
        $id=(int)Yii::$app->request->post('id');
        $coast=Yii::$app->request->post('coast');
        if ($id){
            if (!$model=Spends::findOne($id)){
                return ['status'=>'errorMain', 'errorText'=>"Запись id=$id - не найдена"];
            }
        }else{
            $model=new Spends();
        }
        if ($model->load($_POST) && $model->save()) {
            $rVal=[
                'status'=>'ok',
                'updateElementsText'=>$this->prepareUpdatingColumn($model->date)
            ];
//            $rVal['total']=[
//                'total'=>Yii::$app->formatter->asCurrency(Spends::totalMonth($model->date)),
//                'columnName'=>'coast'
//            ];
            return $rVal;
        }else{
            return [
                'status'=>'errorModal',
                'errors'=>isset($_POST[$model->formName()])?$model->errors:"Не переданы данные"
            ];
        }        
    }
    private function prepareUpdatingColumn($date){
        $m=(new \DateTime($date))->format('M');
        return [
            [
                'selector'=>"[data-tabs-$m-total=\"coast\"]",
                'value'=>Yii::$app->formatter->asCurrency(Spends::totalMonth($date))
            ],
            [
                'selector'=>"[data-tabs-$m-total=\"total\"]",
                'value'=>Yii::$app->formatter->asCurrency(StaticSpends::totalByDate($date))
            ]
        ];
    }
    private function removeSpends(){
        $rVal=['status'=>'ok'];
        $id=(int)Yii::$app->request->post('id');
        if ($id){
            if (!$model=Spends::findOne($id)){
                return ['status'=>'errorMain', 'errorText'=>"Запись id=$id - не найдена!"];
            }
        }else{
            return ['status'=>'errorMain', 'errorText'=>"Не передан id записи!"];
        }
        $date=$model->date;
        $model->delete();
        $rVal['updateElementsText']=$this->prepareUpdatingColumn($date);
        
        return $rVal;
    }
    private function updateMainSpendRecord(){
        if ($id=Yii::$app->request->post('id')){
            if ($model= StaticSpends::findOne((int)$id)){
                if ($model->load(Yii::$app->request->post())){
                    if ($model->save()){
                        return ['status'=>'ok','total'=>['total'=>Yii::$app->formatter->asCurrency($model->getTotal()),'columnName'=>'total']];
                    }else{
                        return ['status'=>'errorModel', 'errors'=>$model->errors];
                    }
                }else{
                    return ['status'=>'errorMain', 'errorText'=>"Данные для сохранения не переданны!"];
                }
            }else{
                return ['status'=>'errorMain', 'errorText'=>"Запись с id=$id - не найдена!"];
            }
        }else{
            return ['status'=>'errorMain', 'errorText'=>"Не передан id модели!"];
        }
    }
    public function runSpands(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        switch (Yii::$app->request->get('actionSpend')){
            case 'saveSpend':
                return $this->saveSpand();
                break;
            case 'removeSpend':
                return $this->removeSpends();
                break;
            case 'saveMainSpend':
                return $this->updateMainSpendRecord();
                break;
            default:
                return ['status'=>'errorMain', 'errorText'=>"Не опознаный экшен!"];
                break;
        }
    }

}
