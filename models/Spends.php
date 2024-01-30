<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "spends".
 *
 * @property int $id
 * @property string $date Дата
 * @property string $name Название
 * @property float|null $coast Стоимость
 */
class Spends extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'spends';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'name'], 'required'],
            [['date'], 'safe'],
            [['coast'], 'number'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Дата'),
            'name' => Yii::t('app', 'Название'),
            'coast' => Yii::t('app', 'Стоимость'),
        ];
    }
    public static function totalMonth($date){
        $dt=new \DateTime($date);
        $fr=new \DateTime($dt->format('Y-m-01'));
        $to = new \DateTime($fr->format('Y-m-d'));
        $to->add(new \DateInterval('P1M'));
        return self::find()
            ->where(['>=', 'date', $fr->format('Y-m-d')])
            ->andWhere(['<', 'date', $to->format('Y-m-d')])
            ->sum('coast');
    }
}
