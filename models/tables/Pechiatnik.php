<?php

namespace app\models\tables;

use Yii;
use app\models\admin\ManagersOur;
/**
 * This is the model class for table "pechiatnik".
 *
 * @property int $id
 * @property int $m_id ИД Печатника
 * @property int $z_id ИД Заказа
 * @property string $z_time Время
 * @property string $z_date Дата
 */
class Pechiatnik extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pechiatnik';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['m_id', 'z_id'], 'required'],
            [['m_id', 'z_id'], 'integer'],
            [['z_date','ready'], 'safe'],
            [['z_time'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'm_id' => 'ИД Печатника',
            'z_id' => 'ИД Заказа',
            'z_time' => 'Время',
            'z_date' => 'Дата',
            'ready' => 'Прокат готов дата'
        ];
    }
    public function getManagersOur(){
        return $this->hasOne(ManagersOur::className(), ['managerOur_id'=>'m_id']);
    }
}
