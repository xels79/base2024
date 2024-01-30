/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

( function ( $ ) {
    $.widget( "custom.banksList", $.ui.dialog, {
        options: {
            id: 'banksListDialog'
        },
        create: function ( event, ui ) {
            console.debug( 'ui', ui );
            ui.element.attr( 'id', ui.options.id );
        }
    } );
}( jQuery ) );


ajaxdialog_autoID = 0;
ajaxdialog_autoZ = 2000;
/*
 * id -id виджета
 * zindex -Стартовое значения z-index
 * parent -Родитель. По умолчанию body
 * style -Стили dialog-body
 * requestList - арест запроса list
 * requestListParam -доп. параметры запроса может быть функцией
 * requestAddParam -доп. параметры запроса может быть функцией
 * requestDeleteParam -доп. параметры запроса может быть функцией
 * headerText -текст заголовка
 * bodyContent -содержимое dialog body может быть функцией
 * footerContent -содержимое dialog footer может быть функцией или false
 * beforeShow -функция
 * beforeClose -функция
 * afterInit -функция
 * afterShow -функция
 * requestListOnShow -по умолчанию true
 * showCloseButton -по умолчанию true
 * middleAlign -по умолчанию true
 * contentBackgroundColor -Цвет общего бэкгроунда
 * headerBackgroundColor -Цвет заголовка бэкгроунда
 * bodyBackgrounColor -Цвет dialog-body бэкгроунда
 */
var ajaxdialogbase = function ( opt ) {
    let rVal = {
        options: {}
    };
    if ( $.type( opt ) === 'undefined' ) {
        console.error( 'ajaxdialog: Не заданы опции' );
        return null;
    } else {
        if ( $.type( opt.options ) == 'undefined' ) {
            opt.options = {};
        }
    }
    if ( $.type( opt.zindex ) == 'undefined' ) {
        rVal.options.zindex = window.ajaxdialog_autoZ;
        window.ajaxdialog_autoZ_start = window.ajaxdialog_autoZ;
        window.ajaxdialog_autoZ++;
        rVal.options.decrZinde = true;
    } else {
        rVal.options.decrZinde = false;
        rVal.options.zindex = opt.zindex;
    }
    if ( $.type( opt.parent ) == 'undefined' )
        rVal.options.parent = 'body';
    else
        rVal.options.parent = opt.parent;
    if ( $.type( rVal.options.parent ) == 'string' )
        rVal.options.parent = $( rVal.options.parent );
    if ( $.type( opt.style ) == 'undefined' )
        rVal.options.style = {
            width: '50%',
            height: '50%',
        };
    else
        rVal.options.style = opt.style;
    //opt.options.parent=opt.parent;
    if ( $.type( opt.noAjax ) == 'undefined' ) {
        if ( $.type( opt.requestList ) == 'undefined' ) {
            console.error( 'ajaxdialog: Не задан requestList' ); //Аддрес запроса list
            return null;
        } else
            rVal.options.requestList = opt.requestList;
        if ( $.type( opt.requestListParam ) == 'undefined' ) {
            rVal.options.requestListParam = {};
        } else
            rVal.options.requestListParam = opt.requestListParam;
    } else {
        rVal.options.requestList = false;
    }
    if ( $.type( opt.items ) == 'undefined' )
        rVal.options.items = {};
    else
        rVal.options.items = opt.items;
    if ( $.type( opt.headerText ) == 'undefined' ) {
        rVal.options.headerText = 'Не задан';
    } else
        rVal.options.headerText = opt.headerText.substring( 0, 1 ).toUpperCase() + opt.headerText.substring( 1 );
    if ( $.type( opt.id ) == 'undefined' ) {
        rVal.options.id = 'ad' + window.ajaxdialog_autoID;
        window.ajaxdialog_autoID++;
    } else
        rVal.options.id = opt.id;
    if ( $.type( opt.bodyContent ) == 'undefined' )
        rVal.bodyContent = null;
    else
        rVal.bodyContent = opt.bodyContent;
    if ( $.type( opt.reqImgUrl ) == 'undefined' )
        rVal.options.reqImgUrl = null;
    else
        rVal.options.reqImgUrl = opt.reqImgUrl;
    if ( $.type( opt.footerContent ) == 'undefined' )
        rVal.footerContent = null;
    else
        rVal.footerContent = opt.footerContent;
    if ( $.type( opt.beforeShow ) == 'undefined' )
        rVal.beforeShow = null;
    else
        rVal.beforeShow = opt.beforeShow;
    if ( $.type( opt.afterInit ) == 'undefined' )
        rVal.afterInit = null;
    else
        rVal.afterInit = opt.afterInit;
    if ( $.type( opt.afterShow ) == 'undefined' )
        rVal.afterShow = null;
    else
        rVal.afterShow = opt.afterShow;
    if ( $.type( opt.middleAlign ) == 'undefined' )
        rVal.middleAlign = true;
    else
        rVal.middleAlign = opt.middleAlign;
    if ( $.type( opt.requestListOnShow ) == 'undefined' )
        rVal.requestListOnShow = true;
    else
        rVal.requestListOnShow = opt.requestListOnShow;
    if ( $.type( opt.showCloseButton ) == 'undefined' )
        rVal.showCloseButton = true;
    else
        rVal.showCloseButton = opt.showCloseButton;
    if ( $.isFunction( opt.afterRequest ) )
        rVal.afterRequest = opt.afterRequest;
    else
        rVal.afterRequest = false;
    if ( $.type( opt.beforeClose ) == 'undefined' )
        rVal.beforeClose = null;
    else
        rVal.beforeClose = opt.beforeClose;
    if ( opt.contentBackgroundColor )
        rVal.options.contentBackgroundColor = opt.contentBackgroundColor;
    if ( opt.headerBackgroundColor )
        rVal.options.headerBackgroundColor = opt.headerBackgroundColor;
    if ( opt.contentClass )
        rVal.options.contentClass = opt.contentClass;
    if ( opt.bodyBackgrounColor )
        rVal.options.bodyBackgrounColor = opt.bodyBackgrounColor;
    rVal.closeGroup = true;
    rVal.body = null;
    rVal.header = null;
    rVal.content = null;
    rVal.timer = null;
    rVal.modal;
    rVal.timerStep = 0;
    return rVal;
};

var ajaxdialog = function ( opt ) {
    let _isOpened = false;
    let rVal = $.extend( {
        ajaxdialog: true
    }, new ajaxdialogbase( opt ) );
    rVal.isOpened = function () {
        return _isOpened;
    };
    let thisDialog = null;
    rVal.closeClick = function ( e ) {
        console.debug( 'ajaxdialog:closeClick', e );
        e.data.dialog.close.call( this );
    }
    rVal.close = function () {
        if ( rVal.timer )
            clearInterval( rVal.timer );
        if ( $.isFunction( rVal.beforeClose ) )
            rVal.beforeClose.call( this );
        if ( thisDialog )
            thisDialog.remove();
        _isOpened = false;
        thisDialog = null;
        if ( rVal.options.decrZinde )
            window.ajaxdialog_autoZ--;
        if ( window.ajaxdialog_autoZ_start == window.ajaxdialog_autoZ )
            $( "body" ).removeAttr( "style" );
        delete rVal;
    }
    rVal.show = function () {
        console.groupCollapsed( 'ajaxDialog:Show' );
        if ( $.isFunction( rVal.beforeShow ) )
            rVal.beforeShow.call( this );
        let backg = $.fn.creatTag( 'div', {
            class: 'm-modal-background',
            id: rVal.options.id,
            style: 'z-index:' + rVal.options.zindex
        } );
        let modal = $.fn.creatTag( 'div', {
            class: 'm-modal'
        } );
        let cClass = 'm-modal-content';
        if ( rVal.options.contentClass ) {
            cClass = rVal.options.contentClass;
        }
        let cnt = $.fn.creatTag( 'div', {
            class: cClass,
            style: rVal.options.style
        } );
        rVal.header = rVal.getHeader();
        cnt.append( rVal.header );
        rVal.body = rVal.getBody();
        rVal.footer = rVal.getFooter();
        cnt.append( rVal.body );
        cnt.append( rVal.footer );
        if ( rVal.options.contentBackgroundColor )
            cnt.css( 'background-color', rVal.options.contentBackgroundColor )
        modal.append( cnt );
        rVal.content = cnt;
        backg.append( modal );
        thisDialog = backg;
        rVal.options.parent.append( backg );
        rVal.modal = modal;
        if ( rVal.requestListOnShow && !rVal.bodyContent ) {
            rVal.requestList();
        } else {
            console.groupEnd();
        }
        cnt.click( function ( e ) {
            e.stopImmediatePropagation();
        } );
        if ( rVal.middleAlign ) {
            $( window ).resize( {dialog: rVal}, function ( e ) {
                console.debug( 'resize', e );
                e.data.dialog.resize();
            } );
        }
        thisDialog.click( function () {
            if ( rVal.timer )
                clearInterval( rVal.timer );
            rVal.timerStep = 20;
            rVal.timer = setInterval( function () {
                if ( rVal.timerStep % 2 ) {
                    thisDialog.css( 'display', 'none' );
                } else {
                    thisDialog.css( 'display', 'block' );
                }
//                console.log(rVal.timerStep);
                rVal.timerStep--;
                if ( !rVal.timerStep ) {
                    clearInterval( rVal.timer );
                    rVal.timer = null;
                    thisDialog.css( 'display', 'block' );
                }
            }, 20 );
        } );
        if ( $( "body" ).width() < rVal.content.width() ) {
            $( "body" ).width( rVal.content.width() );

        }
        rVal.isFadedOut = false;
        $( rVal.content ).draggable( {
            handle: '.m-modal-header'
        } );
        _isOpened = true;
        if ( $.isFunction( rVal.afterShow ) )
            rVal.afterShow.call( this );
    };
    rVal.getHeader = function () {
        let rV = null;
        if ( rVal.options.headerText ) {
            let options = {
                class: 'm-modal-header'
            }
            if ( rVal.options.headerBackgroundColor ) {
                options.style = 'background-color:' + rVal.options.headerBackgroundColor
            }
            rV = $.fn.creatTag( 'div', options );
            let span = $.fn.creatTag( 'span', {} );
            if ( $.type( rVal.options.headerText ) == 'string' )
                span.text( rVal.options.headerText );
            else
                span.html( rVal.options.headerText );
            rV.append( span )
            if ( rVal.showCloseButton ) {
                let btn = $.fn.creatTag( 'button', {
                    class: 'm-modal-close-btn'
                } );
                btn.click( {dialog: rVal}, rVal.closeClick );
                btn.append( $.fn.creatTag( 'span', {
                    class: 'glyphicon glyphicon-remove'
                } ) );
                rV.append( btn );
            }
        }
        return rV;
    };
    rVal.getBody = function () {
        let rV = null;
        let options = {
            class: 'm_modal-body'
        }
        if ( rVal.options.bodyBackgrounColor ) {
            options.style = 'background-color:' + rVal.options.bodyBackgrounColor
        }
        rV = $.fn.creatTag( 'div', options );
        if ( rVal.bodyContent ) {
            rV.html( rVal.bodyContent );
        }
        return rV;
    };
    rVal.getFooter = function () {
        let rV = null;
        let options = {
            class: 'm-dialog-footer'
        }
        if ( rVal.options.footerBackgrounColor ) {
            options.style = 'background-color:' + rVal.options.footerBackgrounColor
        }
        rV = $.fn.creatTag( 'div', options );
        if ( $.isFunction( rVal.footerContent ) )
            rV.hmtl( rVal.footerContent.call( this, rVal ) );
        else {
            if ( $.type( rVal.footerContent ) == 'string' )
                rV.html( rVal.footerContent );
            else if ( $.type( rVal.footerContent ) == 'object' )
                rV.append( rVal.footerContent );
            else
                return null;
        }
        return rV;
    };
    rVal.request = function ( url, param, afterInit ) {
        console.debug( 'request', url, param );
        if ( !url ) {
            console.groupEnd();
            return;
        }
        rVal.resize();
        if ( rVal.options.reqImgUrl )
            rVal.body.empty().append( $.fn.creatTag( 'img', {
                src: rVal.options.reqImgUrl,
                style: {
                    margin: 'auto',
                    display: 'block'
                }
            } ) );
        else
            rVal.body.empty().append( $.fn.creatTag( 'h3', {
                'class': 'text-info',
                style: {
                    'text-align': 'center'
                },
                text: 'Загрузка....'
            } ) );
        $.post( url, param )
                .done( function ( data ) {
                    console.debug( 'request:answer', data );
                    if ( $.type( data ) == 'string' ) {
                        rVal.body.empty().html( data );
                    } else {
                        if ( data.html )
                            rVal.body.empty().html( data.html );
                        else if ( data.errorText ) {
                            rVal.header.children( 'span:first-child' ).text( 'Ошибка загрузки' );
                            rVal.body.empty().html( data.errorText );
                        }
                    }
                    rVal.resize();
                    console.groupEnd();
                    if ( $.isFunction( afterInit ) ) {
                        afterInit.call( this, rVal );
                    }
                    if ( $.isFunction( rVal.afterRequest ) ) {
                        rVal.afterRequest.call( this, data );
                    }
                } )
                .fail( function ( jqXHR ) {
                    console.debug( 'request:fail:answer', jqXHR );
                    rVal.body.empty().html( jqXHR.responseText );
                    rVal.header.children( 'span:first-child' ).text( 'Ошибка загрузки' );
                    console.groupEnd();
                } ).always( function () {
        } );
    };
    rVal.resize = function () {
        if ( rVal.middleAlign ) {
            let mg = ( $( window ).height() - 50 ) / 2 - rVal.content.height() / 2;
            if ( mg < 0 )
                mg = 0;
            //rVal.content.css('margin-top',mg+'px');
            rVal.content.offset( {top: mg} );
            console.debug( 'automargi', mg );
        }
        if ( rVal.content.height() > $( window ).height() - 90 ) {
            let mHeight = $( window ).height() - 90;
            if ( rVal.header )
                mHeight -= rVal.header.height();
            if ( rVal.footer )
                mHeight -= rVal.footer.height();
            rVal.body.css( 'max-height', mHeight + 'px' );
        }
    }
    rVal.requestList = function () {
        let param = {};
        if ( $.isFunction( rVal.options.requestListParam ) )
            param = rVal.options.requestListParam.call( this, rVal );
        else
            param = rVal.options.requestListParam;
        rVal.request( rVal.options.requestList, param, rVal.afterInit );
    }
    rVal.fadeIn = function () {
        if ( rVal.isFadedOut ) {
            console.debug( 'ajaxdialog', 'fadeIn' );
            rVal.isFadedOut.remove();
            rVal.isFadedOut = false;
            $( 'body' ).removeClass( 'fadeIn' )

        }
    }
    rVal.fadeOut = function () {
        if ( rVal.isFadedOut === false ) {
            console.debug( 'ajaxdialog', 'fadeOut' );
            rVal.isFadedOut = $.fn.creatTag( 'div', {
                'class': 'm-modal-background fadeIn',
                'style': {'z-index': window.ajaxdialog_autoZ + 1}
            } );
            rVal.options.parent.append( rVal.isFadedOut );
            $( 'body' ).addClass( 'fadeIn' )
        }
    }
    return rVal;
}
var m_alert = function ( headerText, content, okClick, canselClick, beforeClose, afterInit, showCloseButton ) {
    if ( $.type( headerText ) === 'object' ) {
        content = content ? content : headerText.content ? headerText.content : '';
        okClick = $.type( okClick ) !== 'undefined' ? okClick : $.type( headerText.okClick ) !== 'undefined' ? headerText.okClick : null;
        canselClick = $.type( canselClick ) !== 'undefined' ? canselClick : $.type( headerText.canselClick ) !== 'undefined' ? headerText.canselClick : null;
        beforeClose = beforeClose ? beforeClose : headerText.beforeClose ? headerText.beforeClose : null;
        afterInit = afterInit ? afterInit : headerText.afterInit ? headerText.afterInit : null;
        showCloseButton = $.type( showCloseButton ) !== 'undefined' ? showCloseButton : $.type( headerText.showCloseButton ) !== 'undefined' ? headerText.showCloseButton : true;
        headerText = headerText.headerText ? headerText.headerText : 'Внимание';
    }
    m_alertOld.call( this, headerText, content, okClick, canselClick, beforeClose, afterInit, showCloseButton );
};
let m_alertOld = function ( headerText, content, okClick, canselClick, beforeClose, afterInit, showCloseButton ) {
    let ht = $( window ).height() / 2 - 30;
    let tmp;
    //let btnCloseClicked=false;
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
    let btn = $.fn.creatTag( 'button', {
        'class': 'btn btn-main'
    } );
    let btnC = $.fn.creatTag( 'button', {
        'class': 'btn btn-main-font-black'
    } );
    let cnt = $.fn.creatTag( 'div', {
        'class': 'btn-group'
    } );
    opt['footerContent'] = cnt;

    tmp = new ajaxdialog( opt );
//    if ($.isFunction(okClick)){
//        btn.click(function(){
//            tmp.close();
//        });
//        btnC.click(function(){tmp.close();});
//    }
    if ( $.type( okClick ) === 'object' ) {
        if ( $.type( okClick.label ) === 'string' )
            btn.text( okClick.label );
        else
            btn.text( 'Да' );
        if ( $.isFunction( okClick.click ) ) {
            btn.click( {dialog: tmp}, function ( e ) {
                if ( okClick.click.call( this, e ) !== false )
                    tmp.close()
            } );
        } else {
            btn.click( function () {
                tmp.close();
            } );
        }
        if ( okClick.class ) {
            btn.removeAttr( 'class' ).addClass( okClick.class );
        }
    } else {
        if ( $.type( okClick ) === 'string' )
            btn.text( okClick );
        else
            btn.text( 'Да' );
        if ( $.isFunction( okClick ) ) {
            btn.click( {dialog: tmp}, function ( e ) {
                if ( okClick.call( this, e ) !== false )
                    tmp.close()
            } );
        } else {
            btn.click( function () {
                tmp.close();
            } );
        }
    }
    console.log( 'canselClick', $.type( canselClick ) );
    if ( $.isFunction( canselClick ) ) {
        btnC.click( {dialog: tmp}, function ( e ) {
            if ( canselClick.call( this, e ) !== false )
                tmp.close();
        } );
        btnC.text( 'Нет' );
    } else if ( $.type( canselClick ) === 'object' ) {
        if ( canselClick.label ) {
            btnC.text( canselClick.label );
        } else {
            btnC.text( 'Нет' );
        }
        if ( $.isFunction( canselClick.click ) ) {
            btnC.click( {dialog: tmp}, function ( e ) {
                if ( canselClick.click.call( this, e ) !== false )
                    tmp.close()
            } );
        } else {
            btnC.click( function () {
                tmp.close();
            } );
        }
        if ( canselClick.class ) {
            btnC.removeAttr( 'class' ).addClass( canselClick.class );
        }
    } else {
        btnC.click( function () {
            tmp.close();
        } );
        if ( $.type( canselClick ) === 'string' )
            btnC.text( canselClick );
        else
            btnC.text( 'Нет' );
    }
    if ( okClick !== false )
        cnt.append( btn );
    if ( canselClick !== false )
        cnt.append( btnC );
    console.log( btn );
    $( document ).bind( 'keydown', function ( e ) {
        console.log( 'm_alert', e.key );
        if ( e.key === 'Escape' && tmp.isOpened() ) {
            e.preventDefault();
            if ( canselClick !== false )
                btnC.trigger( 'click', {dialog: tmp} );
            else
                tmp.close();
        } else if ( e.key === ' ' ) {
            e.preventDefault();
        }
    } );
    tmp.show();
    if ( $.isFunction( afterInit ) ) {
        afterInit.call( tmp );
    }
    ;
    return tmp;
};
var m_popUp = function ( opt ) {
    let rVal = null;
    opt = opt ? opt : {};
    opt.noAjax = true;
    opt.requestListOnShow = false;
    opt.showCloseButton = false;
    rVal = $.extend( {}, new ajaxdialogbase( opt ) );
    let backG = $.fn.creatTag( 'div', {
        class: 'm-modal-background',
        id: rVal.options.id,
        style: 'z-index:' + rVal.options.zindex
    } );
    if ( opt.posX )
        rVal.X = opt.posX + 'px';
    else
        rVal.X = 'auto';
    if ( opt.posX )
        rVal.Y = opt.posY + 'px';
    else
        rVal.Y = 'auto';
    let options = {
        class: 'm-modal-content',
        style: {
            'left': rVal.X,
            'top': rVal.Y,
            //'background-color': 'gray',
            'max-width': '400px',
            position: 'absolute',
            width: '250px'
//            height:'100px'
        }
    };
    if ( $.type( opt.options ) === 'object' ) {
        options = $.extend( options, opt.options );
    }
    rVal.content = $.fn.creatTag( 'div', options );
    rVal.content.append( opt.content ? opt.content : $( '<p>Пусто</p>' ) );
    backG.append( rVal.content );
    rVal.options.parent.append( backG );
    rVal.content.click( function ( e ) {
        e.stopPropagation();
        e.preventDefault();
    } );
    rVal.close = function () {
        if ( $.isFunction( opt.beforeClose ) )
            opt.beforeClose.call( this, rVal );
        backG.remove();
        delete rVal;
        if ( !opt.zindex )
            window.ajaxdialog_autoZ--;
    }
    backG.click( rVal.close );
    if ( opt.parentControl ) {
        backG.mousemove( function ( e ) {
            let w = opt.parentControl.width(), h = opt.parentControl.height(), offset = opt.parentControl.offset();
            let wsumm = offset.left + w - e.clientX;
            let hsumm = offset.top + h - e.clientY;
            if ( wsumm < 0 || wsumm > w || hsumm < 0 || hsumm > h ) {
                rVal.close();
            }

        } );
    }
//    backG.contextmenu(function(e){
//        e.preventDefault();
//        rVal.close();
//    });
    if ( $.isFunction( opt.afterInit ) )
        opt.afterInit.call( this, rVal );
    return rVal;
}
var dropDown = function ( opt ) {
    let rVal = null;
    let _beforeClose = opt.beforeClose;
    let tmr = 0;
    let mouseIsIn = false;
    let delataScroll = 0;
    opt.noAjax = true;
    opt.requestListOnShow = false;
    opt.showCloseButton = false;
    opt.beforeClose = function ( ) {
        if ( $.isFunction( _beforeClose ) )
            _beforeClose( );
        $( document ).off( 'keydown' );
    };
    rVal = $.extend( {}, new ajaxdialogbase( opt ) );
    let backG = $.fn.creatTag( 'div', {
        class: 'm-modal-background',
        id: rVal.options.id,
        style: 'z-index:' + rVal.options.zindex
    } );
    if ( opt.posX ) {
        if ( opt.posX + 250 > $( window ).width( ) )
            opt.posX -= 250;
        rVal.X = opt.posX + 'px';
    } else
        rVal.X = 'auto';
    if ( opt.posX )
        rVal.Y = opt.posY + 'px';
    else
        rVal.Y = 'auto';
    let options = {
        class: 'm-modal-content',
        style: {
            'left': rVal.X,
            'top': rVal.Y,
            'background-color': 'whitesmoke',
            'max-width': '400px',
            position: 'absolute',
            width: '250px'
//            height:'100px'
        }
    };
    if ( $.type( opt.options ) === 'object' ) {
        options = $.extend( options, opt.options );
    }
    rVal.content = $.fn.creatTag( 'div', options );
    rVal.menu = $.fn.creatTag( 'ul', {
        class: 'menu'
    } );
    $.each( rVal.options.items, function ( id, val ) {
        let aopt = {};
        let li = $.fn.creatTag( 'li', {} );
        if ( $.type( val ) === 'object' ) {
            if ( $.type( val.header ) === 'string' ) {
                li.append( $( '<p>' ).text( val.header ) ).addClass( 'header' );
            } else {
                if ( $.type( val.options ) == 'object' )
                    aopt = val.options;
                let a = $.fn.creatTag( 'a', aopt );
                if ( $.type( val.value ) )
                    a.attr( 'value', val.value );
                a.text( val.label );
                a.attr( 'href', val.id )
                if ( !val.disabled ) {
                    li.mouseenter( function ( e ) {
                        e.preventDefault( );
                        e.stopPropagation( );
                        $( this ).parent( ).children( ).removeClass( 'selected' );
                        $( this ).addClass( 'selected' );
                        return false;
                    } );
                    if ( $.isArray( val.items ) ) {
                        let isSubOpened = false;
                        a.mouseenter( function ( e ) {
                            e.preventDefault( )
                            if ( !isSubOpened ) {
                                isSubOpened = true;
                                let el = this;
                                $( el ).parent( ).addClass( 'selected' );
                                let subPullLeft = opt.subPullLeft ? true : false;
                                let parX = $( this ).parent( ).parent( ).parent( ).position( ).left + $( this ).parent( ).position( ).left;
                                let psX = parX + ( subPullLeft ? ( -1 * $( this ).parent( ).outerWidth( ) ) : $( this ).parent( ).outerWidth( ) );
                                if ( psX + 255 > $( window ).width( ) && !opt.subPullLeft ) {
                                    subPullLeft = true;
                                    psX = parX - $( this ).parent( ).parent( ).parent( ).width( ) - 2;
                                } else if ( psX - 255 < 0 && opt.subPullLeft ) {
                                    subPullLeft = false;
                                    psX = parX + $( this ).parent( ).parent( ).parent( ).width( ) - 2;
                                } else {
                                    psX -= 2;
                                }
                                new dropDown( {
                                    posX: psX,
                                    posY: $( this ).parent( ).parent( ).parent( ).position( ).top + $( this ).parent( ).position( ).top,
                                    subPullLeft: subPullLeft,
                                    afterClick: function ( ) {
                                        rVal.close( );
                                        if ( $.isFunction( opt.afterClick ) )
                                            opt.afterClick.call( rVal );
                                    },
                                    items: val.items,
                                    parentArea: {
                                        left: $( this ).parent( ).offset( ).left - 1,
                                        top: $( this ).parent( ).offset( ).top - 1,
                                        width: $( this ).parent( ).outerWidth( ) + 1,
                                        height: $( this ).outerHeight( ) + 5
                                    },
                                    beforeClose: function ( ) {
                                        isSubOpened = false;
                                        $( el ).parent( ).removeClass( 'selected' );
                                    }
                                } );
                            }
                        } );
                    } else {
                        if ( $.isFunction( val.click ) ) {
                            a.click( val.click );
                        } else if ( $.type( val.click ) === 'array' && val.click.length > 1 ) {
                            if ( $.isFunction( val.click[1] ) ) {
                                a.click( val.click[0], val.click[1] );
                            } else if ( $.isFunction( val.click[0] ) ) {
                                a.click( val.click[1], val.click[0] );
                            }
                        }
                        a.click( function ( e ) {
                            rVal.close( );
                            if ( $.isFunction( opt.afterClick ) )
                                opt.afterClick.call( rVal );
                        } );
                    }
                } else {
                    li.addClass( 'disabled' );
                }
                if ( $.type( val.widget ) === 'object' ) {
                    a[val.widget.class].call( a, val.options );
                }
                li.append( a );
            }
        } else if ( $.type( val ) === 'string' && val === 'separator' ) {
            li.append( $( '<div>' ) ).addClass( 'separator' );
        }
        rVal.menu.append( li );
    } );
    rVal.content.append( rVal.menu );
    backG.append( rVal.content );
    if ( $.isPlainObject( opt.parentArea ) ) {
        backG.mousemove( function ( e ) {
            let x = e.pageX;
            let y = e.pageY;
            if ( !tmr ) {
                if ( x < rVal.content.offset( ).left - 15
                        || x > rVal.content.offset( ).left + rVal.content.outerWidth( ) + 15
                        || y < rVal.content.offset( ).top
                        || y > rVal.content.offset( ).top + rVal.content.outerHeight( ) ) {
                    if ( x < opt.parentArea.left
                            || x > opt.parentArea.left + opt.parentArea.width
                            || y < opt.parentArea.top
                            || y > opt.parentArea.top + opt.parentArea.height ) {
                        tmr = setTimeout( function ( ) {
                            if ( x < rVal.content.offset( ).left - 15
                                    || x > rVal.content.offset( ).left + rVal.content.outerWidth( ) + 15
                                    || y < rVal.content.offset( ).top
                                    || y > rVal.content.offset( ).top + rVal.content.outerHeight( ) ) {
                                if ( x < opt.parentArea.left
                                        || x > opt.parentArea.left + opt.parentArea.width
                                        || y < opt.parentArea.top
                                        || y > opt.parentArea.top + opt.parentArea.height ) {
                                    rVal.close( );
                                    tmr = 0;
                                }
                            }

                        }, 150 );
                    }
                }
            } else {
                if ( x < rVal.content.offset( ).left - 15
                        || x > rVal.content.offset( ).left + rVal.content.outerWidth( ) + 15
                        || y < rVal.content.offset( ).top
                        || y > rVal.content.offset( ).top + rVal.content.outerHeight( ) ) {
                    if ( x < opt.parentArea.left
                            || x > opt.parentArea.left + opt.parentArea.width
                            || y < opt.parentArea.top
                            || y > opt.parentArea.top + opt.parentArea.height ) {
                    } else {
                        clearTimeout( tmr );
                        tmr = 0;
                    }
                } else {
                    clearTimeout( tmr );
                    tmr = 0;
                }
            }
        } );
    }
    rVal.options.parent.append( backG );
    rVal.content.click( function ( e ) {
        e.stopPropagation( );
        e.preventDefault( );
    } );
    rVal.close = function ( ) {
        if ( tmr ) {
            clearTimeout( tmr );
            tmr = 0;
        }
        if ( $.isFunction( opt.beforeClose ) )
            opt.beforeClose.call( this, rVal );
//        backG.unbind('mousewheel');
        backG.remove( );
        delete rVal;
        window.ajaxdialog_autoZ--;
    }
    rVal.content.children( 'ul:first-child' ).mouseenter( function ( ) {
        mouseIsIn = true;
    } );
    rVal.content.children( 'ul:first-child' ).mouseleave( function ( ) {
        mouseIsIn = false;
    } );
    function _moveToDelta ( dlt ) {
        let menu = rVal.content.children( 'ul:first-child' );
        let sel = menu.children( '.selected' );
        //let ind=1;
        let nxt = null;
        if ( !sel.length ) {
            if ( dlt < 0 ) {
                sel = menu.children( ':first-child' );
            } else {
                sel = menu.children( ':last-child' );
            }
        } else {
            sel.removeClass( 'selected' );
        }
        let ind = sel.index( );
        do {
            if ( dlt > 0 ) {
                sel = sel.prev( );
            } else {
                sel = sel.next( );
            }
            if ( !sel.length ) {
                if ( dlt < 0 ) {
                    sel = menu.children( ':first-child' );
                } else {
                    sel = menu.children( ':last-child' );
                }
            }
            if ( !sel.hasClass( 'header' ) && !sel.hasClass( 'separator' ) ) {
                nxt = sel;
            }
        } while ( !nxt && sel.index( ) !== ind );
        if ( !nxt )
            nxt = sel;
        nxt.addClass( 'selected' );
        nxt.children( 'a' ).trigger( 'mouseenter' );
        console.log( nxt.children( 'a' ) );
    }
    ;
    backG.bind( 'mousewheel', function ( e ) {
        e.preventDefault( );
        let delta = e.originalEvent.wheelDelta / 120;
        if ( delta ) {
            delta = delta > 0 ? -1 : 1;
        } else
            delta = 0;
        delataScroll += delta;
        console.log( Math.abs( delataScroll ) );
        if ( Math.abs( delataScroll ) < 10 || mouseIsIn )
            return;
        _moveToDelta( delataScroll );
        delataScroll = 0;
        console.log( {cnt: rVal.menu, delta: delta, mouseIsIn: mouseIsIn} );
    } );
    backG.click( rVal.close );
    backG.contextmenu( function ( e ) {
        e.preventDefault( );
        rVal.close( );
    } );
    $( document ).on( 'keydown', ( function ( e ) {
        console.log( e.keyCode, e.key );
        if ( e.keyCode === 27 ) {
            e.stopPropagation( );
            e.stopImmediatePropagation( );
            e.preventDefault( );
            rVal.close( );
            return false;
        }
        if ( e.keyCode === 38 ) {
            e.preventDefault( );
            e.stopPropagation( );
//            if (!mouseIsIn){
//                _moveToDelta(1);
//            }
            return false;
        } else if ( e.keyCode === 40 ) {
            e.preventDefault( );
            e.stopPropagation( );
//            if (!mouseIsIn){
//                _moveToDelta(-1);
//            }
            return false;
        } else if ( e.keyCode === 39 ) {
            e.preventDefault( );
            e.stopPropagation( );
            return false;
        } else if ( e.keyCode === 37 ) {
            e.preventDefault( );
            e.stopPropagation( );
            return false;
        }
    } ) );
    if ( $.isFunction( opt.afterInit ) )
        opt.afterInit.call( this, rVal );
    return rVal;
};

