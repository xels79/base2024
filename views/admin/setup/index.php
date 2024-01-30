<?php
/* @var $this yii\web\View */

use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\AList;
use app\widgets\JSRegister;
use yii\helpers\Json;
use app\models\tables\WorkOrproductType;

$this->registerJsFile( $this->assetManager->publish( '@app/web/js/base_widget.js' )[1], [
    'depends'  => [yii\jui\JuiAsset::className()],
    'position' => \yii\web\View::POS_END
        ], 'base_widget' );
$this->registerJsFile( $this->assetManager->publish( '@app/web/js/banks_search.js' )[1], [
    'depends'  => [yii\jui\JuiAsset::className()],
    'position' => \yii\web\View::POS_END
        ], 'banks_search' );
$this->registerJsFile( $this->assetManager->publish( '@app/web/js/simple_form.js' )[1], [
    'depends'  => [\yii\web\JqueryAsset::className()],
    'position' => \yii\web\View::POS_END
        ], 'simple_form' );
$this->registerJsFile( $this->assetManager->publish( '@app/web/js/parent_controller.js' )[1], [
    'depends'  => [yii\jui\JuiAsset::className()],
    'position' => \yii\web\View::POS_END
        ], 'parent_controller' );
$this->registerJsFile( $this->assetManager->publish( '@app/web/js/setup.js' )[1], [
    'depends'  => [\yii\web\JqueryAsset::className()],
    'position' => \yii\web\View::POS_END
        ], 'setupjs' );
$this->title = 'Настройки';
?>

<?php
JSRegister::begin( [
    'key'      => 'setupInit',
    'position' => \yii\web\View::POS_READY
] );
?>
<script>
    $.custom.setupController({
        requestList: "<?= Url::to( ['/admin/setup/ajaxlist_user'] ) ?>",
        userChangeUrl: "<?= Url::to( ['/admin/setup/userchange'] ) ?>",
        userRemoveUrl: "<?= Url::to( ['/admin/setup/userremove'] ) ?>",
        accssecChangeUrl: "<?= Url::to( ['/admin/setup/saveaccssesrul'] ) ?>",
        requestFirmList: "<?= Url::to( ['/admin/setup/ajaxlist_ourfirm'] ) ?>",
        bankSearchUrl: "<?= Url::to( ['/banks/searchbank'] ) ?>",
        loadPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader-hor.gif' )[1] ?>",
        pointPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/w-point.gif' )[1] ?>",
        workTypeTitles:<?= \yii\helpers\Json::encode( app\models\admin\ManagersOur::$payment_list_title ) ?>
    });
    //requestWOPType
    $('#requestWOPType').tablesControllerDialog($.extend({}, {}, {
        title: 'Виды материала',
        formName: "WorkOrproductType",
        showDefaultButton: true,
        bodyBackgrounColor: '#66c2d1',
        loadPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader-hor.gif' )[1] ?>",
        requestUrl: "<?= Yii::$app->urlManager->createUrl( ['/tables/list'] ) ?>",
        validateUrl: "<?= Yii::$app->urlManager->createUrl( ['/tables/validate'] ) ?>",
        form: {
            requestUrl: "<?= Yii::$app->urlManager->createUrl( ['/tables/addedit'] ) ?>",
            removeUrl: "<?= Yii::$app->urlManager->createUrl( ['/tables/remove'] ) ?>",
            fields: {
                id: ['prim'],
                name: ['string', 'Название'],
                category: ['integer', 'Тип продукции 1', null, <?= Json::encode( WorkOrproductType::$catTextArray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT ) ?>],
                category2: ['integer', 'Тип продукции 2', -1, <?= Json::encode( WorkOrproductType::$catText2Array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT ) ?>]
            }
        }

    }));
</script>
<?php JSRegister::end(); ?>

<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-10">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-4">
                        <div class="row">
                            <ul class="a-list">
                                <li><a id="setup-ourfirm" class="btn btn-main">Наша фирма</a></li>
                                <li><a id="setup-user" class="btn btn-main">Пользователи</a></li>
                            </ul>
                        </div>
                        <div class="row">
                            <ul class="a-list">
                                <li><a class="btn btn-main" disabled="disabled">Бухгалтерия</a></li>
                                <li><a class="btn btn-main" disabled="disabled">Сеть</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12">
                        <ul class="a-list">
                            <li><?=
                                Html::a( 'Отчеты', null, ['class'    => 'btn btn-main',
                                    'disabled' => "disabled"] )
                                ?>
                            <li><a class="btn btn-main" disabled="disabled">Формы</a></li>
                            <li><?=
                                Html::a( 'Виды материала', '#', ['id' => 'requestWOPType',
                                    'class' => 'btn btn-main', 'title' => 'Тип материалов.'] )
                                ?></li>

                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <ul class="a-list">
                            <li><a class="btn btn-main-font-black" disabled="disabled">Чёрный список</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xs-1 hidden-xs">
<?= $this->context->pic2; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-11">
                <ul class="a-list a-list-inline">
                    <li><a class="btn btn-blue" disabled="disabled">Сохранить базу</a></li>
                    <li><a class="btn btn-blue" disabled="disabled">Обновление</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>