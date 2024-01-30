<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\admin;

/**
 * Description of Address
 *
 * @author Александр
 * @property int $contactZak_id Идентификатор
 * @property int $post_id Должность
 * @property string $name Ф.И.О
 * @property string $phone Телефон
 * @property string $mail E-mail
 * @property integer $status_id Статус
 * @property string $comment Коментарий
 * @property integer $firm_id Идентификатор фирмы
 * @property string $additional Мобильный
 *
 * @property string $postText Должность текстовый
 * @property string $statusText Статус текстовый
 */
class Contact extends \app\models\BaseActiveRecord {

    public static $posts = [
        1 => 'Менеджер',
        3 => 'Курьер',
        0 => 'Ген.Директор',
        4 => 'Нач. производства',
        2 => 'Бухгалтерия',
    ];
    public static $statuss = [
        'Работает',
        'Уволен(а)',
        'Отпуск'
    ];

    public function attributeLabels()
    {
        return \yii\helpers\ArrayHelper::merge( parent::attributeLabels(), [
                    'postText'   => 'Должность',
                    'statusText' => 'Статус',
                    'additional' => 'Мобильный'
                ] );
    }

    public function getPostText()
    {
        return self::$posts[$this->post_id];
    }

    public function getStatusText()
    {
        return self::$statuss[$this->status_id];
    }

    public function rules()
    {
        return [
            [['post_id', 'status_id', 'firm_id'], 'integer'],
            [['name', 'phone', 'comment'], 'string'],
            [['additional'], 'string', 'max' => 16],
            [['name', 'phone', 'firm_id'], 'required', 'message' => 'Должно быть указано.'],
            [['mail'], 'email']
        ];
    }

}
