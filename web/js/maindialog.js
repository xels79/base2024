/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 *
 * minimizable  - false - запрещает сворачивать
 * resizable    - по умолчанию запретить изменение размера
 * nHeight      - высота кнопок свернутых окон
 * startW       - начальный размер кнопки свернутого окна
 * step         - зазор между кнопками свернутых ококн
 *
 */

( function ( $ ) {
    $.widget( "custom.maindialog", $.ui.dialog, {
        _bunner: null,
        _h1: null,
        _minButton: null,
        _minElement: null,
        _storedPos: {},
        _useBeforeCloseEvent: true,
        _beforeCloseFired: false,
        options: {
            minimizable: true,
            resizable: false,
            nHeight: 25,
            startW: 180,
            step: 4,
            beforeClose: null,
            afterInit: null,
            requestList: null,
            requestListParam: null,
            closeOnEscape: false
        },
        close: function () {
            if ( this._useBeforeCloseEvent && $.isFunction( this.options.beforeClose ) && !this._beforeCloseFired ) {
                this._beforeCloseFired = true;
                if ( this.options.beforeClose.call( this.element.get( 0 ), this ) !== false ) {
                    let tmp = this.options.beforeClose;
                    this.options.beforeClose = function () {
                        console.log( 'Закрываем диалог' );
                    };
                    this._super();
                    this.options.beforeClose = tmp;
                }
            } else
                this._super();
        },
        _create: function () {
            this._super();
            if ( this.options.modal )
                this.options.minimizable = false;
            console.info( 'maindialog: init', this );
            this.uiDialog.addClass( 'main-dialog' );
            this.uiDialogTitlebarClose.text( 'X' ).attr( 'title', 'Закрыть' );
            if ( this.options.minimizable )
                this._createMinimizableButton();
            this.element.bind( 'DOMNodeInserted DOMNodeRemoved', {self: this}, this._centrateBunner );
            if ( this.options.minimizable ) {
                $( window ).resize( {self: this}, function ( e ) {
                    e.data.self._clearMinimized();
                } );
            }
            this.body = this.uiDialog;
            this.header = this.uiDialogTitlebar;
        },
        isMinimized: function () {
            return this._minElement != null;
        },
        headerText: function ( txt ) {
            if ( txt ) {
                this.uiDialogTitlebar.children( '.ui-dialog-title' ).text( txt );
                return txt;
            } else
                return this.uiDialogTitlebar.children( '.ui-dialog-title' ).text();
        },
        _createMinimizableButton: function () { //Создаёт кнопку свёрнутого окна
            this._minButton = $( '<button>' )
                    .attr( {type: 'button', title: 'Свернуть'} )
                    .addClass( 'ui-dialog-titlebar-minimaze' )
                    .insertBefore( this.uiDialogTitlebarClose )
                    .text( '_' ).click( {self: this}, this._minimizeClick );
        },
        _minimizeClick: function ( e ) {
            e.data.self._minimize();
        },
        _calculateWidth: function ( scop, step ) {
            let rVal = 0;
            $.each( scop, function () {
                rVal += step + $( this ).outerWidth();
            } );
            return rVal;
        },
        _decrScop: function ( scop, step ) { //Уменьшает весь набор кнопок на step
            let rVal = 0, self = this;
            $.each( scop, function ( key, val ) {
                $( this ).width( $( this ).width() - step );
                if ( !rVal )
                    rVal = $( this ).width();
                if ( key > 0 ) {
                    let left = 0;
                    left = parseInt( $( scop[key - 1] ).css( 'left' ) );
                    left = !isNaN( left ) ? left : 0;
                    left += $( scop[key - 1] ).outerWidth() + step;
                    $( this ).css( 'left', ( left ) + 'px' );
                }
                self._compCatMode( $( this ) );
            } );
            return rVal;
        },
        _compCatMode: function ( el ) {  //Если размер текста заголовка превышает
            //размер родителя то добовляет класс "maindialog-cut"
            el = el ? el : this._minElement;
            let p = el.children( ':first-child' );
            let span = p.children( ':first-child' );
            if ( span.outerWidth( true ) > el.innerWidth( true ) ) {
                el.attr( 'title', p.text() );
                el.addClass( 'maindialog-cut' );
            } else {
                el.removeAttr( 'title' );
                el.removeClass( 'maindialog-cut' );
            }
        },
        _minimize: function () {   //Уменьшает окно
            this._clearMinimized();
            let nHeight = this.options.nHeight, startW = this.options.startW;
            let other = $( '.maindialog-tranzit' ), bottom = $( window ).height() - nHeight - 2;
            let step = this.options.step, winW = $( window ).width();
            let text = this.uiDialogTitlebar.children( '.ui-dialog-title' ).text();
            let self = this;
            if ( this._minElement ) {
                this._minElement.remove();
            }
            this._storedPos = {
                w: this.uiDialog.width(), h: this.uiDialog.height(), offset: this.uiDialogTitlebar.offset()
            },
                    this._minElement = $( '<div>' )
                    .addClass( 'maindialog-tranzit' )
                    .width( this._storedPos.w )
                    .height( this._storedPos.h )
                    .offset( this._storedPos.offset )
                    .click( {self: this}, this._restoreClick )
                    .appendTo( 'body' );
            this.uiDialog.hide();

            if ( other.length ) {
                let nLeft = this._calculateWidth( other, step );
                while ( ( winW - nLeft < startW + step ) && startW > 10 ) {
                    startW = this._decrScop( other, step );
                    nLeft = this._calculateWidth( other, step );
                }
                this._minElement.animate( {
                    width: startW + "px",
                    height: nHeight + "px",
                    top: bottom + "px",
                    left: nLeft + step
                }, 300, function () {
                    $( this ).append( $( '<p>' ).append( $( '<span>' ).text( text ) ) );
                    self._minElement.addClass( 'maindialog-minimazed' );
                    self._compCatMode();
                } );
            } else {
                this._minElement.animate( {
                    width: startW + "px",
                    height: nHeight + "px",
                    top: bottom + "px",
                    left: step
                }, 300, function () {
                    $( this ).append( $( '<p>' ).append( $( '<span>' ).text( text ) ) );
                    self._minElement.addClass( 'maindialog-minimazed' );
                    self._compCatMode();
                } );
            }
        },
        _clearMinimized: function () {
            /*
             * Убирает пустое пространство в наборе
             * кнопок, если оно есть, и вы равнивает их
             * и настраивает высоту рвсположения
             */
            let scope = $( '.maindialog-tranzit' ), startW = this.options.startW;
            let bottom = $( window ).height() - this.options.nHeight - 2, winW = $( window ).width();
            let step = this.options.step, processWidth = true;
            if ( scope.length ) {
                $.each( scope, function ( key, val ) {
                    if ( processWidth )
                        $( this ).width( startW );
                    $( this ).css( 'top', bottom );
                    let left = 0;
                    if ( !key ) {
                        left = parseInt( $( val ).css( 'left' ) );
                        if ( left > step )
                            $( val ).css( 'left', step );
                    } else {
                        left = parseInt( $( scope[key - 1] ).css( 'left' ) );
                        left += $( scope[key - 1] ).outerWidth() + step;
                        $( this ).css( 'left', ( left ) + 'px' );
                    }

                } );
                let nLeft = this._calculateWidth( scope, step );
                while ( ( winW < nLeft ) && startW > 10 ) {
                    processWidth = false;
                    startW = this._decrScop( scope, step );
                    nLeft = this._calculateWidth( scope, step );
                }
            }
        },
        restore: function () {
            /*
             * Востонавливает окно
             */
            let self = this;
            if ( this._minElement ) {
                this._minElement.empty().removeClass( 'maindialog-minimazed' );
                this.uiDialog.show( 300 );
                this._minElement.animate( {
                    width: this._storedPos.w,
                    height: this._storedPos.h,
                    left: this._storedPos.offset.left,
                    top: this._storedPos.offset.top
                }, 300, function () {
                    self._minElement.remove();
                    self._minElement = null;
                    self._clearMinimized();
                    self.moveToTop();
                } );
            }
        },
        _restoreClick: function ( e ) {
            e.data.self.restore();
        },
        _afterFirstStartComlite: function () {
            /*
             * Выравнивает положение окна по высоте
             */
            let h = this.uiDialog.outerHeight()
            let hW = $( window ).innerHeight();
            if ( hW / 2 - h / 2 > 0 ) {
                this.uiDialog.css( 'top', hW / 2 - h / 2 );
            } else {
                this.uiDialog.css( 'top', 1 );
            }
        },
        moveToCenter: function () {
            /*
             * Выравнивает окно по высоте и по центру
             */
            this._afterFirstStartComlite();
            let w = this.uiDialog.outerWidth()
            let wW = $( window ).innerWidth();
            if ( wW / 2 - w / 2 > 0 ) {
                this.uiDialog.css( 'left', wW / 2 - w / 2 );
            } else {
                this.uiDialog.css( 'left', 1 );
            }
        },
        __centrateBunner: function () {
            /*
             * Выравнивает заставку по центру окна и
             * по высоте
             */
            if ( this._bunner && this._h1 ) {
                let h = this._h1.outerHeight(), w = this._h1.outerWidth();
                let hW = this._bunner.innerHeight(), wW = this._bunner.innerWidth();
                if ( hW / 2 - h / 2 > 0 ) {
                    this._h1.css( 'top', hW / 2 - h / 2 );
                } else {
                    this._h1.css( 'top', 1 );
                }
                if ( wW / 2 - w / 2 > 0 ) {
                    this._h1.css( 'left', wW / 2 - w / 2 );
                } else {
                    this._h1.css( 'lrft', 1 );
                }
            }
        },
        _centrateBunner: function ( e ) { //Событие при изменение маштаба body
            e.data.self.__centrateBunner();
        },
        open: function () {
            this._super();
            this.__centrateBunner();
            if ( this.options.requestList ) {
                this.requestList();
            } else {
                if ( $.isFunction( this.options.afterInit ) ) {
                    this.options.afterInit.call( this.element.get( 0 ), this );
                }
            }
        },
        showBunner: function ( txt ) {
            this._showBunner( txt );
        },
        hideBunner: function () {
            this._hideBunner();
        },
        _showBunner: function ( txt ) {
            if ( !this._bunner && !this._h1 ) {
                if ( $.type( txt ) === 'object' ) {
                    this._h1 = $( '<h1>' ).empty().addClass( 'bunner-opacity' ).append( txt );
                } else {
                    this._h1 = $( '<h1>' ).empty().addClass( 'bunner-opacity' ).text( txt ? txt : 'Загрузка...' );
                }
                this._bunner = $( '<div>' ).addClass( 'bunner' ).append( this._h1 ).appendTo( this.uiDialog );
                this._bunner.contextmenu( {self: this}, function ( e ) {
                    let self = e.data.self;
                    e.preventDefault();
                    new dropDown( {
                        posX: e.clientX,
                        posY: e.clientY,
                        items: [
                            {
                                label: 'Закрыть!',
                                click: function () {
                                    self.close();
                                }
                            }
                        ]
                    } );
                } );
                this.__centrateBunner();
            } else {
                if ( $.type( txt ) === 'object' ) {
                    this._h1.empty().append( txt );
                } else {
                    this._h1.text( txt ? txt : 'Загрузка...' );
                }

            }
        },
        _hideBunner: function () {
            if ( this._bunner ) {
                this._bunner.remove();
                this._bunner = null;
                this._h1 = null;
            }
        },
        dialog: function () {
            return this;
        },
        __request: function ( url, param, afterInit ) {
            let rVal = this;
            console.debug( 'request', url, param );
            if ( !url ) {
                return;
            }
//        rVal.resize();
            rVal.showBunner();
            $.post( url, param )
                    .done( function ( data ) {
                        rVal.hideBunner();
                        console.debug( 'request:answer', data );
                        if ( $.type( data ) == 'string' ) {
                            rVal.element.empty().html( data );
                        } else {
                            if ( data.html )
                                rVal.element.empty().html( data.html );
                            else if ( data.errorText ) {
                                rVal.uiDialogTitlebar.children( '.ui-dialog-title' ).text( 'Ошибка загрузки' );
                                rVal.element.empty().html( data.errorText );
                            }
                        }
//                rVal.resize();
//                console.groupEnd();
                        if ( $.isFunction( afterInit ) ) {
                            afterInit.call( rVal.element.get( 0 ), rVal );
                        }
                        if ( $.isFunction( rVal.afterRequest ) ) {
                            rVal.afterRequest.call( rVal.element.get( 0 ), data );
                        }
                    } )
                    .fail( function ( jqXHR ) {
                        console.debug( 'request:fail:answer', jqXHR );
                        rVal.element.empty().html( jqXHR.responseText );
                        rVal.uiDialogTitlebar.children( '.ui-dialog-title' ).text( 'Ошибка загрузки' );
                        console.groupEnd();
                    } ).always( function () {
            } );
        },
        requestList: function () {
            let param = {};
            if ( $.isFunction( this.options.requestListParam ) )
                param = this.options.requestListParam.call( this, this.element.get( 0 ) );
            else
                param = this.options.requestListParam;
            this.__request( this.options.requestList, param, this.options.afterInit );
        },
        isFadedOut: null,
        fadeIn: function () {
            if ( this.isFadedOut ) {
                console.debug( 'ajaxdialog', 'fadeIn' );
                this.isFadedOut.remove();
                this.isFadedOut = false;
                $( 'body' ).removeClass( 'fadeIn' )

            }
        },
        fadeOut: function () {
            if ( this.isFadedOut === false ) {
                console.debug( 'ajaxdialog', 'fadeOut' );
                this.isFadedOut = $.fn.creatTag( 'div', {
                    'class': 'm-modal-background fadeIn',
                    'style': {'z-index': window.ajaxdialog_autoZ + 1}
                } );
                this.options.parent.append( rVal.isFadedOut );
                $( 'body' ).addClass( 'fadeIn' )
            }
        }


    } );
}( jQuery ) );
