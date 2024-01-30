<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;
use \yii\helpers\Html;
use app\components\MyHelplers;
use yii\helpers\FileHelper;
use yii\helpers\Json;

abstract class ControllerMain extends Controller {

    protected $logOrErr = false;
    protected $role = 'guest';
    public $mMenu = [];
    public $title;
    public $contSz = 0;
    public $backId = 0;
    public $pics = [
        'logo' => null,
        'pic1' => null,
        'pic2' => null
    ];
    public $brandOptions = [
        'class' => 'navbar-brand'
    ];

    abstract protected function getOptionsModel($name = null);

    abstract protected function getCacheOptionKey($name = null);

    protected function getOptions($caching = true, $name = null) {
        $name = $name ? $name : 'zakazlist';
        if ($caching)
            if ($val = Yii::$app->cache->get($this->getCacheOptionKey($name))) {
                Yii::trace('Options: востановлены из кэша.', 'Zakaz-list');
                if (isset($rVal['add']['empt'])) unset($rVal['add']['empt']);
                return $val;
            }
        Yii::trace('Options: сгенерировано.', 'Zakaz-list');
        $model = $this->getOptionsModel($name);
        Yii::$app->cache->set($this->getCacheOptionKey($name), $model->options);
        if (isset($rVal['add']['empt'])) unset($rVal['add']['empt']);
        return $model->options;
    }

    public function getLogo() {
        if ($this->pics['logo'] && file_exists($this->pics['logo'])) {
            return Html::img($this->view->assetManager->publish($this->pics['logo'])[1]);
        } else {
            return Html::encode(Yii::$app->name . ' v' . \yii::$app->version);
        }
    }

    public function getPublishPic1() {
        if ($this->pics['pic1'] && file_exists($this->pics['pic1']))
            return $this->view->assetManager->publish($this->pics['pic1'])[1];
        else
            return '';
    }

    public function getPic1() {
        if ($this->pics['pic1'] && file_exists($this->pics['pic1'])) {
            return Html::img($this->view->assetManager->publish($this->pics['pic1'])[1], [
                        'height' => '420',
            ]);
        } else {
            return '';
        }
    }

    public function getPic2() {
        if ($this->pics['pic2'] && file_exists($this->pics['pic2'])) {
            return Html::img($this->view->assetManager->publish($this->pics['pic2'])[1], [
//                'height'=>'420',
                        'class' => 'mainpic1'
            ]);
        } else {
            return '';
        }
    }

    private function calculateCurrencies() {
        try {
            $xml = simplexml_load_file('https://cbr.ru/scripts/XML_daily.asp');
        } catch (yii\base\ErrorException $e) {
            return [];
        }
        $currencies = array();
        foreach ($xml->xpath('//Valute') as $valute) {
            $currencies[(string) $valute->CharCode] = (float) str_replace(',', '.', $valute->Value);
        }
        return $currencies;
    }

    private $_getCurenciesCache = null;

    private function getCurenciesCache() {
        if (false && $rVal = Yii::$app->cache->get(Yii::$app->name . '_currencies')) {
            $date = getdate();
            if ($rVal['date']['year'] > $rVal['date']['year'] || $rVal['date']['mon'] > $rVal['date']['mon'] || $rVal['date']['mday'] > $rVal['date']['mday']) {
                $currencies = $this->calculateCurrencies();
                $rVal = [
                    'currencies' => $currencies,
                    'old'        => $rVal['currencies'],
                    'date'       => $date
                ];
                Yii::$app->cache->set(Yii::$app->name . '_currencies', $rVal, 14400);
                Yii::debug('Новый день валюты с сайта', 'currencies');
                return $rVal;
            } else {
                Yii::debug('Валюта из кэша', 'currencies');
                return $rVal;
            }
        } else {
            $currencies = $this->calculateCurrencies();
            $rVal = [
                'currencies' => $currencies,
                'old'        => $currencies,
                'date'       => getdate()
            ];
            Yii::$app->cache->set(Yii::$app->name . '_currencies', $rVal, 14400);
            Yii::debug('Валюты с сайта', 'currencies');
            Yii::debug($rVal, 'currencies');
        }
        return $rVal;
    }

    public function curenciesDifference($key) {
        if (!$this->_getCurenciesCache) {
            $this->_getCurenciesCache = $this->getCurenciesCache();
        }
        if (array_key_exists($key, $this->_getCurenciesCache['currencies']) && array_key_exists($key, $this->_getCurenciesCache['old'])) {
            return round((double) $this->_getCurenciesCache['currencies'][$key] - (double) $this->_getCurenciesCache['old'][$key], 2);
        } else {
            return 0;
        }
    }

    public function getCurrencies() {
        if (!$this->_getCurenciesCache) {
            $this->_getCurenciesCache = $this->getCurenciesCache();
        }
        return $this->_getCurenciesCache['currencies'];
    }

    public function init() {
        parent::init();
        $this->title = Yii::$app->name;
        $firm = \app\models\admin\OurFirm::find()->where('firm_id is not null')->one();
        if ($firm) {
            $this->pics['logo'] = $firm->logo;
            $this->pics['pic1'] = $firm->pic1;
            $this->pics['pic2'] = $firm->pic2;
            if ($this->pics['logo'] && file_exists($this->pics['logo'])) {
                Html::addCssClass($this->brandOptions, 'has-img');
            }
        }
    }

    public function getRole() {
        return $this->role;
    }

    public function behaviors() {
        return [
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['login', 'logout', 'error'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['login', 'error'],
                        'roles'   => ['?', '@'],
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['logout'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    private function checkFileAndAdd($dirName, array &$safety, $tmpName, &$count) {
        $mess = '';
        $zakInfo = '';
        $zak = false;
        $cnt = 0;
        $rVal = false;
        Yii::trace($dirName, 'warns-checkFileAndAdd');
        foreach (FileHelper::findFiles($dirName) as $fName) {
            if (time() - ($ft = filectime($fName)) > Yii::$app->params['sendTmpTimeOut'] * 60 || true) {
                setlocale(LC_ALL, 'ru_RU.utf8');
                if (pathinfo($fName)['filename'] === 'content') {
                    Yii::trace($fName, 'warns');
                    $mess = 'Имеются не сохранённые данные ' . Yii::$app->formatter->asRelativeTime($ft);
                    $zak = Json::decode(file_get_contents($fName));
                } else {
                    $cnt++;
                }
            } else {
                $rVal = true;
            }
        }
        if ($cnt) {
            $mess .= $mess ? ' и' : 'Имеются';
            $mess .= ' ' . $cnt . ' файл(а(ов))';
        }
        if (mb_strlen($mess)) {
            if ($zak && isset($zak['id']) && $zak['id']) {
                $zakInfo = 'Заказ №' . $zak['id'];
            } else {
                $zakInfo = 'Не сохраненный заказ';
            }
            $safety[] = [
                'label'       => $zakInfo,
                'encode'      => false,
                'url'         => '#',
                'linkOptions' => [
                    'data'  => [
                        'content' => $mess,
                        'tmpname' => $tmpName
                    ],
                    'class' => 'nav-mess stored-z'
                ],
            ];
            $count++;
            return true;
        } else
            return false || $rVal;
    }

    private function createMessageList() {
        $rVal = [];
        $safety = [];
        $warn = [];
        $count = 0;
        $time = 0;
        $tmpPath = Yii::getAlias('@temp/' . MyHelplers::translit(Yii::$app->user->identity->realname));
//        Yii::trace(MyHelplers::myscandir($tmpPath),'warns');
        if (file_exists($tmpPath)) {
            foreach (MyHelplers::myscandir($tmpPath) as $dirName) {
                if (!$this->checkFileAndAdd($tmpPath . '/' . $dirName, $warn, $dirName, $count))
                    FileHelper::removeDirectory($tmpPath . '/' . $dirName);
            }
            if (count($warn)) {
                $warn[] = Html::tag('li', '', ['role' => 'separator', 'class' => 'divider']);
                $warn[] = [
                    'label'       => 'Удалить все',
                    'encode'      => false,
                    'url'         => '#',
                    'linkOptions' => [
                        'data'  => [
                            'content' => 'Удалить все несохраненные данные',
                            'tmpname' => 'ALL'
                        ],
                        'class' => 'nav-mess stored-z-remove-all'
                ]];
            }
        }
        if ($tmp = $this->checkLastBaseBackUp($time)) {
            if ($time > -1)
                $time = \yii::$app->formatter->asRelativeTime($time);
            else
                $time = false;
            if ($tmp < 3) {
                $safety[] = [
                    'label'       => '<span class="glyphicon glyphicon-bell"></span>Сохранить БД',
                    'encode'      => false,
                    'url'         => Url::to(['site/export']),
                    'linkOptions' => [
                        'data-content' => $time !== false ? "База данных сохранялась $time!" : "База ниразу не сохранялась!",
                        'id'           => 'savebase',
                        'class'        => 'nav-mess'
                    ],
                ];
            } else {
                $safety[] = [
                    'label'       => 'Нет временной папки для сохранения БД',
                    'url'         => '#',
                    'linkOptions' => ['disabled' => true]
                ];
            }
            $count++;
        }
        if (count($warn)) {
            $cnt = count($warn) - 2;
            $messTxt = ' предупрежд';
            if ($cnt === 1) {
                $messTxt .= 'ение';
            } elseif ($cnt < 5) {
                $messTxt .= 'ния';
            } else {
                $messTxt .= 'ений';
            }
            $rVal[] = Html::tag('li', 'Не сохранённые данные ' . $cnt . $messTxt, [
                        'class' => 'dropdown-header']);
            foreach ($warn as $el)
                $rVal[] = $el;
        }
        if ($cnt = count($safety)) {
            if (count($warn))
                $rVal[] = Html::tag('li', '', ['role' => 'separator', 'class' => 'divider']);
            $messTxt = ' сообщ';
            if ($cnt === 1) {
                $messTxt .= 'ение';
            } elseif ($cnt < 5) {
                $messTxt .= 'ния';
            } else {
                $messTxt .= 'ений';
            }
            $rVal[] = Html::tag('li', $cnt . $messTxt . ' безопастности', ['class' => 'dropdown-header']);
            foreach ($safety as $el)
                $rVal[] = $el;
        }
        return ['content' => $rVal, 'count' => $count];
    }

    public function showMessage($returnmenu = false) {
        $mess = $this->createMessageList();
        if ($mess['count']) {
            $tmp = [
                'options' => ['class' => 'nav-mess-main'],
                'label'   => '<span class="glyphicon glyphicon-envelope"></span><span> Сообщения </span><span class="badge badgePrimary">' . $mess['count'] . '</span>',
                'encode'  => false,
                'items'   => $mess['content']
            ];
            if ($returnmenu) {
                return $tmp;
            } else {
                $this->mMenu[] = $tmp;
            }
        }
        return null;
    }

    private function setupMenu() {
        $user = Yii::$app->user->identity;
//        $messPress=false;
        $cachePostFix = Yii::$app->id . $user->username;

        if (!$messPress = $this->checkLastBaseBackUp()) {
            if ($this->mMenu = \yii::$app->cache->get('mMenu' . $cachePostFix)) {
                \yii::trace('Загружено из кэша', 'mMenu');
                return;
            } else {
                $this->mMenu = [];
            }
        } else {
            \yii::$app->cache->delete('mMenu' . $cachePostFix);
            \yii::trace('Удалено из кэша', 'mMenu');
        }
        if ($this->role == 'admin' || $this->role == 'moder') {
            $loggetIn = [];
            $uInfo = Yii::$app->user->identity->activeUserInfo();
            foreach ($uInfo as $el) {
                $loggetIn[] = [
                    
                    'label' => '<div>'.$el['name'] . '</div><div>вошел ' . $el['lastLoginText'] . '.;</div><div> Ос. сесии ' . $el['timeLeft'].';</div><div> Посл.активн: '.$el['activitiTime'].'</div>',
                    //'options'=>['title'=>'осталось '.$el['timeLeft']],
                    'url'   => '#'
                ];
            }
            $this->mMenu[] = [
                'label'  => 'Пользователи в сети ' . Html::tag('span', count($uInfo), [
                    'class' => 'badge badgePrimary']),
                'items'  => $loggetIn,
                'encode' => false,
                'options'=>[
                    'class'=>'user-info'
                ]
            ];
        }
        if (($this->role == 'admin' || $this->role == 'moder') && (($this->id === 'site' && $this->action->id === 'index') || $this->id === 'zakaz/zakazlist')) {
            $this->showMessage();
        }
        if ($user->can('/zakazi/zakaz/deslist') || $this->role == 'admin' || $this->role == 'moder') {
            $this->mMenu[] = ['label' => ($this->role == 'admin' || $this->role == 'moder') ? 'Дизайнер' : 'Заказы',
                'url'   => ['/zakaz/zakazlist/disainerindex']];
        }
        if ($user->can('/zakazi/zakaz/proizvodstvoindex') || $this->role == 'admin' || $this->role == 'moder') {
            $this->mMenu[] = ['label' => ($this->role == 'admin' || $this->role == 'moder') ? 'Производство' : 'Заказы',
                'url'   => ['/zakaz/zakazlist/proizvodstvoindex']];
        }
        if ($user->can('admin/setup/index')) {
            $this->mMenu[] = [
                'label' => 'Настройки',
                'url'   => ['/admin/setup/index']
            ];
        }
//        if ($user->can('/site/tables')) $this->mMenu[]=['label' => 'Таблицы', 'url' => ['/site/tables']];
        $userMenu = [
            ['label'       => 'Редактировать профиль',
                'url'         => '#',
                'linkOptions' => [
                    'data-key' => Yii::$app->user->identity->id,
                    'id'       => 'current-user-profile'
                ]],
            ['label'       => 'Выйти из системы',
                'url'         => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post'],
            ],
        ];
        if ($this->role === 'admin') {
            //$this->showMessage ();
            $userMenu[] = '<li class="divider"></li>';
            $userMenu[] = ['label' => 'Просмотреть логи', 'url' => ['/site/logs']];
            $userMenu[] = '<li class="divider"></li>';
            $userMenu[] = ['label' => 'Сохранить базу', 'url' => ['/site/export']];
        }

        $this->mMenu[] = [
            'label' => 'Профиль',
            'items' => $userMenu
        ];
        \yii::trace('Создано', 'mMenu');
        if (!$messPress) {
            //\yii::$app->cache->set ('mMenu'.$cachePostFix, $this->mMenu,1200);
            \yii::trace('Сохранено в кэш', 'mMenu');
        } else {
            \yii::trace('Не сохранено имеются сообщения!', 'mMenu');
        }
    }

    protected function setHomeUrl() {
        if (!\Yii::$app->user->isGuest)
            switch (\Yii::$app->user->identity->role) {
                case 'logist':
                    \yii::$app->homeUrl = Url::to(['/zakazi/zakaz/logistlist']);
                    break;
                case 'bugalter':
                    \yii::$app->homeUrl = Url::to(['/zakazi/zakaz/bugalterlist']);
                    break;
                case 'dizayner':
                    \yii::$app->homeUrl = Url::to(['/zakaz/zakazlist/disainerindex']);
                    break;
                case 'proizvodstvo':
                case 'proizvodstvochief':
                    \yii::$app->homeUrl = Url::to(['/zakaz/zakazlist/proizvodstvoindex']);
                    break;
            }
//        \yii\helpers\VarDumper::dump([
//            'role'=>Yii::$app->user->identity->role,
//            'homeUrl'=>Yii::$app->homeUrl
//        ], 10, true);Yii::$app->end();
    }

    private function breadcrumbsPrepareStoreValue() {
        $rVal = [$this->route, $this->view->title, 'get' => []];
        foreach (Yii::$app->request->get() as $key => $val) {
            if ($key !== 'bcrpos') {
                $rVal['get'][$key] = $val;
            }
        }
        return $rVal;
    }

    public function createBreadcrumbs() {
        $action = $this->action;
        if ($action->id !== 'login' && $action !== 'logout' && Yii::$app->response->format === \yii\web\Response::FORMAT_HTML) {
            if (!$breadcrumbs = Yii::$app->session->getFlash(MyHelplers::getHashKeyForBreadcrumbs())) {
                $breadcrumbs = ['values' => [], 'pos' => -1];
            }
            if (!isset($breadcrumbs['values']))
                $breadcrumbs['values'] = [];
            if (!isset($breadcrumbs['pos']))
                $breadcrumbs['pos'] = -1;
//            return;
            if ($action->id !== 'logout') {
                $oldPos = $breadcrumbs['pos'];
                $breadcrumbs['pos'] = (int) Yii::$app->request->get('bcrpos', -1);
                if (count($breadcrumbs['values'])) {
                    if ($breadcrumbs['pos'] > -1 || $oldPos > -1) {
                        if ($breadcrumbs['pos'] < 0)
                            $breadcrumbs['pos'] = $oldPos;
                        if ($breadcrumbs['pos'] < count($breadcrumbs['values']) - 1) {
                            if ($breadcrumbs['values'][$breadcrumbs['pos']][0] !== $this->route) {
                                $breadcrumbs['values'] = MyHelplers::arraySlice($breadcrumbs['values'], $breadcrumbs['pos']);
                                $breadcrumbs['values'][] = $this->breadcrumbsPrepareStoreValue();
                                $breadcrumbs['pos'] = count($breadcrumbs['values']) - 1;
                            }
                        } elseif ($breadcrumbs['values'][count($breadcrumbs['values']) - 1][0] !== $this->route) {
                            $breadcrumbs['values'][] = $this->breadcrumbsPrepareStoreValue();
                            $breadcrumbs['pos'] = count($breadcrumbs['values']) - 1;
                        }
                    } elseif ($breadcrumbs['values'][count($breadcrumbs['values']) - 1][0] !== $this->route) {
                        $breadcrumbs['values'][] = $this->breadcrumbsPrepareStoreValue();
                        $breadcrumbs['pos'] = count($breadcrumbs['values']) - 1;
                    }
                } else {
                    $breadcrumbs['values'][] = $this->breadcrumbsPrepareStoreValue();
                    $breadcrumbs['pos'] = 0;
                }
                if (count($breadcrumbs['values']) > 100) {
                    if ($breadcrumbs['pos'] > 0) {
                        array_shift($breadcrumbs['values']);
                    } else {
                        array_pop($breadcrumbs['values']);
                    }
                }
                if ($breadcrumbs['pos'] < 0)
                    $breadcrumbs['pos'] = count($breadcrumbs['values']) - 1;
                $this->view->params['breadcrumbs'] = $breadcrumbs;
                Yii::$app->session->setFlash(MyHelplers::getHashKeyForBreadcrumbs(), $breadcrumbs);
                Yii::trace(\yii\helpers\VarDumper::dumpAsString($breadcrumbs), 'ControllerMain');
            }
        } else {
            $this->view->params['breadcrumbs'] = [];
            $breadcrumbs = ['pos' => 0];
        }
        return $breadcrumbs;
    }

    private static $user_cache = null;

    private function userInfoUpdate(&$action, $logout = false) {
        $user = self::$user_cache;
        if (!$user) {
            $user = \app\models\TblUser::findOne(Yii::$app->user->identity->id);
            self::$user_cache = $user;
        }
        if ($user) {
            //            echo "action - > $action->id redirect\n";Yii::$app->end();
            $update = [];
            if ($logout || $action->id === 'logout') {
                if ($logout)
                    $user->logout_time = $user->control_time ? ($user->control_time + Yii::$app->user->authTimeout) : time();
                else
                    $user->logout_time = time();
                $user->control_time = 0;
                $update = ['login_time', 'control_time', 'logout_time'];
            } else {
                $user->control_time = time();
                $update = ['control_time'];
            }
            $user->update($update);
            Yii::debug('user', 'userFound');
            Yii::debug(Yii::$app->user->identity->activeUserInfo(), 'userFound');
        }
    }

    public function beforeAction($action) {
        if (file_exists(Yii::getAlias('@webroot/icons/main.png')))
            \Yii::$app->view->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => 'icons/main.png']);
        else
            \Yii::$app->view->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => 'pic/base.png']);
        if (!\Yii::$app->user->isGuest) {
            Yii::$app->session->open();
            if ($action->id !== 'storetemp')
                Yii::$app->session->setFlash('storeTOut', Yii::$app->user->authTimeout, true);
            $this->userInfoUpdate($action);
        }
        if (parent::beforeAction($action)) {
            $this->setHomeUrl();

            $this->logOrErr = $action->id == 'logout' || $action->id == 'login' || $action->id == 'error' || $action->id == 'blocked';
            if (!\Yii::$app->user->isGuest) {
                $this->role = \Yii::$app->user->identity->role;
                if ($action->id !== 'blocked')
                    $this->setupMenu();
                else {
                    $this->mMenu = [];
                    $this->mMenu[] = [
                        'label' => '(' . (!Yii::$app->user->identity->realname ? Yii::$app->user->identity->username : Yii::$app->user->identity->realname) . ')',
                        'items' => [
                            ['label'       => 'Выйти из системы',
                                'url'         => ['/site/logout'],
                                'linkOptions' => ['data-method' => 'post'],
                            ],
                        ]
                    ];
                }
            } elseif (!$this->logOrErr) {
                Yii::trace($this->id, 'controllerMain');
                $this->userInfoUpdate($action, true);
                if ($this->id == 'zakaz/zakazlist' && $action->id == 'view') {
                    $this->redirect(['site/login'], 401);
                } else {
                    $this->redirect(['site/login']);
                }
                return false;
            }
            if (\Yii::$app->user->identity)
                if (!\Yii::$app->user->identity->can($this->id . '/' . $action->id) && $action->id !== 'error' && $action->id !== 'blocked' && !($this->action->id === 'ajaxupdaterequest' && ($this->role === 'proizvodstvo' || $this->role === 'proizvodstvochief'))) {
                    $behavior = $this->behaviors();
                    if (Yii::$app->request->isAjax || Yii::$app->request->isPjax || Yii::$app->request->isPost) {
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    }
                    throw new ForbiddenHttpException('Недостаточно прав');
                }
            if (\yii::$app->params['isBlocked'] && \yii::$app->user->id != 1 && !\Yii::$app->user->isGuest) {
                if ($action->id !== 'blocked')
                    $this->redirect(['site/blocked']);
            }
            if ($action->id != 'logout' && $action->id != 'login') {
                $this->view->registerJs('$.fn.enablePopover();', \yii\web\View::POS_READY, 'enblPopover');
                $this->view->registerJs('$.fn.enableButtons();', \yii\web\View::POS_READY, 'enblButton');
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterAction($action, $result) {
        $rVal = parent::afterAction($action, $result);
        return $rVal;
    }

    public function getOrPost($name, $default = null) {
        if (($rVal = yii::$app->request->get($name, $default)) === null) {
            $rVal = yii::$app->request->post($name, $default);
        }
        return $rVal;
    }

    public function brandUrl() {
        return Yii::$app->homeUrl;
    }

    private function checkLastBaseBackUp(&$time = null) {
        $fn = realpath(\yii::getAlias('@app/../../' . \yii::$app->params['sqlBackFolder']));
        if ($time !== null)
            $time = -1;
        if ($fn) {
            $fn .= '/' . \yii::$app->params['sqlBackFileName'];
            if (file_exists($fn)) {
                if ($time !== null) {
                    $time = filectime($fn);
                    $t = time() - $time;
                } else {
                    $t = time() - filectime($fn);
                }
                if ($t > \yii::$app->params['saveBaseTimeOut'])
                    return 1; //пора обновить
                else
                    return 0; //всё ок
            } else
                return 2; //Файл не создан
        }return 3; //Путь не найден
    }

    public function sideMenu() {
        if (!$rVal = Yii::$app->cache->get('sideMenu' . $this->action->id . $this->role . Yii::$app->id, false)) {
            if ($this->role === 'admin' || $this->role === 'moder') {
                $items = [
                    [
                        'label'   => Html::tag('span', null, ['class' => 'glyphicon glyphicon-book']) . Html::tag('p', 'Заказы'),
                        'items'   => [
                            [
                                'label'   => Html::tag('span', null, ['class' => 'glyphicon glyphicon-list-alt']) . Html::tag('p', 'Менеджера'),
                                'url'     => ['/zakaz/zakazlist/index'],
                                'options' => ['class' => Yii::$app->request->get('r') === 'zakaz/zakazlist/index' ? 'dropdown active' : 'dropdown']
                            ],
                            [
                                'label'   => Html::tag('span', null, ['class' => 'glyphicon glyphicon-camera']) . Html::tag('p', 'Дизайнера'),
                                'url'     => ['/zakaz/zakazlist/disainerindex'],
                                'options' => ['class' => Yii::$app->request->get('r') === 'zakaz/zakazlist/disainerindex' ? 'dropdown active' : 'dropdown']
                            ],
                            [
                                'label'   => Html::tag('span', null, ['class' => 'glyphicon glyphicon-camera']) . Html::tag('p', 'Производство'),
                                'url'     => ['/zakaz/zakazlist/proizvodstvoindex'],
                                'options' => ['class' => Yii::$app->request->get('r') === 'zakaz/zakazlist/proizvodstvoindex' ? 'dropdown active' : 'dropdown']
                            ],
//                            ['label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-wrench']).Html::tag('p','Производство'),'url'=>['/zakaz/zakazlist/materialindex','isproduct'=>1]],
                        ],
                        'options' => ['class' => in_array(Yii::$app->request->get('r'), [
                                'zakaz/zakazlist/index', 'zakaz/zakazlist/disainerindex']) ? 'dropdown active' : 'dropdown']
                    ],
                    [
                        'label'   => Html::tag('span', null, ['class' => 'glyphicon glyphicon-tasks']) . Html::tag('p', 'Материалы'),
                        'items'   => [
                            ['label' => Html::tag('span', null, ['class' => 'glyphicon glyphicon-briefcase']) . Html::tag('p', 'Для заказа'),
                                'url'   => ['/zakaz/zakazlist/materialindex', 'isproduct' => false]],
                            ['label' => Html::tag('span', null, ['class' => 'glyphicon glyphicon-wrench']) . Html::tag('p', 'В производ.'),
                                'url'   => ['/zakaz/zakazlist/materialindex', 'isproduct' => true]],
                        ],
                        'options' => ['class' => in_array(Yii::$app->request->get('r'), [
                                'zakaz/zakazlist/materialindex']) ? 'dropdown active' : 'dropdown']
                    ],
                    ['label' => Html::tag('span', null, ['class' => 'glyphicon glyphicon-plane']) . Html::tag('p', 'Логистика'),
                        'url'   => ['/zakazi/zakaz/logistlist']],
                    ['label' => Html::tag('span', Html::tag('img', null, [
                                    'src' => $this->view->assetManager->publish('@app/web/css/pic/sklad_2.png')[1],
                                ]), ['class' => '']) . Html::tag('p', 'Склад'),
                        'url'   => ['/sklad']],
                ];
                if ($this->role == 'admin') {
                    $items[] = [
                        'label'   => Html::tag('span', null, ['class' => 'glyphicon glyphicon-usd']) . Html::tag('p', 'Бухгалтерия'),
                        'items'   => [
                            [
                                'label'   => Html::tag('span', null, ['class' => 'glyphicon glyphicon-usd']) . Html::tag('p', 'Основная'),
                                'url'     => ['/zakaz/bugalterlist/bugalterindex'],
                                'options' => ['class' => Yii::$app->request->get('r') === 'zakaz/bugalterlist/bugalterindex' ? 'dropdown active' : 'dropdown']
                            ],
                            [
                                'label'   => Html::tag('span', null, ['class' => 'glyphicon glyphicon-usd']) . Html::tag('p', 'Расходы'),
                                'url'     => ['/zakaz/bugalterpostav/bugalterindex'],
                                'options' => ['class' => Yii::$app->request->get('r') === 'zakaz/bugalterpostav/bugalterindex' ? 'dropdown active' : 'dropdown']
                            ],
                            [
                                'label'   => Html::tag('span', null, ['class' => 'glyphicon glyphicon-usd']) . Html::tag('p', 'Сверка'),
                                'url'     => ['/zakaz/bugalterpodryad/bugalterindex'],
                                'options' => ['class' => Yii::$app->request->get('r') === 'zakaz/bugalterpodryad/bugalterindex' ? 'dropdown active' : 'dropdown']
                            ],
                            ['label'   => Html::tag('span', Html::tag('img', null, [
                                            'src'   => $this->view->assetManager->publish('@app/web/css/pic/zp1.png')[1],
                                            'width' => 30, 'style' => 'width:30px;']), [
                                    'class' => '']) . Html::tag('p', 'Зарплата'),
                                'url'     => ['/zarplata/index'],
                                'options' => ['class' => Yii::$app->request->get('r') === 'zarplata/index' ? 'dropdown active' : 'dropdown']
                            ],
                        ],
                        'options' => ['class' => in_array(Yii::$app->request->get('r'), [
                                'zakaz/bugalterlist/bugalterindex', 'zakaz/bugalterpostav/bugalterindex',
                                'zakaz/bugalterpodryad/bugalterindex', 'zarplata/index']) ? 'dropdown active' : 'dropdown']
                    ];
                }
                if ($this->role === 'admin' || $this->role === 'moder') {
                    $items[] = [
                        'label' => Html::tag('span', Html::tag('img', null, [
                                    'src' => $this->view->assetManager->publish('@app/web/css/pic/statistic.png')[1],
                                ]), ['class' => '']) . Html::tag('p', 'Статистика'),
                        'items' => [
                            [
                                'label' => 'Таблица',
                                'url'   => ['/site/statistic']
                            ],
                            [
                                'label' => 'График',
                                'url'   => ['/site/statisticgraph']
                            ]
                        ]
                    ];
                }
            } else {
//                echo '<h2>'.$this->role.'</h2>';
//                echo \yii\helpers\VarDumper::dumpAsString($items,10,true);Yii::$app->end();
                $items = [];
            }
            $items2 = [];
//            $items2[]=[
//                'label'=>Html::img($this->view->assetManager->publish('@app/web/css/pic/ico_sklad.png')[1],[
//                ]),
//                'url'=>'#',
//                'linkOptions'=>['title'=>'Склад']
//            ];
            $items2[] = [
                'label'       => Html::img($this->view->assetManager->publish('@app/web/css/pic/Calculator.gif')[1], [
                ]),
                'url'         => '#',
                'linkOptions' => ['title' => 'Калькулятор', 'id' => 'calculator1']
            ];
            $items2[] = [
                'label'       => Html::img($this->view->assetManager->publish('@app/web/css/pic/Printer.gif')[1], [
                        //'width'=>34
                ]),
                'linkOptions' => ['title' => 'Печать', 'id' => 'printerClick'],
                'url'         => '#',
            ];
            $items2[] = [
                'label'       => Html::img($this->view->assetManager->publish('@app/web/css/pic/Save.gif')[1], [
                        //'width'=>34
                ]),
                'linkOptions' => ['title' => 'Сохранить', 'id' => 'main-save-button'],
                'url'         => '#',
            ];

            $rVal = \app\widgets\MNav::widget([
                        'options'         => ['class' => 'nav navbar-nav'],
                        //'id'=>'left-nav-menu',
                        'activateItems'   => false,
                        'activateParents' => true,
                        'encodeLabels'    => false,
                        'items'           => $items,
            ]);
            $rVal .= \app\widgets\MNav::widget([
                        'options'         => ['class' => 'nav navbar-nav'],
                        //'id'=>'left-nav-menu',
                        'activateItems'   => false,
                        'activateParents' => true,
                        'encodeLabels'    => false,
                        'items'           => $items2,
            ]);
            Yii::trace("Боковое меню ('sideMenu" . $this->action->id . $this->role . "')\nРоль: '$this->role'\nЭкшен: '" . $this->action->id . "\nСоздано и сохранено в кэш", 'sideMenu');
            //Yii::$app->cache->set('sideMenu'.$this->action->id.$this->role.Yii::$app->id, $rVal,3600);
        } else {
            Yii::trace("Боковое меню ('sideMenu" . $this->action->id . $this->role . "')\nРоль: '$this->role'\nЭкшен: '" . $this->action->id . "\nЗагружено из кэша", 'sideMenu');
        }
        return $rVal;
    }
        public function generateCacheKey($key){
            $tmp=$key.MyHelplers::translit(\Yii::$app->name).\Yii::$app->id;
            return hash('ripemd160',$tmp);
        }
}
