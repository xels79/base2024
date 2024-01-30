<?php

namespace app\models\sklad;

use Yii;

/**
 * This is the model class for table "sklad_paket_firms".
 *
 * @property int $id Индекс
 * @property string $name Серия
 *
 * @property SkladPaket[] $skladPakets
 */
class SkladPaketFirms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad_paket_firms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Индекс',
            'name' => 'Серия',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSkladPakets()
    {
        return $this->hasMany(SkladPaket::className(), ['firm_ref' => 'id']);
    }
}
