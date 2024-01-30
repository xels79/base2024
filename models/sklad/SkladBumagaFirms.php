<?php

namespace app\models\sklad;

use Yii;

/**
 * This is the model class for table "sklad_bumaga_firms".
 *
 * @property int $id Индекс
 * @property string $name Серия
 *
 * @property SkladBumaga[] $skladBumagas
 */
class SkladBumagaFirms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad_bumaga_firms';
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
    public function getSkladBumagas()
    {
        return $this->hasMany(SkladBumaga::className(), ['firm_ref' => 'id']);
    }
}
