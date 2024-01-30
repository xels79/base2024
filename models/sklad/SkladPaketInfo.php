<?php

namespace app\models\sklad;

use Yii;

/**
 * This is the model class for table "sklad_paket_info".
 *
 * @property int $id Индекс
 * @property string $name Название
 * @property string $color Цвет
 *
 * @property SkladPaket[] $skladPakets
 */
class SkladPaketInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad_paket_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'color'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 7],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Индекс',
            'name' => 'Название',
            'color' => 'Цвет',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSkladPakets()
    {
        return $this->hasMany(SkladPaket::className(), ['color_ref' => 'id']);
    }
}
