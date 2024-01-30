<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\MyHelplers;

/**
 * Description of Mail
 *
 * @author Александр
 * @var $fromEmail string
 * @var $toEmail string
 * @var $subject string
 * @var $fromName string
 * @var $mailBody string
 */
class Mail extends Model {

    public $toEmail;
    public $subject;
    public $fromName;
    public $fromEmail;
    public $mailBody;

    public function rules() {
        return [
            [['toEmail', 'fromEmail'], 'email'],
            [['subject', 'fromName', 'mailBody'], 'string'],
            [['toEmail', 'subject', 'fromName', 'fromEmail'], 'required']
        ];
    }

    public function attributeLabels() {
        return [
            'toEmail'   => 'Кому',
            'subject'   => 'Тема',
            'fromName'  => 'От',
            'fromEmail' => 'Ваша почта',
            'mailBody'  => 'Сообщение'
        ];
    }

    public function sendEmail($file = null) {
        //return Yii::$app->response->sendContentAsFile($tmp['content'], $tmp['fDetail']['filename'].'.'.$tmp['fDetail']['extension'],['mimeType'=>MyHelplers::$mime_types[$tmp['fDetail']['extension']]]);
        if ($this->validate()) {
            $message = Yii::$app->mailer->compose()
                    ->setTo($this->toEmail)
                    ->setFrom([$this->fromEmail => $this->fromName])
                    ->setSubject($this->subject)
                    ->setHtmlBody($this->mailBody);
            if ($file) {
                $message->attachContent($file['content'], [
                    'fileName'    => $file['fDetail']['filename'] . '.' . $file['fDetail']['extension'],
                    'contentType' => MyHelplers::$mime_types[$file['fDetail']['extension']]
                ]);
            }
            $message->send();
            return true;
        }
        return false;
    }

}
