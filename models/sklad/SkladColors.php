<?php

namespace app\models\sklad;

use Yii;

/**
 * This is the model class for table "sklad_colors".
 *
 * @property int $id Индекс
 * @property int $serias_ref К серии
 * @property int $color_ref К цвету
 *
 * @property SkladColorsInfo $colorRef
 * @property SkladColorSerias $seriasRef
 */
class SkladColors extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad_colors';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['serias_ref', 'color_ref','article'], 'required'],
            [['serias_ref', 'color_ref','article'], 'integer'],
            [['proizvodstvo','sklad','procurement'], 'number'],
            [['proizvodstvo','sklad','procurement'], 'default','value'=>0],
            [['color_ref'], 'exist', 'skipOnError' => true, 'targetClass' => SkladColorsInfo::className(), 'targetAttribute' => ['color_ref' => 'id']],
            [['serias_ref'], 'exist', 'skipOnError' => true, 'targetClass' => SkladColorSerias::className(), 'targetAttribute' => ['serias_ref' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Индекс',
            'serias_ref' => 'К серии',
            'color_ref' => 'К цвету',
            'article'=>'Артикул',
            'proizvodstvo'=>'Пр-во',
            'sklad'=>'Склад',
            'procurement'=>'Купить',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColorRef()
    {
        return $this->hasOne(SkladColorsInfo::className(), ['id' => 'color_ref']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeriasRef()
    {
        return $this->hasOne(SkladColorSerias::className(), ['id' => 'serias_ref']);
    }
}
