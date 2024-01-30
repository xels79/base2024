/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
( function ( $ ) {
    $.widget( "custom.setupController", $.custom.baseW, {
        getWidgetName: function () {
            return 'setupController'
        },
        files: [ ],
        initPicForm: function () {
            //let controller=this;
            this.dialog = $( '#setup-ourfirm' ).multiTabController( 'getDialog' );
            let form = $( '#ourFirmPics' );
            if ( form.length ) {
                form.find( '[role=openImgRekvizit]' ).click( function () {
                    $( this ).next().click();
                } );
                form.find( '[type=file]' ).change( function () {
                    let reader = new FileReader();
                    let img = $( this ).parent().find( 'img' );
                    $( this ).parent().find( '>input[type=hidden]' ).remove();
                    reader.onload = function ( e ) {
                        console.debug( 'our_firm:[type=file]change()', 'Предпросмотр готов' );
                        img.attr( 'src', e.target.result );
                        img.next().css( 'visibility', 'visible' );
                        img.css( 'visibility', 'visible' );
                    };
                    reader.readAsDataURL( this.files[0] );
                } );
                form.submit( {controller: this}, function ( e ) {
                    let controller = e.data.controller;
                    e.preventDefault();
                    let fd = new FormData( form.get( 0 ) );
                    let Id = $( '#ourfirm_id' );
                    console.debug( controller.createMessageText( 'submit:data' ), fd );
                    console.debug( controller.createMessageText( 'submit:url' ), controller.options.requestUrl );
                    if ( Id.length ) {
                        fd.append( 'req[name]', 'changeourfirm' );
                        fd.append( 'id', Id.val() )
                        fd.append( 'OurFirm[firm_id]', Id.val() );
                        controller.busy.call( controller, true );
                        controller.ajax( fd );
                    }
                } );
                this.options.afterSubmit = function ( controller, data ) {
                    console.debug( controller.createMessageText( 'afterSubmit:data' ), data );
                    if ( data.status != "saved" ) {
                        form.parent().parent().empty().append( $( data.html ) );
                        controller.initPicForm();
                    } else {
                        new m_alert( 'Успех', 'Настройки сайта изменены!<br>Рекомендуем обновить страницу.', {
                            label: 'Обновить?',
                            click: function () {
                                location.reload();
                            }
                        } );
                    }
                    return false;
                }
            }
        },
        _managerCheckPayment_id: function () {
            $( '#managersour-normal,#managersour-piecework,#managersour-wages,#managersour-recycling_rate' ).removeAttr( 'readonly' ).removeClass('disabled');
            switch ( $( '#managersour-payment_id' ).val() ) {
                case '0':
                    $( '#managersour-wages' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    $( '#managersour-piecework' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    break;
                case '1':
                case '2':
                    $( '#managersour-normal' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    $( '#managersour-recycling_rate' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    $( '#managersour-piecework' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    break;
                case '3':
                    $( '#managersour-normal' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    $( '#managersour-recycling_rate' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    $( '#managersour-wages' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    break;
                case '4':
                    $( '#managersour-normal' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    $( '#managersour-recycling_rate' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    $( '#managersour-wages' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    $( '#managersour-piecework' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                    $( '#managersour-haspercents' ).val( 1 ).trigger( 'change' );
                    break;
            }
        },
        _managerReady: function ( controller ) {
            $( '#managersour-haspercents' ).change( function () {
                if ( $( this ).val() === '1' ) {
                    $( '#managersour-ourfirm_profit,#managersour-profit,#managersour-superprofit,#managersour-material_profit' ).removeClass('disabled').removeAttr( 'readonly' );
                } else {
                    $( '#managersour-ourfirm_profit,#managersour-profit,#managersour-superprofit,#managersour-material_profit' ).addClass('disabled').attr( 'readonly', true ).val( 0 );
                }
            } );
            if ( controller.options.workTypeTitles.length ) {
                $( '#managersour-payment_id' ).tooltip( {
                    title: function () {
                        return controller.options.workTypeTitles[$( this ).val()];
                    }
                } );
                $( '#managersour-payment_id' ).change( function () {
                    $( '#managersour-payment_id' ).tooltip( 'hide' );
                    $( '#managersour-payment_id' ).tooltip( {
                        title: function () {
                            return controller.options.workTypeTitles[$( this ).val()];
                        }
                    } );
                    controller._managerCheckPayment_id.call( controller );
                } );
            }
            controller._managerCheckPayment_id.call( controller );
        },
        _create: function () {
            this.fields = {
                requestList: 'Не задан адрес запроса для UserList',
                requestFirmList: 'Не задан адрес запоса ourFirmList',
                userChangeUrl: 'Не задан адрес запроса для UserChange',
                userRemoveUrl: 'Не задан адрес запроса для UserRemove',
                accssecChangeUrl: 'Не задан адрес запроса для accssecChangeUrl',
                bankSearchUrl: {message: 'Url для поиска банка не задан!', 'default': false},
                loadPicUrl: {message: 'Картинка загрузки не задана!', 'default': ''},
                workTypeTitles: {message: 'Url для поиска банка не задан!', 'default': [ ]},
            };
            let controller = this;
            if ( this._super() === true ) {
                controller.options.requestUrl = controller.options.requestFirmList;
                this.options.form_Id = '#ourFirmPics';
                this.options.firm_id = '#ourfirm_id';
                this.options.baseActionName = 'ourfirm';
                $( '#setup-ourfirm' ).multiTabController( {
                    firm_id: '#ourfirm_id',
                    requestUrl: controller.options.requestFirmList,
                    headerText: 'Карта нашей фирмы',
                    loadPicUrl: controller.options.loadPicUrl,
                    pointPicUrl: controller.options.pointPicUrl,
                    pjaxId: {
                        '#ourFirmListPjax': {
                            requestUrl: controller.options.requestFirmList,
                            baseActionName: 'reqvizit',
                            id_associated_column: '#rekvizitour-firm_id',
                            id_column_with_record_id: '#rekvizitour-rekvizit_id',
                            form_Id: '#addOurFirm',
                            headerText: 'Реквизиты нашей фирмы',
                            onTabValidate: true,
                            bankSearchUrl: controller.options.bankSearchUrl,
                            'address': '#rekvizitour-address',
                            ks: '#rekvizitour-correspondentaccount',
                            okpo: '#rekvizitour-okpo',
                            bank: '#rekvizitour-bank'
                        },
                        '#ourAddressListPjax': {
                            requestUrl: controller.options.requestFirmList,
                            baseActionName: 'address',
                            id_associated_column: '#addressour-firm_id',
                            id_column_with_record_id: '#addressour-address_id',
                            form_Id: '#addOurAddress',
                            headerText: 'Адрес',
                            width: '15cm'
                        },
                        '#ourMangersListPjax': {
                            requestUrl: controller.options.requestFirmList,
                            baseActionName: 'maneger',
                            id_associated_column: '#managersour-firm_id',
                            id_column_with_record_id: '#managersour-managerour_id',
                            form_Id: '#addOurManager',
                            headerText: 'Менеджер',
                            requestParam: {manager: true},
                            onChildReady: function () {
                                controller._managerReady.call( this, controller );
                            }
                        },
                        '#ourEmployeeListPjax': {
                            requestUrl: controller.options.requestFirmList,
                            baseActionName: 'maneger',
                            id_associated_column: '#managersour-firm_id',
                            id_column_with_record_id: '#managersour-managerour_id',
                            form_Id: '#addOurManager',
                            headerText: 'Сотрудник',
                            onChildReady: function () {
                                controller._managerReady.call( this, controller );
                            }
                        }
                    },
                    afterSubmit: function ( controller, data ) {
                        console.log( controller, data )
                        if ( data.status === 'ok' ) {
                            controller.dialog.remove();
                            new m_alert( 'Внимание', 'Наша фирма успешно добавлена', 'Закрыть', false );
                        }
                    },
                    afterInit: function ( controllerSub ) {
                        if ( $( '#addOurFirm' ).length ) {
                            $( '#addOurFirm' ).submit( function ( e ) {
                                e.preventDefault();
                                let fd = new FormData( $( this ).get( 0 ) );
                                controllerSub.ajax( fd );
                                return false;
                            } );
                            return false;
                        } else {
                            controller.initPicForm();
                            controller.initRemovePicKeys();
                            return true;
                        }
                    }
                } );
                $( '#setup-user' ).setupUser( {
                    title: 'Пользователи',
                    requestList: controller.options.requestList,
                    userChangeUrl: controller.options.userChangeUrl,
                    userRemoveUrl: controller.options.userRemoveUrl,
                    accssecChangeUrl: controller.options.accssecChangeUrl,
                } );
            }
        },
    } );
}( jQuery ) );
