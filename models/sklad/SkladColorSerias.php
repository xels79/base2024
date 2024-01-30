<?php

namespace app\models\sklad;

use Yii;

/**
 * This is the model class for table "sklad_color_serias".
 *
 * @property int $id Индекс
 * @property string $name Серия
 *
 * @property SkladColors[] $skladColors
 */
class SkladColorSerias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad_color_serias';
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
    public function getSkladColors()
    {
        return $this->hasMany(SkladColors::className(), ['serias_ref' => 'id']);
    }
}
