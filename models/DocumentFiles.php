<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

/**
 * Description of DocumentFiles
 *
 * @author Александр
 */
use yii;
use yii\base\Model;
use app\components\MyHelplers;

class DocumentFiles extends Model {

    public $rekvizit_id;
    public $name;
    public $basePath = '@documents';
    private static $_basePath = '@documents';
    public $fNamePerfix = '';
    private static $_fNamePerfix = '';
    private static $path;

    public function init() {
        parent::init();
        self::$_basePath = $this->basePath;
        self::$_fNamePerfix = $this->fNamePerfix;
        self::$path = \Yii::getAlias($this->basePath);
        MyHelplers::checkDirToExistAndCreate(self::$path);
    }

    public static function findOne($id) {
        $rVal = null;
        setlocale(LC_ALL, 'ru_RU.utf8');
        \Yii::debug([
            '$_basePath'    => self::$_basePath,
            '$_fNamePerfix' => self::$_fNamePerfix,
            'path'          => self::$path
                ], 'DocumentFiles');
        if ($handle = opendir(self::$path . '/')) {
            while (false !== ($entry = readdir($handle))) {
                \Yii::debug('check: ' . self::$path . '/' . $entry, 'DocumentFiles');
                if (is_file(self::$path . '/' . $entry)) {
                    \Yii::debug('is file: ' . self::$path . '/' . $entry, 'DocumentFiles');
                    if (count($tmp = explode(self::$_fNamePerfix . '_', $entry)) > 1) {
                        \Yii::debug($tmp, 'DocumentFiles');
                        if ((int) $tmp[0] === (int) $id) {
                            $rVal = new DocumentFiles([
                                'name'        => pathinfo($tmp[1], PATHINFO_FILENAME),
                                'rekvizit_id' => (int) $tmp[0],
                                'basePath'    => self::$_basePath,
                                'fNamePerfix' => self::$_fNamePerfix
                            ]);
                        }
                    }
                }
            }
        } else {
            \Yii::debug('Не открыть: ' . $path . '/', 'DocumentFiles');
        }
        return $rVal;
    }

}
