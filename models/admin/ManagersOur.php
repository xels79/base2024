<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\admin;

/**
 * Description of ManagersOur
 *
 * @author Александр
 */
use app\models\admin\OurFirm;

class ManagersOur extends \app\models\BaseActiveRecord {

    public $fotoFile;
    public $fotoremove = null;
    public static $post_list = [
        'Водитель',
        'Курьер',
        'Помошник',
        'Печатник',
        'Менеджер',
        'Дизайнер',
        'Бухгалтер',
        'Нач. производства',
        'Нач. менеджеров',
        'Зам. директора',
        'Директор'
    ];
    public static $payment_list = [
        'Почасовая',
        'Зарплата',
        'Оклад',
        'Сделка',
        '%'
    ];
    public static $status_list = [
        'Работает',
        'Приходящий',
        'Фрилансер',
        'Уволен'
    ];
    public static $payment_list_title = [
        'Руб./час.',
        'Количество рабочих дней в месяц',
        'Чистый оклад',
        'Прокат/руб.',
        'Чистый процент'
    ];

    public function rules() {
        return [
            [
                [
                    'firm_id',
                    'post',
                    'payment_id',
                    'profit',
                    'superprofit',
                    'piecework',
                    'ourfirm_profit',
                    'material_profit'
                ],
                'integer'],
            [['ourfirm_profit', 'material_profit', 'superprofit', 'profit', 'piecework',
            'wages', 'normal'], 'default', 'value' => 0],
            [
                [
                    'status_id'
                ],
                'integer',
                'max' => 255
            ],
            [
                [
                    'phone1',
                    'name',
                    'phone2',
                    'address',
                    'snils',
                    'passport_series',
                    'passport_number',
                    'passport_given',
                    'registration',
                    'foto',
                    'fotoremove'],
                'string'],
            [
                [
                    'passport_given_date',
                    'birthday'],
                'date'],
            [
                [
                    'phone1',
                    'name',
                    'inn',
                    'snils',
                    'post'],
                'required',
                'message' => 'Должно быть указано.'],
            [
                [
                    'employed', 'hasPercents'],
                'boolean'],
            [
                [
                    'wages',
                    'inn',
                    'normal'],
                'number'],
            [
                [
                    'recycling_rate'],
                'double'],
            [
                [
                    'fotoFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions'  => 'png, gif'],
            [
                [
                    'profit'],
                'default',
                'value' => '10'],
            [
                [
                    'inn'],
                'unique'],
        ];
    }

    public function attributeLabels() {
        $tmp = [
            'postText'   => 'Должность',
            'fotoFile'   => 'Фото',
            'statusText' => 'Статус'
        ];
        if ($firm = OurFirm::find()->one()) {
            $tmp['ourfirm_profit'] = $firm->mainName;
        }
        return \yii\helpers\ArrayHelper::merge(parent::attributeLabels(), $tmp);
    }

    public function getPostText() {
        return self::$post_list[$this->post];
    }

    public function getStatusText() {
        return self::$status_list[$this->status_id];
    }

    public function saveFilesAttr() {
        return[
            'fotoFile' => 'foto',
        ];
    }

    public static function tableName() {
        return 'managerOur';
    }

}
