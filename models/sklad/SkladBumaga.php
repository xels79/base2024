<?php

namespace app\models\sklad;

use Yii;

/**
 * This is the model class for table "sklad_bumaga".
 *
 * @property int $id Индекс
 * @property int $color_ref К цвету
 * @property int $firm_ref К фирмы
 * @property double $sklad Склад
 * @property double $rezerv Резерв
 *
 * @property SkladBumagaInfo $colorRef
 * @property SkladBumagaFirms $firmRef
 */
class SkladBumaga extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad_bumaga';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['color_ref', 'firm_ref'], 'required'],
            [['color_ref', 'firm_ref'], 'integer'],
            [['sklad', 'rezerv', 'lastupdate'], 'number'],
            [['color_ref'], 'exist', 'skipOnError' => true, 'targetClass' => SkladBumagaInfo::className(), 'targetAttribute' => ['color_ref' => 'id']],
            [['firm_ref'], 'exist', 'skipOnError' => true, 'targetClass' => SkladBumagaFirms::className(), 'targetAttribute' => ['firm_ref' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Индекс',
            'color_ref' => 'К цвету',
            'firm_ref' => 'К фирмы',
            'sklad' => 'Склад',
            'rezerv' => 'Резерв',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColorRef()
    {
        return $this->hasOne(SkladBumagaInfo::className(), ['id' => 'color_ref']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirmRef()
    {
        return $this->hasOne(SkladBumagaFirms::className(), ['id' => 'firm_ref']);
    }
    public function beforeValidate(){
        $this->lastupdate = time();
    }

}
