<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\tables;

/**
 * Description of MaterialsOnFirmsSearch
 *
 * @author Александр
 */
use yii;
use yii\data\ActiveDataProvider;
use app\models\admin\Post;
use app\models\tables\Tablestypes;
use app\models\tables\DependTable;
use app\models\tables\DependTables;

class MaterialsOnFirmsSearch extends MaterialsOnFirms{
    public static $defaultPageSize=14;
    private $_calculated_colum_cahce=null;
    public function rules()
    {
        return [
            [['firmName', 'matType', 'cCol_0', 'cCol_1', 'cCol_2', 'cCol_3', 'cCol_4', 'cCol_5', 'cCol_6', 'cCol_7', 'cCol_8', 'cCol_9'], 'safe'],
        ];
    }
    public function calculated_colum($m_type,$m_id=null){
        if ($this->_calculated_colum_cahce!==null){
            return $this->_calculated_colum_cahce;
        }else{
            $this->_calculated_colum_cahce = parent::calculated_colum($m_type,$m_id);
            return $this->_calculated_colum_cahce;
        }
    }
    public function getFirmName(){
        return $this->_firmName;
    }
    public function getMatType(){
        return $this->_matType;
    }
    public function __get($name){
        $tmp= explode('_', $name);
        if (count($tmp)===2 && $tmp[0]==='cCol'){
            if (array_key_exists((int)$tmp[1], $this->_cCols)){
                return $this->_cCols[(int)$tmp[1]];
            }else{
                return null;
            }
        }else{
            return parent::__get($name);
        }
    }
    public function search($params){
        $dataProvider = new ActiveDataProvider([
            'query' => MaterialsOnFirms::find(),
            'sort'=>false,
            'pagination' => [
                'pageSize' => (int)Yii::$app->request->post('pageSize',self::$defaultPageSize),
                'page' => (int)Yii::$app->request->post('page',0),
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->_firmName) $dataProvider->query->andFilterWhere(['firm_id'=>$this->_firmName]);
        if ($this->_matType){
            $dataProvider->query->andFilterWhere(['m_type'=>$this->_matType]);
            if (count($this->_cCols)){
                Yii::debug($this->_cCols,'MaterialsOnFirmsSearch_cCols');
                $struct=Tablestypes::find()->where(['id'=>$this->_matType])->one();
                $name=$struct->translitName;
                if ($struct= \yii\helpers\Json::decode($struct->struct)){
                    foreach ($struct as $el){
                        $rVal[]=[
                            'label'=>$el,
                            'value'=>'нет'
                        ];
                    }
                }
                $struct= DependTables::dependsTablesNamesFromRus($name, $struct);
                $i=count($struct)-1;
//                $dataProvider->query= DependTable::createObject($struct[$i])->find()->where(['`'.$struct[$i].'`.`id`'=>'materials_on_firms.m_id']);
//                if (array_key_exists($i, $this->_cCols)){
//                    $dataProvider->query->join('LEFT OUTER JOIN',$struct[$i],[$struct[$i].'.id'=>'materials_on_firms.m_id',$struct[$i].'.id'=>yii\helpers\Json::decode($this->_cCols[count($struct)-1-$i] )]);
//                }else{
                    $dataProvider->query->join('LEFT OUTER JOIN',$struct[$i],'`'.$struct[$i].'`.`id`=`materials_on_firms`.`m_id`');
//                }
                Yii::debug([
                    'array'=>$this->_cCols,
                    '$i'=>$i,
                    '$i_comupte'=>$i,
                    'count'=>count($struct),
                    'value'=>$this->_cCols[$i],
                    'jsonDecode'=>yii\helpers\Json::decode($this->_cCols[$i])
                ],'MaterialsOnFirmsSearch_cCols');
                if (array_key_exists($i, $this->_cCols)){
                    $dataProvider->query->andFilterWhere([$struct[$i].'.id'=> yii\helpers\Json::decode($this->_cCols[$i])]);
                }
                $select=['`'.$struct[$i].'`.`name` as `name'.$i.'`'];
                $prev=$struct[$i--];
                $order='`'.$struct[$i].'`.`name`';
                for (;$i>-1;){
                    $dataProvider->query->join('LEFT OUTER JOIN',$struct[$i],'`'.$struct[$i].'`.`'.DependTables::$pKey.'`=`'.$prev.'`.`'.DependTables::$reference.'`');
                    $select[]='`'.$struct[$i].'`.`name` as `name'.$i.'`';
                    
                    Yii::debug([
                        'array'=>$this->_cCols,
                        '$i'=>$i,
                        '$i_comupte'=>$i,
                        'count'=>count($struct),
                        'value'=>$this->_cCols[$i],
                        'jsonDecode'=>yii\helpers\Json::decode($this->_cCols[$i])
                    ],'MaterialsOnFirmsSearch_cCols');
                    if (array_key_exists($i, $this->_cCols)){
                        $dataProvider->query->andFilterWhere([$struct[$i].'.id'=> yii\helpers\Json::decode($this->_cCols[$i])]);
                    }
                    //$i=count($struct)-1;
                    $order='`'.$struct[$i].'`.`name`';
                    $select=['`'.$struct[$i].'`.`name` as `name'.$i.'`'];
                    $prev=$struct[$i--];

                }
                Yii::debug($order, 'MaterialsOnFirmsSearch_order');
                //$dataProvider->query->orderBy($order);
                //$qdataProvider->uery->select($select);
//                    $res=$query->asArray()->one();
            }
        }
        return $dataProvider;
    }
}
