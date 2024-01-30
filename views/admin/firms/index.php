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
use yii\helpers\ArrayHelper;
use app\models\tables\WorkOrproductType;
use app\models\tables\Worktypes;

$this->title = $title;
$this->renderFile( '@app/views/layouts/zakaz-wrap.php' );
$firmclassnamelow = strtolower( $firmclassname );
?>
<?php
JSRegister::begin( [
    'key'      => 'ZakazListInit',
    'position' => \yii\web\View::POS_READY
] );
?>
<script>
    var ajaxlist_zakone = "<?= Url::to( ['/admin/mainpage/ajaxlist_zakone'] ) ?>";
    $('#zakaz-list').firmList({
        bigLoaderPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader.gif' )[1] ?>",
        requestUrl: "<?= Url::to( ['/admin/firms/list'] ) ?>",
        setsizesUrl: "<?= Url::to( ['/admin/firms/setsizes'] ) ?>",
        getAvailableColumnsUrl: "<?= Url::to( ['/admin/firms/getavailablecolumns'] ) ?>",
        setColumnsUrl: "<?= Url::to( ['/admin/firms/setcolumns'] ) ?>",
//        getOneRawUrl:"<?= Url::to( ['/zakaz/zakazlist/getoneraw'] ) ?>",
        canEditStage: false,
        changeRowUrl: false,
        removeRowUrl: false,
        viewRowUrl: false,
        canEditZakaz: false,
        canEditOtherOrder: false,
        otherRequestOptions: {firmclassname: "<?= $firmclassname ?>"},
        userId: "<?= Yii::$app->user->identity->id ?>",
        editForm: {
            firm_id: '#curfirm_id',
            baseActionName: '-' + "<?= $firmclassname ?>",
            headerText: "<?= $buttonText ?>",
            requestUrl: "<?= Url::to( ['/admin/mainpage/ajaxlist_' . strtolower( $firmclassname )] ) ?>",
            loadPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader-hor.gif' )[1] ?>",
            pointPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/w-point.gif' )[1] ?>",
            loadPjaxPicUrl: "<?= Yii::$app->assetManager->publish( '@app/web/pic/loader-hor.gif' )[1] ?>",
            ajaxlist_zakone: ajaxlist_zakone,
            form_Id: "#addFirmForm",
            requestUpdateParent: function () {
                $('#zakaz-list').firmList('update');
            },
            width: '26cm',
            pjaxId: {
                '#ZakFirmListPjax': {
                    //rekvizitzak-rekvizit_id
                    firm_id: '#curfirm_id',
                    form_Id: '#addRekvizit',
                    onTabValidate: true,
                    id_associated_column: '#rekvizit' + "<?= $firmclassnamelow ?>" + '-firm_id',
                    id_column_with_record_id: '#rekvizit' + "<?= $firmclassnamelow ?>" + '-rekvizit_id',
                    requestUrl: ajaxlist_zakone,
                    headerText: 'Заказчик',
                    width: '26cm',
                    baseActionName: '-Rekvizit' + "<?= $firmclassname ?>",
                    bankSearchUrl: "<?= Url::to( ['/banks/searchbank'] ) ?>",
                    'address': '#rekvizit' + "<?= $firmclassnamelow ?>" + '-address',
                    ks: '#rekvizit' + "<?= $firmclassnamelow ?>" + '-correspondentaccount',
                    okpo: '#rekvizit' + "<?= $firmclassnamelow ?>" + '-okpo',
                    bank: '#rekvizit' + "<?= $firmclassnamelow ?>" + '-bank',
                    classN: "<?= $firmclassname ?>",
                    onChildReady: function () {
                        $('[name*=form]').change(function () {
                            var f = $(this).parent().parent().parent().parent();
                            var c = f.find('.passport-info');
                            var i = f.find('.passport-info').find('input,select');
                            if ($(this).val() == '0') {
                                c.css('opacity', 0.2);
                                i.attr('disabled', '');
                            } else {
                                c.removeAttr('style');
                                i.removeAttr('disabled');
                            }
                        });
                    }
                },
                '#ZakAddressListPjax': {
                    //rekvizitzak-rekvizit_id
                    firm_id: '#curfirm_id',
                    form_Id: '#addAddress',
                    onTabValidate: true,
                    id_associated_column: '#address' + "<?= $firmclassnamelow ?>" + '-firm_id',
                    id_column_with_record_id: '#address' + "<?= $firmclassnamelow ?>" + '-address_id',
                    requestUrl: ajaxlist_zakone,
                    headerText: 'Адрес',
                    width: '15cm',
                    baseActionName: '-Address' + "<?= $firmclassname ?>" + '-address',
                    classN: "<?= $firmclassname ?>",
                },
                '#ZakContactListPjax': {
                    //rekvizitzak-rekvizit_id
                    firm_id: '#curfirm_id',
                    form_Id: '#addContact',
                    onTabValidate: true,
                    id_associated_column: '#contact' + "<?= $firmclassnamelow ?>" + '-firm_id',
                    id_column_with_record_id: '#contact' + "<?= $firmclassnamelow ?>" + '-contactzak_id',
                    requestUrl: ajaxlist_zakone,
                    headerText: 'Контакт',
                    width: '15cm',
                    baseActionName: '-Contact' + "<?= $firmclassname ?>" + '-contact',
                    classN: "<?= $firmclassname ?>",
                },
                //ZakMangersListPjax
                '#ZakMangersListPjax': {
                    //rekvizitzak-rekvizit_id
                    firm_id: '#curfirm_id',
                    form_Id: '#addContact',
                    onTabValidate: true,
                    id_associated_column: '#manager' + "<?= $firmclassnamelow ?>" + '-firm_id',
                    id_column_with_record_id: '#manager' + "<?= $firmclassnamelow ?>" + '-managerzak_id',
                    requestUrl: ajaxlist_zakone,
                    headerText: 'Менеджер',
                    width: '15cm',
                    baseActionName: '-Manager' + "<?= $firmclassname ?>" + '-manager',
                    classN: "<?= $firmclassname ?>",
                },
                '#shippingListPjax': {
                    //rekvizitzak-rekvizit_id
                    firm_id: '#curfirm_id',
                    form_Id: '#addShipping',
                    onTabValidate: true,
                    id_associated_column: '#shipping' + "<?= $firmclassnamelow ?>" + '-firm_id',
                    id_column_with_record_id: '#shipping' + "<?= $firmclassnamelow ?>" + '-shippingid',
                    requestUrl: ajaxlist_zakone,
                    headerText: 'Доставка',
                    width: '15cm',
                    baseActionName: '-Shipping' + "<?= $firmclassname ?>" + '-shipping',
                    classN: "<?= $firmclassname ?>",
                },
                '#WOPListPjax': {
                    //rekvizitzak-rekvizit_id
                    firm_id: '#curfirm_id',
                    form_Id: '#addWOP',
                    onTabValidate: true,
                    id_associated_column: '#wop' + "<?= $firmclassnamelow ?>" + '-firm_id',
                    id_column_with_record_id: '#wop' + "<?= $firmclassnamelow ?>" + '-wopid',
                    requestUrl: ajaxlist_zakone,
                    headerText: 'Вид деятельности',
                    width: '15cm',
                    baseActionName: '-WOP' + "<?= $firmclassname ?>" + '-wop',
                    classN: "<?= $firmclassname ?>",
                },
            }
        }
    });
    $('#zakazAddNew').firm_addchange({
        simpleForm: {
            form_Id: '#addFirmForm',
            requestUrl: "<?= Url::to( ['/admin/mainpage/ajaxlist_' . strtolower( $firmclassname )] ) ?>",
            width: '10cm',
            requestUpdateParent: function () {
                $('#zakaz-list').firmList('update');
            }
        },
        headerText: "<?= $buttonText ?>",
        baseActionName: '-' + "<?= $firmclassname ?>",
        classN: "<?= $firmclassname ?>",
        onChildReady: null,

    });
</script>
<?php JSRegister::end(); ?>
<div class="zakaz-list-cont">
    <div class="zakaz-list-table">
        <div id="zakaz-list" class="resize-table">
            <div class="resize-caption">
                <?php if ( $firmclassname === 'Pod' || $firmclassname === 'Post' ): ?>
                    <div class="c-group">
                        <span>
                            <?= $captionText ?>
                        </span>
                        <span><?= $firmclassname === 'Pod' ? 'Выберите вид работы' : 'Выберите вид материала' ?></span>
                        <?=
                        Html::dropDownList( 's-WOP', null, ArrayHelper::merge( [
                                    'Не выбран'], ArrayHelper::map( ($firmclassname === 'Pod' ? Worktypes::find() : WorkOrproductType::find() )->orderBy( 'name' )->asArray()->all(), 'id', 'name' ) ), [
                            'id' => 'select-WOP'
                        ] )
                        ?>
                    </div>
                <?php else: ?>
                    <?= $captionText ?>
                <?php endif; ?>
                <div class="conrol" style="transform: scale(0.7);"><?=
                    Html::a( 'Новый ' . $buttonText, '#', ['class' => 'btn btn-main btn-xs hidden-print',
                        'id'    => 'zakazAddNew', 'style' => [
                            'background-size' => 'cover',
                            'width'           => '154px',
                            'height'          => '47px',
                            'padding-top'     => '15px',
                            'font-size'       => '0.95em'
                ]] )
                    ?></div>

            </div>
            <div class="resize-header"></div>
            <div class="resize-tbody"></div>
            <div class="resize-tfooter"></div>
        </div>
    </div>
</div>