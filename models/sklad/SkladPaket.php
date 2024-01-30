<?php

namespace app\models\sklad;

use Yii;

/**
 * This is the model class for table "sklad_paket".
 *
 * @property int $id Индекс
 * @property int $color_ref К цвету
 * @property int $firm_ref К фирмы
 * @property double $coast_sz_38x50 Цена 38x50
 * @property double $sklad_sz_38x50 Склад 38x50
 * @property double $rezerv_sz_38x50 Резерв38x50
 * @property double $coast_sz_30x40 Цена 30x40
 * @property double $sklad_sz_30x40 Склад 30x40
 * @property double $rezerv_sz_30x40 Резерв30x40
 * @property double $coast_sz_22x34 Цена 22x34
 * @property double $sklad_sz_22x34 Склад 22x34
 * @property double $rezerv_sz_22x34 Резерв22x34
 * @property double $coast_sz_36x45 Цена 36x45
 * @property double $sklad_sz_36x45 Склад 36x45
 * @property double $rezerv_sz_36x45 Резерв36x45
 * @property double $coast_sz_45x50 Цена 45x50
 * @property double $sklad_sz_45x50 Склад 45x50
 * @property double $rezerv_sz_45x50 Резерв45x50
 * @property double $coast_sz_60x50 Цена 60x50
 * @property double $sklad_sz_60x50 Склад 60x50
 * @property double $rezerv_sz_60x50 Резерв60x50
 * @property double $coast_sz_70x50 Цена 70x50
 * @property double $sklad_sz_70x50 Склад 70x50
 * @property double $rezerv_sz_70x50 Резерв70x50
 * @property double $coast_sz_50x60 Цена 50x60
 * @property double $sklad_sz_50x60 Склад 50x60
 * @property double $rezerv_sz_50x60 Резерв50x60
 * @property double $coast_sz_70x60 Цена 70x60
 * @property double $sklad_sz_70x60 Склад 70x60
 * @property double $rezerv_sz_70x60 Резерв70x60
 *
 * @property SkladPaketInfo $colorRef
 * @property SkladPaketFirms $firmRef
 */
class SkladPaket extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad_paket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['color_ref', 'firm_ref'], 'required'],
            [['color_ref', 'firm_ref'], 'integer'],
            [['coast_sz_38x50', 'sklad_sz_38x50', 'rezerv_sz_38x50', 'coast_sz_30x40', 'sklad_sz_30x40', 'rezerv_sz_30x40', 'coast_sz_22x34', 'sklad_sz_22x34', 'rezerv_sz_22x34', 'coast_sz_36x45', 'sklad_sz_36x45', 'rezerv_sz_36x45', 'coast_sz_45x50', 'sklad_sz_45x50', 'rezerv_sz_45x50', 'coast_sz_60x50', 'sklad_sz_60x50', 'rezerv_sz_60x50', 'coast_sz_70x50', 'sklad_sz_70x50', 'rezerv_sz_70x50', 'coast_sz_50x60', 'sklad_sz_50x60', 'rezerv_sz_50x60', 'coast_sz_70x60', 'sklad_sz_70x60', 'rezerv_sz_70x60'], 'number'],
            [['color_ref'], 'exist', 'skipOnError' => true, 'targetClass' => SkladPaketInfo::className(), 'targetAttribute' => ['color_ref' => 'id']],
            [['firm_ref'], 'exist', 'skipOnError' => true, 'targetClass' => SkladPaketFirms::className(), 'targetAttribute' => ['firm_ref' => 'id']],
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
            'coast_sz_38x50' => 'Цена 38x50',
            'sklad_sz_38x50' => 'Склад 38x50',
            'rezerv_sz_38x50' => 'Резерв38x50',
            'coast_sz_30x40' => 'Цена 30x40',
            'sklad_sz_30x40' => 'Склад 30x40',
            'rezerv_sz_30x40' => 'Резерв30x40',
            'coast_sz_22x34' => 'Цена 22x34',
            'sklad_sz_22x34' => 'Склад 22x34',
            'rezerv_sz_22x34' => 'Резерв22x34',
            'coast_sz_36x45' => 'Цена 36x45',
            'sklad_sz_36x45' => 'Склад 36x45',
            'rezerv_sz_36x45' => 'Резерв36x45',
            'coast_sz_45x50' => 'Цена 45x50',
            'sklad_sz_45x50' => 'Склад 45x50',
            'rezerv_sz_45x50' => 'Резерв45x50',
            'coast_sz_60x50' => 'Цена 60x50',
            'sklad_sz_60x50' => 'Склад 60x50',
            'rezerv_sz_60x50' => 'Резерв60x50',
            'coast_sz_70x50' => 'Цена 70x50',
            'sklad_sz_70x50' => 'Склад 70x50',
            'rezerv_sz_70x50' => 'Резерв70x50',
            'coast_sz_50x60' => 'Цена 50x60',
            'sklad_sz_50x60' => 'Склад 50x60',
            'rezerv_sz_50x60' => 'Резерв50x60',
            'coast_sz_70x60' => 'Цена 70x60',
            'sklad_sz_70x60' => 'Склад 70x60',
            'rezerv_sz_70x60' => 'Резерв70x60',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColorRef()
    {
        return $this->hasOne(SkladPaketInfo::className(), ['id' => 'color_ref']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirmRef()
    {
        return $this->hasOne(SkladPaketFirms::className(), ['id' => 'firm_ref']);
    }
}
