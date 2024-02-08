<?php

namespace app\models\sklad;

use Yii;

/**
 * This is the model class for table "sklad_color_other_date".
 *
 * @property int $id Индекс
 * @property double $proizvodstvo Пр-во
 * @property double $sklad Склад
 * @property double $procurement Купить
 */
class SkladColorOtherDate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sklad_color_other_date';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['proizvodstvo', 'sklad', 'procurement', 'lastupdate'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Индекс',
            'proizvodstvo' => 'Пр-во',
            'sklad' => 'Склад',
            'procurement' => 'Купить',
        ];
    }
    public function beforeValidate(){
        $this->lastupdate = time();
    }

}
