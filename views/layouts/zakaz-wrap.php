<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Url;
use app\widgets\JSRegister;
use yii\helpers\Json;
use app\models\tables\Productions;

$dId = isset($dId) ? $dId : 'zakaz_dialog';
$selector = '#' . $dId;
JSRegister::begin([
    'key'      => 'setupInit-Zakaz',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    var ajaxlist_zakone = "<?= Url::to(['/admin/mainpage/ajaxlist_zakone']) ?>";
    window.storedZClick = function( ){
    console.log( this );
    $.post( "<?= Url::to(['/zakaz/zakaz/getstored']) ?>", {
    tmpName:$( this ).attr( 'data-tmpname' )
    } ).done( function( answ ){
    console.log( answ );
    var d = $( "<?= $selector ?>" );
    if ( !d.length ){
    d = $.fn.creatTag( 'div', {
    id:"<?= $dId ?>"
    } );
    $( 'body' ).append( d );
    d.zakazAddEditController( $.extend( {}, def_opt, {
    z_id:answ.zakaz?answ.zakaz.id:null,
            restore:answ.zakaz?answ.zakaz.content:null,
            tempFileList:answ.files?answ.files:null
    } ) );
    }
    if ( !d.zakazAddEditController( 'isOpen' ) )
            d.zakazAddEditController( 'open' );
    } );
    };
    window.storedZContextMenu = function( e ){
    var el = $( this );
    e.preventDefault( );
    new dropDown( {
    posX:e.pageX,
            posY:e.pageY,
            items:[
            {
            label:'Открыть',
                    click:function( ){
                    storedZClick.call( el, e );
                    }
            },
            {
            label:'Удалить',
                    click:function( e ){
                    console.log( 'remove' );
                    $.post( "<?= Url::to(['/zakaz/zakaz/removestored']) ?>", {
                    tmpName:el.attr( 'data-tmpname' )
                    } ).done( function( answ ){
                    console.log( answ );
                    if ( answ.menu && answ.label ){
                    $( '.nav-mess-main' ).children( '.dropdown-menu' ).empty( ).html( $( answ.menu ).html( ) );
                    $( '.nav-mess-main' ).children( '.dropdown-toggle' ).empty( ).html( answ.label + '<b class="caret"></b>' );
                    $( '.stored-z' )
                            .click( window.storedZClick )
                            .contextmenu( window.storedZContextMenu );
                    $.fn.enablePopover( );
                    }
                    if ( answ.mess ){
                    $.fn.dropInfo( answ.mess );
                    }
                    } );
                    }
            }
            ],
    } );
    };
    window.storedZRemoveAll = function( ){
    var el = $( this );
    console.log( 'remove-all' );
    m_alert( 'Внимание', 'Удалить все не сохранённые заказы?', function( ){
    $.post( "<?= Url::to(['/zakaz/zakaz/removestored']) ?>", {
    tmpName:el.attr( 'data-tmpname' )
    } ).done( function( answ ){
    console.log( answ );
    if ( answ.menu && answ.label ){
    $( '.nav-mess-main' ).children( '.dropdown-menu' ).empty( ).html( $( answ.menu ).html( ) );
    $( '.nav-mess-main' ).children( '.dropdown-toggle' ).empty( ).html( answ.label + '<b class="caret"></b>' );
    $( '.stored-z' )
            .click( window.storedZClick )
            .contextmenu( window.storedZContextMenu );
    $.fn.enablePopover( );
    }
    if ( answ.mess ){
    $.fn.dropInfo( answ.mess );
    }
    } );
    }, true );
    };
    window.dialogSelector = "<?= $selector ?>";
    window.dialogSelectorBase = "<?= $dId ?>";
    window.subSmallTableOPt = {
    bodyBackgrounColor:'#66c2d1',
            loadPicUrl:"<?= Yii::$app->assetManager->publish('@app/web/pic/loader-hor.gif')[1] ?>",
            requestUrl:"<?= Yii::$app->urlManager->createUrl(['tables/list']) ?>",
            validateUrl:"<?= Yii::$app->urlManager->createUrl(['tables/validate']) ?>",
            form:{
            requestUrl:"<?= Yii::$app->urlManager->createUrl(['tables/addedit']) ?>",
                    removeUrl:"<?= Yii::$app->urlManager->createUrl(['tables/remove']) ?>",
                    fields:{
                    id:['prim'],
                            name:['string', 'Название'],
                            category:['integer', 'Категория', null, {0:'А', 1:'Б', 2:'В', 3:'Д'}],
                    }
            }
    };
    window.def_opt = {
<?php if (Yii::$app->user->identity->can('/tables/list')): ?>
        requestWorkTypes:$.extend( {}, window.subSmallTableOPt, {
        title:'Вид работ',
                formName:"Worktypes",
        } ),
                requestProdaction:$.extend( {}, window.subSmallTableOPt, {
                title:'Продукция',
                        formName:"Productions",
                        form:{
                        requestUrl:"<?= Yii::$app->urlManager->createUrl(['tables/addedit']) ?>",
                                removeUrl:"<?= Yii::$app->urlManager->createUrl(['tables/remove']) ?>",
                                fields:{
                                id:['prim'],
                                        name:['string', 'Название'],
                                        category:['integer', 'Технушка', null, {0:'Листовая', 1:'Пакеты п/э', 2:'Сувенирка', 3:'Уф-лак', 4:'Пустая',5:'Термоподъём'}],
                                        category2:['checkList', 'Калькулятор', null,<?=
    Json::encode(\yii\helpers\ArrayHelper::merge([
                0 => 'Все'], Productions::category2values))
    ?>]
                                }
                        }
                } ),
<?php endif; ?>
    customerListUrl:"<?= Url::to(['/zakaz/zakaz/customerlist']) ?>",
            customerManageDetailUrl:"<?= Url::to(['/zakaz/zakaz/getcustomermanager']) ?>",
            bigLoaderPicUrl:"<?= Yii::$app->assetManager->publish('@app/web/pic/loader.gif')[1] ?>",
            requestUrl:"<?= Url::to(['/zakaz/zakaz/addedit']) ?>",
            requestDraftUrl:"<?= Url::to(['/zakaz/zakaz/savedraft']) ?>",
            validateUrl:"<?= Url::to(['/zakaz/zakaz/validate']) ?>",
            smallTableListUrl:"<?= Url::to(['/zakaz/zakaz/smalltablelist']) ?>",
            materialInfoUrl:"<?= Url::to(['/zakaz/zakaz/getfullmaterialinfo']) ?>",
            getpostrmanager:"<?= Url::to(['/zakaz/zakaz/getpostrmanager']) ?>",
            getpodinfo:"<?= Url::to(['/zakaz/zakaz/getpodinfo']) ?>",
            fileuploadurl:"<?= Url::to(['/zakaz/zakaz/fileupload']) ?>",
            canselUrl:"<?= Url::to(['/zakaz/zakaz/cansel']) ?>",
            getFileUrl:"<?= Url::to(['/zakaz/zakaz/getfile']) ?>",
            toRemoveUrl:"<?= Url::to(['/zakaz/zakaz/preaparefiletoreomove']) ?>",
            toTempStoreUrl:"<?=
Yii::$app->user->identity->can('/zakaz/zakaz/storetemp') ? Url::to([
            '/zakaz/zakaz/storetemp']) : ''
?>",
            publishfileFileUrl:"<?= Url::to(['/zakaz/zakaz/publishfile']) ?>",
            liffCutterUrl:"<?= Url::to(['/zakaz/zakaz/liffcutter']) ?>",
            paketCutterUrl:"<?= Url::to(['/zakaz/zakaz/paketcutter']) ?>",
            getonematinfo:"<?= Url::to(['/zakaz/zakaz/getonematerinfo']) ?>",
            toResetMaterialUrl:"<?= Url::to(['/zakaz/zakaz/materialreset']) ?>",
            z_id:null,
            sendTmpTimeOut:"<?= Yii::$app->params['sendTmpTimeOut'] ?>",
            storedZClick:window.storedZClick,
            storedZContextMenu:window.storedZContextMenu,
            storedZRemoveAll:window.storedZRemoveAll,
            mailFileUrl:"<?=
Yii::$app->user->identity->can('/zakaz/zakaz/mailfile') ? Url::to([
            '/zakaz/zakaz/mailfile']) : ''
?>",
            mailGetZkazchikEmails:"<?=
Yii::$app->user->identity->can('/zakaz/zakaz/mailGetZkazchikEmails') ? Url::to([
            '/zakaz/zakaz/mailGetZkazchikEmails']) : ''
?>",
            postPrintMenu:<?=
\yii\helpers\Json::encode(\app\models\tables\Postprint::find()->select([
            'name as label',
            'id'
        ])->orderBy('name')->asArray()->all())
?>,
            isAdmin:<?= Yii::$app->user->identity->role === 'admin' ? 'true' : 'false' ?>,
            materials:{
            loadPicUrl:"<?= Yii::$app->assetManager->publish('@app/web/pic/loader-hor.gif')[1] ?>",
                    bigLoaderPicUrl:"<?= Yii::$app->assetManager->publish('@app/web/pic/loader.gif')[1] ?>",
                    materialRequestListUrl:"<?=
Yii::$app->urlManager->createUrl([
    'material/list'])
?>",
                    materialTablesListUrl:"<?=
Yii::$app->urlManager->createUrl([
    'material/subtableslist'])
?>",
                    suppliersRequestUrl:"<?=
Yii::$app->urlManager->createUrl([
    'material/getsuppliers'])
?>",
                    subtablegetlistUrl:"<?=
Yii::$app->urlManager->createUrl([
    'material/subtablegetlist'])
?>",
                    subtabladdeditUrl:"<?= Yii::$app->urlManager->createUrl(['material/subtabladdedit']) ?>",
                    removeSubTUrl:"<?= Yii::$app->urlManager->createUrl(['material/subtableremove']) ?>",
                    referenceColumnName:"<?= \app\models\tables\DependTables::$reference ?>",
                    subTableDDListUrl:"<?= Yii::$app->urlManager->createUrl(['material/subtablegetallnamesfordd']) ?>",
            },
            form:{
            name:'Zakaz',
                    fields:{
                    dateofadmission:'01.03.2017'
                    }
            },
            close:function( ){
            $( "<?= $selector ?>" ).zakazAddEditController( 'destroy' );
            $( "<?= $selector ?>" ).remove( );
            },
            addCustomerOptions:{
            simpleForm:{
            form_Id:'#addFirmForm',
                    requestUrl:"<?= Url::to(['/admin/mainpage/ajaxlist_zak']) ?>",
                    width:'14cm',
//                header:'Заказчик'
            },
                    headerText:"Клиент",
                    baseActionName:'-Zak',
                    classN:"Zak",
                    onChildReady:null,
            },
            editForm:{
            firm_id:'#curfirm_id',
                    baseActionName:'-Zak',
                    requestUrl:"<?= Url::to(['/admin/mainpage/ajaxlist_zak']) ?>",
                    loadPicUrl:"<?= Yii::$app->assetManager->publish('@app/web/pic/loader-hor.gif')[1] ?>",
                    pointPicUrl:"<?= Yii::$app->assetManager->publish('@app/web/pic/w-point.gif')[1] ?>",
                    loadPjaxPicUrl:"<?= Yii::$app->assetManager->publish('@app/web/pic/loader-hor.gif')[1] ?>",
                    ajaxlist_zakone:ajaxlist_zakone,
                    form_Id:"#addFirmForm",
                    requestUpdateParent:function( ){
                    //$('#zakaz-list').firmList('update');
                    },
                    width:'26cm',
                    pjaxId:{
                    '#ZakFirmListPjax':{
                    //rekvizitzak-rekvizit_id
                    firm_id:'#curfirm_id',
                            form_Id:'#addRekvizit',
                            onTabValidate:true,
                            id_associated_column:'#rekvizitzak-firm_id',
                            id_column_with_record_id:'#rekvizitzak-rekvizit_id',
                            requestUrl:ajaxlist_zakone,
                            headerText:'Заказчик',
                            width:'26cm',
                            baseActionName:'-RekvizitZak',
                            bankSearchUrl:"<?= Url::to(['/banks/searchbank']) ?>",
                            'address':'#rekvizitzak-address',
                            ks:'#rekvizitzak-correspondentaccount',
                            okpo:'#rekvizitzak-okpo',
                            bank:'#rekvizitZak-bank',
                            classN:"Zak",
                            onChildReady:function( ){
                            $( '[name*=form]' ).change( function( ){
                            var f = $( this ).parent( ).parent( ).parent( ).parent( );
                            var c = f.find( '.passport-info' );
                            var i = f.find( '.passport-info' ).find( 'input,select' );
                            if ( $( this ).val( ) == '0' ){
                            c.css( 'opacity', 0.2 );
                            i.attr( 'disabled', '' );
                            } else{
                            c.removeAttr( 'style' );
                            i.removeAttr( 'disabled' );
                            }
                            } );
                            }
                    },
                            '#ZakAddressListPjax':{
                            //rekvizitzak-rekvizit_id
                            firm_id:'#curfirm_id',
                                    form_Id:'#addAddress',
                                    onTabValidate:true,
                                    id_associated_column:'#addresszak-firm_id',
                                    id_column_with_record_id:'#addresszak-address_id',
                                    requestUrl:ajaxlist_zakone,
                                    headerText:'Адрес',
                                    width:'15cm',
                                    baseActionName:'-AddressZak-address',
                                    classN:"Zak",
                            },
                            '#ZakContactListPjax':{
                            //rekvizitzak-rekvizit_id
                            firm_id:'#curfirm_id',
                                    form_Id:'#addContact',
                                    onTabValidate:true,
                                    id_associated_column:'#contactzak-firm_id',
                                    id_column_with_record_id:'#contactzak-contactzak_id',
                                    requestUrl:ajaxlist_zakone,
                                    headerText:'Контакт',
                                    width:'15cm',
                                    baseActionName:'-ContactZak-contact',
                                    classN:"Zak",
                            },
                            //ZakMangersListPjax
                            '#ZakMangersListPjax':{
                            //rekvizitzak-rekvizit_id
                            firm_id:'#curfirm_id',
                                    form_Id:'#addContact',
                                    onTabValidate:true,
                                    id_associated_column:'#managerzak-firm_id',
                                    id_column_with_record_id:'#managerzak-managerzak_id',
                                    requestUrl:ajaxlist_zakone,
                                    headerText:'Менеджер',
                                    width:'15cm',
                                    baseActionName:'-ManagerZak-manager',
                                    classN:"Zak",
                            },
                            '#shippingListPjax':{
                            //rekvizitzak-rekvizit_id
                            firm_id:'#curfirm_id',
                                    form_Id:'#addShipping',
                                    onTabValidate:true,
                                    id_associated_column:'#shippingzak-firm_id',
                                    id_column_with_record_id:'#shippingzak-shippingid',
                                    requestUrl:ajaxlist_zakone,
                                    headerText:'Доставка',
                                    width:'15cm',
                                    baseActionName:'-ShippingZak-shipping',
                                    classN:"Zak",
                            },
                            '#WOPListPjax':{
                            //rekvizitzak-rekvizit_id
                            firm_id:'#curfirm_id',
                                    form_Id:'#addWOP',
                                    onTabValidate:true,
                                    id_associated_column:'#wopzak-firm_id',
                                    id_column_with_record_id:'#wopzak-wopid',
                                    requestUrl:ajaxlist_zakone,
                                    headerText:'Вид деятельности',
                                    width:'15cm',
                                    baseActionName:'-WOPZak-wop',
                                    classN:"Zak",
                            },
                    }
            }

    };
    $( '.stored-z' )
            .click( window.storedZClick )
            .contextmenu( window.storedZContextMenu );
    $( '.stored-z-remove-all' ).click( window.storedZRemoveAll );

</script>
<?php JSRegister::end(); ?>