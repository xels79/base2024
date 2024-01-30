/*
 * colResizable - Изменяемый тип колонок
 * работает на jQuery
 */
if ( typeof Array.isArray === 'undefined' ) {
    Array.isArray = function ( obj ) {
        return Object.prototype.toString.call( obj ) === '[object Array]';
    };
}
let colResizableXELS = {
    _tr: null,
    _under: null,
    options: {
        ignoreByIndex: [ ],
        ignoreByClass: '',
        ignoreByAttr: '',
        ignoreLast: false,
        ignoreAfterClass: '',
        minWidth: 20, //не меньше 18
        onResize: null
    },
    _create: function () {
        this._super();
        this.browser = NEWFUNCTION.browser();
        if ( this.element.children( 'tbody' ).length ) {
            if ( this.element.children( 'tbody' ).children( 'tr:first-child' ).length ) {
                this._tr = this.element.children( 'tbody' ).children( 'tr:first-child' );
            } else {
                console.warn( 'colResizableXELS - таблица пуста!' );
            }
        } else {
            if ( this.element.children( 'tr:first-child' ).length ) {
                this._tr = this.element.children( 'tr:first-child' );
            } else {
                console.warn( 'colResizableXELS - таблица пуста!' );
            }
        }
        this.element.addClass( "col-resizeble" );
        this.options.minWidth = this.options.minWidth < 20 ? 20 : this.options.minWidth;
        this.options.flagClass = this.options.flagClass || 'colResize';
        console.log( this.browser );
        if ( this._tr )
            this._initAll();
        this.element.on( 'change', function ( e ) {
            console.log( 'colResizableXELS', this.element.children( 'tbody' ).children().length );
        } );
    },
    update: function () {
        this._initAll( false );
    },
    _initAll: function ( firstStart ) {
        let cols = this._tr.children();
        let cnt = cols.length;
        let doNext = true;
        firstStart = typeof ( firstStart ) === 'undefined' ? true : false;
        if ( cnt ) {
            for ( let i = 0; i < cnt; i++ ) {
                let el = $( cols.get( i ) );
                if ( doNext ) {
                    this._setColSize( el, 0 );
                } else {
                    el.removeAttr( 'style' );
                }
                if ( doNext && ( ( Array.isArray( this.options.ignoreByIndex ) && this.options.ignoreByIndex.indexOf( i ) === -1 )
                        || typeof ( this.options.ignoreByIndex ) === 'number' && i !== this.options.ignoreByIndex )
                        && ( !this.options.ignoreByClass || !el.hasClass( this.options.ignoreByClass ) )
                        && ( !this.options.ignoreByAttr || el.attr( this.options.ignoreByAttr ) === 'undefined' )
                        && ( this.options.ignoreLast === false || i < cnt - 1 ) ) {
                    el.addClass( this.options.flagClass )
                    doNext = !this.options.ignoreAfterClass || !el.hasClass( this.options.ignoreAfterClass );
                    if ( firstStart ) {
                        el.mousedown( {self: this}, this._mDown );
                        $( 'body' ).mouseup( {self: this}, this._mUp );
                        el.mousemove( {self: this}, this._mMoveFirstLevel );
                        el.mouseleave( {self: this}, this._mLeave );
                    }
                }
            }
            if ( firstStart ) {
                $( 'body' ).mousemove( {self: this}, this._mMoveTwoLevel );
            }
        } else {
            console.warn( 'colResizableXELS - первый ряд не содержит колонок!' );
        }
    },
    _mLeave: function ( e ) {
        let self = e.data.self;
        if ( !self._under )
            $( this ).css( 'cursor', 'default' );
    },
    _mUp: function ( e ) {
        let self = e.data.self;
        if ( self._under ) {
            self._under.css( 'border-color', '' );
            self._under.css( 'cursor', 'default' );
            if ( typeof ( self.options.onResize ) === 'function' ) {
                self.options.onResize.call( this );
            }
            self._under = null;
        }
    },
    _mDown: function ( e ) {
        let self = e.data.self;
        if ( self._under ) {
            self._under.css( 'border-color', '' );
            self._under.css( 'cursor', 'default' );
            self._under = null;
        }
        if ( self._checkZone( e, this ) ) {
            self._under = $( this );
            self._under.css( 'border-color', 'red' );
            e.preventDefault();
        }
    },
    _mMoveFirstLevel: function ( e ) {
        let self = e.data.self;
        if ( !self._under ) {
            if ( self._checkZone( e, this ) ) {
                $( this ).css( 'cursor', 'col-resize' );
            } else {
                $( this ).css( 'cursor', 'default' );
            }
        } else {
            e.preventDefault();
        }
    },
    _setColSize: function ( el, moveSz ) {
        let browser = this.browser, nW = true || browser === 'Firefox' ? ( el.outerWidth( ) + moveSz ) : ( el.width( ) + moveSz );
        let ind = el.index();
        el.parent().parent().children().each( ( key, val ) => {
            let curEl = $( val ).children( ':nth-child(' + ( ind + 1 ) + ')' );
            curEl.width( nW ).css( {
                'max-width': nW + 'px',
            } );
            if ( browser !== 'Fierfox' ) {
                curEl.css( {
                    'max-width': nW + 'px',
                    'min-width': nW + 'px',
                    width: nW + 'px'
                } );
            }
        } );
    },
    _mMoveTwoLevel: function ( e ) {
        let self = e.data.self;
        if ( self._under ) {
            e.preventDefault();
            let moveSz = e.pageX - ( self._under.offset().left + self._under.width() );
            if ( self.browser === 'Firefox' ) {
                moveSz = e.pageX - ( self._under.offset().left + self._under.width() );
            }
            let hasCl = self._under.next( ).hasClass( self.options.flagClass );
            if ( ( moveSz >= 0 && ( self._under.next( ).width( ) - moveSz ) > self.options.minWidth )
                    || ( moveSz < 0 && ( self._under.width( ) - Math.abs( moveSz ) ) > self.options.minWidth ) ) {
                self._setColSize( self._under, moveSz );
                if ( hasCl ) {
                    self._setColSize( self._under.next( ), -1 * ( this.browser !== 'Firefox' ? moveSz : ( moveSz > 0 ? ( moveSz - 1 ) : ( moveSz + 1 ) ) ) );
                }
            } else {
            }
        }
    },
    _checkZone: function ( e, el ) {
        let $el = $( el );
        return e.pageX > $el.offset().left + $el.outerWidth() - 15 && e.pageX < $el.offset().left + $el.outerWidth() + 5
    },
};

( function ( $ ) {
    $.widget( "custom.colResizableXELS", $.Widget, colResizableXELS );
}( jQuery ) );

