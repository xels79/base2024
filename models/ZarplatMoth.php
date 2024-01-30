<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zarplat_moth".
 *
 * @property int $id
 * @property int $month Месяц
 * @property int $year Год
 * @property int $day_count Количество рабочих дней
 */
class ZarplatMoth extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'zarplat_moth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [
                [
                    'month',
                    'year',
                    'day_count'],
                'required'],
            [
                [
                    'year',
                    'month',
                    'day_count'],
                'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'        => 'ID',
            'month'     => 'Месяц',
            'year'      => 'Год',
            'day_count' => 'Количество рабочих дней',
        ];
    }

    public function getZarplata() {
        return $this->hasMany(Zarplata::className(), ['month_id' => 'id'])->orderBy('id');
    }

    public function beforeDelete() {
        if (!parent::beforeDelete()) {
            return false;
        }
        foreach (Zarplata::find()->where(['month_id' => $this->id])->all() as $el) {
            $el->delete();
        }
        return true;
    }

}
