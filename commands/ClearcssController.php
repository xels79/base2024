<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\components\MyHelplers;
use yii\helpers\Console;

class ClearcssController extends Controller
{
    private $removedCount=0;
    
    public $emulate=0;
    
    public function options($actionID)
    {
        $opt=[
            'index'=>['emulate']
        ];
        return $opt[$actionID];
    }
    
    public function optionAliases()
    {
        return ['e' => 'emulate'];
    }

    /**
     * This command echoes what you have entered as the message.
     * 
     */
    public function actionIndex($subPath="web/css")
    {
        if ($subPath['0']!=='/')
            $path=Yii::getAlias("@app/$subPath");
        else
            $path=Yii::getAlias("@app$subPath");
        if (!file_exists($path)){
            echo "Файл $path - не найден!".PHP_EOL;
            return false;
        }
        if (!is_dir($path)){
            echo "Файл $path - не евляется каталогом!".PHP_EOL;
            return false;
        }
        if ($this->emulate){
            echo Console::ansiFormat('Режим имитации'.PHP_EOL,[Console::FG_GREEN]);
        }
        $this->proceedDir($path);
        if ($this->removedCount){
            echo "Удалено - ".$this->removedCount." ".MyHelplers::true_wordform($this->removedCount , 'файл', 'файла', 'файлов');
        }else{
            echo "Ни одного файла не найдено!";
        }
        echo PHP_EOL."Конец.".PHP_EOL;
    }
    private function proceedDir($path){
        $fCnt=0;
        //echo "Очищаем каталог '$path'".PHP_EOL;
        if (($dir=opendir($path))!==false){
            while (($file = readdir($dir)) !== false) {
                if ($file!=='.' && $file!=='..'){
                    if (is_dir($path.'/'.$file)){
                        $this->proceedDir($path.'/'.$file);
                    }else{
                        if (pathinfo( $file, PATHINFO_EXTENSION )==='css'){
                            if (!$this->emulate){                                
                                unlink($path.'/'.$file);
                                echo $file.Console::ansiFormat(' - удален.'.PHP_EOL,[Console::FG_RED]);
                                $fCnt++;
                            }else{
                                echo Console::ansiFormat($file.PHP_EOL,[Console::FG_GREEN]);
                            }
                        }
                    }
                }
            }
            $this->removedCount+=$fCnt;
            closedir($dir);
        }
        //echo "Каталог $path".PHP_EOL."    найдено $fCnt ".MyHelplers::true_wordform($fCnt , 'файл', 'файла', 'файлов').PHP_EOL;
    }
}
