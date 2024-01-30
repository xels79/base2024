/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var _TCDEBUG = false;
var tablesControlle_tIndex = 201;
var bunners = {
    showBunner: function ( ) {
        var b1 = $( '<div>' ).attr( {'class': 'load-banner load-backgr'} ).css( 'background-color', this.element.css( 'background-color' ) );
        var flt = $( '<div>' ).addClass( 'load-floater' );
        //b1.css('background-color',this.element.css('background-color'));
        var b2 = $( '<div>' ).addClass( 'load-banner load-messege' );
        if ( this.options.loadPicUrl === '#' ) {
            b2.append( flt );
            flt.css( 'margin-top', '-22px' );
            b2.append( $( '<h2>' ).text( 'Загрузка' ) );
        } else {
            var img = $( '<img>' ).attr( {src: this.options.loadPicUrl} );
            b2.append( flt );
            b2.append( img );
            img.on( 'load', function ( ) {
                flt.css( 'margin-bottom', '-' + ( img.height( ) / 2 ) + 'px' );
            } );
        }
        this.element.append( b1 );
        this.element.append( b2 );
    },
    removeBunner: function ( ) {
        this.element.children( '.load-banner' ).remove( );
    },
    _scroll: function ( e ) {
        var self = e.data.self;
        var dM = self.element;
        e.preventDefault( );
        var raz = dM.scrollTop( ) + dM.height( ) - dM.children( '.small-table' ).height( );
        if ( self.element.children( 'table' ).length ) {
//                if ( _TCDEBUG ) console.log('v1');
            raz = dM.offset( ).top + dM.innerHeight( ) - self.element.children( 'table' ).children( 'tbody' ).children( ':last-child' ).offset( ).top - self.element.children( 'table' ).children( 'tbody' ).children( ':last-child' ).innerHeight( ) > 0;
        } else {
//                if ( _TCDEBUG ) console.log('v2');
            raz = dM.offset( ).top + dM.innerHeight( ) - dM.children( ':last-child' ).offset( ).top - dM.children( ':last-child' ).innerHeight( ) > 0;
        }
//            if ( _TCDEBUG ) console.log(dM.offset().top+dM.innerHeight()-dM.children(':last-child').offset().top-dM.children(':last-child').innerHeight());

        if ( raz ) {
//                if ( _TCDEBUG ) console.log ('Пора.');
//                if ( _TCDEBUG ) console.log(dM.innerHeight(),dM.children(':last-child').position().top,dM.children(':last-child').innerHeight());
//                if ( _TCDEBUG ) console.log(raz);
//                return;
            //this.options.pager.page*this.options.pager.pageSize<this.count
            //self.options.pager.page<self.count/self.options.pager.pageSize
//                if (self.options.pager.page*self.options.pager.pageSize<self.count){
            if ( self.cnt < self.count ) {
//                    if ( _TCDEBUG ) console.log(self.isBusy);
                self.options.pager.page++;
                var tmp = this;
//                    $(this).unbind('scroll');
                self._checkAndLoad( );
//                    self._doRequestList(true,function(){
//                        $(tmp).scroll({self:self},self._scroll);
//                    });
            }
        }
    }
};
( function ( $ ) {
    $.widget( "custom.tablesControllerBase", $.custom.maindialog, $.extend( {}, {
        options: {
            but: null,
            requestUrl: '#',
            loadPicUrl: '#',
            resizable: true,
            autoOpen: false,
            bodyBackgrounColor: '#fff',
            contentBackgroundColor: '#fff',
            headerBackgroundColor: 'transparent',
            picMode: false,
            onOkClick: null,
            onCloseClick: null,
            height: 650,
            width: 'auto',
            showDefaultButton: false,
            category: null,
            resizeStop: function ( event, ui ) {
                $( this ).css( {
                    width: '100%',
                    //height: ( ui.size.height - $( this ).prev().height() - $( this ).next().height() - $( this ).next().next().next().height() - 7 ) + 'px'
                } );
            },
            show: {effect: "fade", duration: 400},
            hide: {effect: "fade", duration: 300},
        },
        _create: function ( ) {
            let self = this;
            this._super( );
            this.options.modal = this.options.picMode;
            if ( _TCDEBUG ) console.log( 'tablesControllerBase init' );
            this.element.css( 'background-color', this.options.contentBackgroundColor );
//            this.element.css( 'color', 650 );
            if ( this.options.showDefaultButton ) {
                let butt = $( '<div>' ).addClass( 'ui-dialog-buttonset' );
                butt.append( $( '<a>' ).append( $( '<img>' ).attr( {
                    src: 'pic/button_main_page/j_dobaviti_a1.png',
                    height: 30
                } ).mouseenter( function () {
                    $( this ).attr( 'src', 'pic/button_main_page/j_dobaviti_a2.png' );
                } ).mouseleave( function () {
                    $( this ).attr( 'src', 'pic/button_main_page/j_dobaviti_a1.png' );
                } ) ).click( {self: this}, function ( e ) {
                    self._addClick.call( this, e );
                } ) );
                butt.append( $( '<a>' ).append( $( '<img>' ).attr( {
                    src: 'pic/button_main_page/j_zakriti_1.png',
                    height: 30
                } ).mouseenter( function () {
                    $( this ).attr( 'src', 'pic/button_main_page/j_zakriti_2.png' );
                } ).mouseleave( function () {
                    $( this ).attr( 'src', 'pic/button_main_page/j_zakriti_1.png' );
                } ).click( function () {
                    self.close();
                } ) ) );

                this.uiDialog.append( $( '<div>' ).addClass( 'ui-dialog-buttonpane ui-widget-content ui-helper-clearfix' )
                        .css( 'background-color', 'transparent' )
                        .append( butt ) );
            }
        },
        open: function ( ) {
            $.ui.dialog.prototype.open.call( this );
            this.element.empty( );
            this.showBunner( );
            var height = this.uiDialog.height( ) + this.element.prev( ).height( ) - this.element.next( ).height( ) - this.element.next( ).next( ).next( ).height( ) - 7;
            var m = this.element.prev( ).height( ) + this.element.next( ).height( ) + this.element.next( ).next( ).next( ).height( );
            if ( height + m > $( 'body' ).height( ) ) {
                height = $( 'body' ).height( ) - m - 20;
            }
//            this.element.css( {height: height + 'px'} );
            if ( this.uiDialog.position( ).top < 0 )
                this.uiDialog.offset( {top: 0, left: this.uiDialog.position( ).left} );
            if ( this.options.requestUrl != '#' && $.isFunction( this._doRequestList ) ) {
                this._doRequestList( );
            } else {
                this.removeBunner( );
            }
            if ( $.isFunction( this._scroll ) ) {
                this.element.unbind( 'scroll' );
                this.element.scroll( {self: this}, this._scroll );
            }
        },
    }, bunners ) );
}( jQuery ) );
( function ( $ ) {
    $.widget( "custom.formPopUp", $.custom.tablesControllerBase, {
        oldVal: [ ],
//        ddList:{},//Загруженные списки значений для выбора
        options: {
            validateUrl: null,
            resizable: false,
            draggable: true,
            autoOpen: true,
            modal: true,
            closeOnEscape: true,
            formName: null,
            mode: 'add',
            recordId: null,
            width: 470,
            height: 'auto',
            changed: false,
            afterSave: null,
            onAddComplit: null,
            onReady: null,
            hideUnsetedFields: false,
//            ddListRequest:false,//URL Запроса для дропдаун.
            otherParam: {}
        },
        _create: function ( ) {
            $.custom.tablesControllerBase.prototype._create.call( this );
            this.options.modal = true;
            var self = this;
            this.oldVal = [ ];
            if ( $.type( this.options.fields ) !== 'object' )
                self.options.fields = {};
            if ( $.type( this.options.otherParam ) !== 'object' && !$.isFunction( this.options.otherParam ) )
                self.options.otherParam = {};
            if ( _TCDEBUG ) console.log( 'create', this );
        },
        _showErrors: function ( errors ) {
            var self = this;
            $.each( errors, function ( key, val ) {
                var group = $( '#' + self.options.formName + '-' + key ).parent( );
                if ( _TCDEBUG ) console.log( 'error for (' + '#' + self.options.formName + '-' + key + ')', group );
                var p = group.children( 'p:last-child' );
                if ( !p.length ) {
                    p = $( '<p>' ).addClass( 'help-block help-block-error' ).css( {display: 'none'} );
                    group.append( p );
                }
                p.text( val );
                p.css( {display: 'block'} );
                group.addClass( 'has-error' );
            } );
        },
        _saveClick: function ( e ) {
            var self = e.data.self;
            e.preventDefault( );
            var model = {};
            if ( self.options.requestUrl && self.options.formName ) {
                $.each( self.options.fields, function ( key, val ) {
                    if ( val[0] !== 'prim' || self.options.mode !== 'add' ) {
                        model[key] = $( '#' + self.options.formName + '-' + key ).val( );
                    }
                } );
                var data = {formName: self.options.formName};
                data[self.options.formName] = model;
                if ( self.options.mode === 'change' && self.options.recordId !== null )
                    data.id = self.options.recordId;
                let dataToSend=$.isFunction( self.options.otherParam ) ? self.options.otherParam.call( self, data, self.options.mode ) : $.extend( data, self.options.otherParam );
                if ( _TCDEBUG ) console.log('try save',dataToSend);
                $.post( self.options.requestUrl, dataToSend, function ( data ) {
//                    if ( _TCDEBUG ) console.log(data);
                        if (dataToSend.treeId){
                            stones.cacheRuntime.unSetCategory('materialTableList'+dataToSend.treeId);
                        }else{
                            stones.cacheRuntime.clearAll();
                        }
                    if ( data.status === 'ok' ) {
                        self.options.changed = false;
                        if ( $.isFunction( self.options.afterSave ) )
                            self.options.afterSave.call( this, data.item );
                        if ( self.options.mode === 'add' && $.isFunction( self.options.onAddComplit ) )
                            self.options.onAddComplit.call( this, data );
                        self.element.formPopUp( 'close' );
                    } else {
                        if ( $.type( data.errorText ) === 'string' ) {
                            new m_alert( 'Ошибка сервера', data.errorText, true, false );
                        } else {
                            if ( $.type( data.errors ) === 'object' ) {
                                self._showErrors( data.errors );
                            } else {
                                new m_alert( 'Ошибка сервера', 'Неизвестная ошибка', true, false );
                            }
                        }
                    }
                } ).fail( function ( answ ) {
                    new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                    console.error( answ );
                } );
            } else {
                new m_alert( 'Ошибка настройки', 'Не задан requestUrl или formName. Запрос не отправлен.', true, false );
            }
        },
        _renderButtons: function ( ) {
            var btns = $( '<div>' ).addClass( 'btn-group btnset' );
            var sv = $( '<a>' ).addClass( 'btn btn-success' ).text( 'Сохранить' );
            var cn = $( '<a>' ).addClass( 'btn btn-default' ).text( 'Отменить' );
            btns.append( sv );
            btns.append( cn );
            sv.click( {self: this}, this._saveClick );
            cn.click( {self: this}, function ( e ) {
                e.preventDefault( );
                e.data.self.element.formPopUp( 'close' );
            } );
            this.element.children( '.formPopUp-form' ).append( btns );
        },
        _validate: function ( e ) {
            var self = e.data.self;
            var spl = $( this ).attr( 'id' ).split( '-' );
            var group = $( this ).parent( );
            var rqDt = {
                formName: spl[0],
            };
            rqDt[spl[0]] = {};
            rqDt[spl[0]][spl[1]] = $( this ).val( );
            if ( self.options.recordId ) {
                rqDt['id'] = self.options.recordId;
            }
            $.post( self.options.validateUrl, $.isFunction( self.options.otherParam ) ? self.options.otherParam.call( self, rqDt, 'validate' ) : $.extend( rqDt, self.options.otherParam ), function ( data ) {
                var p = group.children( 'p:last-child' );
                if ( data.status === 'ok' ) {
                    if ( p.length ) {
                        p.text( '' );
                        p.css( {display: 'none'} );
                    }
                    group.removeClass( 'has-error' );
                    group.addClass( 'has-success' );
                } else {
                    if ( $.type( data.errorText ) === 'string' ) {
                        new m_alert( 'Ошибка сервера', data.errorText, true, false );
                    } else {
                        if ( $.type( data.errors ) === 'object' ) {
                            self._showErrors( data.errors );
                        } else {
                            new m_alert( 'Ошибка сервера', 'Неизвестная ошибка', true, false );
                        }
                    }
                }
            } ).fail( function ( answ ) {
                new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                console.error( answ );
            } );
        },
        _hasChenged: function ( e ) {
            var answer = false;
            var self = e.data.self;
            $.each( self.oldVal, function ( key, val ) {
                answer = $( '#' + key ).val( ) !== val;
                return !answer;
            } );
            self.options.changed = answer;
            var inf = self.uiDialogTitlebar.children( 'span:first-child' );
            var infT = inf.text( );
            if ( answer ) {
                if ( !infT.length || infT[0] !== '*' )
                    infT = '*' + infT;
            } else {
                if ( infT.length && infT[0] === '*' )
                    infT = infT.substr( 1 );
            }
            inf.text( infT );
        },
        __renderFields: function ( ) {
            var self = this,
                    tabIndex = 0,
                    fcs = null,
                    fm = $( '<div>' ).addClass( 'formPopUp-form' );
            $.each( this.options.fields, function ( key, val ) {
                var lng = val.length;
                var ln = $( '<div>' ).addClass( 'form-group' );
                var grp = null;
                if ( !$.isFunction( val[0] ) && !$.isArray( val[0] ) ) {
                    if ( val[0] !== 'prim' || self.options.mode != 'add' ) {
                        var label = null, control = null, def = null;
                        if ( lng > 2 ) {//Значение по умолчанию
                            if ( val[2] !== null ) {
                                def = val[2];
                            }
                        }
                        if ( lng > 1 ) {
                            if ( val[1] !== null )//Метка
                                label = $( '<label>' ).text( val[1] ).addClass( 'control-label' );
                        }
                        if ( val[0] === 'prim' ) {
                            control = $( '<span>' ).text( self.options.recordId );
                        } else {
                            if ( lng > 3 && val[0] === 'integer' && $.type( val[3] ) === 'object' ) { //Список
                                if ( !val[3].requestUrl ) {
                                    control = $( '<select>' ).attr( {
                                        id: self.options.formName + '-' + key,
                                        'class': 'form-control'
                                    } );
                                    $.each( val[3], function ( keyS, valS ) {
                                        var it = $.fn.creatTag( 'option', {
                                            value: keyS,
                                            text: valS
                                        } );
                                        if ( def !== null && keyS == def ) {
                                            it.attr( 'selected', 'selected' );
                                        }
                                        control.append( it );
                                    } );
                                } else {
                                    control = $.fn.creatTag( 'input', {
                                        //'class':'form-control',
                                        type: 'text',
                                        id: self.options.formName + '-' + key
                                    } );
                                    var zi = parseInt( self.uiDialog.css( 'z-index' ) );
                                    grp = $.fn.creatTag( 'div', {class: 'activeDD'} ).append( control );
                                    control.activeDD( {
//                                        appendTo:self.element,
                                        requestUrl: val[3].requestUrl,
                                        loadPicUrl: val[3].loadPicUrl ? val[3].loadPicUrl : self.options.loadPicUrl,
                                        otherParam: val[3].otherParam ? val[3].otherParam : {},
                                        zindex: zi + 100
                                    } );
                                    //append($.fn.creatTag('div',{class:'activeDD'}).append(this.postavshik))
                                }
                            } else if ( lng > 3 && val[0] === 'checkList' && ( $.type( val[3] ) === 'object' || $.type( val[3] ) === 'array' ) ) {
                                var selItCnt = 0;
                                if ( def )
                                    def = JSON.parse( def );
                                control = $( '<input>' ).attr( {
                                    id: self.options.formName + '-' + key,
                                    type: 'hidden'
                                } );
                                grp = $( '<div>' ).attr( {
                                    'class': 'check-b'
                                } ).append( control );
                                if ( _TCDEBUG ) console.log( def );
                                $.each( val[3], function ( keyS, valS ) {
                                    var isSelected = $.type( def ) === 'object' && $.inArray( ( parseInt( keyS ) - 1 ).toString( ), Object.keys( def ) ) > -1;
                                    if ( isSelected )
                                        selItCnt++;
                                    var it = $( '<span>' ).attr( {
                                        'data-value': keyS,
                                        'data-name': valS,
                                        class: 'glyphicon ' + ( isSelected ? 'glyphicon-check' : 'glyphicon-unchecked' )
                                    } ).click( function ( e ) {
                                        var dt = $( '#' + self.options.formName + '-' + key ).val( ) ? JSON.parse( $( '#' + self.options.formName + '-' + key ).val( ) ) : {};
                                        if ( _TCDEBUG ) console.log( $( this ).attr( 'data-value' ) );
                                        if ( !parseInt( $( this ).attr( 'data-value' ) ) ) {
                                            if ( _TCDEBUG ) console.log( $( this ).parent( ).parent( ).children( 'div' ).length - 1, Object.keys( dt ).length );
                                            if ( $( this ).parent( ).parent( ).children( 'div' ).length - 1 === Object.keys( dt ).length ) {
                                                dt = {};
                                                $( this ).parent( ).parent( ).children( 'div' ).children( 'span' ).removeClass( 'glyphicon-check' ).addClass( 'glyphicon-unchecked' );
                                            } else {
                                                dt = {};
                                                $( this ).parent( ).parent( ).children( 'div' ).children( 'span' ).removeClass( 'glyphicon-unchecked' ).addClass( 'glyphicon-check' );
                                                $( this ).parent( ).parent( ).children( 'div' ).children( 'span' ).each( function ( ) {
                                                    if ( $( this ).attr( 'data-value' ) !== '0' ) {
                                                        if ( _TCDEBUG ) console.log( this );
                                                        dt[parseInt( $( this ).attr( 'data-value' ) ) - 1] = $( this ).attr( 'data-name' );
                                                    }
                                                } );
                                            }
                                        } else {
                                            if ( _TCDEBUG ) console.log( Object.keys( dt ), ( parseInt( $( this ).attr( 'data-value' ) ) - 1 ).toString( ), $.inArray( ( parseInt( $( this ).attr( 'data-value' ) ) - 1 ) + "", Object.keys( dt ) ) );
                                            if ( $.inArray( ( parseInt( $( this ).attr( 'data-value' ) ) - 1 ).toString( ), Object.keys( dt ) ) > -1 ) {
                                                delete dt[parseInt( $( this ).attr( 'data-value' ) ) - 1];
                                                $( this ).removeClass( 'glyphicon-check' ).addClass( 'glyphicon-unchecked' );
                                            } else {
                                                dt[parseInt( $( this ).attr( 'data-value' ) ) - 1] = $( this ).attr( 'data-name' );
                                                $( this ).removeClass( 'glyphicon-unchecked' ).addClass( 'glyphicon-check' );
                                            }
                                        }
                                        $( '#' + self.options.formName + '-' + key ).val( JSON.stringify( dt ) );
                                        if ( _TCDEBUG ) console.log( $( '#' + self.options.formName + '-' + key ).val( ) );
                                        var tmpLen = Object.keys( dt ).length;
                                        if ( ( $.type( val[3] ) === 'object' || $.type( val[3] ) === 'array' ) && Object.keys( val[3] ).length - 1 === tmpLen && tmpLen > 0 ) {
                                            grp.children( 'div:nth-child(2)' ).children( 'span' ).removeClass( 'glyphicon-unchecked' ).addClass( 'glyphicon-check' );
                                        } else {
                                            grp.children( 'div:nth-child(2)' ).children( 'span' ).removeClass( 'glyphicon-check' ).addClass( 'glyphicon-unchecked' );
                                        }
                                        control.trigger( 'change' );
                                    } );
                                    var lbl = $( '<label>' ).text( valS );
                                    grp.append( $( '<div>' ).append( lbl ).append( it ) );
                                } );
                                if ( ( $.type( val[3] ) === 'object' || $.type( val[3] ) === 'array' ) && Object.keys( val[3] ).length - 1 === selItCnt && selItCnt > 0 ) {
                                    grp.children( 'div:nth-child(2)' ).children( 'span' ).removeClass( 'glyphicon-unchecked' ).addClass( 'glyphicon-check' );
                                }
                            } else {
                                control = $.fn.creatTag( 'input', {
                                    'class': 'form-control',
                                    type: 'text',
                                    id: self.options.formName + '-' + key
                                } );
                            }
                        }
                        if ( lng < 5 ) {
                            if ( label )
                                ln.append( label );
                            if ( !grp )
                                ln.append( control );
                            else
                                ln.append( grp );
                        } else if ( val[4] !== 'hide' ) {
                            if ( label )
                                ln.append( label );
                            if ( !grp )
                                ln.append( control );
                            else
                                ln.append( grp );
                        } else {
                            control.attr( 'type', 'hidden' );
                            if ( !grp )
                                ln.append( control );
                            else
                                ln.append( grp );
                        }
                        fm.append( ln );
                        if ( val[0] !== 'prim' ) {
                            if ( def !== null && self.options.mode !== 'add' )
                                control.val( $.type( def ) === 'object' ? JSON.stringify( def ) : def );
                            self.oldVal[self.options.formName + '-' + key] = control.val( );
                            control.change( {self: self}, self._hasChenged );
                            if ( self.options.validateUrl )
                                control.focusout( {self: self}, self._validate );
                            control.attr( 'tabindex', tabIndex++ );
                            if ( !fcs )
                                fcs = control;
                        }
                    }
                } else {
                    if ( $.isFunction( val[0] ) )
                        val[0].call( self, ln, lng > 2 ? val[2] !== null ? val[2] : null : null, {} );
                    else {
                        if ( !$.isFunction( val[0][0] ) ) {
                            if ( val[0].length > 1 && $.isFunction( val[0][1] ) ) {
                                val[0][1].call( self, ln, lng > 2 ? val[2] !== null ? val[2] : null : null, val[0][0] );
                            }
                        } else {
                            val[0][0].call( self, ln, lng > 2 ? val[2] !== null ? val[2] : null : null, val[0].length > 1 ? val[0][1] : {} );
                        }
                    }
                    fm.append( ln );
                }
            } );
            self.element.append( fm );
            if ( fcs )
                fcs.focus( );
            this._renderButtons( );
            if ( $.isFunction( self.options.onReady ) ) {
                self.options.onReady.call( this, self.oldVal );
            }
        },
        _prepareFieldToEdit: function ( ) {
            this.showBunner( );
            var rqDt = {
                formName: this.options.formName,
                id: this.options.recordId
            };
            var self = this;
//            if ( _TCDEBUG ) console.log(rqDt);
            $.post( this.options.requestUrl, $.isFunction( self.options.otherParam ) ? self.options.otherParam.call( self, rqDt, 'editRequest' ) : $.extend( rqDt, self.options.otherParam ), function ( data ) {
//                if ( _TCDEBUG ) console.log(data);
                if ( data.status === 'ok' ) {
//                    if ( _TCDEBUG ) console.log(self.options.fields);
                    $.each( data[self.options.formName], function ( key, val ) {
                        if ( $.type( self.options.fields[key] ) === 'undefined' ) {
//                            if ( _TCDEBUG ) console.log(self.options.hideUnsetedFields);
                            if ( self.options.hideUnsetedFields )
                                return;
                            self.options.fields[key] = [ 'string' ];
                        }
                        if ( self.options.fields[key][0] === 'prim' ) {
                            self.options.fields[key][1] = 'Первичн. ключ';
                        }
                        if ( self.options.fields[key].length < 2 ) {
                            self.options.fields[key][1] = '';
                        }
                        self.options.fields[key][2] = val;
                    } );
//                    if ( _TCDEBUG ) console.log(self.options.fields);
                    self.__renderFields( );
                    self.removeBunner( );
                } else {

                    var txt = 'Неизвестная ошибка';
                    if ( $.type( data.errorText ) === 'string' )
                        txt = data.errorText;
                    new m_alert( 'Ошибка сервера', txt, true, false );
                    self.element.formPopUp( 'close' );
                }
            } ).fail( function ( answ ) {
                new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                console.error( answ );
            } );
        },
        _renderFields: function ( ) {
            var self = this, tabIndex = 0, fcs = null;
            self.oldVal = {};
            if ( self.options.mode === 'change' && self.options.recordId !== null )
                self._prepareFieldToEdit( );
            else
                self.__renderFields( );
            let par = self.element.parent( );
            par.css( {
                top: $( window ).height( ) / 2 - par.height( ) / 2,
                left: $( window ).width( ) / 2 - par.width( ) / 2
            } );
        },
        open: function ( ) {
            $.ui.dialog.prototype.open.call( this );
            //this._super();
            this.element.empty( );
//            this.element.css('min-height','auto');
            if ( this.options.requestUrl === '#' ) {
                new m_alert( 'Ошибка настройки', 'Не задан requestUrl', true, false );
            } else if ( !Object.keys( this.options.fields ).length ) {
                new m_alert( 'Ошибка настройки', 'Не задан поля (fields)', true, false );
            } else if ( !this.options.formName ) {
                new m_alert( 'Ошибка настройки', 'Не задан поля имя формы (formName)', true, false );
            } else {
                this._renderFields( );
            }

            this.element.keypress( {self: this}, function ( e ) {
                if ( e.keyCode === 13 ) {
                    e.data.self._saveClick( e );
                }
            } );
        },
        close: function ( ) {
            var self = this;
            if ( this.options.changed ) {
                new m_alert( 'Внимание', 'Изменения не сохранены, закрыть окно?', function ( ) {
                    $.ui.dialog.prototype.close.call( self );
                    self.uiDialog.remove( );
                }, true );
            } else {
                $.ui.dialog.prototype.close.call( this );
                this.uiDialog.remove( );
            }
        }
    } );
}( jQuery ) );
var baseTablesController = {
    table: null,
    cnt: 0,
    isBusy: false,
    count: 0,
    hasErr: false,
    options: {
        autoClose: false,
        requestUrl: '#',
        otherParam: {},
        loadPicUrl: '#',
        pager: {
            page: 0,
            pageSize: 1800
        },
        drawRaw: null,
        formName: null,
        validateUrl: null,
        emptyText: 'Записей нет',
        serialColumn: true,
        primId: null,
        showHeader: true,
        addButton: null,
        useElAsContainer: false,
        messPrefix: null,
        afterErrorComplit: null,
        onAddComplit: null,
        beforeRightClick: null,
        onItemClick: null,
        onReady: null,
        onDrawTableComplite: null,
        hideColumnName: [ ],
        hideUnsetedFields: false,
        useKeyboard: false,
        useRigthClick: true,
        childPos: {my: "left top", at: "left top", of: null},
        answerVarName: 'result'
    },
    hasError: function ( ) {
        return this.hasErr;
    },
    _init: function ( ) {
        this.hasErr = false;
        var self = this;
        this.table = null;
        this.cnt = 0;
        if ( $.type( self.options.otherParam ) !== 'object' && !$.isFunction( self.options.otherParam ) )
            self.options.otherParam = {};
        if ( $.type( self.options.form ) != 'object' ) {
            self.options.form = {
                removeUrl: null,
                requestUrl: null,
                formName: null,
                fields: {}
            };
        } else {
            if ( $.type( self.options.form.fields ) != 'object' ) {
                self.options.form.fields = {};
            }
            if ( $.type( self.options.form.requestUrl ) != 'string' )
                self.options.form.requestUrl = null;
        }
        if ( !this.options.primId ) {
            $.each( this.options.form.fields, function ( key, val ) {
                if ( val[0] === 'prim' ) {
                    self.options.primId = key;
                    return false;
                }
            } );
        }
    },
    _showEmpty: function ( ) {
//            if ( _TCDEBUG ) console.log('showEmpo');
        if ( !this.options.useElAsContainer )
            this.element.children( '.small-table' ).append( $.fn.creatTag( 'p', {text: this.options.emptyText} ) );
        else
            this.element.append( $.fn.creatTag( 'p', {text: this.options.emptyText} ) );
    },
    _td: function ( val, th ) {
        var opt = {};
        var tNm = 'td';
        if ( th === true )
            tNm = 'th';
        opt.text = val;
        return $.fn.creatTag( tNm, opt );
    },
    _row: function ( rw, num ) {
        var tr = null;
        if ( this.options.drawRaw ) {
            if ( $.type( this.options.drawRaw ) === 'array' ) {
                if ( this.options.drawRaw.length ) {
                    if ( $.isFunction( this.options.drawRaw[0] ) ) {
                        tr = this.options.drawRaw[0].call( this, rw, num );
                    } else {
                        if ( this.options.drawRaw.length > 1 && $.isFunction( this.options.drawRaw[1] ) ) {
                            tr = this.options.drawRaw[1].call( this, rw, num, this.options.drawRaw[0] );
                        }
                    }
                }
            } else if ( $.isFunction( this.options.drawRaw ) ) {
                tr = this.options.drawRaw.call( this, rw, num );
            }
        }
        if ( !tr ) {
            tr = $.fn.creatTag( 'tr', {'data-key': rw[this.options.primId]} ), self = this;
            if ( $.type( num ) === 'undefined' )
                num = false;
            if ( this.options.serialColumn ) {
                if ( num === false )
                    tr.append( this._td( ++this.cnt ) );
                else
                    tr.append( this._td( num ) );
            } else if ( num === false )
                ++this.cnt;
            $.each( rw, function ( key, val ) {
                if ( key !== self.options.primId && $.inArray( key, self.options.hideColumnName ) === -1 ) {
                    tr.append( self._td( val ) );
                }
            } );
        }
        if ( this.options.useRigthClick )
            tr.contextmenu( {self: this}, this._rightClick );
        if ( $.isFunction( this.options.onItemClick ) ) {
            tr.click( {controller: this}, this.options.onItemClick );
        } else if ( $.isArray( this.options.onItemClick ) ) {
            if ( this.options.onItemClick.length > 1 && $.isFunction( this.options.onItemClick[1] ) ) {
                let tmpOpt=$.extend( {}, {controller: this}, this.options.onItemClick[0] );
                tr.click( tmpOpt, this.options.onItemClick[1] );
                if (tmpOpt.parentId && this.options.onItemClick.length > 2){
//                    //let cacheKey='suppList'+tmpOpt.parentId;
//                    //let category='materialTableList'+tmpOpt.parentId;
//                    let tmpReqD=$.extend({},this.options.pager);
//                    //this.options.primId:rw[this.options.primId]
//                    //t1ULuJrwurUtjLzyXY7EBHpNgWBFNFGbUhZ
//                    tmpReqD.reference_id=rw[this.options.primId];
//                    tmpReqD.formName=this.options.onItemClick[2];
//                    let cacheKey=this._doRequestListCreateCacheName(tmpReqD);
//                    let categoryName='materialTableList'+(this.options.pager.treeId?this.options.pager.treeId:'');
//
//                    $.post(this.options.requestUrl,tmpReqD).done(function(data){
//                        if (data.status==='ok'){
//                            stones.cacheRuntime.setData(cacheKey,data,categoryName);
//                        }
//                    });
                }
            }
        }
        return tr;
    },
    _removePost: function ( el, url, params ) {
        var self = this
        $.post( url, params, function ( data ) {
            if ( data.status === 'check' ) {
                m_alert( 'Внимание', data.checkHTML, function ( ) {
                    self._removePost( el, url, $.extend( {}, params, {removeAnyWay: true} ) )
                }, true );
            } else if ( data.status != 'ok' ) {
                var txt = 'Текст ошибки не передан';
                if ( $.type( data.errorText ) === 'string' )
                    txt = data.errorText;
                self._errorMess( txt, 'ошибка сервера' );
            } else {
                var next = el.next( );
                el.remove( );
                if ( self.options.serialColumn ) {
                    while ( next.length ) {
                        next.children( ':first' ).text( parseInt( next.children( ':first' ).text( ) ) - 1 );
                        next = next.next( );
                    }
                }
                self.cnt = -1;
                self.count = -1;
            }
        } ).fail( function ( answ ) {
            new m_alert( 'Ошибка сервера', answ.responseText, true, false );
            console.error( answ );
        } );
    },
    _rightClick: function ( e ) {
        var self = e.data.self, items = [ ];
        var el = $( this );
        e.preventDefault( );
        if ( !self.options.form.requestUrl || self.options.form.requestUrl === '#' || !self.options.formName || !self.options.form )
            return;
        items[items.length] = {
            label: 'Изменить',
            click: function ( ) {
                if ( $.isFunction( self.options.beforeRightClick ) )
                    self.options.beforeRightClick.call( this, e, el );
                $.custom.formPopUp( {
                    title: 'Изменить',
                    requestUrl: self.options.form.requestUrl,
                    formName: self.options.formName,
                    fields: $.extend( {}, self.options.form.fields ),
                    validateUrl: self.options.validateUrl,
                    otherParam: self.options.otherParam,
                    hideUnsetedFields: self.options.hideUnsetedFields,
                    mode: 'change',
                    recordId: parseInt( el.attr( 'data-key' ) ),
                    onReady: self.options.onReady,
                    afterSave: function ( dt ) {
                        if ( $.type( dt ) === 'object' ) {
                            el.replaceWith( self._row( dt, el.children( ':first' ).text( ) ) );
                        }
                        if ( self.options.autoClose ) {
                            self.close( );
                        }
                    },
                } );
            }
        };
        items[items.length] = {
            label: !el.parent().parent().parent().parent().index()
                ?(self.options.firmId?'Убрать материал от поставщика':'Удалить материал')
                :('Удалить запись '),
            click: function ( ) {
                if ( _TCDEBUG ) console.log('remove:parent index:',el.parent().parent().parent().parent().index()); 
                m_alert( 'Внимание', 'Удалить запись "' + el.children( ':first' ).text( ) + '" ?', function ( e ) {
                    let dt = {formName: self.options.formName, id: el.attr( 'data-key' )};
                    let params;
                    if ( $.isFunction( self.options.beforeRightClick ) )
                        self.options.beforeRightClick.call( this, e, el );
                    /////
                    params=$.isFunction( self.options.otherParam ) ? self.options.otherParam.call( self, dt, 'remove' ) : $.extend( dt, self.options.otherParam );
                    if (!el.parent().parent().parent().parent().index() && self.options.firmId){
                        params.firm_id=self.options.firmId; 
                    }
                    self._removePost(
                        el,
                        self.options.form.removeUrl,
                        params 
                    );
                } );
            }
        };
        var wasActive = el.hasClass( 'active-m' );
        el.addClass( 'active-m' );
        if ( items.length )
            new dropDown( {
                posX: e.clientX,
                posY: e.clientY,
                items: items,
                beforeClose: function ( ) {
                    if ( !wasActive )
                        el.removeClass( 'active-m' );
                }
            } );
        else {
            if ( !wasActive )
                el.removeClass( 'active-m' );
            self._errorMess( 'Незаданы removeUrl, editUrl' );
        }
    },
    _showHeader: function ( arr ) {
        if ( this.options.showHeader ) {
            var h = [ ], fld = this.options.form.fields, hCnt = 0;
            if ( this.options.showHeader ) {
                if ( this.options.serialColumn ) {
                    h[0] = '№';
                }
                $.each( Object.keys( arr ), function ( key, val ) {
                    if ( fld[val] ) {
                        hCnt++;
                        if ( fld[val][0] !== 'prim' ) {
                            if ( fld[val][0].length > 1 ) {
                                if ( fld[val][1] )
                                    h[h.length] = fld[val][1];
                            } else
                                h[h.length] = val;
                        }
                    }
                } );
                if ( hCnt != Object.keys( fld ).length )
                    return;
                var tr = $.fn.creatTag( 'tr', {} ), self = this;
                $.each( h, function ( ) {
                    tr.append( self._td( this, true ) );
                } );
                this.table.append( tr );
            }
        }
    },
    _addRows: function ( arr ) {
        if ( !this.table.children( 'tbody' ).children( ).length && arr.length )
            this._showHeader( arr[0] );
        if ( !this.table )
            return;
        for ( var i = 0; i < arr.length; i++ ) {
            this.table.append( this._row( arr[i] ) );
        }
        if ( $.isFunction( this.options.onDrawTableComplite ) ) {
            this.options.onDrawTableComplite.call( this.table[0] );
        } else if ( $.type( this.options.onDrawTableComplite ) === 'array' ) {
            if ( $.isFunction( this.options.onDrawTableComplite[0] ) ) {
                this.options.onDrawTableComplite[0].call( this.table[0], this.options.onDrawTableComplite.length > 1 ? this.options.onDrawTableComplite[1] : null );
            } else if ( this.options.onDrawTableComplite.length > 1 && $.isFunction( this.options.onDrawTableComplite[1] ) ) {
                this.options.onDrawTableComplite[1].call( this.table[0], this.options.onDrawTableComplite[0] );
            }
        }
    },
    _checkAndLoad: function ( onEnd ) {
        var tmp1 = this.table ? this.table.next( ).length ? ( this.table.next( ).position( ).top + this.table.next( ).height( ) )
                : ( this.table.children( 'tbody' ).children( ':last-child' ).length ? this.table.children( 'tbody' ).children( ':last-child' ).position( ).top : 0 )
                : 0;
        var self = this;
        if ( !this.isBusy && tmp1 < this.element.height( ) && this.options.pager.page * this.options.pager.pageSize < this.count ) {
            if ( !$.isFunction( onEnd ) ) {
                onEnd = function ( ) {
                    self.isBusy = false
                    tmp1 = this.table ? this.table.next( ).length ? ( this.table.next( ).position( ).top + this.table.next( ).height( ) )
                            : ( this.table.children( 'tbody' ).children( ':last-child' ).length ? this.table.children( 'tbody' ).children( ':last-child' ).position( ).top : 0 )
                            : 0;
                    if ( tmp1 < 0 )
                        tmp1 = 0;
                    if ( tmp1 < this.element.height( ) && this.options.pager.page * this.options.pager.pageSize < this.count ) {
                        this.options.pager.page++;
                        this._doRequestList( true );
                    }
                };
            }
            this._doRequestList( true, onEnd );
            this.isBusy = true;
        } else {
            if ( $.isFunction( onEnd ) )
                onEnd.call( this );
        }
    },
    _listIsGet: function ( data, addIt, onEnd ) {
        this.count = parseInt( data.count );
        if ( !addIt ) {
            if ( !this.options.useElAsContainer )
                this.element.empty( ).append( $.fn.creatTag( 'div', {'class': 'small-table'} ) );
            else
                this.element.empty( );
            if ( this.table ) {
                delete this.table;
                this.table = null;
            }
        }
        this._addTable( );
//            if ( _TCDEBUG ) console.log(data,this.options.answerVarName,data[this.options.answerVarName]);
        if ( data[this.options.answerVarName] ) {
            if ( data[this.options.answerVarName].length ) {
                this._addRows( data[this.options.answerVarName] );
            } else {
                if ( !addIt )
                    this._showEmpty( );
            }
        } else {
            if ( data.length ) {
                this._addRows( data );
            } else {
                if ( !addIt )
                    this._showEmpty( );
            }
        }
        if ( !addIt ) {
            this._drawFooter( );
            this.removeBunner( );
        }
        if ( $.isFunction( onEnd ) )
            onEnd.call( this );
        //this._checkAndLoad(onEnd);
//            if ( _TCDEBUG ) console.log(this.table.next().position().top,this.element.height());
    },
    _removeLoader: function ( ) {
        if ( this.table ) {
            var tr = this.table.children( 'tbody' ).find( '.loader' );
            if ( tr.length ) {
                tr.remove( );
            }
        }
    },
    _showLoader: function ( ) {
        if ( this.table ) {
            var tr = this.table.children( 'tbody' ).find( '.loader' );
            if ( !tr.length ) {
                tr = $.fn.creatTag( 'tr', {'class': 'loader'} );
                var td = $.fn.creatTag( 'td', {
                    colspan: this.table.children( 'tbody' ).children( 'tr:first-child' ).children( ).length
                } );
                if ( this.options.loadPicUrl !== '#' ) {
                    td.append( $.fn.creatTag( 'img', {src: this.options.loadPicUrl} ) );
                } else {
                    td.text( 'Загрузка' );
                }
                tr.append( td );
                this.table.append( tr );
            }
        }
    },
    _drawFooter: function ( ) {
        if ( this.options.addButton === null ) {
            var div = $.fn.creatTag( 'div', {'class': 'd-footer'} );
            var a = $.fn.creatTag( 'a', {} );
//            a.append( $.fn.creatTag( 'span', {
//                'class': 'glyphicon glyphicon-plus-sign text-success'
//            } ) );
//            div.append( a );
            if ( !this.options.useElAsContainer )
                this.element.children( '.small-table' ).append( div );
            else
                this.element.append( div );
//            a.unbind( 'click' );
//            a.click( {self: this}, this._addClick );
        } else {
            this.options.addButton.unbind( 'click' );
            this.options.addButton.click( {self: this}, this._addClick );
        }
    },
    _keyPress: function ( e ) {
        var self = e.data.self, tb = $( this ).find( 'tbody' );
        if ( $.isFunction( self.options.useKeyboard ) ) {
            if ( self.options.useKeyboard.call( this, e ) === false )
                return;
            else if ( e.isDefaultPrevented( ) )
                return;
        }
        if ( !tb.length )
            return;
        var selected = tb.children( '.active-m' );
        if ( e.keyCode === 38 ) {
            e.preventDefault( );
            if ( selected.length ) {
                selected.prev( ).trigger( 'click', {self: self} );
            } else {
//                    if ( _TCDEBUG ) console.log(tb.children(':first-child').children(':first-child'));
                if ( tb.children( ':first-child' ).children( ':first-child' ).get( 0 ).tagName !== 'TH' )
                    tb.children( ':first-child' ).trigger( 'click', {self: self} );
                else
                    tb.children( ':first-child' ).next( ).trigger( 'click', {self: self} );
            }
        } else if ( e.keyCode === 40 ) {
            e.preventDefault( );
            if ( selected.length ) {
                selected.next( ).trigger( 'click', {self: self} );
            } else {
                if ( tb.children( ':last-child' ) )
                    tb.children( ':last-child' ).trigger( 'click', {self: self} );
            }
        } else if ( e.keyCode === 37 ) {
            e.preventDefault( );
//                if ( _TCDEBUG ) console.log($(this).parent().prev().children('.content'));
            $( this ).parent( ).prev( ).children( '.content' ).trigger( 'focus' );
        } else if ( e.keyCode === 39 ) {
            e.preventDefault( );
//                if ( _TCDEBUG ) console.log($(this).parent().next().prev().children('.content'));
            if ( $( this ).parent( ).next( ).children( '.content' ).children( 'table' ).children( 'tbody' ).length )
                $( this ).parent( ).next( ).children( '.content' ).trigger( 'focus' );
        }
//            if ( _TCDEBUG ) console.log(e.keyCode);
    },
    _addTable: function ( ) {
        if ( this.table )
            return;
        this.cnt = 0;
        this.options.pager.page = 0;
        var tbl = $.fn.creatTag( 'table', {'class': 'table'} );
        tbl.append( $( '<tbody>' ) );
        if ( this.options.useKeyboard ) {
            this.element.keydown( {self: this}, this._keyPress );
            this.element.attr( 'tabindex', window.tablesControlle_tIndex );
            window.tablesControlle_tIndex += 1;
        }
        this.table = tbl;
        if ( !this.options.useElAsContainer )
            this.element.children( '.small-table' ).append( tbl );
        else
            this.element.append( tbl );
    },
    _addClick: function ( e ) {
        var self = e.data.self;
        var form = self.options.form;
        var fields = form.fields;
        if ( self.hasErr )
            return;
        e.preventDefault( );
        if ( Object.keys( fields ).length && form.requestUrl && self.options.formName ) {
            if ( !self.options.childPos.of )
                self.options.childPos.of = this;
            $.custom.formPopUp( {
//                    position:self.options.childPos,//{ my: "left top", at: "left top", of: this },
                title: 'Добавить',
                requestUrl: form.requestUrl,
                formName: self.options.formName,
                hideUnsetedFields: self.options.hideUnsetedFields,
                fields: $.extend( {}, fields ),
                validateUrl: self.options.validateUrl,
                onAddComplit: self.options.onAddComplit,
                onReady: self.options.onReady,
                otherParam: self.options.otherParam,
                beforeClose: function ( ) {
                    self._refreshOnDown.call( self );
                },
                afterSave: function ( dt ) {
                    if ( self.options.autoClose ) {
                        self.close( );
                    }
                },
            } );
        } else {
            self._errorMess( 'Не заданы поля, имя формы, или requestUrl' );
        }
    },
    _errorMess: function ( mess, head ) {
        this.hasErr = true;
        var h = 'ошибка настройки';
        var self = this;
        if ( $.type( head ) === 'string' )
            h = head;
        if ( $.type( mess ) !== 'string' )
            mess = 'Неизвестная ошибка';
        if ( $.type( this.options.messPrefix ) === 'string' )
            h = this.options.messPrefix + ' ' + h;
        else
            h = 'baseTablesController' + ' ' + h;
        new m_alert( h, mess, 'Закрыть', false, function ( ) {
            if ( $.isFunction( self.options.afterErrorComplit ) ) {
                self.options.afterErrorComplit.call( self );
            }
        } );
    },
    _doRequestListCreateCacheName:function(reqDt){
        return 'tList'+reqDt.formName+reqDt.reference_id+reqDt.treeId;
    },
    _doRequestList: function ( addIt, onEnd ) {
//            if ( _TCDEBUG ) console.log ('doRequestList: Страница № '+this.options.pager.page+' Размер страницы: '+this.options.pager.pageSize+'шт. Всего: '+this.count);
        if ( this.isBusy )
            return;
        if ( this.hasErr )
            return;
        this.isBudy = addIt;
        if ( $.type( addIt ) === 'undefined' )
            addIt = false;
        if ( this.options.requestUrl === '#' ) {
            this._errorMess( 'Не задано requestUrl' );
            return;
        }
        var self = this;
//            if ( _TCDEBUG ) console.log(this.options);
        if ( this.options.formName || this.options.drawRaw ) {
            var param = this.options.pager;
            if ( this.options.formName )
                param.formName = this.options.formName;
            
            var reqDt = $.isFunction( self.options.otherParam ) ? self.options.otherParam.call( self, param, 'doRequestList' ) : $.extend( param, self.options.otherParam );
            //if ( _TCDEBUG ) console.log(reqDt);
            let cacheProceed=!(reqDt.materialId || reqDt.parentId || (reqDt.zakmaterial && !reqDt.reference_id && !reqDt.treeId));
            let cacheKey=this._doRequestListCreateCacheName(reqDt);
            let categoryName='materialTableList'+(reqDt.treeId?reqDt.treeId:'');
            let cacheDt=cacheProceed?stones.cacheRuntime.getData(cacheKey,categoryName):null;
            if (cacheDt){
                this._listIsGet.call( self, cacheDt, addIt, onEnd );
                stones.cacheRuntime.setData(cacheKey,cacheDt,categoryName);
                if ( _TCDEBUG ) console.log('_doRequestList from cache');
            }else{
                self._showLoader( );
                $.post( this.options.requestUrl, reqDt, function ( data ) {
                    if ( _TCDEBUG ) console.log('doRequetsList from network');
                    self._removeLoader( );
                    self._listIsGet.call( self, data, addIt, onEnd );
                    if (cacheProceed)
                        stones.cacheRuntime.setData(cacheKey,data,categoryName);
                } ).fail( function ( answ ) {
                    new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                    console.error( answ );
                } );
            }
        } else {
            this._errorMess( 'Не задано имя формы и функция drawRaw' );
        }
    },
    _refreshOnDown: function ( ) {
        if ( this.isBusy )
            return;
        //var toRm=this.cnt-(this.options.pager.page-1)*this.options.pager.pageSize;
        if ( this.options.pager.page ) {
            var toRm = this.cnt - ( this.options.pager.page ) * this.options.pager.pageSize;
//                if ( _TCDEBUG ) console.log(toRm);
            while ( toRm > 0 ) {
                var tr = this.table.children( 'tbody' ).children( 'tr:last-child' );
//                    if ( _TCDEBUG ) console.log(tr);
                tr.remove( );
                toRm--;
                this.cnt--;
            }
            //this.options.pager.page--;
            this._doRequestList( true );
        } else {
            this.cnt = 0;
            if ( this.table )
                this.table.empty( );
            this._doRequestList( );
        }
    },
};
( function ( $ ) {
    $.widget( "custom.tablesController", $.custom.tablesControllerBase, $.extend( {
        downReq: 0,
        _create: function ( ) {
            this._super( );
            this.options.messPrefix = 'tablesController';
            this.options.useElAsContainer = false;
            this._init( );
        },
        open: function ( ) {
            this._super( );
            this.uiDialog.resize( {self: this}, function ( e ) {
                var self = e.data.self;
                if ( !self.table )
                    return;
                if ( self.table.next( ).position( ).top + self.table.next( ).height( ) < self.element.height( ) && self.options.pager.page * self.options.pager.pageSize < self.count ) {

                    if ( !self.isBusy ) {
                        self.options.pager.page++;
                        self._doRequestList( true, function ( ) {
                            self.isBusy = false;
                        } );
                    }
                    self.isBusy = true;
                }
            } );
        },
        close: function ( ) {
            $.ui.dialog.prototype.close.call( this );
//            this.table.remove();
            delete this.table;
            this.table = null;
            this.cnt = 0;
            this.options.pager.page = 0;
            delete this;
        },
    }, baseTablesController ) );
}( jQuery ) );
( function ( $ ) {
    $.widget( "custom.tablesControllerDialog", $.custom.openButton, {_className: 'tablesController'} );
}( jQuery ) );
( function ( $ ) {
    $.widget( "custom.baseTablesControllerMaterialWin", $.Widget, $.extend( {
        options:{
            firmId:null,
        },
        _create: function ( ) {
            this._super( );
            this.options.useElAsContainer = true;
            //this.options.useRigthClick=!this.options.picMode;
            if ( !this.options.messPrefix )
                this.options.messPrefix = 'baseTablesControllerMaterialWin';
//            if ( _TCDEBUG ) console.log('scrollInit');
            if ( $.isFunction( this._scroll ) ) {
                this.element.unbind( 'scroll' );
                this.element.scroll( {self: this}, this._scroll );
            }
            this.showBunner( );
            this._init( );
            this._doRequestList( );
            if ( this.options.width )
                this.element.css( 'width', this.options.width );
            if ( this.options['min-width'] )
                this.element.css( 'min-width', this.options['min-width'] );
        },
        doRequestList: function ( ) {
            this._doRequestList( );
        },
    }, baseTablesController, bunners, {
//        _doRequestList:function(addIt,onEnd){
//            this._super(addIt,onEnd);
//        },
    } ) );
}( jQuery ) );
( function ( $ ) {
    $.widget( "custom.tablesControllerMaterialTypes", $.custom.tablesControllerBase, {
        postavshik: null,
        postavshik_selected_materials: null,
        postavshik_selected_materials_opt: null,
        postavshik_currency: {},
        activeBack: null,
        selectedVal: null,
        options: {
            messPrefix: 'tablesControllerMaterialTypes',
            materialRequestListUrl: '#',
            subtablegetlistUrl: '#',
            materialFormName: null,
            minHeight: 400,
            minWidth: 1400,
            width: 1400,
            height: 420,
            onAddComplit: null,
            materialTablesListUrl: null,
            subTableDDListUrl: null,
            bigLoaderPicUrl: null,
            referenceColumnName: null,
            subtabladdeditUrl: null,
            removeSubTUrl: '#',
            suppliersRequestUrl: null,
//            onOkClick:null,
//            onCloseClick:null,
            renderFooter: true,
//            picMode:false,
            category: null,
            materialForm: {},
            beforeClose: function ( e, u1 ) {
//                if ( _TCDEBUG ) console.log(u1,this,e);
                $( this ).tablesControllerMaterialTypes( 'transf' );
            }
        },
        transf: function ( ) {
            if ( this.options.but ) {
                this._postavshik_select_IsFirst=true;
                this.uiDialog.effect( 'transfer', {to: this.options.but, className: 'ui-effects-transfer'}, 500 );
            }
        },
        content: null,
        m_types: null,
        _appendAll: function ( targ, obj ) {
            $.each( obj, function ( k, v ) {
                if ( k != 'parent' )
                    targ.append( v );
            } );
        },
        _errorMess: function ( mess, head ) {
            this.hasErr = true;
            var h = 'ошибка настройки';
            var self = this;
            if ( $.type( head ) === 'string' )
                h = head;
            if ( $.type( mess ) !== 'string' )
                mess = 'Неизвестная ошибка';
            if ( $.type( this.options.messPrefix ) === 'string' )
                h = this.options.messPrefix + ' ' + h;
            else
                h = 'baseTablesController' + ' ' + h;
            new m_alert( h, mess, 'Закрыть', false, function ( ) {
                //if ($.isFunction(self.options.afterErrorComplit)){
                self.close( );
                ///}
            } );
        },
        _renderT: function ( opt ) {
            var footerOk = $.type( opt.renderFooter ) === 'undefined' || opt.renderFooter === true;
            opt.headerText = opt.headerText ? opt.headerText : 'Без заголовка';
            opt.width = opt.width ? opt.width : null;
            opt['min-width'] = opt['min-width'] ? opt['min-width'] : null;
            opt.onItemClick = opt.onItemClick ? opt.onItemClick : null;
            opt.onAddComplit = opt.onAddComplit ? opt.onAddComplit : null;
            opt.beforeRightClick = opt.beforeRightClick ? opt.beforeRightClick : null;
            opt.onReady = opt.onReady ? opt.onReady : null;
            opt.onDrawTableComplite = opt.onDrawTableComplite ? opt.onDrawTableComplite : null;
            opt.showHeader = opt.showHeader ? opt.showHeader : false;
            opt.drawRaw = opt.drawRaw ? opt.drawRaw : null;
            opt.answerVarName = opt.answerVarName ? opt.answerVarName : 'result';
            opt.useKeyboard = $.type( opt.useKeyboard ) !== 'undefined' ? opt.useKeyboard : true;
            if ( $.type( opt.formName ) === 'undefined' && !$.isFunction( opt.drawRaw ) && !$.isArray( opt.drawRaw ) ) {
                this._errorMess( 'Настройка', 'Не задано имя формы "formName" и "drawRaw' );
                return null;
            }
            if ( $.type( opt.form ) === 'undefined' && !$.isFunction( opt.drawRaw ) && !$.isArray( opt.drawRaw ) ) {
                this._errorMess( 'Настройка', 'Не задано поля формы "form" и "drawRaw' );
                return null;
            }
            if ( $.type( opt.requestUrl ) === 'undefined' ) {
                this._errorMess( 'Настройка', 'Не задано Url запроса списка "requestUrl"' );
                return null;
            }
            opt.otherParam = opt.otherParam ? opt.otherParam : {};
            if ( $.type( opt.hideColumnName ) !== 'array' ) {
                if ( $.type( opt.hideColumnName ) === 'string' ) {
                    var tmp = [ ];
                    tmp[0] = opt.hideColumnName;
                    opt.hideColumnName = tmp;
                } else {
                    opt.hideColumnName = [ ];
                }
            }
//            if ( _TCDEBUG ) console.log('_renderT',opt);
            var a = $.fn.creatTag( 'a', {html: '<span class="glyphicon glyphicon-plus-sign text-success"></span>'} );
            var self = this;
            var rVal = {
                parent: $.fn.creatTag( 'div' ),
                header: $.fn.creatTag( 'h4', {text: opt.headerText} ),
                body: $.fn.creatTag( 'div', {'class': 'content'} ),
                //footer:$.fn.creatTag('div',{'class':'footer'})
            };
            if ( this.options.renderFooter && footerOk ) {
                rVal.footer = $.fn.creatTag( 'div', {'class': 'footer'} );
                rVal.footer.append( a );
            }
            this._appendAll( rVal.parent, rVal );
            this.content.append( rVal.parent );
            rVal.body.baseTablesControllerMaterialWin( {
                addButton: a, //$.type(opt.addButton)==='undefined'?a:opt.addButton,
                childPos: {my: "left bottom-100%", at: "left bottom-100%"},
                firmId:opt.firmId,
                requestUrl: opt.requestUrl,
                formName: opt.formName,
                messPrefix: '"Контроллер материалы"',
                loadPicUrl: this.options.loadPicUrl,
                otherParam: opt.otherParam,
                form: opt.form,
                onItemClick: opt.onItemClick,
                onReady: opt.onReady,
                onDrawTableComplite: opt.onDrawTableComplite,
                showHeader: opt.showHeader,
                serialColumn: false,
                beforeRightClick: opt.beforeRightClick,
                drawRaw: opt.drawRaw,
                afterErrorComplit: function ( e ) {
                    self.close( );
                },
                onAddComplit: opt.onAddComplit,
                hideUnsetedFields: true,
                hideColumnName: opt.hideColumnName,
                answerVarName: opt.answerVarName,
                useKeyboard: opt.useKeyboard,
                useRigthClick: !this.options.picMode,
                width: opt.width,
                'min-width': opt['min-width']
            } );
            return rVal;
        },
        _showHelpBlock: function ( ) {
            var help = this.content.prev( ).children( 'a' ), d = null;
            if ( !help.length ) {
                help = $( '<a>' ).addClass( 'btn glyphicon glyphicon-question-sign' );
                help.mouseenter( function ( e ) {
//                    if ( _TCDEBUG ) console.log('tt');
                    if ( d )
                        return;
                    d = $.ui.dialog( {
                        title: false,
                        //modal:true,
                        closeOnEscape: true,
                        show: {effect: "fade", duration: 1800},
                        position: {my: "left top", at: "right top", of: $( this )}
                    } );
                    d.uiDialog.css( 'background-color', 'white' );
                    d.uiDialogTitlebar.remove( );
                    d.element.text( 'Выберите постовщика чтобы посмотреть какие материалы унего добавлены. Чтобы продолжить редактирование снимите выбор.' );
                } ).mouseleave( function ( e ) {
                    if ( d ) {
                        d.close( );
                        d.uiDialog.remove( );
                        delete d;
                        d = null;
                    }
                } );
                this.content.prev( ).append( help );
            }
        },
        _showPicBanner: function ( matId, parentId ) {
            //if ( _TCDEBUG ) console.log( '_showPicBanner', this, matId );
            if ( !this.options.picMode ) {
                this._showHelpBlock( );
                return;
            }
            var bun = this.content.prev( ).children( 'span' );
            var postId = this.postavshik.attr( 'data-key' );
            var btnGroup = this.content.parent( ).children( '.btn-group' );
            var self = this;
            if ( !btnGroup.length ) {
                btnGroup = $( '<div>' ).addClass( 'btn-group' ).
                        append( $( '<a>' )
                                .addClass( 'btn btn-success btn-xs' )
                                .text( 'Выбрать' )
                                .click( {self: this}, function ( e ) {
                                    if ( $.isFunction( e.data.self.options.onOkClick ) )
                                        e.data.self.options.onOkClick.call( this, e.data.self );
                                    else if ( $.isArray( e.data.self.options.onOkClick ) ) {
                                        if ( e.data.self.options.onOkClick.length > 1 ) {
                                            if ( $.isFunction( e.data.self.options.onOkClick[0] ) )
                                                e.data.self.options.onOkClick[0].call( this, e.data.self, e.data.self.options.onOkClick[1] );
                                            else if ( $.isFunction( e.data.self.options.onOkClick[1] ) )
                                                e.data.self.options.onOkClick[1].call( this, e.data.self, e.data.self.options.onOkClick[0] );
                                        } else if ( $.isFunction( e.data.self.options.onOkClick[0] ) )
                                            e.data.self.options.onOkClick[0].call( this, e.data.self );
                                    }
                                    e.data.self.close( );
                                } ) )
                        .append( $( '<a>' ).addClass( 'btn btn-default btn-xs' ).text( 'Отменить' ).click( {self: this}, function ( e ) {
                            if ( $.isFunction( e.data.self.options.onCloseClick ) )
                                e.data.self.options.onCloseClick.call( this, e.data.self );
                            e.data.self.close( );
                        } ) );
                this.content.parent( ).append( btnGroup );
//                this.content.parent().height(450);
                this.content.height( 280 );
                //this.content.children('div').children('.content').height(356);
            }
            matId = matId ? matId : false;
            var txt = matId && postId ? '' : postId ? 'Выберете материал' : 'Выберите поставщика';
            if ( !txt ) {
//                if ( _TCDEBUG ) console.log(this.content);
                $.each( this.content.children( ), function ( ) {
                    //if ( _TCDEBUG ) console.log($(this).find('.active-m').children(':first-child').text());
                    if ( txt )
                        txt += ' ' + $( this ).find( '.active-m' ).children( ':first-child' ).text( );
                    else {
                        txt = ':' + $( this ).find( '.active-m' ).children( ':first-child' ).text( );
                    }
                } );
                txt += '; ' + self.postavshik_selected_materials[parseInt( parentId )][parseInt( matId )] + 'руб';
                btnGroup.children( ':first-child' ).removeClass( 'disabled' );
            } else {
                btnGroup.children( ':first-child' ).addClass( 'disabled' );
            }
            if ( !bun.length )
                this.content.prev( ).append( $( '<span>' ).text( txt ) );
            else
                bun.text( txt );
        },
        _postavshik_select_IsFirst:true,
        _postavshik_select: function ( dt ) {
            let self = dt.self, id = $( this ).attr( 'data-key' );
            if (typeof(id)==='undefined' && self._postavshik_select_IsFirst){    
                self._postavshik_select_IsFirst=false;
                return;
            }
            if ( !self.options.suppliersRequestUrl && id ) {
                delete( self.postavshik_selected_materials );
                delete( self.postavshik_selected_materials_opt );
                self.postavshik_selected_materials = null;
                self.postavshik_selected_materials_opt = null;
//                if ( _TCDEBUG ) console.log(self.postavshik_selected_materials);
                return;
            }
            self.content.children( ).remove( );
            if ( $.type( self.m_types ) === 'object' ) {
                delete ( self.m_types );
                self.m_types = null;
            }
            if ( !id && self.options.picMode ) {
                self._showPicBanner( );
                return;
            }
            $.post( self.options.suppliersRequestUrl, {id: id} ).done( function ( data ) {
                if ( _TCDEBUG ) console.log( data );
                self.postavshik_selected_materials = data.materials ? JSON.parse( data.materials ) : null
                self.postavshik_selected_materials_opt = data.materialsOpt;
                if ( _TCDEBUG ) console.log( self.postavshik_selected_materials );
                if ( !self.postavshik_selected_materials ) {
                    console.warn( '_postavshik_select - Данные не получены' );
                }
//                if ( _TCDEBUG ) console.log
                if ( !$.isFunction( self.options.onOkClick ) && id && id != -1 || self.options.picMode )
                    self.options.renderFooter = false;
                else
                    self.options.renderFooter = true;
                self._renderAtEnd( id );
                self._showPicBanner( );
            } ).fail( function ( answ ) {
                new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                console.error( answ );
            } );
        },
        _renderAtEnd: function ( firmId ) {
            let cont = this.content, self = this;
            firmId=firmId?firmId:null;
            this.m_types = this._renderT( {
                headerText: 'Материал:',
                firmId:firmId,
                requestUrl: this.options.materialRequestListUrl,
                formName: this.options.materialForm.formName,
                form: this.options.materialForm,
                onReady: this.options.materialForm.onReady ? this.options.materialForm.onReady : null,
                onAddComplit: this.options.onAddComplit,
                onItemClick: [ {self: this}, this.onMaterialItemClick ],
                otherParam: function ( data, inf ) {
                    while ( cont.children( ).length > 1 ) {
                        cont.children( ':last-child' ).remove( );
                    }
                    if ( self.postavshik_selected_materials ) {
                        var ln = $.type( self.postavshik_selected_materials ) === 'object' ?
                                Object.keys( self.postavshik_selected_materials ).length :
                                $.type( self.postavshik_selected_materials ) === 'array' ?
                                self.postavshik_selected_materials.length : 0;
                        if ( ln )
                            data.zakmaterial = self.postavshik_selected_materials;
                        else
                            data.zakmaterial = 'not mat';
                    }
//                    if ( _TCDEBUG ) console.log(data,self.postavshik_selected_materials);
                    return data;
                },
            } );
            this.element.unbind( 'resize' );
            this.uiDialog.resize( {targetArr: this.m_types}, function ( e ) {
//                if ( _TCDEBUG ) console.log(e.data.targetArr.parent.height()-(e.data.targetArr.header.outerHeight()+e.data.targetArr.footer.outerHeight())+'px');
                e.data.targetArr.body.css( 'height', e.data.targetArr.parent.height( ) - ( e.data.targetArr.header.outerHeight( ) + e.data.targetArr.footer.outerHeight( ) ) + 'px' );
            } );
        },
        _renderMain: function ( ) {
            let self=this;
            this.content = $.fn.creatTag( 'div', {'class': 'mat-types'} );
            this.element.append( this.content );
            let cont = this.content;
            let infPart = $.fn.creatTag( 'div', {class: 'mat-info-part'} )
            this.postavshik = $( '<input>' ).attr( 'tabindex', window.tablesControlle_tIndex++ );
            infPart.append( $.fn.creatTag( 'div', {class: 'activeDD'} ).append( this.postavshik ) );
            infPart.insertBefore( cont );
            let ddOpt = {
                requestUrl: this.options.suppliersRequestUrl,
                loadPicUrl: this.options.bigLoaderPicUrl,
                strictly: true,
                onClickOrChange: [ {self: this}, this._postavshik_select ]
            };
            if ( this.options.category !== null && this.options.category != -1 && this.options.category < 4 ) {
                ddOpt.otherParam = {category: this.options.category};
            }
            ddOpt.onReady=function(aDDEl, data){
                if ( _TCDEBUG ) console.log(data);
                self.postavshik_currency={};
                for (let i in data.source){
                    if (parseInt(data.source[i].value)>-1){
                        self.postavshik_currency[parseInt(data.source[i].value)] = parseFloat(data.source[i].curency_coast);
                    }
                }
                if ( !self.options.picMode )
                    self._renderAtEnd( );
                self._showPicBanner( );                
            };
            this.postavshik.activeDD( ddOpt );
            /*
            if ( !this.options.picMode )
                this._renderAtEnd( );
            this._showPicBanner( );
             * 
             */
        },
        _create: function ( ) {
            var etalon = {
                formName: null,
                requestUrl: "#",
                removeUrl: "#",
                fields: {
                }
            };
//            this.options.minHeight=400;
//            this.options.minWidth=600;
            this._super( );
            this.element.keyup( {self: this}, function ( e ) {
                if ( e.keyCode == 13 ) {
                    //if ( _TCDEBUG ) console.log('Тест',e,this);
                    var gp = $( this ).children( '.btn-group' );
                    if ( gp.length ) {
                        if ( _TCDEBUG ) console.log( 'enter' );
                        var fc = gp.children( ':first-child' );
                        if ( !fc.hasClass( 'disabled' ) )
                            fc.trigger( 'click', {self: e.data.self} );
                    }
                }
            } );
            this.options.materialForm = $.extend( etalon, this.options.materialForm );
        },
        open: function ( category ) {
            this._super( );
            this.content = null;
            this.m_types = null;
            if ( $.type( category ) !== 'undefined' ) {
                this.options.category = category;
            }
            if ( !this.content )
                this._renderMain( );
        },
        _eraseAll: function ( ) {
        },
        close: function ( ) {
            this._super( );
            this.content.remove( );
            delete this.m_types;
            delete this.content;
        },
        __postChangeStateDo: function ( params ) {
            let {materialId, parentId, postId, coast, optfrom=0, optcoast=0,rCoast=0, parentIt, ind}=params;
            $.post( this.options.suppliersRequestUrl, {
                materialId: materialId,
                parentId: parentId,
                changeStateToFirmId: postId,
                coast: coast,
                optfrom:optfrom,
                optcoast:optcoast,
                rCoast:rCoast
            } ).done( function ( data ) {
//                if ( _TCDEBUG ) console.log(data);
                $( parentIt ).trigger( 'click', {
                    //materialId:materialId,
                    parentId: parentId,
                    self: this,
                    index: ind
                } );
            } ).fail( function ( answ ) {
                new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                console.error( answ );
            } );
        },
        __postChangeState: function ( params ) {
            var self = this;
            if ( params.postId === '-1' ) {
                m_alert( 'Внимание', '<p>Сбросить все фирмы?</p><h3 style="color:#FF1111;">Отмена невозможна.</h3>', function ( ) {
                    self.__postChangeStateDo( params );
                }, true );
            } else {
                this.__postChangeStateDo( params );
            }
        },
        _postChangeState: function ( e ) {
            var self = e.data.self, parentIt = e.data.parentIt, materialId = e.data.materialId, parentId = e.data.parentId, ind = e.data.ind;
            e.data.postId = $( this ).parent( ).parent( ).attr( 'data-key' );
            e.data.self.__postChangeState.call( e.data.self, e.data );
        },
        _onLastItemClickChange:function ( e ) {
            let {materialId, parentId, curent, ind}=e.data.params;
            let self=e.data.self;
            if ( _TCDEBUG ) console.log( $( this ).val( ) )
            coastInput = $( this ).val( );
            //materialId, parentId, postId, coast, parentIt, ind
            self.__postChangeState.call( self,{
                materialId:materialId,
                parentId:parentId,
                postId:$( this ).parent( ).parent( ).attr( 'data-key' ),
                coast:$( this ).parent().parent().children().children('[data-name=coast]').val( ),
                optcoast:$( this ).parent().parent().children().children('[data-name=optcoast]').val( ),
                optfrom:$( this ).parent().parent().children().children('[data-name=optfrom]').val( ),
                rCoast:$( this ).parent().parent().children().children('[data-name=rCoast]').val( ),
                parentIt:curent,
                ind:ind });
        },
        _onLastItemClickCreatInput:function(params){
            let {allowPoint=true, curent, dataName, materialId, parentId, value, ind, title=false}=params;
            let inpAttr={
                type: 'text',
                'data-name':dataName                
            };
            if (title){
                inpAttr.title = title;
            }
            return $( '<input>' ).attr( inpAttr ).val( value ).change({
                    self:this,
                    params:{
                        materialId:materialId,
                        parentId:parentId,
                        curent:curent,
                        ind:ind
                    }
                },this._onLastItemClickChange ).onlyNumeric( {allowPoint: allowPoint} );
        },
        onLastItemClick: function ( mE ) {
            var self = mE.data.self, cont = $( this ).parent( ).parent( ).parent( ).parent( ).parent( );
            var materialId = $( this ).attr( 'data-key' );
            var parentId = mE.data.parentId; //cont.children('div:first-child').find('.active-m').attr('data-key');
//            alert(parentId+'X'+materialId);
            var ind = mE.data.index ? mE.data.index : 1;
//            if ( _TCDEBUG ) console.log($(this).parent());
            var curent = $( this );
            var coastInput = null;
            if ( !self.postavshik || !self.postavshik.attr( 'data-key' ) ) {
                if ( self.options.suppliersRequestUrl ) {
                    curent.parent( ).children( ).removeClass( 'active-m' );
                    //$.each(curent.parent().children(),function(){$(this).removeClass('active-m')});
                    curent.addClass( 'active-m' );
                    while ( cont.children( ).length > ind ) {
                        cont.children( ':last-child' ).remove( );
                    }
                    self._renderT( {
                        renderFooter: false,
                        headerText: 'Поставщики',
                        requestUrl: self.options.suppliersRequestUrl,
                        'min-width': 263,
                        drawRaw: [ {self: this}, function ( e, num, dt ) {
                                if ( _TCDEBUG ) console.log( 'drawRaw', e, num, dt );
                                var tr = $.fn.creatTag( 'tr', {'data-key': e.value} );
                                tr.append( $.fn.creatTag( 'td', {text: e.label} ) );
                                if ( e.label.length > 15 )
                                    tr.attr( 'title', e.label );
                                if ( !e.selected ) {
                                    tr.append( $.fn.creatTag( 'td' ).append( $.fn.creatTag( 'span', {class: 'glyphicon glyphicon-unchecked'} ).click( {
                                        materialId: materialId,
                                        parentId: parentId,
                                        self: self,
                                        parentIt: curent,
                                        ind: ind,
                                        coast: 'no',
                                        optfrom: 'no',
                                        optcoast: 'no',
                                        rCoast: 'no'
                                    }, self._postChangeState ) ) );
                                } else {
                                    tr.append( $.fn.creatTag( 'td' ).append( $.fn.creatTag( 'span', {class: 'glyphicon glyphicon-check', checked: 'true'} ).click( {
                                        materialId: materialId,
                                        parentId: parentId,
                                        self: self,
                                        parentIt: curent,
                                        ind: ind,
                                        coast: 'no',
                                        optfrom: 'no',
                                        optcoast: 'no',
                                        rCoast: 'no'
                                    }, self._postChangeState ) ) );
                                }
                                let inpCol = $( '<td>' );
                                let inpOptfrom=$( '<td>' );
                                let inpOptcoast=$( '<td>' );
                                let inpRecomend=$('<td>');
                                let ttCoastCol = $( '<td>' ).css( {'text-align': 'center', color: '#33CC33;'} );
                                if ( $.type( e.coast ) !== 'undefined' ) {
                                    let params={
                                        dataName:'coast',
                                        materialId:materialId,
                                        parentId:parentId,
                                        curent:curent,
                                        ind:ind,
                                        value:e.coast
                                    };
                                    let tmpInp;
                                    inpCol.append( self._onLastItemClickCreatInput(params) );
                                    params.dataName='optcoast';
                                    params.value=e.optcoast;
                                    params.title='Оптовая цена';
                                    inpOptcoast.append(self._onLastItemClickCreatInput(params));
                                    
                                    params.dataName='optfrom';
                                    params.value=e.optfrom;
                                    params.allowPoint=false;
                                    params.title='Минимальное количество для опта';
                                    inpOptfrom.append(self._onLastItemClickCreatInput(params));
                                    
                                    params.dataName='rCoast';
                                    params.value=e.rCoast;
                                    params.allowPoint=true;
                                    params.title='Рекомендуемая цена (По прайсу)';
                                    inpRecomend.append(self._onLastItemClickCreatInput(params));
                                    //inpOptfrom.append( tmpInp.clone().val(e.optfrom).attr('data-name','optfrom') );
                                    //inpOptcoast.append( tmpInp.clone().val(e.optcoast).attr('data-name','optcoast') );
                                    ttCoastCol.text( Math.round( e.coast * e.curency_coast * 100 ) / 100 );
                                }
                                tr.append( ttCoastCol );
                                tr.append( $( '<td>' ).css( 'text-align', 'center' ).text( e.curency_type ) );
                                tr.append( inpCol );
                                tr.append( inpOptcoast );
                                tr.append( inpOptfrom );
                                tr.append(inpRecomend);
                                tr.click( {self: self}, function ( e ) {
                                    $( this ).parent( ).children( ).removeClass( 'active-m' );
                                    $( this ).addClass( 'active-m' );
                                    e.data.self.activeBack = $( this ).index( );
                                } );
                                return tr;
                            } ],
                        onDrawTableComplite: [ {self: self}, function ( dt ) {
//                            if ( _TCDEBUG ) console.log('activeBack',dt.self.activeBack);
                                if ( dt.self.activeBack !== null ) {
                                    let ch = $( this ).children( 'tbody' ).children( );
                                    if ( dt.self.activeBack < ch.length ) {
                                        $( ch[dt.self.activeBack] ).addClass( 'active-m' );
                                        $( this ).parent( ).trigger( 'focus' );
                                    }
                                    dt.self.activeBack = null;
                                }
                                let td = dt.self.content.children( ':last-child' ).children( '.content' ).children( 'table' ).children( 'tbody' ).children( 'tr:first-child' ).children( 'td:nth-child(3)' );
                                if ( _TCDEBUG ) console.log( td );
                                td.html( '&#x584;' ).css( 'text-align', 'center' );
                                td = td.next( );
                                td.html( '&#36;/&euro;' ).css( 'text-align', 'center' );
                                td = td.next( );
                                td.text( 'У/Е' ).css( 'text-align', 'center' );
                                td = td.next( );
                                td.text( 'ОПТ/ЦЕН' ).css( 'text-align', 'center' );
                                td = td.next( );
                                td.text( 'ОПТ/КОЛ' ).css( 'text-align', 'center' );
                                td = td.next( );
                                td.text( 'ПРАЙС' ).css( 'text-align', 'center' );
                            } ],
                        useKeyboard: function ( e ) {
                            if ( e.keyCode === 32 ) {
//                                if ( _TCDEBUG ) console.log('space process');
                                //activeBack=$(this).find('.active-m').length?$(this).find('.active-m').index():null;
                                $( this ).find( '.active-m' ).children( 'td:last-child' ).children( 'span' ).trigger( 'click', {
                                    materialId: materialId,
                                    parentId: parentId,
                                    self: self,
                                    parentIt: curent,
                                    ind: ind
                                } );
                                e.preventDefault( );
                            }
                        },
                        answerVarName: 'source',
                        otherParam: {
                            materialId: materialId,
                            parentId: parentId,
                        }
                    } );
                } else {
                    console.warn( 'suppliersRequestUrl - не задано.' );
                }
            } else {
                let firmId = ( self.postavshik && self.postavshik.attr( 'data-key' ) ) ? self.postavshik.attr( 'data-key' ) : null;
                curent.parent( ).children( ).removeClass( 'active-m' );
                curent.addClass( 'active-m' );
                self._showPicBanner( materialId, parentId );
                self.selectedVal = {
                    type_id: parentId,
                    mat_id: materialId,
                    firm_id: firmId,
                    coast: self.postavshik_selected_materials[parentId][materialId],
                    optBaseCoast: typeof(self.postavshik_selected_materials_opt[parentId][materialId])==='object'?self.postavshik_selected_materials_opt[parentId][materialId].optcoast:0,
                    optFrom: typeof(self.postavshik_selected_materials_opt[parentId][materialId])==='object'?self.postavshik_selected_materials_opt[parentId][materialId].optfrom:0,
                    rCoast: 
                            (typeof(self.postavshik_selected_materials_opt[parentId][materialId])==='object'?self.postavshik_selected_materials_opt[parentId][materialId].rCoast:0)
                            *
                            (Object.keys(self.postavshik_currency).indexOf(firmId)>-1?self.postavshik_currency[parseInt(firmId)]:0)
                };
                if ( _TCDEBUG ) console.log( 'selectedVal', self.selectedVal, self.postavshik_selected_materials[parentId] );
            }
        },
        _onMaterialItemClickProceed:function(opt){
            let {
                loader=null,
                cont,
                mainId,
                parentId,
                ind,
                data
            }=opt, self=this;
            if ( loader )
                loader.remove( );
            var fields = {id: [ 'prim' ]};
            if ( this.options.subTableDDListUrl ) {
                fields.name = [ 'string', data.tables[ind - 1].rusname, null, {
                        requestUrl: this.options.subTableDDListUrl,
                        loadPicUrl: this.options.bigLoaderPicUrl,
                        otherParam: {formName: data.tables[ind - 1].fullname}
                    } ];
            } else {
                fields.name = [ 'string', data.tables[ind - 1].rusname ];
            }
            if ( this.options.referenceColumnName ) {
                fields[this.options.referenceColumnName] = [ function ( el, defVal ) {
                        var input = $.fn.creatTag( 'input', {
                            type: 'hidden',
                            id: data.tables[ind - 1].fullname + '-' + self.options.referenceColumnName
                        } );
                        input.val( mainId );
                        el.append( input );
                    } ], '';
            }
            var opt = {
                headerText: data.tables[ind - 1].rusname,
                requestUrl: this.options.subtablegetlistUrl,
                formName: data.tables[ind - 1].fullname,
                hideColumnName: this.options.referenceColumnName ? this.options.referenceColumnName : [ ],
                beforeRightClick: function ( e, tr ) {
                    $.each( tr.parent( ).children( ), function ( ) {
                        $( this ).removeClass( 'active-m' );
                    } );
                },
                form: {
                    requestUrl: this.options.subtabladdeditUrl,
                    removeUrl: this.options.removeSubTUrl,
                    fields: fields
                },
                otherParam: function ( data, inf ) {
                    while ( cont.children( ).length > ind + 1 ) {
                        cont.children( ':last-child' ).remove( );
                    }
                    data.treeId = cont.children( 'div:first-child' ).find( '>.content >table >tbody >.active-m' );
                    if ( data.treeId.length ) {
                        data.matRusName = data.treeId.children( ':first-child' ).text( ).toLowerCase( );
                        data.treeId = parseInt( data.treeId.attr( 'data-key' ) );
                    } else
                        data.treeId = null;
                    if ( self.postavshik_selected_materials ) {
                        if ( _TCDEBUG ) console.log( self.postavshik_selected_materials );
                        var ln = $.type( self.postavshik_selected_materials ) === 'object' ?
                                Object.keys( self.postavshik_selected_materials ).length :
                                $.type( self.postavshik_selected_materials ) === 'array' ?
                                self.postavshik_selected_materials.length : 0;
                        if ( ln && data.treeId && $.type( self.postavshik_selected_materials[data.treeId] ) ) {
                            data.zakmaterial = {};
                            if ( $.type( self.postavshik_selected_materials[data.treeId] ) === 'object' )
                                data.zakmaterial[data.treeId] = Object.keys( self.postavshik_selected_materials[data.treeId] );
                            else
                                data.zakmaterial[data.treeId] = self.postavshik_selected_materials[data.treeId];
                        } else
                            data.zakmaterial = 'not mat';
                    }
                    if ( self.options.referenceColumnName )
                        data[self.options.referenceColumnName] = mainId;
                    return data;
                }
            };
            if ( data.tables.length > ind )
                opt.onItemClick = [ {self: this, index: ind + 1, parentId: parentId}, this.onMaterialItemClick, data.tables[ind - 1].fullname?data.tables[ind - 1].fullname:'' ];
            else {
                opt.onItemClick = [ {self: this, index: ind + 1, parentId: parentId, mainId: mainId}, self.onLastItemClick ];
                opt.drawRaw = function ( rw ) {
                    return $( '<tr>' ).attr( {'data-key': rw.id} )
                            .append( $( '<td>' ).text( rw.name ) );
                    //.append($('<td>').text(rw.article.length?'арт: '+rw.article:''));
                };
            }
            this._renderT( opt );
        },
        onMaterialItemClick: function ( e ) {
            let self = e.data.self, loader = null, cont = $( this ).parent( ).parent( ).parent( ).parent( ).parent( );
            let ind = e.data.index ? e.data.index : 1;
            let mainId = $( this ).attr( 'data-key' ), el = $( this );
            let parentId = e.data.parentId ? e.data.parentId : mainId;
            let dt = {
                id: parentId
            };
            el.parent( ).children( ).removeClass( 'active-m' );
            $( this ).addClass( 'active-m' );
            self._showPicBanner( );
            while ( cont.children( ).length > ind ) {
                cont.children( ':last-child' ).remove( );
            }
            if ( self.selectedVal )
                delete self.selectedVal;
            self.selectedVal = null;
            if ( self.options.materialTablesListUrl ) {
                //materialTableList
                let tCreateOpt={
                    cont:cont,
                    mainId:mainId,
                    parentId:parentId,
                    ind:ind,                   
                };
                let cacheKey='suppList'+dt.id;
                let cacheData=stones.cacheRuntime.getData(cacheKey,'materialTableList'+parentId);
                if (cacheData){
                    if ( _TCDEBUG ) console.log('table content from cache');
                    tCreateOpt.data=cacheData;
                    stones.cacheRuntime.setData(cacheKey,cacheData,'materialTableList'+parentId);
                    self._onMaterialItemClickProceed.call(self,tCreateOpt);                    
                }else{
                    if ( self.options.bigLoaderPicUrl ) {
                        var fw = 0;
                        $.each( cont.children( ), function ( ) {
                            fw += $( this ).innerWidth( );
                        } );
                        var left = fw + ( cont.width( ) - fw ) / 2;
                        loader = $.fn.creatTag( 'img', {
                            src: self.options.bigLoaderPicUrl
                        } );
                        cont.append( loader );
                        loader.ready( function ( ) {
                            loader.css( {
                                'left': ( left - loader.width( ) / 2 ) + 'px',
                                'top': ( cont.height( ) / 2 - loader.height( ) / 2 ) + 'px'
                            } );
                        } );
                    }
                    $.post( self.options.materialTablesListUrl, dt, function ( data ) {
                        stones.cacheRuntime.setData(cacheKey,data,'materialTableList'+parentId);
                        tCreateOpt.data=data;
                        tCreateOpt.loader=loader;
                        self._onMaterialItemClickProceed.call(self,tCreateOpt);
                    } ).fail( function ( answ ) {
                        new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                        console.error( answ );
                    } );
                }
            }
        },
    } );
}( jQuery ) );