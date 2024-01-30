<?php

namespace app\models\zakaz;

use Yii;
use app\models\TblUser;
use app\models\admin\Zak;

/**
 * This is the model class for table "zakaz_dirt".
 *
 * @property int $id Индекс
 * @property int $zak_id Идентификатор фирмы заказчика
 * @property int $manager_id Идентификатор менеджера заказчика
 * @property int $ourmanager_id Идентификатор нашего менеджера
 * @property int $production_id Идентификатор подукции
 * @property int $worktypes_id Идентификатор способа печати
 * @property string $name Наименование
 * @property string $total_coast Общая стоимость
 * @property string $other_date Остальные данные JSON
 */
class ZakazDirt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zakaz_dirt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zak_id', 'manager_id', 'ourmanager_id', 'production_id', 'worktypes_id', 'name', 'total_coast', 'other_date'], 'required'],
            [['zak_id', 'manager_id', 'ourmanager_id', 'production_id', 'worktypes_id'], 'integer','min'=>1,'message'=>'Нужно что-нибудь выбрать.'],
            [['name', 'other_date'], 'string'],
            [['dateofadmission'], 'date', 'format'=>'dd.MM.yyyy'],
            [['total_coast'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Индекс',
            'zak_id' => 'Идентификатор фирмы заказчика',
            'manager_id' => 'Идентификатор менеджера заказчика',
            'ourmanager_id' => 'Идентификатор нашего менеджера',
            'production_id' => 'Идентификатор подукции',
            'worktypes_id' => 'Идентификатор способа печати',
            'name' => 'Наименование',
            'total_coast' => 'Общая стоимость',
            'other_date' => 'Остальные данные JSON',
            'empt'=>''
        ];
    }
    public function beforeSave($insert){
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->dateofadmission){
            $this->dateofadmission=Yii::$app->formatter->asDate($this->dateofadmission,'php:Y-m-d');
        }
        return true;
    }
    public function getOurmanagername(){
        if ($el= TblUser::findOne((int)$this->ourmanager_id)){
            return $el->realname;
        }else
            return 'Не найден';
    }
    public function getZak_idText(){
        if ($el= Zak::findOne((int)$this->zak_id)){
            return $el->mainName;
        }else
            return 'Не найден';
        
    }
    public function getTotal_coastText(){
        return Yii::$app->formatter->asInteger($this->total_coast?$this->total_coast:0);
    }
    public function getProduction_idText(){
        if ($this->production_id){
            if ($model= \app\models\tables\Productions::find()->where(['id'=>$this->production_id])->select(['name'])->one()){
                return $model->name;
            }else{
                return 'Не найдено';
            }
        }else{
            return 'Не задано';
        }
    }
    public function getDateofadmissionText(){
        return $this->dateofadmission?Yii::$app->formatter->asDate($this->dateofadmission):'Не задано';
    }

    public function getNumber_of_copiesText(){
        $tmp= \yii\helpers\Json::decode($this->other_date);
        
        return $tmp['number_of_copies'];
    }
    public function getEmpt(){return '';}
    
    private static $_production_idText_cach=[];
    
    private function findProductionOrCache(){
        if (array_key_exists($this->production_id, self::$_production_idText_cach)){
            return self::$_production_idText_cach[$this->production_id];
        }else{
            if ($model= \app\models\tables\Productions::find()->where(['id'=>$this->production_id])->one()){
                self::$_production_idText_cach[$this->production_id]=['name'=>$model->name,'category'=>$model->category,'category2'=>$model->canGetProperty('category2')? \yii\helpers\Json::decode($model->category2):[]];
                return self::$_production_idText_cach[$this->production_id];
            }else{
                return null;
            }
        }        
    }
    public function getProductCategory(){
        if ($this->production_id){
            $prod=$this->findProductionOrCache();
            if ($prod)
                return !$prod['category']?0:(int)$prod['category'];
            else
                return 0;
        }else{
            return 0;
        }
    }
    public function getProductCategory2(){
        if ($this->production_id){
            $prod=$this->findProductionOrCache();
            if ($prod && isset($prod['category2']))
                return $prod['category2'];
            else
                return [];
        }else{
            return [];
        }
    }

}
