<?php

/**
 * Краткое описание класса
 * 
 * Подробное описание класса
 * которое может растянуться на несколько строк с использованием HTML тегов.
 * Теги можно использовать следующие:
 * <b> — жирное начертание;
 * <code> — код;
 * <br> — разрыв строки;
 * <i> — курсив;
 * <kbd> — сочетание клавиш;
 * <li> — элемент списка;
 * <ol> — нумерованный список;
 * <p> — абзац;
 * <pre> — форматированный текст;
 * <samp> — пример;
 * <ul> — маркированный список;
 * <var> — имя переменной.
 * Инлайн тег. Использует данные с {@link http://base78.ru/data.php}
 * Далее будет список используемых тегов
 *
 * @author Alexander Coder RUS <xel_s@mail.ru>
 * @version 1.0
 * @package stones
 * @category component
 * @todo описание необходимых доработок
 * @copyright Copyright (c) 2014, Coder UA
 */
namespace app\components;

use yii;
use app\models\tables\DependTables;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\FileHelper;

/**
 * Description of MyHelplers
 *
 * @author Александр
 */
class MyHelplers {

    public static $mime_types = array(
        'txt'  => 'text/plain',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'php'  => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'swf'  => 'application/x-shockwave-flash',
        'flv'  => 'video/x-flv',
        // images
        'png'  => 'image/png',
        'jpe'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'ico'  => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif'  => 'image/tiff',
        'svg'  => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip'  => 'application/zip',
        'rar'  => 'application/x-rar-compressed',
        'exe'  => 'application/x-msdownload',
        'msi'  => 'application/x-msdownload',
        'cab'  => 'application/vnd.ms-cab-compressed',
        // audio/video
        'mp3'  => 'audio/mpeg',
        'qt'   => 'video/quicktime',
        'mov'  => 'video/quicktime',
        // adobe
        'pdf'  => 'application/pdf',
        'psd'  => 'image/vnd.adobe.photoshop',
        'ai'   => 'application/postscript',
        'eps'  => 'application/postscript',
        'ps'   => 'application/postscript',
        // ms office
        'doc'  => 'application/msword',
        'rtf'  => 'application/rtf',
        'xls'  => 'application/vnd.ms-excel',
        'ppt'  => 'application/vnd.ms-powerpoint',
        // open office
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
    );
    protected static $e_logFName = 'yii_errors.json';
    protected static $e_logSubDirName = 'yii_errors';

    public static function readLogs() {
        $path = \Yii::getAlias('@file') . '/' . self::$e_logSubDirName . '/' . self::$e_logFName;
        if (is_file($path)) {
            return yii\helpers\Json::decode(file_get_contents($path));
        } else {
            return [];
        }
    }

    protected static function creatChainForLogs() {
        if (!file_exists(\Yii::getAlias('@file'))) {
            mkdir(\Yii::getAlias('@file'));
        }
        if (!file_exists(\Yii::getAlias('@file') . '/' . self::$e_logSubDirName)) {
            mkdir(\Yii::getAlias('@file') . '/' . self::$e_logSubDirName);
        }
    }

    public static function writeLogs($logs = []) {
        //if (YII_DEBUG === true){
            $path = \Yii::getAlias('@file') . '/' . self::$e_logSubDirName; //. '/' . self::$e_logFName;
            if (!is_dir($path)) {
                self::creatChainForLogs();
            }
            if (file_put_contents($path . '/' . self::$e_logFName, yii\helpers\Json::encode($logs)) !== false) {
                return true;
            } else {
                return false;
            }
        //}
    }

    public static function log($header, $message, $level = 'main') {
        $tmp = self::readLogs();
        if (!array_key_exists($level, $tmp)) {
            $tmp[$level] = [];
        }
        $tmp[$level][] = [
            'time'    => time(),
            'header'  => $header,
            'message' => $message
        ];
        if (count($tmp[$level])>20){
            array_shift($tmp[$level]);
        }
        return self::writeLogs($tmp);
    }

    public static function translit($s, $isSqlFor = false) {
        $s = (string) $s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        $s = str_replace(" ", $isSqlFor ? "_" : "-", $s); // заменяем пробелы знаком минус
        if ($isSqlFor) {
            $s = str_replace("-", "_", $s); // заменяем все минусы на подчёркивание
        }
        return $s; // возвращаем результат
    }

    public static function generateRandName($sz = 10) {
        $rVal = '';
        for ($i = 0; $i < $sz; $i++) {
            $rVal .= chr(rand(97, 122));
        }
        return $rVal;
    }

    public static function myscandir($dir, $sort = 0) {
        $list = scandir($dir, $sort);

        // если директории не существует
        if (!$list)
            return [];

        // удаляем . и .. (я думаю редко кто использует)
        if ($sort == 0)
            unset($list[0], $list[1]);
        else
            unset($list[count($list) - 1], $list[count($list) - 1]);
        return $list;
    }

    public static function formatName($val, $patern = '0', $sz = 6) {
        $rVal = '';
        $cnt = strlen($val);
        if ($cnt < $sz) {
            for ($i = 0; $i < $sz - $cnt; $i++)
                $rVal .= $patern;
        }
        return $rVal . $val;
    }

    public static function zipPathToStore($id) {
        $pathToStore = Yii::getAlias('@file');
        if (!file_exists($pathToStore))
            FileHelper::createDirectory($pathToStore);
        return $pathToStore . '/' . self::formatName((string) floor($id / 100)) . '.zip';
    }

    public static function zipListByPath($path) {
        $rVal = ['des' => [], 'main' => []];
        $list = yii\helpers\FileHelper::findFiles($path, ['recursive' => false]);
        for ($i = 0; $i < count($list); $i++) {
            self::zipCreatItemForList($list[$i], $rVal, 'temp_' . $i);
        }
        return $rVal;
    }

    public static function createPublishFileName($name, $ext, $zakazId) {
        $pathToDir = Yii::$app->assetManager->basePath;
        $urlToDir = Yii::$app->assetManager->baseUrl;
        //$dirName = sprintf('%x', hash('ripemd160',MyHelplers::translit(Yii::$app->user->identity->realname) . Yii::getVersion()));
        $dirName = hash('ripemd160',MyHelplers::translit(Yii::$app->user->identity->realname) . Yii::getVersion());
        if (is_dir($pathToDir . '/' . $dirName)) {
            $list = FileHelper::findFiles($pathToDir . '/' . $dirName, ['recursive' => false]);
            foreach ($list as $fName) {
                if (time() - filectime($fName) > 3600) {
                    unlink($fName);
                }
            }
        } else
            FileHelper::createDirectory($pathToDir . '/' . $dirName);
        return [
            $pathToDir . '/' . $dirName . '/' . MyHelplers::hashString($name.$zakazId) . '.' . $ext,
            $urlToDir . '/' . $dirName . '/' . MyHelplers::hashString($name.$zakazId) . '.' . $ext
        ];
    }

    public static function zipPublishFileNew($idZipResorch, $zakazId, $asJPG = 0) {
        $zipPath = self::zipPathToStore($zakazId);
        if (file_exists($zipPath)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === true) {
                $fDetail = pathinfo($zip->getNameIndex((int) $idZipResorch));
                //$fName= 'zakaz_'.self::formatName($zakazId).'_'.explode('_', $fDetail['basename'],2)[1];
                if ($asJPG) {
                    $tmpPath = \Yii::getAlias("@temp/" . $fDetail['filename'] . '.' . $fDetail['extension']);
                    if (file_put_contents($tmpPath, $zip->getFromIndex((int) $idZipResorch))) {
                        $zip->close();
                        $imagick = new \Imagick();
                        $imagick->readImage($tmpPath . '[' . ((int) $asJPG - 1) . ']');

                        unset($imagick);
                        unlink($tmpPath);
                    }
                } else {
                    if (in_array($fDetail['extension'], ['img', 'bmp', 'png', 'gif', 'jpg', 'ico'])) {
                        $blob = $zip->getFromIndex((int) $idZipResorch);
                        $zip->close();
                        $tmp = ['status' => 'ok', 'img_blob' => base64_encode($blob), 'ext' => $fDetail['extension'], 'basename' => $fDetail['basename']]; //,'hd'=>\Yii::$app->response->headers->toArray()];
//                    $tmp=yii\helpers\Json::encode($tmp);
//                    Yii::$app->response->clear();
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        Yii::$app->response->headers->set('Test-len', strlen($tmp['img_blob']) + strlen($tmp['status']) + strlen($tmp['basename']) + strlen($tmp['ext']) + strlen('img_blobstatusbasenameext"""""""",,,,'));
                        $tmp = ['status' => 'ok', 'img_blob' => base64_encode($blob), 'ext' => $fDetail['extension'], 'basename' => $fDetail['basename']];
                        return $tmp;
                    } else {
                        $publish = self::createPublishFileName($fDetail['filename'], $fDetail['extension'],$zakazId);
                        Yii::debug([
                            'fDetail'=>$fDetail,
                            'publish'=>$publish
                        ],'publish');
                        if (is_link($publish[0]))
                            unlink($publish[0]);
                        if (file_exists($publish[0])) {
                            $zip->close();
                            return ['status' => 'ok', 'newPath' => $publish[0], 'url' => $publish[1], 'ext' => $fDetail['extension'], 'basename' => $fDetail['basename']];
                        } else {
                            if (file_put_contents($publish[0], $zip->getFromIndex((int) $idZipResorch)) !== false) {
                                $zip->close();
                                //0775
                                @chmod($publish[0], 0775);
                                return ['status' => 'ok', 'newPath' => $publish[0], 'url' => $publish[1], 'ext' => $fDetail['extension'], 'basename' => $fDetail['basename']];
                            } else {
                                $zip->close();
                                return ['status' => 'error', 'errorText' => 'Не удалось создать временный файл.'];
                            }
                        }
                    }
                }
            } else {
                return ['status' => 'error', 'errorText' => "Не удается открыть файл $zipPath : " . $zip->getStatusString()];
            }
        } else {
            return ['status' => 'error', 'errorText' => "Файл '$zipPath' не найден!"];
        }
    }

    public static function zipPublishFile($idZipResorch, $zakazId) {
        $zipPath = self::zipPathToStore($zakazId);
        if (file_exists($zipPath)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === true) {
                $fDetail = pathinfo($zip->getNameIndex((int) $idZipResorch));
                //$fName= 'zakaz_'.self::formatName($zakazId).'_'.explode('_', $fDetail['basename'],2)[1];
                $publish = self::createPublishFileName($fDetail['filename'], $fDetail['extension'],$zakazId);
                if (is_link($publish[0]))
                    unlink($publish[0]);
                if (file_exists($publish[0])) {
                    $zip->close();
                    return ['status' => 'ok', 'newPath' => $publish[0], 'url' => $publish[1], 'ext' => $fDetail['extension'], 'basename' => $fDetail['basename']];
                } else {
                    if (file_put_contents($publish[0], $zip->getFromIndex((int) $idZipResorch)) !== false) {
                        $zip->close();
                        //0775
                        @chmod($publish[0], 0775);
                        return ['status' => 'ok', 'newPath' => $publish[0], 'url' => $publish[1], 'ext' => $fDetail['extension'], 'basename' => $fDetail['basename']];
                    } else {
                        $zip->close();
                        return ['status' => 'error', 'errorText' => 'Не удалось создать временный файл.'];
                    }
                }
            } else {
                return ['status' => 'error', 'errorText' => "Не удается открыть файл $zipPath : " . $zip->getStatusString()];
            }
        } else {
            return ['status' => 'error', 'errorText' => "Файл '$zipPath' не найден!"];
        }
    }

    public static function renderFileListById($id, $key = 'main') {
        $tmp = self::zipListById($id);
        $rVal = Html::a('Пусто', '#', ['class' => 'list-group-item disabled']);
        setlocale(LC_ALL, 'ru_RU.utf8');
        if (isset($tmp[$key]) && count($tmp[$key])) {
            //list-group-item-text
            $rVal = '';
            foreach ($tmp[$key] as $keyId => $val) {
                $aOptions = [
                    'class'     => 'list-group-item', 'data-type' => 'file-to-view'
                ];
                if (mb_strlen($val) > 15) {
                    $aOptions['title'] = $val;
                }
                $ext = pathinfo($val, PATHINFO_EXTENSION);

                $rVal .= Html::a(Html::tag('p', Html::tag('span', $val), [
                                    'class' => 'list-group-item-text'
                                ]), Url::to([
                                    '/zakaz/zakaz/getfile',
                                    'idZipResorch' => $keyId,
                                    'id'           => $id
                                ]), $aOptions);
                if (strtolower($ext) == 'pdf') {
                    /*
                      $zipPath = self::zipPathToStore($zakazId);
                      if (file_exists($zipPath)) {
                      $zip = new \ZipArchive();
                      if ($zip->open($zipPath) === true) {
                      //$ext = pathinfo($val);
                      $tmpPath = \Yii::getAlias("@temp") . '/temppdf_' . time() . '.pdf'; // . '/' . self::translit($ext['filename']) . '.' . $ext['extension'];
                      if (file_put_contents($tmpPath, $zip->getFromIndex((int) $keyId)) !== FALSE) {
                      $zip->close();
                      chmod($tmpPath, 777);
                      if (file_exists($tmpPath)) {
                      $imagick = new \Imagick();
                      $imagick->readImage($tmpPath);
                      $pCnt = $imagick->getNumberImages();
                      unset($imagick);
                      unlink($tmpPath);
                      for ($i = 0; $i < $pCnt; $i++) {
                      $ext = pathinfo($val, PATHINFO_FILENAME);
                      $nFNAme = 'jpgPAGE' . ($i + 1) . '_' . $ext . '.jpg';
                      if (mb_strlen($nFNAme) > 15) {
                      $aOptions['title'] = $nFNAme;
                      }
                      $rVal .= Html::a(Html::tag('p', Html::tag('span', $nFNAme), [
                      'class' => 'list-group-item-text'
                      ]), Url::to([
                      '/zakaz/zakaz/getfile',
                      'idZipResorch' => $keyId,
                      'id'           => $id,
                      'asJPG'        => $i + 1
                      ]), $aOptions);
                      }
                      }
                      } else {
                      $zip->close();
                      }
                      }
                      }
                     */
                }
            }
        }
        return Html::tag('div', $rVal, ['class' => 'list-group']);
    }

    private static function zipCreatItemForList(string $fName, array &$rVal, $index, int $id = 0) {
        $file = pathinfo($fName);
        //$zakazPerfix= mb_split('', $file['dirname'])[0];
        $zakazPerfix = explode('/', $file['dirname'], 2)[0];
        if ($zakazPerfix === 'Z' . self::formatName((string) $id, '0', 8) || !$id) {
            $fileDetails = explode('_', $file['basename'], 2);
            $key = is_integer($index) ? (int) $index : (string) $index;
            if ($fileDetails[0] === 'main') {
                $rVal['main'][$key] = $fileDetails[1];
            } else {
                $rVal['des'][$key] = $fileDetails[1];
            }
        }
    }

    public static function zipListById($id) {
        $rVal = ['des' => [], 'main' => []];
        $pathToStore = Yii::getAlias('@file');
        if ($id && file_exists($pathToStore) && is_dir($pathToStore)) {
            $ZipfName = self::zipPathToStore($id);
            $zip = new \ZipArchive();
            if ($zip->open($ZipfName) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++)
                    self::zipCreatItemForList($zip->getNameIndex($i), $rVal, $i, $id);
            }
        }
        return $rVal;
    }

    public static function hashString($str) {
        return hash('ripemd160',$str . Yii::getVersion());
        return sprintf('%x', crc32($str . Yii::getVersion()));
    }

    public static function getHashKeyForBreadcrumbs() {
        return self::hashString(Yii::$app->id . Yii::$app->user->identity->username);
    }

    public static function arraySlice(array &$array, int $to) {
        $rVal = [];
        $cnt = count($array);
        for ($i = 0; $i < $cnt && $i <= $to; $i++)
            $rVal[] = $array[$i];
        return $rVal;
    }

    private static $_materialInfoByContentSQLCache = [];
    private static $_materialInfoByContentSQLCache2 = [];

    private static function _materialInfoByContent($mat, $val, $query, $struct, $useCache = true) {
        $i = count($struct) - 1;
        $select = ['`' . $struct[$i] . '`.`name` as `name' . $i . '`'];
        $prev = $struct[$i--];
        for (; $i > -1;) {
            $query->join('LEFT OUTER JOIN', $struct[$i], '`' . $struct[$i] . '`.`' . DependTables::$pKey . '`=`' . $prev . '`.`' . DependTables::$reference . '`');
            $select[] = '`' . $struct[$i] . '`.`name` as `name' . $i . '`';
            $prev = $struct[$i--];
        }
        $query->select($select);

        //Кэш
        if ($val) {
            if ($useCache && array_key_exists($val['firm_id'], self::$_materialInfoByContentSQLCache) && array_key_exists($val["type_id"], self::$_materialInfoByContentSQLCache[$val['firm_id']]) && array_key_exists($val['mat_id'], self::$_materialInfoByContentSQLCache[$val['firm_id']][$val["type_id"]])) {
                $post = self::$_materialInfoByContentSQLCache[$val['firm_id']][$val["type_id"]][$val['mat_id']]['post'];
            } else {
                if ($post = \app\models\admin\Post::find()
                        ->where(['firmPost.firm_id' => $val['firm_id']])
                        ->with('materialParams')
                        ->asArray()
                        ->one()) {
                    Yii::debug(['$post' => $post, '$mat' => $mat->toArray(), '$val' => $val], '_materialInfoByContent');
                    $curency = $post['curency_type'];
                    $coast = 0; //yii\helpers\ArrayHelper::map($post['materialParams'], 'm_id', 'coast');
                    $update = 0;
                    $optFrom = 0;
                    $optBaseCoast = 0;
                    $i = 0;
                    $cnt = count($post['materialParams']);
                    for (; $i < $cnt && !$coast; $i++) {
                        $el = $post['materialParams'][$i];
                        if ((int) $el['m_type'] === (int) $val['type_id'] && (int) $el['m_id'] === (int) $val['mat_id']) {
                            $coast = $el['coast'];
                            $optFrom = $el['optfrom'];
                            $optBaseCoast = $el['optcoast'];
                            $update = $el['update'];
                        }
                        Yii::debug([
                            '$curency'             => $curency,
                            '$i'                   => $i,
                            '$el'                  => $el,
                            '(int)$el["m_type"]'   => (int) $el['m_type'],
                            '(int)$val["type_id"]' => (int) $val['type_id'],
                            '(int)$el["m_id"]'     => (int) $el['m_id'],
                            '(int)$val["mat_id"]'  => (int) $val['mat_id'],
                            '$coast'               => $coast
                                ], '_materialInfoByContent');
                    }
                    $post = [
                        'name'         => $post['mainName'],
                        'baseCoast'    => $curency === 'RUB' ? (double) $coast : round((double) $coast * round(Yii::$app->controller->currencies[$curency], 2), 2),
                        'optBaseCoast' => $curency === 'RUB' ? (double) $optBaseCoast : round((double) $optBaseCoast * round(Yii::$app->controller->currencies[$curency], 2), 2),
                        'optFrom'      => $optFrom,
                        'updatetime'   => Yii::$app->formatter->asDatetime((int) $update)];
                    if ($useCache) {
                        if (!array_key_exists($val['firm_id'], self::$_materialInfoByContentSQLCache)) {
                            self::$_materialInfoByContentSQLCache[$val['firm_id']] = [];
                        }
                        if (!array_key_exists($val["type_id"], self::$_materialInfoByContentSQLCache[$val['firm_id']])) {
                            self::$_materialInfoByContentSQLCache[$val['firm_id']][$val["type_id"]] = [];
                        }
                        //if (!array_key_exists($val['mat_id'], self::$_materialInfoByContentSQLCache[$val['firm_id']][$val["type_id"]])){
                        self::$_materialInfoByContentSQLCache[$val['firm_id']][$val["type_id"]][$val['mat_id']] = ['post' => $post];
                        //}
                        //self::$_materialInfoByContentSQLCache[$val['firm_id']]=['post'=>$post];
                    }
                } else {
                    $post = ['name' => '', 'baseCoast' => 0];
                }
            }
        } else
            $post = ['name' => '', 'baseCoast' => 0];
        return [
            'value'  => $mat,
            'query'  => $val ? $query->asArray()->one() : $query->asArray()->all(),
            'postav' => $post
        ];
    }

    public static function materialInfoByContent(&$mat, &$val = null, $useCache = true) {
        $name = $mat->translit($mat->name);
        $struct = DependTables::dependsTablesNamesFromRus($name, \yii\helpers\Json::decode($mat->struct));
        $i = count($struct) - 1;
        Yii::debug($val, 'cache test');
        if ($val) {
            if ($useCache && array_key_exists($mat['id'] . $val['firm_id'] . $struct[$i] . $val['mat_id'], self::$_materialInfoByContentSQLCache2)) {
                return self::$_materialInfoByContentSQLCache2[$mat['id'] . $val['firm_id'] . $struct[$i] . $val['mat_id']];
            } else {
                $query = \app\models\tables\DependTable::createObject($struct[$i])->find()->where(['`' . $struct[$i] . '`.`id`' => $val['mat_id']]);
                if ($useCache) {
                    self::$_materialInfoByContentSQLCache2[$mat['id'] . $val['firm_id'] . $struct[$i] . $val['mat_id']] = self::_materialInfoByContent($mat, $val, $query, $struct);
                    return self::$_materialInfoByContentSQLCache2[$mat['id'] . $val['firm_id'] . $struct[$i] . $val['mat_id']];
                } else {
                    return self::_materialInfoByContent($mat, $val, $query, $struct, $useCache);
                }
            }
        } else {
            $query = \app\models\tables\DependTable::createObject($struct[$i])->find();
            return self::_materialInfoByContent($mat, $val, $query, $struct, $useCache);
        }
    }

    public static function levelSelectUl($arr, $exclude = null, $id = '') {
        $exclude = $exclude ? $exclude : ["allow" => [], "denied" => []];
        $rVal = '';
        foreach ($arr as $key => $val) {
            if (isset($val[0])) {
                $rVal .= Html::tag('li', $val[0], ['class' => 'dropdown-header', 'data-controller' => $key]);
                unset($val[0]);
            }
            foreach ($val as $k => $v) {
                $rVal .= Html::tag('li', Html::a($v, '#', [
                                    'data' => [
                                        'controller' => $key,
                                        'action'     => $k,
                                        'value'      => $key . '/' . $k,
                                    ]
                ]));
            }
            $rVal .= Html::tag('li', Html::a('Все', '#', [
                                'data' => [
                                    'controller' => $key,
                                    'action'     => 'all',
                                    'value'      => $key . '/all',
                                ]
            ]));
        }
        return Html::tag('ul', $rVal, ['class' => 'dropdown-menu', 'aria-labelledby' => $id]);
    }

    public static function endingNums($number, $titles, $showNumer = false) {
        $cases = [2, 0, 1, 1, 1, 2];
        $rVal = $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[($number % 10 < 5) ? $number % 10 : 5]];
        if ($showNumer) {
            return $number . ' ' . $rVal;
        } else {
            return $rVal;
        }
    }
    public static function formatTimeInterval(&$interval){
        $rVal='';
        if ($interval->h){
            $rVal=$interval->h.' '.self::endingNums($interval->h, ['час','часа','часов']);
            $tmpMin=$interval->i-($interval->h*60);
            if ($tmpMin>0){
                $rVal.=$tmpMin.' мин. назад.';
            }else{
                $rVal.=' назад.';
            }
        }elseif($interval->i){
            $rVal.=$interval->i.' мин. назад.';
        }elseif($interval->s){
            $rVal.=' меньше мин. назад.';
        }else{
            $rVal.=' только что.';
        }
        return $rVal;
    }
    public static function checkDirToExistAndCreate($path) {
        if (is_dir($path)) {
            return $path;
        } elseif (!file_exists($path)) {
            mkdir($path, 0700);
        } else {
            throw new \yii\base\UserException('"' . $path . '" - евляется файлом!');
        }
        return $path;
    }
    /*
     * Склонение слов
     * 
     * Склонение слов в зависимости от стоящих рядом с ними цифр (существительных после числительных)
     * 
     * @since 0.0.1
     * @package stones
     * @subpackage myhelpers
     * 
     * @method string true_wordform(number $num, string $form_for_1, string $form_for_2, string $form_for_5)
     * @param number $num - число, от которого будет зависеть форма слова
     * @param string $form_for_1 - первая форма слова, например Товар
     * @param string $form_for_2 - вторая форма слова - Товара
     * @param string $form_for_5 - третья форма множественного числа слова - Товаров
     * @return string
     */
    public static  function true_wordform($num, $form_for_1, $form_for_2, $form_for_5){
	$num = abs($num) % 100; // берем число по модулю и сбрасываем сотни (делим на 100, а остаток присваиваем переменной $num)
	$num_x = $num % 10; // сбрасываем десятки и записываем в новую переменную
	if ($num > 10 && $num < 20) // если число принадлежит отрезку [11;19]
		return $form_for_5;
	if ($num_x > 1 && $num_x < 5) // иначе если число оканчивается на 2,3,4
		return $form_for_2;
	if ($num_x == 1) // иначе если оканчивается на 1
		return $form_for_1;
	return $form_for_5;
    }
}
