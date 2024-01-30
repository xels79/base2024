/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Просмотр и управление Материалы для производства
 */

var material_to_order = {
    _rSpan: 0,
    _isOdd: false,
    _oddColor: '#EEEEEE',
    options: {
        changeMaterialStateUrl: null,
        isProduction: false,
        changeRowUrl: null
    },
    _create: function () {
        this._editCollName = 'stage';
        this._super();

        $.extend( this.options.otherRequestOptions, this.options.otherRequestOptions, {isproduct: this.options.isProduction} );
        var uri = new URI( window.location.href );
        var params = URI.parseQuery( uri.query() );
        if ( params.page )
            this._requstDate( this._firstStart, {page: params.page} );
        else
            this._requstDate( this._firstStart );
    },
    _checkForRSpan: function ( row ) {
        var self = this;
        this._rSpan = 0;
        if ( this._list ) {
            $.each( row, function ( key, val ) {
                if ( $.isArray( this ) ) {
                    if ( this.length > self._rSpan )
                        self._rSpan = this.length;
                } else if ( $.type( this ) === 'object' ) {
                    var kCount = Object.keys( this ).length;
                    if ( kCount > self._rSpan )
                        self._rSpan = kCount;
                }
            } );
        }
    },
    _firstStart: function ( answ, onEnd ) { //Вывод первых колонок в пустую таблицу
        this.element.find( '.bunn' ).remove();
        if ( answ.colOptions ) {
            if ( answ.attention )
                this._attention = answ.attention;
            this._drawCols();

            this._drawContent();
            this._drawFooter();
            if ( $.type( onEnd ) === 'array' && onEnd.length > 1 && $.isFunction( onEnd[1] ) ) {
                onEnd[1].call( onEnd[0] );
            }

        } else
            console.warn( 'materialToOrder._firstStart()', 'Сервер не передал параметры колонок' );
    },
    _secondStart: function ( answ, onEnd ) { //Вывод следующей стр
        this.element.find( '.bunn' ).remove();
        if ( answ.colOptions ) {
            if ( answ.attention )
                this._attention = answ.attention;
            this._showAttentionColumn = answ.colOptions.showAttentionColumn ? true : false;
            this._drawContent();
            this._drawFooter();
            if ( $.type( onEnd ) === 'array' && onEnd.length > 1 && $.isFunction( onEnd[1] ) ) {
                onEnd[1].call( onEnd[0] );
            }
        } else
            console.warn( 'zakazListController._secondStart', 'Сервер не передал параметры колонок' );
    },

    _generateTD: function ( key, val, id ) {
        var td = $( '<div class="resize-cell">' ), ht = 14;
//        if ( !this._isOdd )
//            td.css( 'background-color', this._oddColor );
        if ( key === 'material_ordered' || key === 'material_delivery' ) {
            console.log( $.type( val ), '"' + val + '"', val );
            if ( val ) {
                td.append( $( '<svg fill="#000000" height="' + ht + '" viewBox="0 0 24 24" width="' + ht + '" xmlns="http://www.w3.org/2000/svg">'
                        + '<path d="M0 0h24v24H0z" fill="none"/>'
                        + '<path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>'
                        + '</svg>' ) ).attr( {
                    title: val
                } );
            } else {
                if ( key === 'material_ordered' && this.options.isProduction == '1' )
                    td.append( $( '<svg fill="#000000" height="18" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg">'
                            + '<path d="M19 13H5v-2h14v2z"/>'
                            + '<path d="M0 0h24v24H0z" fill="none"/>'
                            + '</svg>' ) );
                else
                    td.append( $( '<svg fill="#000000" height="' + ht + '" viewBox="0 0 24 24" width="' + ht + '" xmlns="http://www.w3.org/2000/svg">'
                            + '<path d="M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>'
                            + '<path d="M0 0h24v24H0z" fill="none"/>'
                            + '</svg>' ).click( {
                        self: this,
                        ht: ht,
                        key: key
                    }, this._changeOrder ) );
            }
            td.addClass('resize-center');
        } else if ( key === 'stageText' ) {
            let stage = parseInt( this._hidden[id].stage );
            stage = isNaN( stage ) ? 0 : stage;
            if ( stage == 4 || stage == 5 || stage == 6 ) {
                td.append( $( '<button>' )
                        .text( val )
                        .click( {self: this}, function ( e ) {
                            $( this ).parent().parent().attr( 'not-remove-hover', true )
                            e.data.self.__showChangeStateDialogAndDoIt( $( this ).parent().parent(), [ 3, 5, 6, 8 ] );
                        } )
                        );
            } else {
                td.text( val );
            }
            if ( this._fieldParams && this._fieldParams.colors && $.isArray( this._fieldParams.colors.stage ) && this._hidden[id] ) {
                var color = this._fieldParams.colors.stage[parseInt( this._hidden[id].stage )];
                if ( color ) {
                    td.css( 'background-color', color );
                }
            }

        } else {
            td.text( val );
        }
        return td;
    },
    _changeOrder: function ( e ) {
        let self = e.data.self, ht = e.data.ht;
        let p = $( this ).parent();
        let d = $( '<div>' ).addClass( 'input-group' );
        let inp = $( '<input>' ).addClass( 'form-control' ).attr( 'type', 'text' );
        let dt = new Date();
        $.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );
        d.append( $( '<span>' ).addClass( 'input-group-addon' ).text( 'Материал ' + ( e.data.key === 'material_ordered' ? 'заказан' : 'получен' ) ) );
        d.append( inp );
        inp.datepicker( {
            beforeShow: function () {
                console.log( 'Opening', $( '.ui-datepicker' ) );
                $( '.ui-datepicker' ).addClass( 'material-datepicker' );
            },
            changeYear: true,
            changeMonth : true,
            gotoCurrent: true,
            showOtherMonths: true,
            defaultDate: dt
        } );
        inp.val( dt.getDate() + '.' + ( dt.getMonth() + 1 ) + '.' + dt.getFullYear() );
        m_alert( 'Изменить состояние по материалу в заказе №' + p.parent().attr( 'data-key' ) + ' ', d, function () {
            if ( self.options.changeMaterialStateUrl ) {
                let opt = {id: p.attr( 'data-key' )};
                opt[e.data.key] = inp.val();
                $.post( self.options.changeMaterialStateUrl, opt ).done( function ( answ ) {
                    console.log( answ );
                    if ( answ.status === 'ok' ) {
                        self.update();
                    } else {
                        $.fn.dropInfo( 'Ошибка сервера: ' + answ.errorText, 'danger' );
                        m_alert( 'Ошибка сервера', answ.errorText );
                    }
                } ).fail( function ( jqXHR, textStatus, errorThrown ) {
                    m_alert( 'Ошибка сервера', jqXHR.responseText );
                } );
            } else {
                $.fn.dropInfo( 'Ошибка: не указан changeMaterialStateUrl', 'danger' );
            }
        }, true );
    },
    _generateDateRow: function ( dt, hidden ) { //Новый ряд dt=Содержимое
        let cnt = this._tHeader.children( ':first-child' ).children().length;
        let self = this;//, nxtR = null;
        let newCnt = 1, rVal = $( '<div class="resize-row">' )
                .attr( 'data-key', dt.id );
        let fldOpt=this._allFieldsWidthByName();
        self._isOdd = !self._isOdd;
        //self._checkForRSpan( dt );
        
        $.each( dt, function ( key, val ) {
            
            if ( newCnt <= cnt ) {
//                if ( self._rSpan > 1 ) {
                    if ( $.isArray( val ) || $.type( val ) === 'object' ) {
                        let td;
                        let mainTd=$( '<div class="resize-cell">' );//self._generateTD( key, val[i], dt.id );//;
                        if ( $.isArray( val ) ) {
                            if (val.length>1){
                                rVal.addClass('resize-has-sub-cell');
                                for ( let i = 0; i < val.length; i++ ) {
                                    td = self._generateTD( key, val[i], dt.id );
                                    td.addClass('resize-sub-cell')
                                    //nxtR[i - 1].append( td ).attr( 'data-key', dt.id );
                                    mainTd.append( td ).attr( 'data-key', dt.id );
                                }
                            }else{
                                mainTd = self._generateTD( key, val[0], dt.id );
                            }
                        } else {
                            let keys = Object.keys( val );
                            if (keys.length>1){
                                rVal.addClass('resize-has-sub-cell');
                                for ( let i = 0; i < keys.length; i++ ) {
                                    td = self._generateTD( key, val[keys[i]], dt.id, keys[i] );
                                    td.addClass('resize-sub-cell')
                                    td.attr( 'data-key', keys[i] );
                                    //nxtR[i - 1].append( td ).attr( 'data-key', dt.id );
                                    mainTd.append( td ).attr( 'data-key', dt.id );
                                }
                            }else{
                                mainTd = self._generateTD( key, val[keys[0]], dt.id ).attr( 'data-key', keys[0] );
                            }
//                            td = self._generateTD( key, val[keys[0]], dt.id, keys[0] );
//                            td.attr( 'data-key', keys[0] );
                        }
                        mainTd.css({
                            'max-width': fldOpt[key]+'px',
                            'min-width': fldOpt[key]+'px',
                            'width': fldOpt[key]+'px'
                        });

                        rVal.append(mainTd);
                        //rVal.append( td );
                    } else {
                        td = self._generateTD( key, val, dt.id );
                        td.css({
                            'max-width': fldOpt[key]+'px',
                            'min-width': fldOpt[key]+'px',
                            'width': fldOpt[key]+'px'
                        });

//                        if ( newCnt === cnt - 1 ) {//Устоновить последнюю колонку
//                            td.addClass( 'last' );
//                            //td.attr( 'rowspan', self._rSpan );
//                            td.removeAttr( 'style' );
//                        } //else
                            //td.attr( 'rowspan', self._rSpan );
                        rVal.append( td );
                    }
//                } else {
//                    let td;
//                    if ( $.isArray( val ) || $.type( val ) === 'object' )
//                        if ( $.isArray( val ) )
//                            td = self._generateTD( key, val[0], dt.id );
//                        else {
//                            td = self._generateTD( key, val[Object.keys( val )[0]], dt.id, Object.keys( val )[0] );
//                            td.attr( 'data-key', Object.keys( val )[0] );
//                        }
//                    else
//                        td = self._generateTD( key, val, dt.id );
//                    td.css({
//                        'max-width': fldOpt[key]+'px',
//                        'min-width': fldOpt[key]+'px',
//                        'width': fldOpt[key]+'px'
//                    });
//
////                    if ( newCnt === cnt - 1 ) { //Устоновить последнюю колонку
////                        td.addClass( 'last' );
////                        td.removeAttr( 'style' );
////                    }
//                    rVal.append( td );
//                }
            }
            newCnt++;
        } );
       // if ( self._rSpan > 1 )
//            rVal.append( $( '<td>' ).attr( 'rowspan', self._rSpan ) );
//        else
//        rVal.append( $( '<div class="resize-cell">' ).css({
//                        'max-width': fldOpt.empt+'px',
//                        'min-width': fldOpt.empt+'px',
//                        'width': fldOpt.empt+'px'
//                    }) );
        //bcGnd
        self._tBody.append( rVal );
        rVal.mouseenter( {self: self}, self._mEnter );
        rVal.mouseleave( {self: self}, self._mLeave );
//        if ( nxtR ) {
//            $.each( nxtR, function () {
//                self.element.append( this );
//                this.mouseenter( {self: self}, self._mEnter );
//                this.mouseleave( {self: self}, self._mLeave );
//            } );
//        }
        if ( this.options.canEditOtherOrder || this._hidden[dt.id].ourmanager_id == this.options.userId )
            rVal.contextmenu( self._rightClick );
        else
            rVal.contextmenu( function ( e ) {
                e.preventDefault();
                new dropDown( {
                    posX: e.clientX,
                    posY: e.clientY,
                    items: [
                        {
                            label: 'Нельзя копатся в чужом заказе',
                        }
                    ],
                } );

            } );
    },
    _rightClick: function ( e ) {
        e.preventDefault();
        var zID = parseInt( $( this ).attr( 'data-key' ) );
        zID = !isNaN( zID ) ? zID : -1;
        new dropDown( {
            posX: e.clientX,
            posY: e.clientY,
            items: [
                {
                    label: 'Изменить заказ',
                    click: function ( e ) {
                        console.log( 'zakaz-MTOORDER', zID );
                        var d = $( '#zakaz_dialog' );
                        if ( !d.length ) {
                            d = $.fn.creatTag( 'div', {
                                id: 'zakaz_dialog'
                            } );
                            $( 'body' ).append( d );
                            d.zakazAddEditController( $.extend( {}, window.def_opt, {z_id: zID, close: function () {
                                    $( window.dialogSelector ).zakazAddEditController( 'destroy' );
                                    $( window.dialogSelector ).remove();
                                    //self._updateRaw(id);
                                }} ) );
                        }
                        if ( !d.zakazAddEditController( 'isOpen' ) )
                            d.zakazAddEditController( 'open' );

                    }
                }
            ],
            beforeClose: function () {
            }
        } );
    },
    _findFirstRowByKeyVal: function ( key ) {
        var els = this.element.children( '.resize-tbody' ).children( '[data-key=' + key + ']' );
        if ( els.length )
            return els.get( 0 );
        else
            return null;
    },

};
( function ( $ ) {
    $.widget( "custom.materialToOrder", $.custom.resizebleTable, $.extend( {}, material_to_order, showChangeStateDialogAndDoIt ) );
}( jQuery ) );

