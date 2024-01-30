<?php
/* @var $this yii\web\View */

use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
//use app\widgets\AList;
use app\widgets\JSRegister;
use app\models\tables\Productions;
use app\models\tables\DependTables;
use yii\helpers\Json;
use app\models\tables\WorkOrproductType;

$this->title = 'Главная';
$this->registerJsFile( $this->assetManager->publish( '@app/web/js/indexController.js' )[1], [
    'depends'  => [yii\jui\JuiAsset::className()],
    'position' => \yii\web\View::POS_END
        ], 'indexController' );
$this->renderFile( '@app/views/layouts/zakaz-wrap.php' );
$this->registerJsFile( 'js/tinymce/tinymce.min.js', ['position' => \yii\web\View::POS_END] );
?>
<?php
JSRegister::begin( [
    'key'      => 'setupInit',
    'position' => \yii\web\View::POS_READY
] );
?>
<script>

    $('#zakazAddNew').click(function ( ) {
        var d = $('#zakaz_dialog');
        if (!d.length) {
            d = $.fn.creatTag('div', {
                id: 'zakaz_dialog'
            });
            $('body').append(d);
            d.zakazAddEditController($.extend({}, def_opt));
        }
        if (!d.zakazAddEditController('isOpen'))
            d.zakazAddEditController('open');
    });
    let opt = window.subSmallTableOPt;
    $('#requestWorkTypes').tablesControllerDialog($.extend({}, opt, {
        title: 'Вид работ',
        formName: "Worktypes",
        showDefaultButton: true,
        form: {
            requestUrl: "<?= Yii::$app->urlManager->createUrl( ['tables/addedit'] ) ?>",
            removeUrl: "<?= Yii::$app->urlManager->createUrl( ['tables/remove'] ) ?>",
            fields: {
                id: ['prim'],
                name: ['string', 'Название'],
            }
        }
    }));
    $('#requestProdaction').tablesControllerDialog($.extend({}, opt, {
        title: 'Продукция',
        formName: "Productions",
        showDefaultButton: true,
        form: {
            requestUrl: "<?= Yii::$app->urlManager->createUrl( ['tables/addedit'] ) ?>",
            removeUrl: "<?= Yii::$app->urlManager->createUrl( ['tables/remove'] ) ?>",
            fields: {
                id: ['prim'],
                name: ['string', 'Название'],
                category: ['integer', 'Технушка', null, {0: 'Листовая', 1: 'Пакеты п/э', 2: 'Сувенирка', 3: 'Уф-лак', 4: 'Пустая',5:'Термоподъём'}],
                category2: ['checkList', 'Калькулятор', null,<?=
Json::encode( \yii\helpers\ArrayHelper::merge( [
            0 => 'Все'], Productions::category2values ) )
?>]
            }
        }
    }));
    $('#requestPostPrint').tablesControllerDialog($.extend({}, opt, {
        title: 'Постпечать',
        formName: "Postprint",
        showDefaultButton: true,
        form: {
            requestUrl: "<?= Yii::$app->urlManager->createUrl( ['tables/addedit'] ) ?>",
            removeUrl: "<?= Yii::$app->urlManager->createUrl( ['tables/remove'] ) ?>",
            fields: {
                id: ['prim'],
                name: ['string', 'Название'],
            }
        }
    }));
    $('#requestMaterialsTable').click(function ( ) {
        var d = $('#' + $(this).attr('id') + '_dialog');
        if (!d.length) {
            d = $.fn.creatTag('div', {
                id: $(this).attr('id') + '_dialog'
            });
            $('body').append(d);
            d.materialTable({
                requestIndex: "<?= Yii::$app->urlManager->createUrl( ['materialtable/index'] ) ?>",
                requestTable: "<?= Yii::$app->urlManager->createUrl( ['materialtable/list'] ) ?>",
                requestUpdate: "<?= Yii::$app->urlManager->createUrl( ['materialtable/update'] ) ?>",
                pageSize:<?= \app\models\tables\MaterialsOnFirmsSearch::$defaultPageSize ?>
            });
        }
        if (!d.materialTable('isOpen'))
            d.materialTable('open');
    });
    $('#requestMaterials').click(function ( ) {
        var d = $('#' + $(this).attr('id') + '_dialog');
        if (!d.length) {
            d = $.fn.creatTag('div', {
                id: $(this).attr('id') + '_dialog'
            });
            $('body').append(d);
            d.tablesControllerMaterialTypes({
                but: '#requestMaterials',
                title: 'Материалы',
                bodyBackgrounColor: '#66c2d1',
                formName: "Tablestypes",
                loadPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader-hor.gif' )[1] ?>",
                bigLoaderPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader.gif' )[1] ?>",
//                requestUrl:"<?= Yii::$app->urlManager->createUrl( ['tables/list'] ) ?>",
//                validateUrl:"<?= Yii::$app->urlManager->createUrl( ['tables/validate'] ) ?>",
                materialRequestListUrl: "<?=
Yii::$app->urlManager->createUrl( [
    'material/list'] )
?>",
                materialTablesListUrl: "<?=
Yii::$app->urlManager->createUrl( [
    'material/subtableslist'] )
?>",
                suppliersRequestUrl: "<?= Yii::$app->urlManager->createUrl( ['material/getsuppliers'] ) ?>",
                subtablegetlistUrl: "<?= Yii::$app->urlManager->createUrl( ['material/subtablegetlist'] ) ?>",
                subtabladdeditUrl: "<?= Yii::$app->urlManager->createUrl( ['material/subtabladdedit'] ) ?>",
                removeSubTUrl: "<?= Yii::$app->urlManager->createUrl( ['material/subtableremove'] ) ?>",
                referenceColumnName: "<?= DependTables::$reference ?>",
                subTableDDListUrl: "<?= Yii::$app->urlManager->createUrl( ['material/subtablegetallnamesfordd'] ) ?>",
                onAddComplit: function (data) {
                    console.log(data);
                    if (data.dependTables) {
                        d = $.ui.dialog({
                            title: 'Материал добавлен',
                            modal: true,
                            width: 700,
                            closeOnEscape: true,
                        });
                        d.uiDialog.css('background-color', 'white');
                        $.each(data.dependTables, function ( ) {
                            d.element.append($.fn.creatTag('p', {text: this}));
                        });
                    }
                },
                materialForm: {
                    formName: 'Tablestypes',
                    requestUrl: "<?= Yii::$app->urlManager->createUrl( ['material/addedit'] ) ?>",
                    removeUrl: "<?= Yii::$app->urlManager->createUrl( ['material/remove'] ) ?>",
                    onReady: function (oldV) {
                        var defV = null;
                        if (oldV['Tablestypes-struct']) {
                            defV = JSON.parse(oldV['Tablestypes-struct']);
                        }
                        if (!defV)
                            return;
                        console.group('Корекция выбранных');
                        var structL = $('#Tablestypes-structlist');
                        if (!structL.length) {
                            console.log('Tablestypes-structlis - не найден, конец.');
                            console.groupEnd( );
                            return;
                        }
                        var listSel = structL.prev( ).children('.list-group:first-child'), listEv = structL.prev( ).children('.list-group:last-child');
                        if (!listSel.length || !listEv.length) {
                            console.log('.list-group - не найден , конец.');
                            console.groupEnd( );
                            return;
                        }
                        console.log('oldV', defV);
                        console.log('Проверка выбраных');
                        $.each(listEv.children('a'), function ( ) {
                            if ($.inArray($(this).text( ), defV) > -1) {
                                listSel.append($.fn.creatTag('a', {class: 'list-group-item ui-state-disabled', text: $(this).text( )}));
                                console.log('Обработали:', $(this).text( ));
                                $(this).remove( );
                            }
                        });
                        console.log('Сохранение резыльтата', oldV['Tablestypes-struct']);
                        structL.val(oldV['Tablestypes-struct']);
                        console.log('Корекция завершена, конец.');
                        console.groupEnd( );
                    },
                    fields: {
                        id: ['prim'],
                        name: ['strind', 'Название'],
                        structlist: [function (el, defVal) {
                                if (!defVal)
                                    defVal = [];
                                var input = $.fn.creatTag('input', {
                                    type: 'hidden',
                                    id: 'Tablestypes-structlist'
                                });
                                input.val(JSON.stringify(defVal));
                                el.append($.fn.creatTag('h5', {text: 'Структура:', style: {width: '100%', 'text-align': 'left'}}));
                                var inf = $.fn.creatTag('div', {'class': 'inf'});
                                var help = $.fn.creatTag('a', {'class': 'btn glyphicon glyphicon-question-sign'});
                                var d = null;
                                inf.append($.fn.creatTag('p', {'html': 'Выбранно:'})).find('p').append(help).find('.btn').mouseenter(function (e) {
//                                e.preventDefault();
                                    console.log('tt');
                                    if (d)
                                        return;
                                    d = $.ui.dialog({
                                        title: false,
                                        //modal:true,
                                        closeOnEscape: true,
                                        show: {effect: "fade", duration: 1800},
                                        position: {my: "left top", at: "right top", of: $(this)}
                                    });
                                    d.uiDialog.css('background-color', 'white');
                                    d.uiDialogTitlebar.remove( );
                                    d.element.text('Вы берите необходимые таблицы. Первая таблица будет основной, последующие зависимыми. После сохранения изменения невозножны!');
                                });
                                inf.find('p').find('.btn').mouseleave(function (e) {
                                    if (d) {
                                        d.close( );
                                        d.uiDialog.remove( );
                                        delete d;
                                        d = null;
                                    }
                                });
                                inf.append($.fn.creatTag('p', {'text': 'Доступно:'}));
                                el.append(inf);
                                el.append($.fn.twoEditableLists(defVal,<?= Json::encode( array_keys( DependTables::structBas() ) ) ?>, function (e, ui) {
                                    if ($(this).index( ))
                                        return;
                                    var vl = [];
                                    $.each($(this).parent( ).children(':first-child').children( ), function ( ) {
                                        console.log(this);
                                        vl[vl.length] = $(this).text( );
                                    });
                                    console.log('index: ' + $(this).index( ), vl);
                                    input.val(JSON.stringify(vl));
                                }));
                                el.append(input);
                                this.oldVal['Tablestypes-structlist'] = defVal;
                                console.log(JSON.parse(input.val( )));
                            }, 'Структура'],
                        struct: ['string', 'Структура', null, null, 'hide']
                    }
                },
            });
        }
        if (!d.tablesControllerMaterialTypes('isOpen'))
            d.tablesControllerMaterialTypes('open');
    });
    if ($('#rekvizit').length) {
        $('#rekvizit').rekvizitOpen({
            listUrl: '<?= Url::to( ['/rekvizit/list'] ) ?>',
            saveFileUrl: '<?= Url::to( ['/rekvizit/save'] ) ?>',
            removeUrl: '<?= Url::to( ['/rekvizit/remove'] ) ?>',
            getfileUrl: '<?= Url::to( ['/rekvizit/getfile'] ) ?>',
            sendUrl: '<?= Url::to( ['/rekvizit/send'] ) ?>',
        });
    }
    //trebov
    if ($('#trebov').length) {
        $('#trebov').rekvizitOpen({
            listUrl: '<?= Url::to( ['/rekvizit/list'] ) ?>',
            saveFileUrl: '<?= Url::to( ['/rekvizit/save'] ) ?>',
            removeUrl: '<?= Url::to( ['/rekvizit/remove'] ) ?>',
            getfileUrl: '<?= Url::to( ['/rekvizit/getfile'] ) ?>',
            sendUrl: '<?= Url::to( ['/rekvizit/send'] ) ?>',
            addTabUrl: '<?= Url::to( ['/rekvizit/addtab'] ) ?>',
            removeTabUrl: '<?= Url::to( ['/rekvizit/removetab'] ) ?>',
            fNamePerfix: 'trebov',
            title: 'Тех. Требования',
        });
    }
    if ($('#trebov').length) {
        $('#trebov').rekvizitOpen({
            listUrl: '<?= Url::to( ['/rekvizit/list'] ) ?>',
            saveFileUrl: '<?= Url::to( ['/rekvizit/save'] ) ?>',
            removeUrl: '<?= Url::to( ['/rekvizit/remove'] ) ?>',
            getfileUrl: '<?= Url::to( ['/rekvizit/getfile'] ) ?>',
            sendUrl: '<?= Url::to( ['/rekvizit/send'] ) ?>',
            addTabUrl: '<?= Url::to( ['/rekvizit/addtab'] ) ?>',
            removeTabUrl: '<?= Url::to( ['/rekvizit/removetab'] ) ?>',
            fNamePerfix: 'trebov',
            title: 'Тех. Требования',
        });
    }
    if ($('#zametki').length) {
        $('#zametki').zametkiOpen({
            listUrl: '<?= Url::to( ['/zametki/list'] ) ?>',
            saveFileUrl: '<?= Url::to( ['/zametki/save'] ) ?>',
            removeUrl: '<?= Url::to( ['/zametki/remove'] ) ?>',
            getfileUrl: '<?= Url::to( ['/zametki/getfile'] ) ?>',
            //sendUrl: '<?= Url::to( ['/rekvizit/send'] ) ?>',
            addTabUrl: '<?= Url::to( ['/zametki/addtab'] ) ?>',
            removeTabUrl: '<?= Url::to( ['/zametki/removetab'] ) ?>',
            renameTabUrl: '<?= Url::to( ['/zametki/renametab'] ) ?>',
            renameZametkaUrl: '<?= Url::to( ['/zametki/renametab'] ) ?>',
            uploadMCIUrl: '<?= Url::to( ['/mceimage/save'] ) ?>',
            fNamePerfix: 'zametki',
            title: 'Заметки',
        });
    }

</script>
<?php JSRegister::end(); ?>

<table class="table a-main-table">
    <tr>
        <td>
            <ul class="a-list">
                <?php
                if ( Yii::$app->user->identity->can( ['/zakaz/zakazlist/index',
                            '/zakaz/zakazlist/list'] ) ):
                    ?><li><?=
                        Html::a( Html::tag( 'div', '', [
                                    'class' => 'main-button m-btn-zakaz'] ), Url::to( [
                                    '/zakaz/zakazlist/index'] ) )
                        ?></li><?php endif; ?>
                <?php
                if ( Yii::$app->user->identity->can( [
                            'zakaz/zakaz/addedit',
                            'zakaz/zakaz/getfullmaterialinfo',
                            'zakaz/zakaz/fileupload',
                            'zakaz/zakaz/preaparefiletoreomove',
                            'zakaz/zakaz/publishfile',
                            'zakaz/zakaz/getfile',
                            'zakaz/zakaz/getstored',
                            'zakaz/zakaz/removestored',
                            'zakaz/zakaz/storetemp',
                            'zakaz/zakaz/customerlist',
                            'zakaz/zakaz/smalltablelist',
                            'zakaz/zakaz/cansel',
                            'zakaz/zakaz/getpodinfo',
                            'zakaz/zakaz/getcustomermanager',
                            'zakaz/zakaz/liffcutter',
                            'zakaz/zakaz/copy',
                            'zakaz/zakaz/validate',
                        ] ) ):
                    ?><li><?=
                            Html::a( Html::tag( 'div', '', ['class' => 'main-button m-btn-zakaz-nov'] ), '#', [
                                'id' => 'zakazAddNew'] )
                            ?></li><?php endif; ?>
                <?php
                if ( Yii::$app->user->identity->can( [
                            'zakaz/zakazlist/dirtindex',
                            'zakaz/zakazlist/listdirt',
                            'zakaz/zakazlist/setsizesdirt',
                            'zakaz/zakazlist/getavailablecolumnsdirt',
                            'zakaz/zakazlist/setcolumnsdirt'
                        ] ) ):
                    ?><li><?=
                            Html::a( Html::tag( 'div', '', ['class' => 'm-btn-chernovik main-button'] ), Url::to( [
                                        '/zakaz/zakazlist/dirtindex'] ) )
                            ?></li><?php endif; ?>
                <?php if ( Yii::$app->user->identity->can( '/admin/firms/index' ) ): ?><li><?=
                        Html::a( Html::tag( 'div', '', [
                                    'class' => 'm-btn-zakazchik main-button'] ), Url::to( [
                                    '/admin/firms/index', 'firmclassname' => 'Zak'] ) )
                        ?></li><?php endif; ?>
                <?php if ( Yii::$app->user->identity->can( '/admin/firms/index' ) ): ?><li><?=
                        Html::a( Html::tag( 'div', '', [
                                    'class' => 'm-btn-podriadchik main-button'] ), Url::to( [
                                    '/admin/firms/index',
                                    'firmclassname' => 'Pod'] ) )
                        ?></li><?php endif; ?>
                <?php if ( Yii::$app->user->identity->can( '/admin/firms/index' ) ): ?><li><?=
                        Html::a( Html::tag( 'div', '', [
                                    'class' => 'm-btn-postavshik main-button'] ), Url::to( [
                                    '/admin/firms/index', 'firmclassname' => 'Post'] ) )
                        ?></li><?php endif; ?>

            </ul>
        </td>
        <td>
            <ul class="a-list">
                <?php if ( Yii::$app->user->identity->can( '/tables/list' ) ): ?>
                    <li><?=
                        Html::a( Html::tag( 'div', '', ['class' => 'm-btn-vidrabot main-button'] ), '#', [
                            'id' => 'requestWorkTypes'] )
                        ?></li>
                    <li><?=
                        Html::a( Html::tag( 'div', '', ['class' => 'm-btn-postpechat main-button'] ), '#', [
                            'id' => 'requestPostPrint'] )
                        ?></li>
                    <li><?=
                        Html::a( Html::tag( 'div', '', ['class' => 'm-btn-produkcia main-button'] ), '#', [
                            'id' => 'requestProdaction'] )
                        ?></li>
                <?php endif; ?>
                <li><?=
                    Html::a( Html::tag( 'div', '', ['class' => 'm-btn-material main-button'] ), '#', [
                        'id' => 'requestMaterials'] )
                    ?></li>
                <?php
                if ( Yii::$app->user->identity->can( [
                            'materialtable/index',
                            'materialtable/list',
                        ] ) ):
                    ?><li><?=
                            Html::a( Html::tag( 'div', '', ['class' => 'm-btn-material-cena main-button'] ), '#', [
                                'id' => 'requestMaterialsTable'] )
                            ?></li><?php endif; ?>
            </ul>
        </td>
        <td class="pic1" align="right" style="background-image: url('<?= $this->context->publishPic1 ?>')"></td>
        <td><?= $this->context->pic2; ?></td>
    </tr>
    <tr>
        <td colspan="4">
            <ul class="a-list a-list-inline">
                <?php
                if ( Yii::$app->user->identity->can( [
                            '/rekvizit/list',
                            '/rekvizit/send'
                        ] ) && $this->context->action->id === 'index' ):
                    ?><li><?=
                        Html::a( Html::tag( 'div', '', ['class' => 'm-btn-material-rekvizit main-button'] ), '#', [
                            'id' => 'rekvizit'] )
                        ?></li><?php endif; ?>
                <?php
                if ( Yii::$app->user->identity->can( [
                            '/rekvizit/list',
                            '/rekvizit/send'
                        ] ) && $this->context->action->id === 'index' ):
                    ?><li><?=
                            Html::a( Html::tag( 'div', '', ['class' => 'm-btn-material-tex-trebov main-button'] ), '#', [
                                'id' => 'trebov'] )
                            ?></li><?php endif; ?>
                <li><?=
                    Html::a( Html::tag( 'div', '', ['class' => 'm-btn-material-zametki main-button'] ), '#', [
                        'id' => 'zametki'] )
                    ?></li>
            </ul>
        </td>
    </tr>
</table>

<?php if ((!defined('YII_DEBUG') || YII_DEBUG) && Yii::$app->user->identity && Yii::$app->user->identity->id<3):?>
<small ><a href="<?= Yii::$app->urlManager->createUrl( ['site/test'] ) ?>">Тесты</a></small>
<?php endif;?>  