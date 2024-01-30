<?php

namespace app\models\admin;

use Yii;
use app\models\admin\Post;

/**
 * This is the model class for table "WOPPost".
 *
 * @property int $WOPid
 * @property int $referensId Сноска к связаной таблице
 * @property int $firm_id К фирме
 *
 * @property FirmPost $firm
 */
class WOPPost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'WOPPost';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['referensId', 'firm_id'], 'required'],
            [['referensId', 'firm_id'], 'integer'],
            [['firm_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['firm_id' => 'firm_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'WOPid' => 'Wopid',
            'referensId' => 'Сноска к связаной таблице',
            'firm_id' => 'К фирме',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirm()
    {
        return $this->hasOne(FirmPost::className(), ['firm_id' => 'firm_id']);
    }
    
    /*
     * @return string
     */
    public function getNameText(){
        if ($model= \app\models\tables\WorkOrproductType::findOne($this->referensId)){
            return $model->name;
        }else{
            return 'Не найден/удалён';
        }
    }

}
