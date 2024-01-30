<?php

namespace app\models\sklad;

use Yii;

/**
 * This is the model class for table "sklad_colors_info".
 *
 * @property int $id Индекс
 * @property string $name Название
 * @property int $color Цвет
 *
 * @property SkladColors[] $skladColors
 */
class SkladColorsInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad_colors_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'color'], 'required'],
            [['color'], 'string', 'max'=>7],
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
            'name' => 'Название',
            'color' => 'Цвет',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSkladColors()
    {
        return $this->hasMany(SkladColors::className(), ['color_ref' => 'id']);
    }
}
