/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var mDialog = mDialog || {
    ajaxdialog_autoID: 0,
    ajaxdialog_autoZ: 2000
};

mDialog.ajaxdialogbase = function ( opt ) {
    if ( $.type( opt ) === 'undefined' ) {
        console.error( 'ajaxdialog: Не заданы опции' );
        return null;
    } else {
        opt.options = opt.options || {};
    }
    this.options = {
        parent: opt.parent ? opt.parent : 'body',
        style: opt.style ? opt.style : {
            width: '50%',
            height: '50%',
        },
        items: opt.items ? opt.items : {},
        headerText: opt.headerText ? ( opt.headerText.substring( 0, 1 ).toUpperCase() + opt.headerText.substring( 1 ) ) : 'Не задан',
        reqImgUrl: opt.reqImgUrl || null,
    };
    if ( $.type( opt.zindex ) == 'undefined' ) {
        this.options.zindex = mDialog.ajaxdialog_autoZ;
        mDialog.ajaxdialog_autoZ_start = mDialog.ajaxdialog_autoZ;
        mDialog.ajaxdialog_autoZ++;
        this.options.decrZinde = true;
    } else {
        this.options.decrZinde = false;
        this.options.zindex = opt.zindex;
    }
    if ( typeof ( this.options.parent ) == 'string' )
        this.options.parent = $( this.options.parent );
    if ( typeof ( opt.noAjax ) === 'undefined' ) {
        if ( typeof ( opt.requestList ) === 'undefined' ) {
            console.error( 'ajaxdialog: Не задан requestList' ); //Аддрес запроса list
            return null;
        } else
            this.options.requestList = opt.requestList;
        if ( $.type( opt.requestListParam ) === 'undefined' ) {
            this.options.requestListParam = {};
        } else
            this.options.requestListParam = opt.requestListParam;
    } else {
        this.options.requestList = false;
    }

    if ( $.type( opt.id ) == 'undefined' ) {
        this.options.id = 'ad' + mDialog.ajaxdialog_autoID;
        mDialog.ajaxdialog_autoID++;
    } else
        this.options.id = opt.id;
    this.bodyContent = opt.bodyContent || null;
    this.footerContent = opt.footerContent || null;
    this.beforeShow = opt.beforeShow || null;
    this.afterInit = opt.afterInit || null;
    this.afterShow = opt.afterShow || null;
    if ( $.type( opt.middleAlign ) == 'undefined' )
        this.middleAlign = true;
    else
        this.middleAlign = opt.middleAlign;
    if ( $.type( opt.requestListOnShow ) == 'undefined' )
        this.requestListOnShow = true;
    else
        this.requestListOnShow = opt.requestListOnShow;
    if ( $.type( opt.showCloseButton ) == 'undefined' )
        this.showCloseButton = true;
    else
        this.showCloseButton = opt.showCloseButton;
    if ( $.isFunction( opt.afterRequest ) )
        this.afterRequest = opt.afterRequest;
    else
        this.afterRequest = false;
    this.beforeClose = opt.beforeClose || null;
    if ( opt.contentBackgroundColor )
        this.options.contentBackgroundColor = opt.contentBackgroundColor;
    if ( opt.headerBackgroundColor )
        this.options.headerBackgroundColor = opt.headerBackgroundColor;
    if ( opt.contentClass )
        this.options.contentClass = opt.contentClass;
    if ( opt.bodyBackgrounColor )
        this.options.bodyBackgrounColor = opt.bodyBackgrounColor;
    this.closeGroup = true;
    this.body = null;
    this.header = null;
    this.content = null;
    this.timer = null;
    this.modal;
    this.timerStep = 0;
    return this;
};
mDialog.ajaxdialog = function ( opt ) {
    mDialog.ajaxdialogbase.call( this, $.extend( {ajaxdialog: true}, opt ) );
    let _isOpened = false, thisDialog = null;
    this.timer = 0;
    Object.defineProperty( this, 'isOpened', {
        get: function () {
            return _isOpened;
        }
    } );
    this.close = function () {
        if ( this.timer )
            clearInterval( this.timer );
        if ( typeof ( this.beforeClose ) === 'function' )
            this.beforeClose.call( this );
        if ( thisDialog )
            thisDialog.remove();
        _isOpened = false;
        thisDialog = null;
        if ( this.options.decrZinde )
            mDialog.ajaxdialog_autoZ--;
        if ( mDialog.ajaxdialog_autoZ_start == mDialog.ajaxdialog_autoZ )
            $( "body" ).removeAttr( "style" );
        delete this;
    };
    this.show = function () {
        console.groupCollapsed( 'ajaxDialog:Show' );
        if ( $.isFunction( this.beforeShow ) )
            this.beforeShow.call( this );
        let backg = $( '<div>' ).attr( {
            class: 'm-modal-background',
            id: this.options.id
        } ).css( {'z-index': this.options.zindex} );
        let modal = $( '<div>' ).addClass( 'm-modal' );
        let cClass = this.options.contentClass || 'm-modal-content';
        let cnt = $( '<div>' ).addClass( cClass );
        if ( typeof ( this.options.style ) === 'string' )
            cnt.attr( 'style', this.options.style );
        else if ( typeof ( this.options.style ) === 'object' )
            cnt.css( this.options.style );
        this.header = this.getHeader();
        cnt.append( this.header );
        this.body = this.getBody();
        this.footer = this.getFooter();
        cnt.append( this.body );
        cnt.append( this.footer );
        if ( this.options.contentBackgroundColor )
            cnt.css( 'background-color', this.options.contentBackgroundColor )
        modal.append( cnt );
        this.content = cnt;
        backg.append( modal );
        thisDialog = backg;
        this.options.parent.append( backg );
        this.modal = modal;
        if ( this.requestListOnShow && !this.bodyContent ) {
            this.requestList();
        } else {
            console.groupEnd();
        }
        cnt.click( function ( e ) {
            e.stopImmediatePropagation();
        } );
        if ( this.middleAlign ) {
            $( window ).resize( {dialog: this}, function ( e ) {
                console.debug( 'resize', e );
                e.data.dialog.resize();
            } );
        }
        thisDialog.click( {self: this}, function ( e ) {
            let self = e.data.self;
            if ( self.timer )
                clearInterval( self.timer );
            self.timerStep = 20;
            self.timer = setInterval( () => {
                if ( self.timerStep % 2 ) {
                    if ( thisDialog )
                        thisDialog.css( 'display', 'none' );
                    else {
                        clearInterval( self.timer );
                        self.timer = null;
                    }
                } else {
                    if ( thisDialog )
                        thisDialog.css( 'display', 'block' );
                    else {
                        clearInterval( self.timer );
                        self.timer = null;
                    }
                }
                //                console.log(rVal.timerStep);
                self.timerStep--;
                if ( !self.timerStep ) {
                    clearInterval( self.timer );
                    self.timer = null;
                    thisDialog.css( 'display', 'block' );
                }
            }, 20 );
        } );
        if ( $( "body" ).width() < this.content.width() ) {
            $( "body" ).width( this.content.width() );

        }
        this.isFadedOut = false;
        $( this.content ).draggable( {
            handle: '.m-modal-header'
        } );
        _isOpened = true;
        if ( $.isFunction( this.afterShow ) )
            this.afterShow.call( this );
    };
    return this;
}
mDialog.ajaxdialog.prototype = Object.create( mDialog.ajaxdialogbase.prototype );
mDialog.ajaxdialog.prototype.closeClick = function ( e ) {
    console.debug( 'ajaxdialog:closeClick', e );
    e.data.dialog.close.call( e.data.dialog );
};
mDialog.ajaxdialog.prototype.getHeader = function () {
    let rV = null;
    if ( this.options.headerText ) {
        let options = {
            class: 'm-modal-header'
        }
        if ( this.options.headerBackgroundColor ) {
            options.style = 'background-color:' + this.options.headerBackgroundColor
        }
        rV = $( '<div>' ).attr( options );
        let span = $( '<span>' );
        if ( typeof ( this.options.headerText ) === 'string' )
            span.text( this.options.headerText );
        else
            span.html( this.options.headerText );
        rV.append( span )
        if ( this.showCloseButton ) {
            let btn = $( '<button>' ).addClass( 'm-modal-close-btn' );
            btn.click( {dialog: this}, this.closeClick );
            btn.append( $( '<span>' ).addClass( 'glyphicon glyphicon-remove' ) );
            rV.append( btn );
        }
    }
    return rV;
};
mDialog.ajaxdialog.prototype.getBody = function () {
    let rV = null;
    let options = {
        class: 'm_modal-body'
    }
    if ( this.options.bodyBackgrounColor ) {
        options.style = 'background-color:' + this.options.bodyBackgrounColor
    }
    rV = $( '<div>' ).attr( options );
    if ( this.bodyContent ) {
        rV.html( this.bodyContent );
    }
    return rV;
};
mDialog.ajaxdialog.prototype.getFooter = function () {
    let rV = null;
    let options = {
        class: 'm-dialog-footer'
    }
    if ( this.options.footerBackgrounColor ) {
        options.style = 'background-color:' + this.options.footerBackgrounColor
    }
    rV = $( '<div>' ).attr( options );
    if ( $.isFunction( this.footerContent ) )
        rV.hmtl( this.footerContent.call( this, this ) );
    else {
        if ( typeof ( this.footerContent ) === 'string' )
            rV.html( this.footerContent );
        else if ( typeof ( this.footerContent ) === 'object' )
            rV.append( this.footerContent );
        else
            return null;
    }
    return rV;
};
mDialog.ajaxdialog.prototype.request = function ( url, param, afterInit ) {
    let self = this;
    console.debug( 'request', url, param );
    if ( !url ) {
        console.groupEnd();
        return;
    }
    this.resize();
    if ( this.options.reqImgUrl )
        this.body.empty().append( $( '<img>' ).attr( {
            src: this.options.reqImgUrl,
        } ).css( {
            margin: 'auto',
            display: 'block'
        } ) );
    else
        this.body.empty().append( $( '<h3>' ).addClass( 'text-info' ).css( {
            'text-align': 'center'
        } ).text( 'Загрузка....' ) );
    $.post( url, param )
            .done( function ( data ) {
                console.debug( 'request:answer', data );
                if ( $.type( data ) == 'string' ) {
                    self.body.empty().html( data );
                } else {
                    if ( data.html )
                        self.body.empty().html( data.html );
                    else if ( data.errorText ) {
                        self.header.children( 'span:first-child' ).text( 'Ошибка загрузки' );
                        self.body.empty().html( data.errorText );
                    }
                }
                self.resize();
                console.groupEnd();
                if ( $.isFunction( afterInit ) ) {
                    afterInit.call( self, self );
                }
                if ( $.isFunction( self.afterRequest ) ) {
                    self.afterRequest.call( self, data );
                }
            } )
            .fail( function ( jqXHR ) {
                console.debug( 'request:fail:answer', jqXHR );
                self.body.empty().html( jqXHR.responseText );
                self.header.children( 'span:first-child' ).text( 'Ошибка загрузки' );
                console.groupEnd();
            } ).always( function () {
    } );
};
mDialog.ajaxdialog.prototype.resize = function () {
    if ( this.middleAlign ) {
        let mg = ( $( window ).height() - 50 ) / 2 - this.content.height() / 2;
        if ( mg < 0 )
            mg = 0;
        //rVal.content.css('margin-top',mg+'px');
        this.content.offset( {top: mg} );
        console.debug( 'automargi', mg );
    }
    if ( this.content.height() > $( window ).height() - 90 ) {
        let mHeight = $( window ).height() - 90;
        if ( this.header )
            mHeight -= this.header.height();
        if ( this.footer )
            mHeight -= this.footer.height();
        this.body.css( 'max-height', mHeight + 'px' );
    }
};
mDialog.ajaxdialog.prototype.requestList = function () {
    let param = {};
    if ( $.isFunction( this.options.requestListParam ) )
        param = this.options.requestListParam.call( this, this );
    else
        param = this.options.requestListParam;
    this.request( this.options.requestList, param, this.afterInit );
};
mDialog.ajaxdialog.prototype.fadeIn = function () {
    if ( this.isFadedOut ) {
        console.debug( 'ajaxdialog', 'fadeIn' );
        this.isFadedOut.remove();
        this.isFadedOut = false;
        $( 'body' ).removeClass( 'fadeIn' );
    }
};
mDialog.ajaxdialog.prototype.fadeOut = function () {
    if ( this.isFadedOut === false ) {
        console.debug( 'ajaxdialog', 'fadeOut' );
        this.isFadedOut = $( '<div>' ).addClass( 'm-modal-background fadeIn' ).attr( {
            'style': {'z-index': mDialog.ajaxdialog_autoZ + 1}
        } );
        this.options.parent.append( this.isFadedOut );
        $( 'body' ).addClass( 'fadeIn' )
    }
};
mDialog.m_alertOld = function ( headerText, content, okClick, canselClick, beforeClose, afterInit, showCloseButton ) {
    let ht = $( window ).height() / 2 - 30;
    let opt = {
        headerText: headerText,
        bodyContent: content,
        requestList: '',
        requestUpdate: '',
        requestAdd: '',
        requestDelete: '',
        contentBackgroundColor: '#e5e4e9',
        headerBackgroundColor: '#dddce0',
        bodyBackgrounColor: '#e5e4e9',
        requestListOnShow: false,
        contentClass: 'm-modal-content m-modal-alert',
        zindex: 60000,
        showCloseButton: showCloseButton == true,
        style: {
            width: '600px',
            'top': ht + 'px'
        }
    };
    if ( $.isFunction( beforeClose ) ) {
        opt.beforeClose = function () {
            beforeClose.call( this );
            $( document ).unbind( 'keydown' );
        }
    } else {
        opt.beforeClose = function () {
            $( document ).unbind( 'keydown' );
        }
    }
    let cnt = $( '<div>' ).addClass( 'btn-group' );
    opt['footerContent'] = cnt;

    mDialog.ajaxdialog.call( this, opt );
    let btn = this.createButton( okClick, 'btn btn-main', 'ок' );
    let btnM = this.createButton( this.midButton, 'btn btn-main', 'Средняя' );
    let btnC = this.createButton( canselClick, 'btn btn-main-font-black', 'Нет' );
    if ( btn )
        cnt.append( btn );
    if ( btnM )
        cnt.append( btnM );
    if ( btnC )
        cnt.append( btnC );
    console.log( btn );
    $( document ).bind( 'keydown', {dialog: this}, function ( e ) {
        console.log( 'm_alert', e.key );
        if ( e.key === 'Escape' && e.data.dialog.isOpened ) {
            e.preventDefault();
            if ( canselClick !== false )
                if ( btnC )
                    btnC.trigger( 'click', {dialog: this} );
                else
                    e.data.dialog.close();
        } else if ( e.key === ' ' ) {
            e.preventDefault();
        }
    } );
    this.show();
    if ( $.isFunction( afterInit ) ) {
        afterInit.call( this );
    }
    this.content.css('top',($(window).height()-this.content.height())/2+$(window).scrollTop());
    return this;
};
mDialog.m_alertOld.prototype = Object.create( mDialog.ajaxdialog.prototype );
mDialog.m_alertOld.prototype.createButton = function ( option, className, defaultLabel ) {
    if ( !option )
        return null;
    defaultLabel = defaultLabel ? defaultLabel : 'Неопределена';
    className = className ? className : 'btn btn-main';
    let btn = $( '<button>' ).addClass( className );
    if ( $.type( option ) === 'object' ) {
        if ( $.type( option.label ) === 'string' )
            btn.text( option.label );
        else
            btn.text( defaultLabel );
        if ( $.isFunction( option.click ) ) {
            btn.click( {dialog: this}, function ( e ) {
                if ( option.click.call( this, e ) !== false )
                    e.data.dialog.close();
            } );
        } else {
            btn.click( {dialog: this}, function ( e ) {
                e.data.dialog.close();
            } );
        }
        if ( option.class ) {
            btn.removeAttr( 'class' ).addClass( option.class );
        }
    } else {
        if ( $.type( option ) === 'string' )
            btn.text( option );
        else
            btn.text( defaultLabel );
        if ( $.isFunction( option ) ) {
            btn.click( {dialog: this}, function ( e ) {
                if ( option.call( this, e ) !== false )
                    e.data.dialog.close();
            } );
        } else {
            btn.click( {dialog: this}, function ( e ) {
                e.data.dialog.close();
            } );
        }
    }
    return btn;
};

mDialog.m_alert = function ( headerText, content, okClick, canselClick, beforeClose, afterInit, showCloseButton ) {
    if ( $.type( headerText ) === 'object' ) {
        content = content ? content : headerText.content ? headerText.content : '';
        okClick = $.type( okClick ) !== 'undefined' ? okClick : $.type( headerText.okClick ) !== 'undefined' ? headerText.okClick : null;
        canselClick = $.type( canselClick ) !== 'undefined' ? canselClick : $.type( headerText.canselClick ) !== 'undefined' ? headerText.canselClick : null;
        beforeClose = beforeClose ? beforeClose : headerText.beforeClose ? headerText.beforeClose : null;
        afterInit = afterInit ? afterInit : headerText.afterInit ? headerText.afterInit : null;
        showCloseButton = $.type( showCloseButton ) !== 'undefined' ? showCloseButton : $.type( headerText.showCloseButton ) !== 'undefined' ? headerText.showCloseButton : true;
        this.midButton = headerText.midButton ? headerText.midButton : null;
        headerText = headerText.headerText ? headerText.headerText : 'Внимание';
    }
    mDialog.m_alertOld.call( this, headerText, content, okClick, canselClick, beforeClose, afterInit, showCloseButton );
};
mDialog.m_alert.prototype = Object.create( mDialog.m_alertOld.prototype );