/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
( function ( $ ) {
    $.widget( "custom.multiTabController", $.custom.baseW, {
        getWidgetName: function () {
            return 'multiTabController'
        },
        dialog: null,
        isInit: false,
        fullRestart: function () {
            this._reInit();
        },
        _reInit: function () {
            if ( this.isInit )
                this.element.unbind( 'click' );
            this.isInit = true;
            let self = this;
            console.log( this.element );
            if ( this.element[0].tagName !== 'DIV' ) {
                this.element.click( {self: this}, function ( e ) {
                    let d = $( '<div>' ).appendTo( 'body' );
                    d.maindialog( {
                        requestList: self.options.requestUrl,
                        contentBackgroundColor: self.options.contentBackgroundColor, //'#66c2d1',
                        bodyBackgrounColor: self.options.bodyBackgrounColor, //'#66c2d1',
                        headerText: self.options.headerText,
                        width: self.options.width,
                        modal: true,
                        beforeClose: function () {
                            d.remove();
                        },
                        afterInit: function ( dialog ) {
                            if ( $.isFunction( self.options.afterInit ) )
                                if ( self.options.afterInit.call( this, self, dialog ) === false )
                                    return;
                            $( '.nav-tabs >li >a' ).click( function ( e ) {
                                if ( !$( this ).parent().hasClass( 'disabled' ) ) {
                                    e.preventDefault();
                                    $( this ).tab( 'show' );
                                }
                            } ).on( 'shown.bs.tab', function ( e ) {
                                let dialog = $( this ).parent().parent().parent().parent().parent().parent();
                                dialog.css( 'top', $( window ).height() / 2 - dialog.height() / 2 );
                            } );
                            $.each( self.options.pjaxId, function ( key, val ) {
                                $( key ).tabController( $.extend( val, {
                                    firm_id: self.options.firm_id,
                                    loadPicUrl: self.options.loadPicUrl,
                                    pointPicUrl: self.options.pointPicUrl
                                } ) );
                            } );
                        }

//                        style:{
//                            width:self.options.width,
//                        },
                    } );
                    d.maindialog( 'open' );
                    e.data.self.dialog = d;
                } );
            } else {
                this.dialog = this.element.parent();
                if ( $.isFunction( self.options.afterInit ) )
                    if ( self.options.afterInit.call( this, self, dialog ) === false )
                        return;
                this.element.find( '.nav-tabs >li >a' ).click( function ( e ) {
                    if ( !$( this ).parent().hasClass( 'disabled' ) ) {
                        console.log();
                        e.preventDefault();
                        $( this ).tab( 'show' );
                        self.dialog.css( 'height', 'auto' );
                    }
                } ).on( 'shown.bs.tab', function ( e ) {
                    let dialog = $( this ).parent().parent().parent().parent().parent().parent();
                    dialog.css( 'top', $( window ).height() / 2 - dialog.height() / 2 );
                } );
                let par = $.fn.findParentByClass( self.element, 'm-modal-content' );
                if ( par )
                    par.css( 'background-color', self.options.contentBackgroundColor );
                $.each( self.options.pjaxId, function ( key, val ) {
                    $( key ).tabController( $.extend( {
                        firm_id: self.options.firm_id,
                        loadPicUrl: self.options.loadPicUrl,
                        pointPicUrl: self.options.pointPicUrl,
                        loadPjaxPicUrl: self.options.loadPjaxPicUrl,
                        modal: true
                    }, val ) );
                } );

            }

        },
        _create: function () {
            console.log( this.getWidgetName(), this );
            this.fields = {
                'firm_id': {message: 'Не заданн идентификатор поля с идентификатором записи родителя', 'default': false},
                pjaxId: 'Не заданн массив идентификатор обектов PJAX (key:options)',
                requestUrl: 'Не заданн URL запроса',
                afterInit: {message: 'Функция не задана!', 'default': null},
                headerText: {message: 'Заголовок не заданн!', 'default': ''},
                width: {message: 'Размер не задан используем стандартный!', 'default': '26.36cm'},
                contentBackgroundColor: {message: 'Цвет контента по умолчанию', default: '#66c2d1'},
                bodyBackgrounColor: {message: 'Цвет тела по умолчанию', default: '#66c2d1'}
            };
            if ( this._super() === true ) {
                this._reInit();
            }
        },
    } );
}( jQuery ) );

( function ( $ ) {
    $.widget( "custom.tabController", $.custom.baseW, {
        getWidgetName: function () {
            return 'tabController'
        },
        dialog: null,
        _pjaxReInit: function () {
            let self = this;
            this.element.unbind( 'pjax:timeout' );
            this.element.unbind( 'pjax:end' );
            let urlS = '';//self.options.requestUrl;
            if ( $( self.options.firm_id ).length )
                urlS += '&id=' + $( self.options.firm_id ).val();
            this.element.pjax( 'a', this.id, {
                push: false,
                timeout: 2000,
                type: 'POST',
                container: '#' + this.element.attr( 'id' )
                        //url:urlS
            } );
            this.element.on( 'pjax:timeout', function ( event ) {
                event.preventDefault();
                let a = new m_alert( 'Ошибка зарузки', 'Превышено время ожидания.' );
            } );
            this.element.find( 'a[data-page]' ).unbind( 'click' );
            this.element.find( 'a[data-page]' ).click( function ( e ) {
                e.preventDefault();
                console.log( self.element );
                console.log( urlS );
                let tmpUrlS = urlS;
                if ( $( this ).attr( 'classn' ) )
                    tmpUrlS = tmpUrlS + '&classN=' + $( this ).attr( 'classn' );
                self.lastPjaxUrl = $( this ).attr( 'href' ) + tmpUrlS;
                $.pjax.reload( '#' + $( self.element ).attr( 'id' ), {
                    push: false,
                    replace: false,
                    replaceRedirect: false,
                    timeout: 2000,
                    type: 'POST',
                    url: self.lastPjaxUrl
                } );
            } );
            //loadPjaxPicUrl
            this.element.on( 'pjax:beforeSend', function () {
                let par = $.fn.findParentByClass( self.element, 'm_modal-body' );//self.element.parent().parent().parent().parent().parent();
                console.log( par );
                if ( par ) {
                    let ban = par.find( '.bunner' );
                    if ( !ban.length ) {
                        ban = $.fn.creatTag( 'div', {
                            'class': 'bunner',
                            style: {
                                'background-color': self.options.contentBackgroundColor
                            }
                        } );
                        if ( self.options.loadPjaxPicUrl ) {
                            ban.append( $.fn.creatTag( 'img', {
                                src: self.options.loadPjaxPicUrl
                            } ) );
                        } else {
                            ban.append( $.fn.creatTag( 'h3', {
                                'class': 'text-info',
                                style: {
                                    'text-align': 'center'
                                },
                                text: 'Загрузка...'
                            } ) );

                        }
                        par.append( ban );
                    }
                }
            } );
            this.element.on( 'pjax:end', function () {
                let par = $.fn.findParentByClass( self.element, 'm_modal-body' );//self.element.parent().parent().parent().parent().parent();
                console.log( par );
                if ( par ) {
                    let ban = par.find( '.bunner' );
                    if ( ban.length ) {
                        ban.remove();
                    }
                }
                self._reInit();
            } );

        },
        _reInit: function () {
            console.debug( 'tabController:reInit', this.element.find( '[role=add]' ), this );
            this.element.find( '[role=add]' ).unbind( 'click' );
            this.element.find( '[role=add]' ).click( {self: this}, this._addClick );
            this.element.find( '[data-key]' ).contextmenu( {self: this}, this._rightClick );
            this._pjaxReInit();
            let dialog = this.element.parent().parent().parent().parent().parent().parent();
            dialog.css( 'top', $( window ).height() / 2 - dialog.height() / 2 );
        },
        _rightClick: function ( mainEvent ) {
            mainEvent.preventDefault();
            mainEvent.stopPropagation();
            let self = mainEvent.data.self;
            let items = [ ];
            let id = $( mainEvent.currentTarget ).attr( 'data-key' );
            let isMain = $( mainEvent.currentTarget ).attr( 'data-ismain' ) ? true : false;
            let msgTxt = $( mainEvent.currentTarget ).attr( 'data-message' );
            if ( msgTxt === '' )
                msgTxt = id;
            if ( !msgTxt )
                msgTxt = $( mainEvent.currentTarget ).children( ':first-child' ).text();
            console.groupCollapsed( self.createMessageText( 'rightClick' ), mainEvent );
            console.debug( self.createMessageText( self.id + ' [data-key]:view:id', id ) );
            console.log( mainEvent.toElement ? {tag: mainEvent.toElement.tagName, text: $( mainEvent.toElement ).text()} : 'нет noElement' );
            if ( $.type( self.options.contextMenuKeys.view ) === 'string' ) {
                items[items.length] = {
                    label: self.options.contextMenuKeys.view,
                    click: function ( e ) {
                        console.debug( self.createMessageText( self.id + ' [data-key]:view' ) );
                        self._addClick( mainEvent, {
                            'req': {
                                name: 'view' + self.options.baseActionName
                            },
                            id: id
                        } );
                    }
                }
            }
            ;
            if ( $.type( self.options.contextMenuKeys.change ) === 'string' ) {
                items[items.length] = {
                    label: self.options.contextMenuKeys.change,
                    click: function ( e ) {
                        console.debug( self.createMessageText( self.id + ' [data-key]:change' ) );
                        self._addClick( mainEvent, {
                            'req': {
                                name: 'change' + self.options.baseActionName
                            },
                            id: id
                        } );
                    }
                }
            }
            ;
            if ( isMain ) {
                items[items.length] = {
                    label: 'Параметры оплаты',
                    click: function ( e ) {
                        console.debug( self.createMessageText( self.id + ' [data-key]:change' ) );
                        self._addClick( mainEvent, {
                            'req': {
                                name: 'change-main' + self.options.baseActionName
                            },
                            id: id
                        } );
                    }
                }

            }
            if ( $.type( self.options.contextMenuKeys.remove ) === 'string' ) {
                items[items.length] = {
                    label: self.options.contextMenuKeys.remove,
                    click: function ( e ) {
                        console.debug( self.createMessageText( self.id + ' [data-key]:remove' ) );
                        console.debug( self.createMessageText( self.id + ' [data-key]:remove' ), self );
                        new m_alert( 'Внимание', 'Удалить запись №' + msgTxt + ' ?', function ( e ) {
                            console.log( self.options.requestUrl, 'remove' + self.options.baseActionName );
                            $.post( self.options.requestUrl, {
                                'req': {
                                    name: 'remove' + ( !isMain ? self.options.baseActionName : '' )
                                },
                                id: id
                            } ).done( function ( data ) {
                                console.log( data );
                                let url = self.options.requestUrl + self.options.classN;
                                if ( $( self.options.firm_id ).length )
                                    url += '&id=' + $( self.options.firm_id ).val();
                                if ( self.lastPjaxUrl )
                                    url = self.lastPjaxUrl;
                                $.pjax.reload( self.id, {
                                    push: false,
                                    replace: false,
                                    timeout: 2000,
                                    type: 'POST',
                                    url: url
                                } );
                            } ).always( function () {
                                console.groupEnd();
                            } ).fail( function ( answ ) {
                                console.error( answ );
                            } );
                        } );
                    }
                }
            }
            ;
            if ( mainEvent.toElement && $( mainEvent.toElement ).text().length ) {
                items[items.length] = 'separator';
                items[items.length] = {
                    label: 'Скопировать текст',
                    click: function () {
                        let $tmp = $( '<input type="text" style="z-index:90000">' );
                        $( mainEvent.toElement ).parent().append( $tmp );
                        $tmp.val( $( mainEvent.toElement ).text() );
//                        console.log( $tmp.val().length );
                        $tmp.focus();
                        $tmp.get( 0 ).select();
                        document.execCommand( "copy" );
                        $tmp.remove();
                    }
                };
            }
            new dropDown( {
                posX: mainEvent.clientX,
                posY: mainEvent.clientY,
                items: items,
                beforeClose: function () {
                    console.groupEnd();
                }
            } );
        },
        _addClick: function ( e, action ) {
            let self = e.data.self;
            if ( $.type( action ) !== 'object' )
                action = {'req': {
                        name: 'add' + self.options.baseActionName
                    }};
            if ( $.type( action.req ) !== 'object' )
                action.req = {};
            $.each( self.options.requestParam, function ( key, val ) {
                action.req[key] = val;
            } );
            console.groupCollapsed( self.createMessageText( 'addClick' ) );
            let header = self.options.headerText;
            if ( $.type( self.options.actionNames ) === 'object' ) {
                $.each( self.options.actionNames, function ( key, val ) {
                    if ( action.req.name.indexOf( key ) === 0 ) {
                        header += ' ' + val;
                        return false;
                    }
                } );
            }
            let options = {
                headerText: header,
                firm_id: self.options.firm_id,
                id_associated_column: self.options.id_associated_column,
                id_column_with_record_id: self.options.id_column_with_record_id,
                form_Id: self.options.form_Id,
                baseActionName: self.options.baseActionName,
                controller: self,
                requestUrl: self.options.requestUrl,
                loadPicUrl: self.options.loadPicUrl,
                width: self.options.width,
                requestOtherParam: action,
                loadPicUrl: self.options.loadPicUrl,
                pointPicUrl: self.options.pointPicUrl,
                onTabValidate: self.options.onTabValidate,
                'address': self.options.address,
                ks: self.options.ks,
                okpo: self.options.okpo,
                bank: self.options.bank,
                modal: true,
                contentBackgroundColor: self.options.contentBackgroundColor,
                bodyBackgrounColor: self.options.bodyBackgrounColor,
                beforeClose: function () {
                    console.groupEnd();
                },
//                afterInit: $.isFunction( self.options.afterInit ) ? self.options.afterInit : function () {
//                    console.log( 'tabController: children afterInit not set' );
//                },
                requestUpdateParent: function () {
                    let url = self.options.requestUrl + self.options.classN;
                    ;
                    let uri = new URI( self.options.requestUrl + self.options.classN );
                    let page = self.element.find( '[name=current_page]' );
                    let tmpUrl = '';
                    let tmpDt = {req: {}};
//                if (self.options.baseActionName){
//                    let tmp=self.options.baseActionName.split('-');
//                    let classN='';
//                    console.log(tmp);
//                    for (let i=1;i<tmp[1].length&&classN==='';i++){
//                        if (tmp[1].charCodeAt(i)<91) classN=tmp[1].substring(i);
//                        //console.log(charCodeAt(i));
//                    }
//                    console.log (tmp[1],classN);
//                    if (classN!==''){
//                        tmpUrl+='&'+'reqq[name]'+'=change-'+classN;
//                        tmpDt.req.name='change-'+classN
//                    }else{
//                        tmpUrl+='&'+'reqq[name]'+'=change'+self.options.baseActionName;
//                        tmpDt.req.name='change'+self.options.baseActionName
//                    }
//                    if ($(self.options.firm_id).length){
//                        tmpUrl+='&'+'reqq[id]='+$(self.options.firm_id).val();
//                        tmpDt.req.id=$(self.options.firm_id).val();
//                    }
//
//                }
                    if ( $( self.options.firm_id ).length ) {
                        tmpUrl += '&id=' + $( self.options.firm_id ).val();
                        tmpDt.id = $( self.options.firm_id ).val();
                    }
                    if ( $( self.options.firm_id ).length ) {
                        url += '&id=' + $( self.options.firm_id ).val();
                    }
                    //'req[name]','change'+self.options.baseActionName
//                url=url+encodeURI(tmpUrl);
                    if ( self.lastPjaxUrl )
                        url = self.lastPjaxUrl;
                    console.log( 'tmpDt', tmpDt );
                    console.log( 'update', url );
                    console.log( self.options.requestUrl + self.options.classN + '&' + URI.buildQuery( tmpDt, true ) );
                    $.pjax.reload( self.id, {
                        push: false,
                        replace: false,
                        timeout: 2000,
                        type: 'POST',
                        url: url
                    } );
                }
            };
            if ( self.options.loadPjaxPicUrl )
                options.loadPjaxPicUrl = self.options.loadPjaxPicUrl
            if ( $.isFunction( self.options.onChildReady ) )
                options.afterInit = self.options.onChildReady;
            if ( self.options.bankSearchUrl )
                options.bankSearchUrl = self.options.bankSearchUrl;
            if ( e.data )
                if ( e.data.post ) {
                    options.requestOtherParam = e.data.post;
                }
            console.debug( self.createMessageText( 'addClick.options' ), options );
            //new simple_form(options);
            $.custom.simpleForm( options );
        },
        _create: function () {
            this.fields = {
                'firm_id': {message: 'Не заданн идентификатор поля с идентификатором записи родителя', 'default': false},
                id_associated_column: {message: 'Не заданн идентификатор связонного поля формы', 'default': false},
                id_column_with_record_id: {message: 'Не заданн идентификатор колонки формы с id записи', 'default': false},
                requestUrl: 'Не заданн URL запроса',
                baseActionName: 'Не заданно базовое имя действия',
                form_Id: 'Не заданн идентификатор формы',
                bankSearchUrl: {message: 'Url для поиска банка не задан!', 'default': false},
                onTabValidate: {message: 'Заголовок не заданн!', 'default': false},
                requestParam: {message: 'Дополнительные параметры запроса не заданны', 'default': {}}, //Параметры которые будут добавлены в req[]
                onChildReady: {message: 'Функция не задана', default: false},
                contextMenuKeys: {message: 'Контекстное меню по умолчанию', 'default': {
                        view: 'Просмотр',
                        change: 'Изменить',
                        remove: 'Удалить'
                    }},
                headerText: {message: 'Заголовок не заданн!', 'default': ''},
                width: {message: 'Размер не задан используем стандартный!', 'default': '26.36cm'},
                actionNames: {message: 'Название действий', 'default': {
                        add: 'добавить',
                        change: 'изменить',
                        view: 'просмотр'
                    }},
                'address': {message: 'Идентификатор адреса не задан ', 'default': false},
                ks: {message: 'Идентификатор кор.счет не задан ', 'default': false},
                okpo: {message: 'Идентификатор ОКПО не задан ', 'default': false},
                bank: {message: 'Идентификатор Банк не задан ', 'default': false},
                contentBackgroundColor: {message: 'Цвет контента по умолчанию', default: '#c4c7c7'},
                bodyBackgrounColor: {message: 'Цвет тела по умолчанию', default: '#c4c7c7'},
                classN: {message: 'Имя класса не переданно!', 'default': ''},

            }
            if ( this._super() ) {
                this.id = '#' + this.element.attr( 'id' );
                if ( this.options.classN !== '' ) {
                    this.options.classN = '&classN=' + this.options.classN;
                }
                this._reInit();
                this._pjaxReInit()
            }
        },
    } );
}( jQuery ) );
