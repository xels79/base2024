<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zametkiTabs".
 *
 * @property int $id
 * @property string $name Название колонки
 * @property int $add_time Время создания
 * @property array $zametki Все заметки
 */
class ZametkiTabs extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'zametkiTabs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            ['name', 'string', 'max' => 120],
            ['add_time', 'integer'],
            ['name', 'unique'],
            ['name', 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'       => 'ID',
            'name'     => 'Название колонки',
            'add_time' => 'Время создания',
        ];
    }

    public function getZametki() {
        return $this->hasMany(Zametki::className(), ['tabId' => 'id'])->select(['id', 'name', 'tabId', 'add_time', 'update_time', 'size']); //->column();
    }

    public function beforeDelete() {
        if (!parent::beforeDelete()) {
            return false;
        } else {
            Zametki::deleteAll(['tabId' => $this->id]);
            return true;
        }
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->isNewRecord) {
            $this->add_time = time();
        }
        return true;
    }

}
