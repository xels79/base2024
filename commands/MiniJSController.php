<?php

namespace app\commands;

use yii\helpers\Console;

class MiniJSController extends MiniJSHelp {

    private $file = "";
    public $output = "";
    private $outputFileName = "";

    public function options($actionID) {
        return ['output'];
    }

    public function optionAliases() {
        return [
            'o' => 'output'
        ];
    }

    public function actionIndex($file_name = "") {
        if ($file_name) {
            $this->file = $file_name;
        } else {
            echo $this->ansiFormat("Не указано имя файла -file=Имя или -f=Имя или mini-j-s Имя\n", Console::BG_RED);
            return 1;
        }
        $tmpP = pathinfo($this->file);
        if (!isset($tmpP['extension']) || ($tmpP['extension'] !== 'js' && $tmpP['extension'] !== 'php')) {
            echo $this->ansiFormat("Обробатываются только файлы js\n", Console::BG_RED);
            return 1;
        }
        if (!isset($tmpP['filename'])) {
            echo $this->ansiFormat("Ошибка в указаном пути нет имени файла.\n", Console::BG_RED);
            return 1;
        }
        if (!file_exists($this->file) || !is_file($this->file)) {
            echo $this->ansiFormat("Исходный файл \"$this->file\" - не найден или не евляется файлом!\n", Console::BG_RED);
            return 1;
        }
        echo $this->file . "\n";
        if ($this->output) {
            if (is_dir($this->output)) {
                $this->outputFileName = realpath($this->output) . '/min.' . $tmpP['basename'];
            } else {
                $tmpS = pathinfo($this->output);
//                echo \yii\helpers\VarDumper::dumpAsString($this->output) . "\n";
//                echo \yii\helpers\VarDumper::dumpAsString(realpath($this->output)) . "\n";
//                echo \yii\helpers\VarDumper::dumpAsString($tmpS) . "\n";
                if (!isset($tmpS['dirname']))
                    $tmpS['dirname'] = "..";
                if (!is_dir($tmpS['dirname'])) {
                    echo $this->ansiFormat("Директория выходного файла \"" . $tmpS['dirname'] . "\" - не найден!\n", Console::BG_RED);
                    return 1;
                }
                if (!isset($tmpS['extension']) || $tmpS['extension'] !== 'js') {
                    echo $this->ansiFormat("Не верное расширение выходного файла.\n", Console::BG_RED);
                    return 1;
                }
                $this->outputFileName = $tmpS['dirname'] . '/' . $tmpS['basename'];
            }
        } else {
            $this->outputFileName = $tmpP['dirname'] . '/min.' . $tmpP['basename'];
        }
        if (file_exists($this->outputFileName)) {
            $s1 = $this->ansiFormat("Внимание файл", Console::BG_RED);
            if (!$this->confirm($s1 . " \"$this->outputFileName\" уже существует, заменить?")) {
                return 1;
            }
        }
        echo $this->outputFileName . "\n";
        $this->fileIn = file_get_contents ($this->file);//fopen($this->file, 'r');
        $this->fileOut = \fopen($this->outputFileName, 'w');
        $this->_Run();
    }

}
