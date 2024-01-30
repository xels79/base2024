/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var indexController = function ( ajaxlist_zak, ajaxlist_zakone, ajaxlist_pod,
        ajaxlist_zakpod, ajaxlist_zakpost,
        bankSearchUrl, pointPicUrl,
        loadPicUrl, loadPjaxPicUrl ) {
    $( '#requestCustomer' ).multiTabController( {
        requestUrl: ajaxlist_zak,
        headerText: 'Заказчики',
        width: '13cm',
        loadPicUrl: loadPicUrl,
        pointPicUrl: pointPicUrl,
        bodyBackgrounColor: '#fff',
        pjaxId: {
            '#customerListPjax': {
//                firm_id:'#curfirm_id',
                requestUrl: ajaxlist_zak,
                headerText: 'Заказчик',
                width: '20cm',
                form_Id: '#addFirmForm',
                baseActionName: '-Zak',
                contentBackgroundColor: '#66c2d1',
                onChildReady: function ( e ) {
                    console.log( 'childReady', this.dialog.body );
                    $( this.dialog.body ).multiTabController( {
                        requestUrl: ajaxlist_zakone,
                        headerText: 'Заказчики',
                        pointPicUrl: pointPicUrl,
                        loadPicUrl: loadPicUrl,
                        contentBackgroundColor: '#c4c7c7',
                        bodyBackgrounColor: '#fff',
                        firm_id: '#curfirm_id',
                        pjaxId: {
                            '#ZakFirmListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addRekvizit',
                                onTabValidate: true,
                                id_associated_column: '#rekvizitzak-firm_id',
                                id_column_with_record_id: '#rekvizitzak-rekvizit_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Заказчик',
                                width: '26cm',
                                baseActionName: '-RekvizitZak',
                                bankSearchUrl: bankSearchUrl,
                                'address': '#rekvizitzak-address',
                                ks: '#rekvizitzak-correspondentaccount',
                                okpo: '#rekvizitzak-okpo',
                                bank: '#rekvizitzak-bank',
                                classN: 'Zak',
                                onChildReady: function () {
                                    $( '[name*=form]' ).change( function () {
                                        var f = $( this ).parent().parent().parent().parent();
                                        var c = f.find( '.passport-info' );
                                        var i = f.find( '.passport-info' ).find( 'input,select' );
                                        if ( $( this ).val() == '0' ) {
                                            c.css( 'opacity', 0.2 );
                                            i.attr( 'disabled', '' );
                                        } else {
                                            c.removeAttr( 'style' );
                                            i.removeAttr( 'disabled' );
                                        }
                                    } );
                                }
                            },
                            '#ZakAddressListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addAddress',
                                onTabValidate: true,
                                id_associated_column: '#addresszak-firm_id',
                                id_column_with_record_id: '#addresszak-address_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Адрес',
                                width: '15cm',
                                baseActionName: '-AddressZak-address',
                                classN: 'Zak',
                            },
                            '#ZakContactListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addContact',
                                onTabValidate: true,
                                id_associated_column: '#contactzak-firm_id',
                                id_column_with_record_id: '#contactzak-contactzak_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Контакт',
                                width: '15cm',
                                baseActionName: '-ContactZak-contact',
                                classN: 'Zak',
                            },
                            //ZakMangersListPjax
                            '#ZakMangersListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addContact',
                                onTabValidate: true,
                                id_associated_column: '#managerzak-firm_id',
                                id_column_with_record_id: '#managerzak-managerzak_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Менеджер',
                                width: '15cm',
                                baseActionName: '-ManagerZak-manager',
                                classN: 'Zak',
                            },
                            '#shippingListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addShipping',
                                onTabValidate: true,
                                id_associated_column: '#shippingzak-firm_id',
                                id_column_with_record_id: '#shippingzak-shippingid',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Доставка',
                                width: '15cm',
                                baseActionName: '-ShippingZak-shipping',
                                classN: 'Zak',
                            },
                        }
                    } );
                }
            }
        }
    } );

    $( '#requestContractor' ).multiTabController( {
        requestUrl: ajaxlist_pod,
        headerText: 'Подрядчик',
        pointPicUrl: pointPicUrl,
        width: '13cm',
        loadPicUrl: loadPicUrl,
//        contentBackgroundColor:'#66c2d1',
        bodyBackgrounColor: '#fff',
        pjaxId: {
            '#customerListPjax': {
//                firm_id:'#curfirm_id',
                requestUrl: ajaxlist_pod,
                headerText: 'Подрядчик',
                width: '20cm',
                form_Id: '#addFirmForm',
                baseActionName: '-Pod',
                contentBackgroundColor: '#66c2d1',
                onChildReady: function ( e ) {
                    console.log( 'childReady', this.dialog.body );
                    $( this.dialog.body ).multiTabController( {
                        requestUrl: ajaxlist_zakpod,
                        headerText: 'Заказчики',
                        pointPicUrl: pointPicUrl,
                        loadPicUrl: loadPicUrl,
                        contentBackgroundColor: '#c4c7c7',
                        bodyBackgrounColor: '#fff',
                        firm_id: '#curfirm_id',
                        loadPjaxPicUrl: loadPjaxPicUrl,
                        pjaxId: {
                            '#ZakFirmListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addRekvizit',
                                onTabValidate: true,
                                id_associated_column: '#rekvizitpod-firm_id',
                                id_column_with_record_id: '#rekvizitpod-rekvizit_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Заказчик',
                                width: '26cm',
                                baseActionName: '-RekvizitPod',
                                bankSearchUrl: bankSearchUrl,
                                'address': '#rekvizitpod-address',
                                ks: '#rekvizitpod-correspondentaccount',
                                okpo: '#rekvizitpod-okpo',
                                bank: '#rekvizitpod-bank',
                                classN: 'Pod',
                                onChildReady: function () {
                                    $( '[name*=form]' ).change( function () {
                                        var f = $( this ).parent().parent().parent().parent();
                                        var c = f.find( '.passport-info' );
                                        var i = f.find( '.passport-info' ).find( 'input,select' );
                                        if ( $( this ).val() == '0' ) {
                                            c.css( 'opacity', 0.2 );
                                            i.attr( 'disabled', '' );
                                        } else {
                                            c.removeAttr( 'style' );
                                            i.removeAttr( 'disabled' );
                                        }
                                    } );
                                }
                            },
                            '#ZakAddressListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addAddress',
                                onTabValidate: true,
                                id_associated_column: '#addresspod-firm_id',
                                id_column_with_record_id: '#addresspod-address_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Адрес',
                                width: '15cm',
                                baseActionName: '-AddressPod-address',
                                classN: 'Pod'
                            },
                            '#ZakContactListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addContact',
                                onTabValidate: true,
                                id_associated_column: '#contactpod-firm_id',
                                id_column_with_record_id: '#contactpod-contactpod_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Контакт',
                                width: '15cm',
                                baseActionName: '-ContactPod-contact',
                                classN: 'Pod'
                            },
                            //ZakMangersListPjax
                            '#ZakMangersListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addContact',
                                onTabValidate: true,
                                id_associated_column: '#managerpod-firm_id',
                                id_column_with_record_id: '#managerpod-managerpod_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Менеджер',
                                width: '15cm',
                                baseActionName: '-ManagerPod-manager',
                                classN: 'Pod'
                            },
                            '#shippingListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addShipping',
                                onTabValidate: true,
                                id_associated_column: '#shippingpod-firm_id',
                                id_column_with_record_id: '#shippingpod-shippingid',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Доставка',
                                width: '17cm',
                                baseActionName: '-ShippingPod-shippingP',
                                classN: 'Pod'
                            },
                        }
                    } );
                }
            }
        }
    } );
    $( '#requestPost' ).multiTabController( {
        requestUrl: ajaxlist_zakpost,
        headerText: 'Поставщики',
        pointPicUrl: pointPicUrl,
        width: '13cm',
        loadPicUrl: loadPicUrl,
//        contentBackgroundColor:'#66c2d1',
        bodyBackgrounColor: '#fff',
        pjaxId: {
            '#customerListPjax': {
//                firm_id:'#curfirm_id',
                requestUrl: ajaxlist_zakpost,
                headerText: 'Поставщик',
                width: '20cm',
                form_Id: '#addFirmForm',
                baseActionName: '-Post',
                contentBackgroundColor: '#66c2d1',
                onChildReady: function ( e ) {
                    console.log( 'childReady', this.dialog.body );
                    $( this.dialog.body ).multiTabController( {
                        requestUrl: ajaxlist_zakpost,
                        headerText: 'Поставщик',
                        pointPicUrl: pointPicUrl,
                        loadPicUrl: loadPicUrl,
                        contentBackgroundColor: '#c4c7c7',
                        bodyBackgrounColor: '#fff',
                        firm_id: '#curfirm_id',
                        loadPjaxPicUrl: loadPjaxPicUrl,
                        pjaxId: {
                            '#ZakFirmListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addRekvizit',
                                onTabValidate: true,
                                id_associated_column: '#rekvizitpost-firm_id',
                                id_column_with_record_id: '#rekvizitpost-rekvizit_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Заказчик',
                                width: '26cm',
                                baseActionName: '-RekvizitPost',
                                bankSearchUrl: bankSearchUrl,
                                'address': '#rekvizitpost-address',
                                ks: '#rekvizitpost-correspondentaccount',
                                okpo: '#rekvizitpost-okpo',
                                bank: '#rekvizitpost-bank',
                                classN: 'Post',
                                onChildReady: function () {
                                    $( '[name*=form]' ).change( function () {
                                        var f = $( this ).parent().parent().parent().parent();
                                        var c = f.find( '.passport-info' );
                                        var i = f.find( '.passport-info' ).find( 'input,select' );
                                        if ( $( this ).val() == '0' ) {
                                            c.css( 'opacity', 0.2 );
                                            i.attr( 'disabled', '' );
                                        } else {
                                            c.removeAttr( 'style' );
                                            i.removeAttr( 'disabled' );
                                        }
                                    } );
                                }
                            },
                            '#ZakAddressListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addAddress',
                                onTabValidate: true,
                                id_associated_column: '#addresspost-firm_id',
                                id_column_with_record_id: '#addresspost-address_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Адрес',
                                width: '15cm',
                                baseActionName: '-AddressPost-address',
                                classN: 'Post'
                            },
                            '#ZakContactListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addContact',
                                onTabValidate: true,
                                id_associated_column: '#contactpost-firm_id',
                                id_column_with_record_id: '#contactpost-contactpod_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Контакт',
                                width: '15cm',
                                baseActionName: '-ContactPost-contact',
                                classN: 'Post'
                            },
//                            //ZakMangersListPjax
                            '#ZakMangersListPjax': {
                                //rekvizitzak-rekvizit_id
                                firm_id: '#curfirm_id',
                                form_Id: '#addContact',
                                onTabValidate: true,
                                id_associated_column: '#managerpost-firm_id',
                                id_column_with_record_id: '#managerpost-managerpost_id',
                                requestUrl: ajaxlist_zakone,
                                headerText: 'Менеджер',
                                width: '15cm',
                                baseActionName: '-ManagerPost-manager',
                                classN: 'Post'
                            },
//                            '#shippingListPjax':{
//                                //rekvizitzak-rekvizit_id
//                                firm_id:'#curfirm_id',
//                                form_Id:'#addShipping',
//                                onTabValidate:true,
//                                id_associated_column:'#shippingpod-firm_id',
//                                id_column_with_record_id:'#shippingpod-shippingid',
//                                requestUrl:ajaxlist_zakone,
//                                headerText:'Доставка',
//                                width:'17cm',
//                                baseActionName:'-ShippingPod-shippingP',
//                                classN:'Pod'
//                            },
                        }
                    } );
                }
            }
        }
    } );
};