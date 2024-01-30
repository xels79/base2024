<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\tables;

/**
 * Description of DependTables
 *
 * @author Александр
 */
use Yii;

class DependTables extends \yii\db\Migration {

    public $mainName = null;
    public $mainNameRus = null;
    public $struct = [];
    public static $perfix = 'for_material';
    public static $delimmer = '_';
    public static $pKey = 'id';
    public static $reference = 'reference_id';

    public static function structBasWithoutSklad($mainNameRus = '') {
        $rVal = self::structBas($mainNameRus);
        if (isset($rVal['Склад']))
            unset($rVal['Склад']);
        return $rVal;
    }

    public static function structBas($mainNameRus = '') {
        return[
            'Название'     => [
                'tblNamePart' => 'names',
                'rusname'     => 'Название',
                'comment'     => 'Название для ' . $mainNameRus,
                'type'        => 'string'
            ],
            'Размер'       => [
                'tblNamePart' => 'sizes',
                'rusname'     => 'Размеры',
                'comment'     => 'Размеры для ' . $mainNameRus,
                'type'        => 'size'
            ],
            'Цвет'         => [
                'tblNamePart' => 'colors',
                'rusname'     => 'Цвета',
                'comment'     => 'Цвета для ' . $mainNameRus,
                'type'        => 'string'
            ],
            'Формат'       => [
                'tblNamePart' => 'format',
                'rusname'     => 'Форматы',
                'comment'     => 'Форматы для ' . $mainNameRus,
                'type'        => 'size'
            ],
            'Размер листа' => [
                'tblNamePart' => 'liff_sizes',
                'rusname'     => 'Размеры листов',
                'comment'     => 'Размеры листов ' . $mainNameRus,
                'type'        => 'size'
            ],
            'Толщина'      => [
                'tblNamePart' => 'thickness',
                'rusname'     => 'Толщины',
                'comment'     => 'Толщины для ' . $mainNameRus,
                'type'        => 'number'
            ],
            'Плотность'    => [
                'tblNamePart' => 'density',
                'rusname'     => 'Плотность',
                'comment'     => 'Плотность для ' . $mainNameRus,
                'type'        => 'number'
            ],
            'Артикул'      => [
                'tblNamePart' => 'articul',
                'rusname'     => 'Артикул',
                'comment'     => 'Артикул для ' . $mainNameRus,
                'type'        => 'number'
            ],
            'Cостав'       => [
                'tblNamePart' => 'composition',
                'rusname'     => 'Состав',
                'comment'     => 'Состав для ' . $mainNameRus,
                'type'        => 'string'
            ],
            'Типография'   => [
                'tblNamePart' => 'tipograff',
                'rusname'     => 'Типография',
                'comment'     => 'Типография ' . $mainNameRus,
                'type'        => 'string'
            ],
            'Машина'       => [
                'tblNamePart' => 'machine',
                'rusname'     => 'Машина',
                'comment'     => 'Машина ' . $mainNameRus,
                'type'        => 'string'
            ],
        ];
    }

    public static function dependsTablesNamesFromRus(string $mainName, array $struct) {
        $rVal = [];
        $base = self::structBas();
        foreach ($struct as $el) {
            if (array_key_exists($el, $base))
                $rVal[] = self::creatFullTableName($mainName, $base[$el]['tblNamePart']);
        }
        return $rVal;
    }

    public static function getDependsTable($mainName, $struct) {
        $tmp = self::structBas($mainName);
        $rVal = [];
        if (is_string($struct))
            $struct = \yii\helpers\Json::decode($struct);
        foreach ($struct as $el) {
            if (array_key_exists($el, $tmp)) {
                $fullName = self::creatFullTableName($mainName, $tmp[$el]['tblNamePart']);
                $rVal[] = [
                    'fullname' => $fullName,
                    'rusname'  => $tmp[$el]['rusname'],
                    'model'    => DependTable::createObject($fullName)
                ];
            }
        }
        return $rVal;
    }

    public function createCollumn($comment) {
        return [
            self::$pKey      => $this->primaryKey(),
            'name'           => $this->text()->notNull()->comment($comment),
            self::$reference => $this->integer()->notNull()->comment('Ссылка')
        ];
    }

    public static function creatFullTableName($mainName, $nameSpecifictation) {
        return (string) (self::$perfix . self::$delimmer . $mainName . self::$delimmer . $nameSpecifictation);
    }

    public function checkAndAdd() {
        $rVal = ['Ничего не сделано.'];
        $process = [];
        if ($this->mainName && $this->mainNameRus) {

            $tmp = self::structBas($this->mainNameRus);
            $prevTName = null;
            $i = count($this->struct);
//            return [\yii\helpers\VarDumper::dumpAsString($this->struct)];
            ob_start();
            foreach ($this->struct as $el) {
                $i--;
                if (array_key_exists($el, $tmp)) {
                    $fullTName = self::creatFullTableName($this->mainName, $tmp[$el]['tblNamePart']);
                    if (Yii::$app->db->schema->getTableSchema($fullTName) === null) {
                        $process[$el] = $tmp[$el];
                        $tmpColumn = $this->createCollumn($el);
                        //if (!$i) $tmpColumn['article']=$this->text ()->null ()->comment('Артикл');
                        $this->createTable($fullTName, $tmpColumn);
                        $this->addCommentOnTable($fullTName, $tmp[$el]['comment']);
                        if ($prevTName) {
//                            $this->createIndex('fg-index-'.$fullTName, $fullTName, self::$reference);
//                            $this->addForeignKey('fg-index-'.$fullTName, $fullTName, self::$reference, $prevTName, self::$pKey, 'CASCADE', 'NO ACTION');
                            $prevTName = $fullTName;
                        } else {
                            $prevTName = $fullTName;
                        }
                        $rVal[count($process) - 1] = "Добавлена таблица $fullTName. " . $tmp[$el]['comment'];
                    } else {
                        $rVal[count($process) - 1] = "Таблица $fullTName. " . $tmp[$el]['comment'] . ' уже существует.';
                    }
                }
            }
            ob_end_clean();
        }
        return $rVal;
    }

}
