/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var m_table = {
    _oldHeight: 0,
    _oldHeight2: 0,
    _isBusy: false,
    options: {
        title: 'Материалы таблица цен',
        width: 1535,
        requestIndex: '',
        requestTable: '',
        requestUpdate: '',
        page: 0,
        pageSize: 15
    },
    _create: function () {
        var self = this;
        this._super();
        this.element.addClass( 'exel-dialog' );
        $( document ).on( 'wheel', function ( e ) {
            if ( self._isOpen && !self.isMinimized() && !self._isBusy ) {
                var el = self.element.find( 'input:focus' );
                if ( el.length ) {
                    if ( e.originalEvent.deltaY < 0 ) {
                        self._moveUp( el.get( 0 ) );
                    } else {
                        self._moveDown( el.get( 0 ) );
                    }
                }
            }
        } );
    },
    open: function () {
        this._super();
        this._requestIndex();
    },
    _requestIndex: function () {
        var self = this;
        if ( !this.options.requestIndex ) {
            console.error( 'materialTable не указан requestIndex' );
        }
        $.post( this.options.requestIndex ).done( function ( dt ) {
            self.element.empty();
            self.element.html( dt );
            self._requestTable();
        } );
    },
    _update: function ( el ) {
        let self = this, index = 5 - ( $( el ).parent().parent().children().length - $( el ).parent().index() );
        if ( this.options.requestUpdate ) {
            $( el ).attr( 'disabled', true );
            let data = {
                id: parseInt( $( el ).parent().parent().attr( 'data-key' ) )
            };
            data[index == 2 ? 'optfrom' : ( index == 3 ? 'optcoast' : ( index == 4 ? 'recomendetcoast' :'coast' ) )] = $( el ).val();
            console.log( 'try update index:', index );
            console.log( 'data:', data );
            $.post( this.options.requestUpdate, data ).done( function ( dt ) {
                if ( dt.status === 'ok' ) {
                    $( el ).removeAttr( 'disabled' );
                    if ( index != 2 ) {
                        $( el ).parent().parent().children( ':last-child' ).prev().prev().prev().prev().prev().text( dt.coast_rub_opt ? ( dt.coast_rub + '/' + dt.coast_rub_opt ) : dt.coast_rub );
                    }
                    console.log( dt );
                } else {
                    m_alert( 'Ошибка', dt.errorText, true, false );
                    console.log( dt );
                }
            } );
        } else {
            console.error( 'materialTable _update: не передан requestUpdate' );
        }
    },
    update: function () {
        this._requestTable( this.options.page, this._filterReadParams() );
    },
    _moveUp: function ( inp, fromEnd ) {
        let index = $( inp ).parent().index(), self = this;
        let el = $( inp ).parent().parent().prev();
        fromEnd = fromEnd || false;
        if ( this._isBusy )
            return false;
        if ( !el.length ) {
            let p = $( '.pagination' ).children( '.active' ).prev().children( 'a' );
            if ( p.length ) {
                self._requestTable( parseInt( p.attr( 'data-page' ) ), self._filterReadParams(), -1 );
                return true;
            } else {
                //el=$(inp).parent().parent().parent().children().last();
                return false;
            }
        }
        if ( el.length ) {
            if ( !fromEnd ) {
                el.children( ':nth-child(' + ( index + 1 ) + ')' ).children( 'input' ).trigger( 'focus' );
            } else {
                el.children( ':last-child' ).children( 'input' ).trigger( 'focus' );
            }
            return true;
        }
    },
    _moveDown: function ( inp, fromStart ) {
        let index = $( inp ).parent().index(), self = this;
        let el = $( inp ).parent().parent().next();
        fromStart = fromStart || false;
        if ( this._isBusy )
            return false;
        if ( !el.length ) {
            var p = $( '.pagination' ).children( '.active' ).next().children( 'a' );
            if ( p.length ) {
                self._requestTable( parseInt( p.attr( 'data-page' ) ), self._filterReadParams(), 1 );
                return true;
            } else {
                return false;
            }
        }
        if ( el.length ) {
            if ( !fromStart )
                el.children( ':nth-child(' + ( index + 1 ) + ')' ).children( 'input' ).trigger( 'focus' );
            else
                el.children( ':last-child' ).prev().prev().prev().children( 'input' ).trigger( 'focus' );
            return true;
        }
    },
    _moveRight: function ( inp ) {
        let el = $( inp ).parent().next();
        if ( this._isBusy )
            return false;
        if ( !el.length ) {
            return this._moveDown( inp, true );
        } else {
            el.children( 'input:first-child' ).trigger( 'focus' );
            return true;
        }
    },
    _moveLeft: function ( inp ) {
        let el = $( inp ).parent().prev();
        if ( this._isBusy )
            return false;
        if ( el.length && el.children( 'input:first-child' ).length ) {
            el.children( 'input:first-child' ).trigger( 'focus' );
            return true;
        } else {
            return this._moveUp( inp, true );
        }
    },
    _requestTable: function ( page, otherParams, setFocusTo, isPagination ) {
        let self = this;
        let oldVal;
        let inputHasFocus = false;
        isPagination = isPagination || false;
        setFocusTo = setFocusTo ? setFocusTo : 0;
        otherParams = otherParams ? otherParams : {};
        page = $.type( page ) === 'undefined' || isNaN( page ) ? this.options.page : page;
        this.options.page = page;
        if ( !this.options.requestTable ) {
            console.error( 'materialTable не указан requestTable' );
        }
        this._isBusy = true;
        this.showBunner();
        $.post( this.options.requestTable, $.extend( {}, {page: page, pageSize: this.options.pageSize}, otherParams ) ).done( function ( dt ) {
            $( '#ex-greed' ).empty().html( dt.content );
            $( '#ex-greed' ).find( '[data-page]' ).click( {self: self}, self._paginationClick );
            $( '#ex-greed' ).children( '.grid-view' ).children( 'table' ).children( 'tbody' ).children( 'tr' ).click( function ( e ) {
                e.preventDefault();
                if ( !inputHasFocus || $( inputHasFocus ).parent().parent().index() != $( this ).index() ) {
                    $( this ).children( 'td:last-child' ).prev().prev().prev().children( 'input' ).trigger( 'focus' );
                } else if ( inputHasFocus ) {
                    $( inputHasFocus ).trigger( 'focus' );
                }
            } );
            $( '#ex-greed' ).find( 'input' ).onlyNumeric( {allowPoint: true} )
                    .focus( function () {
                        $( this ).get( 0 ).setSelectionRange( 0, $( this ).val().length );
                        $( this ).parent().parent().addClass( 'active' );
                        oldVal = $( this ).val();
                        inputHasFocus = this;
                    } )
                    .focusout( function () {
                        $( this ).parent().parent().removeClass( 'active' );
                        //inputHasFocus = false;
                        if ( oldVal !== $( this ).val() ) {
                            self._update.call( self, this );
                        }
                    } )
                    .keydown( {self: this}, function ( e ) {
                        var k = e.key, index = $( this ).parent().index();
                        console.log( k );
                        if ( k == 'Enter' || k == 'Tab' ) {
                            if ( self._moveRight( this ) ) {
                                e.preventDefault();
                            }
                        } else if ( k == 'ArrowDown' ) {
                            e.preventDefault();
                            self._moveDown( this );
                        } else if ( k == 'ArrowUp' ) {
                            e.preventDefault();
                            self._moveUp( this );
                        } else if ( k == 'Escape' ) {
                            if ( $.type( oldVal ) !== 'undefined' )
                                $( this ).val( oldVal ).trigger( 'focus' );
                        } else if ( k == 'ArrowRight' ) {
                            if ( self._moveRight( this ) ) {
                                e.preventDefault();
                            }
                        } else if ( k == 'ArrowLeft' ) {
                            if ( self._moveLeft( this ) ) {
                                e.preventDefault();
                            }
                        }

                    } );
            $( '#ex-greed' ).find( '[name]' ).change( {self: self}, self._filterProceed );

            $( '.summary' ).append( $( '<span>' )
                    .addClass( 'glyphicon glyphicon-refresh' )
                    .css( {
                        'float': 'right',
                        color: '#0E0',
                        padding: '4px',
                        margin: '3px',
                        'font-size': '1.1em'
                    } )
                    .mouseenter( function () {
                        $( this ).css( 'background-color', '#DDD' );
                    } )
                    .mouseleave( function () {
                        $( this ).css( 'background-color', 'transparent' );
                    } )
                    .click( function () {
                        self.update();
                    } )
                    );
            $( '.Svodka' ).each( function () {
                var w1 = $( this ).width(), w2 = $( this ).children( 'span' ).width();
                if ( w2 > w1 ) {
                    $( this ).children( 'span:first-child' ).mouseenter( {self: self}, self._hoverLong );
                }
            } );
            var newTop = 0;
            if ( self._oldHeight < self.element.parent().height() ) {
                self._oldHeight = self.element.parent().height() + self.element.find( '.pagination' ).height() * 2;
                self._oldHeight2 = self.element.height() + self.element.find( '.pagination' ).height() * 2;
                newTop = $( window ).height() / 2 - self.element.parent().height() / 2;
                if ( newTop < 0 )
                    newTop = 0;
                console.log( self.element.find( '.pagination' ).height() );
                self.element.parent().css( 'top', newTop );
                self.element.css( 'height', self._oldHeight2 );
            } else {
                self.element.css( 'height', self._oldHeight2 );
            }
            self.hideBunner();
            if ( setFocusTo ) {
                if ( setFocusTo > 0 ) {
                    $( '#ex-greed' ).children( '.grid-view' ).children( 'table' ).children( 'tbody' ).children( 'tr:first-child' ).trigger( 'click' );
                } else {
                    $( '#ex-greed' ).children( '.grid-view' ).children( 'table' ).children( 'tbody' ).children( 'tr:last-child' ).children( ':last-child' ).children().trigger( 'focus' );
                }
            }
            self._isBusy = false;
        } );
    },
    _hoverLong_timer: 0,
    _hoverLong_el: null,
    _hoverLong: function ( e ) {
        var self = e.data.self, el = $( this );
        el.parent().css( 'color', '#999' );
        if ( self._hoverLong_timer ) {
            clearTimeout( self._hoverLong_timer );
            self._hoverLong_timer = 0;
        }
        el.on( 'mouseout', function () {
            if ( self._hoverLong_timer ) {
                clearTimeout( self._hoverLong_timer );
                self._hoverLong_timer = 0;
            }
            if ( self._hoverLong_el )
                self._hoverLong_el.remove();
            el.parent().removeAttr( 'style' );
            el.off( 'mouseout' );
        } );
        self._hoverLong_timer = setTimeout( function () {
            el.off( 'mouseout' );
            el.parent().removeAttr( 'style' );
            self._hoverLong_timer = 0;
            if ( self._hoverLong_el )
                self._hoverLong_el.remove();
            var pos = el.offset();
            pos = {top: pos.top - 3, left: pos.left - 19};
            self._hoverLong_el = el.clone();
            self._hoverLong_el.css( {
                position: 'absolute',
                'z-index': 100000,
                'background-color': '#EEE',
                'border': '1px solid',
                'line-height': 1.42857143,
                'font-size': '1.1em',
                'padding-left': '5px',
                'padding-right': '5px'
            } )
                    .css( pos )
                    .mouseout( function () {
                        $( this ).remove();
                        self._hoverLong_el = null;
                    } )
                    .click( function () {
                        el.parent().parent().trigger( 'click' );
                    } )
                    .appendTo( 'body' );
        }, 500 );
    },
    _paginationClick: function ( e ) {
        e.preventDefault();
        if ( e.data.self._isBusy )
            return;
        e.data.self._requestTable( parseInt( $( this ).attr( 'data-page' ) ), e.data.self._filterReadParams(),0 , true );
    },
    _proceedName: function ( val ) {
        var rVal = {key: '', subKey: ''};
        var brckt = val.indexOf( '[' );
        if ( brckt > -1 ) {
            rVal.key = val.substr( 0, brckt );
            rVal.subKey = val.substr( brckt + 1, val.length - 2 - rVal.key.length );
        }
        return rVal;
    },
    _filterReadParams: function () {
        var rVal = {}, self = this;
        this.element.find( '[name]' ).each( function () {
            var keys = self._proceedName( $( this ).attr( 'name' ) );
            if ( $.type( rVal[keys.key] ) === 'undefined' ) {
                rVal[keys.key] = {};
            }
            rVal[keys.key][keys.subKey] = $( this ).val();
        } );
        return rVal;
    },
    _filterProceed: function ( e ) {
        var self = e.data.self;
        var otherParam = {};
        self._requestTable( 0, self._filterReadParams() );
    }

};

( function ( $ ) {
    $.widget( "custom.materialTable", $.custom.maindialog, $.extend( {}, m_table ) );
}( jQuery ) );