<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

/**
 * Description of TinymceImageLoader
 *
 * @author Александр
 */
use yii\base\Model;
use yii\web\UploadedFile;

class TinymceImageLoader extends Model {

    public $image;

    public function rules() {
        return[
            [['file'], 'file', 'extensions' => 'png, jpg, gif'],
        ];
    }

    public function upload() {
        if ($this->validate()) {
            $this->file->saveAs("@app/web/pic/tinymce/{$this->image->baseName}.{$this->image->extension}");
            return true;
        } else {
            return false;
        }
    }

}
