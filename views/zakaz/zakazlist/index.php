<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of index
 *
 * @author Александр
 */
use app\widgets\JSRegister;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\zakaz\Zakaz;
use yii\helpers\Json;

/* @var $this yii\web\View */

$this->title = 'Заказы';
$this->renderFile( '@app/views/layouts/zakaz-wrap.php' );
?>
<?php
JSRegister::begin( [
    'key'      => 'ZakazListInit',
    'position' => \yii\web\View::POS_READY
] );
?>
<script>
    var options = {
        bigLoaderPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader.gif' )[1] ?>",
        requestUrl: "<?= Url::to( ['/zakaz/zakazlist/list'] ) ?>",
        setsizesUrl: "<?= Url::to( ['/zakaz/zakazlist/setsizes'] ) ?>",
        getAvailableColumnsUrl: "<?= Url::to( ['/zakaz/zakazlist/getavailablecolumns'] ) ?>",
        setColumnsUrl: "<?= Url::to( ['/zakaz/zakazlist/setcolumns'] ) ?>",
        getOneRawUrl: "<?= Url::to( ['/zakaz/zakazlist/getoneraw'] ) ?>",
        canEditStage: "<?= (bool) \Yii::$app->user->identity->can( 'zakaz/zakazlist/changerow' ) ?>",
        changeRowUrl: "<?=
(bool) \Yii::$app->user->identity->can( 'zakaz/zakazlist/changerow' ) ? Url::to( [
            '/zakaz/zakazlist/changerow'] ) : null
?>",
        removeRowUrl: "<?=
(bool) \Yii::$app->user->identity->can( '/zakaz/zakaz/removezakaz' ) ? Url::to( [
            '/zakaz/zakaz/removezakaz'] ) : null
?>",
        viewRowUrl: "<?=
(bool) \Yii::$app->user->identity->can( '/zakaz/zakazlist/view' ) ? Url::to( [
            '/zakaz/zakazlist/view'] ) : null
?>",
        canEditZakaz: "<?= (bool) \Yii::$app->user->identity->can( '/zakaz/zakaz/addedit' ) ?>",
        canEditOtherOrder: "<?= (bool) \Yii::$app->user->identity->can( '/zakaz/zakazlist/editanyorder' ) ?>",
        stageLevels:<?= Json::encode( Zakaz::$_stage ) ?>,
        userId: "<?= Yii::$app->user->identity->id ?>",
        copyRowUrl: "<?= Url::to( ['/zakaz/zakaz/copy'] ) ?>",
        publishfileFileUrl: "<?= Url::to( ['/zakaz/zakaz/publishfile'] ) ?>",
        addCustomerOptions: {
            simpleForm: {
                form_Id: '#addFirmForm',
                requestUrl: "<?= Url::to( ['/admin/mainpage/ajaxlist_zak'] ) ?>",
                width: '14cm',
                requestUpdateParent: function () {
                    //$('#zakaz-listM').firmList('update');
                }
            },
            headerText: "test",
            baseActionName: '-Zak',
            classN: "Zak",
            onChildReady: null,
        }
    };
    $('#zakaz-listM').zakazListController(options);
    $('#zakazAddNew').click(function () {
        var d = $(window.dialogSelector);
        if (!d.length) {
            d = $.fn.creatTag('div', {
                id: window.dialogSelectorBase
            });
            $('body').append(d);
            d.zakazAddEditController($.extend({}, def_opt, {
                close: function (dt) {
                    $(window.dialogSelector).zakazAddEditController('destroy');
                    $(window.dialogSelector).remove();
//                    $( '#zakaz-listM' ).zakazListController( 'update' );
                },
                afterSave: function (dt) {
                    console.log('index-after close', this, dt.lastZakaz);
                    $('#zakaz-listM').zakazListController('findZakaz', dt.lastZakaz, true);
                }
            }));
        }
        if (!d.zakazAddEditController('isOpen'))
            d.zakazAddEditController('open');
    });
    let evBind = false;
    $('#findZakaz').click(function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        $(this).children().removeClass('all').addClass('fnd').children(':first-child').trigger('focus');
    });
    $('#findZakaz').children(':first-child').children('.arr')
            .click(function (e) {
                let el = $(this).parent().children(':first-child');
                if (el.val().length && el.val() !== '0') {
                    $('#zakaz-listM').zakazListController('findZakaz', parseInt($(el).val()));
                }
            })
            .mouseenter(function () {
                let el = $(this).parent().children(':first-child');
                if (el.val().length && el.val() !== '0') {
                    $(this).addClass('rot');
                }
            })
            .mouseleave(function () {
                $(this).removeClass('rot');
            })
    $('#findZakaz').children().children(':first-child')
            .focusin(function (e) {
                if (!evBind) {
                    let el = this;
                    $('body').on('click', function (e) {
                        console.log('body click');
                        evBind = false;
                        $(el).parent().removeClass('fnd').addClass('all');
                        $('body').off('click');
                    });
                    evBind = true;
                }
            })
            .keypress(function (e) {
                //$('#zakaz-listM').zakazListController
                if (e.keyCode === 13) {
                    $('#zakaz-listM').zakazListController('findZakaz', parseInt($(this).val()));
                    $(this).blur();
                }
            })
            .onlyNumeric()
            .selectAllOnFocus();
</script>
<?php JSRegister::end(); ?>
<div class="zakaz-list-cont hidden-print">
    <!--<div class="header"><h3><?= $this->title ?></h3></div>-->

    <div class="zakaz-list-table">
        <div id="zakaz-listM" class="resize-table">
            <div class="resize-caption"><div class="table-header"><h3>Заказы</h3></div><?php
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
                    ?><div class="conrol"><?=
                    Html::a( 'Добавить', '#', ['class' => 'btn btn-main btn-xs hidden-print',
                        'id'    => 'zakazAddNew'] )
                    ?></div>
                <?php endif; ?>
                <div class="re-print"><?=
                    Html::a( Html::img( '/pic/button_main_page/j_perepechatka_' . ($showReprint ? '1' : '2') . '.png', [
                                'width'        => '100px',
                                'height'       => '30px',
                                'data-nexturl' => '/pic/button_main_page/j_perepechatka_' . ($showReprint ? '2' : '1') . '.png'
                            ] ), '#', [
                        'id'           => 'show-reprint',
                        'data-reprint' => $showReprint ? 'true' : 'false'
                    ] )
                    ?></div>
                <div class="find-zakaz" id="findZakaz"><div class="all"><input type="text"/><div class="arr"></div></div></div>
            </div>
            <div class="resize-header"></div>
            <div class="resize-tbody"></div>
            <div class="resize-tfooter"></div>
        </div>
    </div>
</div>