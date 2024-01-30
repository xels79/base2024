<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakaz\ZakazActions;

/**
 * Description of ZSmalltablelist
 *
 * @author Александр
 */
use Yii;
use app\models\zakaz\Zakaz;
use yii\helpers\ArrayHelper;
use app\models\admin\RekvizitZak;

class ZSmalltablelist extends ZAction {

    private function queryCheckCategory($query, $select = '*') {
        $query->select($select);
        if ($category = Yii::$app->request->post('category')) {
            if (is_array($category)) {
                $query->where(['in', 'category', $category]);
            } else if (is_numeric($category)) {
                $query->where(['category' => $category]);
            }
        }
        return $query->orderBy('name');
    }

    public function run() {
        $tName = Yii::$app->request->post('tableName');
        switch ($tName) {
            case 'contactPod':
                if (!$this->postID) {
                    $tmp = [];
                    break;
                }
                $tmp = \app\models\admin\ContactPod::find()->where(['firm_id' => $this->postID])->select(['contactPod_id as id', 'name'])->orderBy('name')->asArray()->all();
                break;
            case 'worktypes':
//                $tmp1 = $this->queryCheckCategory(\app\models\tables\Worktypes::find(), ['id', 'name'])
//                        ->orderBy('name')
//                        ->andWhere(['in', 'name', ['Уф-лак', 'Шелкография', 'Тампопечать']])
//                        ->asArray()
//                        ->all();
                $tmp1 = [];
                if ($tmp0 = ($this->queryCheckCategory(\app\models\tables\Worktypes::find(), ['id', 'name'])->andWhere(['name' => 'Уф-лак'])->one())) {
                    $tmp1[] = $tmp0->toArray();
                }
                if ($tmp0 = $this->queryCheckCategory(\app\models\tables\Worktypes::find(), ['id', 'name'])->andWhere(['name' => 'Шелкография'])->one()) {
                    $tmp1[] = $tmp0->toArray();
                }
                if ($tmp0 = $this->queryCheckCategory(\app\models\tables\Worktypes::find(), ['id', 'name'])->andWhere(['name' => 'Тампопечать'])->one()) {
                    $tmp1[] = $tmp0->toArray();
                }
                $tmp = $this->queryCheckCategory(\app\models\tables\Worktypes::find(), ['id', 'name'])
                        ->orderBy('name')
                        ->andWhere(['not', ['in', 'name', ['Уф-лак', 'Шелкография', 'Тампопечать']]])
                        ->asArray()
                        ->all();
                $tmp = ArrayHelper::merge($tmp1, $tmp);
                break;
            case 'production':
                $tmp = $this->queryCheckCategory(\app\models\tables\Productions::find(), ['id', 'name', 'category', 'category2'])->asArray()->all();
                break;
            case 'stage':
                $tmp = Zakaz::$_stage; //['Согласование', 'У дизайнера', 'Печать', 'Готов' ,'Сдан'];
                break; //method_of_payment
            case 'division_of_work':
                $tmp = ['50/50', '100%', '0%'];
                break;
            case 'method_of_payment':
                $tmp = ['Договорная', 'Счет', 'В/З'];
                break;
            case 'invoice_from_this_company':
                $tmp = ArrayHelper::merge(['0' => 'Не выбран'], ArrayHelper::map(\app\models\admin\RekvizitOur::find()->orderBy('name')->asArray()->all(), 'rekvizit_id', 'name'));
                break;
            case 'zakUrFace_list':
                if (!$zakazchikId = (int) Yii::$app->request->post('zakazchikId')) {
                    $tmp = ['заказчик не выбран'];
                } else {
                    $tmp = ArrayHelper::map(RekvizitZak::find()->where(['firm_id' => $zakazchikId])->orderBy('name')->asArray()->all(), 'rekvizit_id', 'name');
                    if (count($tmp)) {
                        $tmp = ArrayHelper::merge(['0' => 'Не выбран'], $tmp);
                    } else {
                        $tmp = ['пусто'];
                    }
                }
                break;
            default:
                $tmp = [];
        }
        return ['status' => 'ok', 'source' => $this->createForSource($tmp), 'post' => Yii::$app->request->post()];
    }

}
