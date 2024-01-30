/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


let disainer_zakaz_controller = {
    options: {
        viewRowUrl: null,
        changeRowUrl: null,
        canChange: [ 1, 2, 6 ],
        canChangeTo: [ 4, 5, 9 ],
        isProizvodstvo: false,
        isAdmin: false,
    },
    _create: function () {
        this._editCollName = 'stage';
        this._super();
        this.options.isDisainer = true;
        console.log( 'disainer_zakaz_controller', this );
//        if (this.options.isProizvodstvo){
//            $.fn.includeJS('/js/pechatnics.js',null,function(dt){
//                let t=new Function ([],dt);
//                console.log('pechatnics',t);
//                tmp=t.call();
//                $.extend(this,t.call());
//                this.test();
//            });
//        }
    },
    _doubleClick: function ( e ) {
        let self = e.data.self, id = $( this ).attr( 'data-key' );
        if ( !self._zakazDialog ) {
            self._zakazDialog = $( '<div>' ).attr( 'id', 'zakaz-disayner-' + id ).appendTo( $( 'body' ) );
            self._zakazDialog.zakazAddEditController( $.extend( {}, window.def_opt, {
                z_id: id,
                isDisainer: true,
                viewRowUrl: self.options.viewRowUrl,
                parent: self,
                isProizvodstvo: self.options.isProizvodstvo,
                close: function () {
                    self._zakazDialog.zakazAddEditController( 'destroy' );
                    self._zakazDialog.remove();

                    delete ( self._zakazDialog );
                    self._zakazDialog = null;
                    console.log( '_doubleClick', self._zakazDialog );
                }} ) );
        } else {
            m_alert( 'Ошибка', 'Нельзя открыть обновремнно два заказа.', true, false );
        }
        console.log( '_doubleClick', self._zakazDialog );
    },
    _creatCanChangetTo: function ( stage ) {
        let rVal = this.options.canChangeTo;
        if ( this.options.isProizvodstvo ) {
            switch ( stage ) {
                case 4:
                    rVal = [ 6, 3, 7, 8, 9 ];
                    break;
                case 5:
                    rVal = [ 7, 8, 9 ];
                    break;
            }
        } else {
            switch ( stage ) {
                case 2:
                    rVal = [ 3, 4, 5, 7, 8, 9 ];
                    break;
                case 6:
                    rVal = [ 7, 9 ];
                    break;
            }
        }
        return rVal;
    },
    _generateTD: function ( k, v, id ) {
        let rVal = $( '<div class="resize-cell">' );

        if ( k == 'stageText' ) {
            console.log( k, this._hidden[id].stage );
            let stage = parseInt( this._hidden[id].stage );
            stage = isNaN( stage ) ? 0 : stage;
            if ( $.inArray( stage, this.options.canChange ) > -1 && stage !== 8 ) {
                rVal.addClass('resize-center');
                rVal.append( $( '<button>' )
                        .text( v )
                        .click( {self: this}, function ( e ) {
                            $( this ).parent().parent().attr( 'not-remove-hover', true )
                            e.data.self.__showChangeStateDialogAndDoIt( $( this ).parent().parent(), e.data.self._creatCanChangetTo( stage ) );
                        } )
                        );
            } else {
                rVal.text( v );
            }
        } else
            rVal.text( v );
        return rVal;
    },
    _rightClick: function ( e ) {
        let zID = parseInt( $( this ).parent().attr( 'data-key' ) ), el = $( this ).parent().get( 0 );
        zID = !isNaN( zID ) ? zID : -1;
        e.preventDefault();
        e.stopPropagation();
        let tmp = [ {
                label: 'Открыть',
                click: function () {
                    e.data.self._doubleClick.call( el, e );
                },
            } ];
        if ( zID > 0 ) {
            let stage = parseInt( e.data.self._hidden[zID].stage );
            if ( $.inArray( stage, e.data.self.options.canChange ) > -1 ) {
                tmp[tmp.length] = {
                    label: 'Изменить этап работы',
                    click: function () {
                        e.data.self.__showChangeStateDialogAndDoIt.call( e.data.self, el, e.data.self._creatCanChangetTo( stage ) );
                    },
                };
                tmp[tmp.length] = {
                    label: 'Пометить как заказ с ошибкой',
                    click: function () {
                        e.data.self._changeStateToErrorClick.call( e.data.self, el );
                    },
                };
            }
        }
        let opt = [ {header: 'Заказ №' + zID}, 'separator' ];
        $.each( tmp, function ( key, val ) {
            opt[opt.length] = val;
        } );
        $.each( e.data.self.__rightClickOptions( zID, parseInt( e.data.self._hidden[zID].stage ) ), function ( key, val ) {
            opt[opt.length] = val;
        } );
        new dropDown( {
            posX: e.clientX,
            posY: e.clientY,
            items: opt,
            beforeClose: function ( e2 ) {
                $( el ).children( '.hand' ).removeClass( 'dirt-hover' );
                $( el ).children( '[style]' ).css( 'color', 'inherit' );
                e.data.self._doMouseLeave = true;
            },
            afterInit: function ( e2 ) {
                e.data.self._doMouseLeave = false;
            }
        } );
    },
    _changeStateToErrorClick: function ( row ) {
        let id = parseInt( $( row ).attr( 'data-key' ) ), self = this;
        id = !isNaN( id ) ? id : 0;
        if ( !this.options.changeRowUrl ) {
            console.warn( 'changeStateToErrorClick', 'Не задан URL запроса' );
            return;
        }
        $( row ).children( '.hand' ).addClass( 'dirt-hover' );
        m_alert( 'Внимание', 'Изменить статус заказа на "Ошибка"', function () {
            $.post( self.options.changeRowUrl, {id: id, stage: 9} ).done( function ( answ ) {
                if ( answ.status != 'ok' ) {
                    m_alert( 'Ошибка сервера', answ.errorText, true, false );
                }
                self.update();
            } );
        }, true, function () {
            $( row ).children( '.hand' ).removeClass( 'dirt-hover' );
            $( row ).children( '[style]' ).css( 'color', 'inherit' );
            $( row ).removeAttr( 'not-remove-hover' );
        } );

    },
    _changeStateClick: function ( row ) {
        this.__showChangeStateDialogAndDoIt( row, this._creatCanChangetTo( stage, parseInt( row.attr( 'data-key' ) ) ) );
    },
    tVAGCAP: function ( object ) {
        let self = this;
        console.log( object.find( '.dropdown-menu' ).find( 'a' ) );
        object.find( '.dropdown-menu' ).find( 'a' ).click( function ( e ) {
            e.preventDefault();
            console.log( this );
            let url = $( this ).attr( 'data-url' );
            let imgs = $( '.tImg-view' ), img;
            if ( imgs.children().length > 1 ) {
                imgs.children( ':first-child' ).remove();
            }
            if ( $.inArray( $( this ).attr( 'data-ext' ), [ 'doc', 'pdf', 'docx', 'ai', 'ppt', 'pptx', 'txt', 'xls', 'xlsx', 'psd', 'zip', 'rar', 'fb2' ] ) > -1 ) {
                console.log( 'rc', url );
                console.log( location.origin );
                img = ( $( '<iframe>' ).attr( {
                    src: 'https://docs.google.com/viewer?url=' + encodeURIComponent( location.origin + url ) + '&a=bi&embedded=true',
                } ) );
            } else if ( $.inArray( $( this ).attr( 'data-ext' ), [ 'img', 'bmp', 'png', 'gif', 'jpg', 'ico' ] ) > -1 ) {
                img = $( '<img>' ).attr( {
                    src: url,
                } );
            } else {
                img = $( '<h3>' ).html( 'Просмотр<br>Не поддерживается!' );
            }
            if ( imgs.children().length ) {
                imgs.children( ':first-child' ).addClass( 'img1' ).removeClass( 'img2' );
                img.addClass( 'img2' );
            }
            img.appendTo( imgs );
        } );

    },

};

( function ( $ ) {
    $.widget( "custom.disainerZakazController", $.custom.pechatnics, $.extend( {}, disainer_zakaz_controller, showChangeStateDialogAndDoIt ) );
}( jQuery ) );
