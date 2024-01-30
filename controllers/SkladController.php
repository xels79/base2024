<?php

namespace app\controllers;
use yii;
use \app\controllers\ControllerTait;
use \app\models\sklad\SkladColors;
use \app\models\sklad\SkladColorSerias;
use \app\models\sklad\SkladPaketFirms;
use \app\models\sklad\SkladPaket;
use \app\models\sklad\SkladBumagaFirms;
use \app\models\sklad\SkladBumaga;
use \app\models\sklad\SkladColorOtherDate;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
class SkladController extends ControllerMain
{
    use ControllerTait;
    public $defaultAction='indexsklad';
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'indexsklad'=>['get'],
                    'updatecolors'=>['post']
                ]
            ]
        ]);
    }

    public function actionIndexsklad()
    {
        $this->layout='main_2.php';
        $series=ArrayHelper::map(SkladColorSerias::find()->asArray()->all(),'id','name');
        $colorDate=[];
        $maxRow=0;
        foreach ($series as $key=>$val){
            $tmp=[
                'name'=>$val,
                'content'=>SkladColors::find()
                ->where(['serias_ref'=>$key])
                ->with('colorRef')
                ->asArray()
                ->all()
            ];
            if (count($tmp['content'])>$maxRow) $maxRow=count($tmp['content']);
            $colorDate[]=$tmp;
        }
        $bumaga=[];
        foreach (SkladBumagaFirms::find()->asArray()->all() as $el){
            $bumaga[]=[
                'name'=>$el['name'],
                'content'=>SkladBumaga::find()
                    ->where(['firm_ref'=>$el['id']])
                    ->with('colorRef')
                    ->asArray()
                    ->all()
            ];
        }
        $colorMaxDate=SkladColors::find()->max('lastupdate');
        $otherMaxDate=SkladColorOtherDate::find()->max('lastupdate');
        if ((int)Yii::$app->formatter->asTimestamp($otherMaxDate)>(int)Yii::$app->formatter->asTimestamp($colorMaxDate)) $colorMaxDate=$otherMaxDate;
        //$colorDate=;
        return $this->render('index',[
            'colorMaxDate'=>$colorMaxDate,
            'paketMaxDate'=>SkladPaket::find()->max('lastupdate'),
            'bumagaMaxDate'=>SkladBumaga::find()->max('lastupdate'),
            'colorDate'=>$colorDate,
            'maxRow'=>$maxRow,
            'chemikal'=>SkladColorOtherDate::find()->asArray()->all(),
//            'paketAll'=>SkladPaket::find()->with('color_ref')->asArray()->all(),
            'paketFirmAll'=>SkladPaketFirms::find()->with('skladPakets.colorRef')->asArray()->all(),
            'bumaga'=>$bumaga
        ]);
    }
    public function actionUpdatecolors(){
        Yii::$app->response->format=yii\web\Response::FORMAT_JSON;
        $id=Yii::$app->request->post('id');
        $isChemikal=Yii::$app->request->post('datachemikal',0);
        $val=(double)Yii::$app->request->post('val',0);
        $dColN=Yii::$app->request->post('datacolumnname');
        if ($id && $dColN){
            switch ($isChemikal){
                case 'other':
                    $model=SkladColorOtherDate::findOne((int)$id);
                    $maxQery=SkladColorOtherDate::find();
                    break;
                case 'paket':
                    $model=SkladPaket::findOne((int)$id);
                    $maxQery=SkladPaket::find();
                    break;
                case 'bumaga':
                    $model=SkladBumaga::findOne((int)$id);
                    $maxQery=SkladBumaga::find();
                    break;
                default:
                    $model=SkladColors::findOne((int)$id);
                    $maxQery=SkladColors::find();
                    break;
            }
            if ($model){
                $model[$dColN]=$val;
                if ($model->save()){
                    $maxDate=$maxQery->max('lastupdate');
                    return [
                        'status'=>'ok',
                        'post'=>Yii::$app->request->post(),
                        'maxDate'=>Yii::$app->formatter->asDate($maxDate),
                        'maxTime'=>Yii::$app->formatter->asTime($maxDate)
                    ];
                }else{
                    return ['status'=>'error','errorText'=>"Ошибки сохранения",'errors'=>$model->errors];
                }
            }else
                return ['status'=>'error','errorText'=>"Запись №$id не найдена"];
        }else
            return ['status'=>'error','errorText'=>'Неверные парамтры'];
    }

}
