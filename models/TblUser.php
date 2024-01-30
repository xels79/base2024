<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use app\components\MyHelplers;

/**
 * This is the model class for table "tbl_user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property integer $utype
 * @property string $realname
 */
class TblUser extends \yii\db\ActiveRecord {

    public $_utypeRus = false;
    public $_utypesList = null;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_user';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['username', 'password', 'email'], 'required'],
            [['utype', 'update_time', 'login_time', 'last_zakaz', 'control_time',
            'logout_time'], 'integer'],
            [['username', 'password', 'email', 'realname', 'passwordnew'], 'string',
                'max' => 128],
            [['wages', 'percent1', 'percent2', 'percent3'], 'number']
        ];
    }

    public static function allowListDefault() {
        return [
            'site'            => [
                'Основная',
                'login'          => 'Вход',
                'logout'         => 'Выход',
                'index'          => 'Главная',
                'tables'         => 'Таблицы',
                'statistic'      => 'Статистика',
                'statisticgraph' => 'Статистика график'
            ],
            'tables'          => [
                'Маленькие таблицы',
                'list'     => 'Просмотр',
                'validate' => 'Валидация значения',
                'addedit'  => 'Редактирование',
                'remove'   => 'Удаление',
//                'gettecnicalsoptions'=>'Настройка технички'
            ],
            'material'        => [
                'Материалы',
                'list'                     => 'Просмотр типов',
                'addedit'                  => 'Редактирование типов',
                'validate'                 => 'Валидация типов',
                'remove'                   => 'Удаление типов',
                'subtableslist'            => 'Получ. структ. матер.',
                'subtablegetlist'          => 'Получ. содержим. пункта',
                'subtabladdedit'           => 'Редактирование содержимого',
                'getsuppliers'             => 'Поставщики',
                'subtableremove'           => 'Удаление содержимого',
                'subtablegetallnamesfordd' => 'Чтобы избежать повторы'
            ],
            'banks'           => [
                'Банковская информация',
                'searchbank' => 'Поиск банка'
            ],
            'admin/firms'     => [
                'Фирмы',
                'index'               => 'Фирмы главная',
                'list'                => 'Список фирмы',
                'setsizes'            => 'Размер колонок',
                'getavailablecolumns' => 'Получить доступн. кол-ки',
                'setcolumns'          => 'Сохранение настроек колонок',
            ],
            'admin/mainpage'  => [
                'Управление фирмами',
                'ajaxlist_zak'    => 'Заказчики',
                'ajaxlist_pod'    => 'Подрядчики',
                'ajaxlist_post'   => 'Поставщики',
                'ajaxlist_zakone' => 'Добавить/Удалить/Изменить'
            ],
            'zakaz/zakazlist' => [
                'Заказы',
                'index'                           => 'Заказы главная',
                'list'                            => 'Список заказов',
                'view'                            => 'Просмотр подробностей/технички',
                'setsizes'                        => 'Размер колонок',
                'getavailablecolumns'             => 'Получить доступн. кол-ки',
                'setcolumns'                      => 'Сохранение настроек колонок',
                'changerow'                       => 'Изменить запись',
                'getoneraw'                       => 'Получить оди ряд с записью',
                'editanyorder'                    => 'Разрешить редакт. чужой заказ',
                'materialindex'                   => 'Материалы для заказа/производство',
                'listmaterialp'                   => 'Список материалы для производство',
                'setsizesmp'                      => 'Размер колонок для материалы продукции',
                'getavailablecolumnsmaterialsp'   => 'Получить доступн. кол-ки для материалы производство',
                'setcolumnsmp'                    => 'Сохранение настроек колонок для материалы производство',
                'proizvodstvoindex'               => 'Список заказов для производства главная',
                'listproizvodstvo'                => 'Список заказов для производство',
                'setsizesmproizvodstvo'           => 'Размер колонок для материалы производство',
                'getavailablecolumnsproizvodstvo' => 'Получить доступн. кол-ки для заказы производство',
                'setcolumnsproizvodstvo'          => 'Сохранение настроек колонок для заказы производство',
                'addpechatnik'                    => 'Добавить заказ к печатнику',
                'removepechatnik'                 => 'Убрать заказ от печатнмка',
                'totomorowpechatnik'              => 'Перенести на след день',
                'toreadypechatnik'                => 'Пометить как прокат готов',
                'dirtindex'                       => 'Черновики',
                'listdirt'                        => 'Список черновиков',
                'setsizesdirt'                    => 'Размер колонок черновиков',
                'getavailablecolumnsdirt'         => 'Получить доступн. кол-ки черновиков',
                'setcolumnsdirt'                  => 'Сохранение настроек колонок черновиков',
                'disainerindex'                   => 'Список заказов для дизайнера главная',
                'listdisainer'                    => 'Получение списка для дизайнера',
                'getavailablecolumnsdisainer'     => 'Получить доступн. кол-ки для дизайнера',
                'setcolumnsdisainer'              => 'Сохранение настроек колонок для дизайнера',
                'setsizesmdisainer'               => 'Размер колонок для дизайнера',
                'stagedisainer'                   => 'Изменение этапа работы для дизайнера',
                'stageproizvodstvo'               => 'Изменение этапа работы для производство'
            ],
            'zakaz/zakaz'     => [
                'Редакитрование заказа',
                'addedit'               => 'Добавить/Изменить',
                'getfullmaterialinfo'   => 'Подробности по материалу полное',
                'getonematerinfo'       => 'Подробности по материалу сокращенное',
                'fileupload'            => 'Загрузка файлов',
                'preaparefiletoreomove' => 'Удаление файлов',
                'publishfile'           => 'Публикация пред. просмотра',
                'getfile'               => 'Скачивание файла',
                'getstored'             => 'Получить несохраненный заказ',
                'removestored'          => 'Удалить временные данные',
                'storetemp'             => 'Сохранить временные данные',
                'removezakaz'           => 'Удалить заказ',
                'customerlist'          => 'Получить сп-ок исполнит.',
                'smalltablelist'        => 'Вспомогательн данные',
                'cansel'                => 'Отмена',
                'getpodinfo'            => 'Получ. информ. о подрядчике',
                'getcustomermanager'    => 'Получ. менеджера порядчика',
                'liffcutter'            => 'Калькулятор резки листа',
                'copy'                  => 'Копировать заказ',
                'validate'              => 'Проверка введённых данных',
                'mailfile'              => 'Отправка почты',
                'mailGetZkazchikEmails' => 'Получение адресов заказчика',
                'paketcutter'           => 'Резка пакета'
            ],
            'admin/setup'     => [
                'Управление настройками/пользователями',
                'index'      => 'Основная стр',
                'userchange' => 'Редактировать пользователя'
            ],
            'rekvizit'        => [
                'Управление реквизитами фирмы',
                'list'    => 'Получить список',
                'send'    => 'Отправить по e-mail',
                'getfile' => 'Скачать файл',
                'remove'  => 'Удалить файл',
                'save'    => 'Добавить/Изменить'
            ],
            'sklad'           => [
                'Управление складом',
                'indexsklad'   => 'Просмотр',
                'updatecolors' => 'Изменение'
            ],
            'zametki'         => [
                'Заметки',
                'list'      => 'Спиок заметок',
                'save'      => 'Сохранение заметки',
                'remove'    => 'Удаление заметки',
                'getfile'   => 'Работа с файлами',
                'send'      => 'Отправить файл',
                'addtab'    => 'Добавить вкладку',
                'removetab' => 'Удалить вкладку',
                'renametab' => 'Переименовать вкладку'
            ],
        ];
    }

    public function getPasswordnew() {
        return '';
    }

    public function setPasswordnew($password) {
        if ($password) {
            $this->password = Yii::$app->security->generatePasswordHash($password);
        }
    }

    public static function getUserAccssesSelectDD($id) {
        return Html::tag('div',
                        Html::button(Html::tag('span', null, ['class' => 'glyphicon glyphicon-plus'])
                                , ['class'         => 'btn btn-main btn-xs disabled', 'id'            => $id,
                            'data-toggle'   => "dropdown", 'aria-haspopup' => "true",
                            'aria-expanded' => "false"])
                        . MyHelplers::levelSelectUl(self::allowListDefault(), null, $id)
                        , ['class' => 'dropup']);
    }

    public static function getUtypesListS() {
        $rVal = [];
        foreach (MyRbac::find()->select(['id', 'name', 'value'])->asArray()->all() as $val) {
            $rVal[(int) $val['id']] = $val['name'];
        }
        return $rVal;
    }

    public static function getUtypesListSOptions() {
        $rVal = ['options' => []];
        foreach (MyRbac::find()->select(['id', 'name', 'value'])->asArray()->all() as $val) {
            $rVal['options'][(int) $val['id']] = ['data-value' => $val['value']];
        }
        return $rVal;
    }

    public function getUtypesList() {
        if (!$this->_utypesList) {
            $this->_utypesList = self::getUtypesListS();
        }
        return $this->_utypesList;
    }

    public function getUtypeRus() {
        if (!$this->_utypeRus) {
            if ($model = MyRbac::findOne($this->utype)) {
                $this->_utypeRus = $model->name;
            } else {
                $this->_utypeRus = '';
            }
        }
        return $this->_utypeRus;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'        => 'Идентификатор',
            'username'  => 'Имя пользователя',
            'npassword' => 'Пароль',
            'email'     => 'Почта',
            'utype'     => 'Идентификатор типа',
            'utypeRus'  => 'Уровень доступа',
            'realname'  => 'Менеджер',
            'wages'     => 'Оклад',
            'percent1'  => '% Астерион',
            'percent2'  => '% Подрядчик',
            'percent3'  => '% Сверх прибыль',
        ];
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->update_time = time();
        // ...custom code here...
        return true;
    }

    public function userInfo() {
        $lOut = $this->logout_time;
        if ($this->control_time + Yii::$app->user->authTimeout > time() || $lOut > $this->control_time) {
            $isLin = false;
            if ($lOut < $this->control_time)
                $lOut = $this->control_time + Yii::$app->user->authTimeout;
        } else {
            $isLin = true;
        }
        $tDN=new \DateTime("@".time()."");
        $tI=$tDN->diff(new \DateTime("@".$this->control_time.""));
        
        return [
            'isLoggetIn'     => $isLin,
            'lastLoginText'  => $this->login_time ? Yii::$app->formatter->asDatetime($this->login_time) : '',
            'lastLogoutText' => $lOut ? Yii::$app->formatter->asDatetime($lOut) : '',
            'timeLeft'       => Yii::$app->formatter->asDuration(($this->control_time + Yii::$app->user->authTimeout) - time()),
            'name'           => $this->realname,
            'activitiTime'=> MyHelplers::formatTimeInterval($tI) //Yii::$app->formatter->asDatetime($this->control_time)
        ];
    }

    public function activeUserInfo() {
        $rVal = [];
        foreach (TblUser::find()
                ->select(['id', 'login_time', 'control_time', 'logout_time', 'realname'])
                ->where(['>', 'tbl_user.control_time', time() - Yii::$app->user->authTimeout])
                ->all() as $el) {
            $rVal[$el->id] = $el->userInfo();
        }
        return $rVal;
    }

}
