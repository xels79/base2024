<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "workOrproductType".
 *
 * @property int $id
 * @property string $name Название
 * @property int $category Тип продукции 1
 * @property int $category2 Тип продукции 2
 * @property int $update_time Время последнего обновления
 */
class WorkOrproductType extends \yii\db\ActiveRecord {

    public static $catTextArray = ['Листовая', 'Пакеты п/э', 'Сувенирка', 'Уф-лак'];
    public static $catText2Array = ['-1' => 'Нет', 'Листовая', 'Пакеты п/э', 'Сувенирка', 'Уф-лак'];

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->update_time = time();
            return true;
        } else
            return false;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'workOrproductType';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
            [['category', 'category2'], 'integer'],
            ['name', 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'        => 'ID',
            'name'      => 'Название',
            'category'  => 'Категория',
            'category2' => 'Категория',
        ];
    }

    public function getCatText() {
        if (array_key_exists($this->category, self::$catTextArray))
            return self::$catTextArray[$this->category];
        else
            return '';
    }

    public function getCat2Text() {
        if ($this->category2 == -1)
            return 'Нет';
        if (array_key_exists($this->category2, self::$catText2Array))
            return self::$catText2Array[$this->category2];
        else
            return '';
    }

}
