<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZMailGetZkazchikEmails
 *
 * @author Александр
 */
use Yii;
use yii\base\Action;
use app\models\admin\ContactZak;
use app\models\admin\ManagerZak;

class ZMailGetZkazchikEmails extends Action{
    public function run(){
        if ($firmId=Yii::$app->request->post('firmId')){
            $q1= ContactZak::find()->where(['firm_id'=>$firmId])->andWhere(['and',['not',['mail'=>null]] ,['not',['mail'=>'']]]);
            $q2= ManagerZak::find()->where(['firm_id'=>$firmId])->andWhere(['and',['not',['mail'=>null]] ,['not',['mail'=>'']]]);
        }else{
            $q1= ContactZak::find()->where(['and',['not',['mail'=>null]] ,['not',['mail'=>'']]]);
            $q2= ManagerZak::find()->where(['and',['not',['mail'=>null]] ,['not',['mail'=>'']]]);
        }
        $q1->select('name,mail');
        $q2->select('name,mail');
        if ($like=Yii::$app->request->post('like')){
            $q1->andFilterWhere(['or', ['like','name',$like], ['like','mail',$like]]);
            $q2->andFilterWhere(['or', ['like','name',$like], ['like','mail',$like]]);
        }
        return ['status'=>'ok','list1'=>$q1->asArray()->all(), 'list2'=>$q2->asArray()->all()];
    }
}
