<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "spendingtoyourselftable".
 *
 * @property int $id
 * @property float|null $summ
 */
class Spendingtoyourselftable extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'spendingtoyourselftable';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['summ'], 'number'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'summ' => 'Summ',
        ];
    }
}
