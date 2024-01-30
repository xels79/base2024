<?php

namespace app\models\admin;

use Yii;
use app\models\admin\Pod;

/**
 * This is the model class for table "WOPPod".
 *
 * @property int $WOPid
 * @property int $referensId Сноска к связаной таблице
 * @property int $firm_id К фирме
 *
 * @property FirmPod $firm
 */
class WOPPod extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'WOPPod';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['referensId', 'firm_id'], 'required'],
            [['referensId', 'firm_id'], 'integer'],
            [['firm_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pod::className(), 'targetAttribute' => ['firm_id' => 'firm_id']],
        ];
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($model = WOPPod::find()->where(['referensId' => $this->referensId])->andWhere(['firm_id' => $this->firm_id])->one()) {
            $this->addError('referensId', 'Уже добавлено');
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'WOPid'      => 'Wopid',
            'referensId' => 'Вид мат./раб.',
            'firm_id'    => 'К фирме',
            'nameText'   => 'Тип мат./раб.'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirm() {
        return $this->hasOne(FirmPod::className(), ['firm_id' => 'firm_id']);
    }

    /**
     * @return string
     */
    public function getNameText() {
        if ($model = \app\models\tables\Worktypes::findOne($this->referensId)) {
            return $model->name;
        } else {
            return 'Не найден/удалён';
        }
    }

}
