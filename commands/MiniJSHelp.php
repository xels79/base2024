<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\commands;

/**
 * Description of MiniJSHelp
 *
 * @author Александр
 */
class MiniJSHelp extends MiniJSHelpMain {

    public function getHelpSummary() {
        return 'Модуль для сжатие файлов JS.';
    }

    public function getActionHelp($action) {
        switch ($action->id) {
            case "index":
                return "Сжимает указаный файл JS";
                break;
            default:
                return "";
        }
    }

    public function getActionArgsHelp($action) {
        $rVal = [];
        switch ($action->id) {
            case "index":
                $rVal['file'] = [
                    'required' => true,
                    'type'     => 'string',
                    'comment'  => 'Путь к исходному файлу'
                ];
                break;
        }
        return $rVal;
    }

    public function getActionOptionsHelp($action) {
        $rVal = [];
        switch ($action->id) {
            case "index":
                $rVal['output'] = [
                    'type'    => 'string',
                    'comment' => 'Путь к выходному файлу'
                ];
                break;
        }
        return $rVal;
    }

}
