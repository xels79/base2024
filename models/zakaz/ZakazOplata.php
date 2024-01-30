<?php

namespace app\models\zakaz;

use Yii;

/**
 * This is the model class for table "zakaz_oplata".
 *
 * @property int $id
 * @property int $zakaz_id К заказу
 * @property string $date Дата
 * @property string $summ Сумма
 */
class ZakazOplata extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zakaz_oplata';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['zakaz_id', 'date', 'summ'], 'required'],
            [['zakaz_id'], 'integer'],
            [['date','dateText'], 'safe'],
            [['summ'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'zakaz_id' => 'К заказу',
            'date' => 'Дата',
            'summ' => 'Сумма',
        ];
    }
    public function getDateText(){
        return Yii::$app->formatter->asDate($this->date,'php:d.m.Y');
    }
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->date=Yii::$app->formatter->asDate($this->date,'php:Y-m-d');
        return true;
    }
    public function getOpl(){
        return $this->hasOne(Zakaz::className(), ['id' => 'zakaz_id']);
    }

}
