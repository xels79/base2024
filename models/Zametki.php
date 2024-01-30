<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zametki".
 *
 * @property int $id
 * @property int $tabId Сноска на вкладку
 * @property int $size Для сохранения размера
 * @property int $add_time Время создания
 * @property int $update_time Время последнего обновления
 * @property string $name Название колонки
 * @property string $content Содержимое
 * @property array $zamitkiTab Вкладка
 */
class Zametki extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'zametki';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['tabId', 'name'], 'required'],
            [['tabId', 'add_time', 'update_time', 'size'], 'integer'],
            ['content', 'string'],
            ['name', 'string', 'max' => 120],
            ['name', 'unique']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'          => 'ID',
            'tabId'       => 'Сноска на вкладку',
            'name'        => 'Название заметки',
            'content'     => 'Содержимое',
            'add_time'    => 'Время создания',
            'update_time' => 'Время последнего обновления'
        ];
    }

    public function getZametkiTab() {
        return $this->hasOne(ZametkiTabs::className(), ['tabId' => 'id']);
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->isNewRecord) {
            $this->add_time = time();
        }
        $this->update_time = time();
        return true;
    }

}
