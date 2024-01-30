/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var view_dialog = {
    _iframe: null,
    options: {
        resizeble: false,
        url: null,
        id: 0,
        minWidth: 900,
        afterGetCompliteAndPaste: null,
        optionName: 'zakaz-num',
        showPrintButton: false,
        showUpdateButton: true,
        showEditButton: false,
        checkId: true,
        addClass: null,
        otherOptions: {},
        getOptionToPrint: {},
        post: false,
    },
    _create: function () {
        this._super();
        this.uiDialog.addClass( 'view-dialog' );
        if ( this.options.addClass )
            this.uiDialog.addClass( this.options.addClass );
        if ( !this.options.title )
            this.uiDialogTitlebar.children( '.ui-dialog-title' ).text( 'Подробности заказа №' + this.options.id );
        if ( this.uiButtonSet ) {
            this.uiButtonSet.addClass( 'btn-group btn-group-xs' );
            this.uiButtonSet.attr( 'role', 'group' );
        }
        this._reloadData();
        console.log( this );
    },
    _proceedAnswer: function ( answ, stayOnPos ) {
        this._dateIsGet.call( this, answ );
        if ( !stayOnPos )
            this._afterFirstStartComlite.call( this );
        if ( $.isFunction( this.options.afterGetCompliteAndPaste ) )
            this.options.afterGetCompliteAndPaste.call( this.element, this );
        if ( this.options.showPrintButton )
            this._createPrintButt();
        if ( this.options.showEditButton )
            this._createEditButt();
        if ( this.options.showUpdateButton )
            this._createUpdateButt();
        this.element.find( '[data-toggle="popover"]' ).popover( {
            trigger: 'focus hover',
            html: true,
//                    container:this.element
        } );
        this.element.find( '[data-toggle="tooltip"]' ).tooltip( {
            delay: {"show": 700, "hide": 100},
            container: this.element
        } );
    },
    _proceedFail: function ( answ, stayOnPos ) {
        console.log( answ );
        let div = $( '<div>' ).addClass( 'panel panel-danger' );
        let h2 = $( '<h2>' );
        if ( window.statusCodes && window.statusCodes[answ.status] ) {
            h2.text( 'Статус: ' + window.statusCodes[answ.status] );
        } else {
            h2.text( 'Статус: ' + answ.status );
        }
        div.append( $( '<div>' ).addClass( 'panel-heading' ).text( 'Ошибка загрузки!' ) );
        div.append( $( '<div>' )
                .append( h2 )
                .append( $( '<p>' ).html( answ.responseText ) )
                .addClass( 'panel-body' ) );
        this._dateIsGet.call( this, div );
        if ( this.options.showUpdateButton )
            this._createUpdateButt();
        if ( !stayOnPos )
            this._afterFirstStartComlite.call( this );
    },
    _post: function ( stayOnPos, uri ) {
        let self = this;
        let uriP = URI.parse( uri.toString() );
        let opt = URI.parseQuery( uriP.query );
        if ( opt.r ) {
            uriP.query = URI.buildQuery( {r: opt.r} );
            delete opt.r;
        } else
            uriP.query = null;
        this._showBunner();
        $.post( URI.build( uriP ), opt ).done( function ( answ ) {
            self._proceedAnswer( answ, stayOnPos );
        } ).fail( function ( answ ) {
            self._proceedFail( answ, stayOnPos );
        } );
        this.element.attr( this.options.optionName, this.options.id );

    },
    _get: function ( stayOnPos, uri ) {
        let self = this;
        this._showBunner();
        $.get( uri.toString() ).done( function ( answ ) {
            self._proceedAnswer( answ, stayOnPos );
        } ).fail( function ( answ ) {
            self._proceedFail( answ, stayOnPos );
        } );
        this.element.attr( this.options.optionName, this.options.id );
    },
    _reloadData: function ( stayOnPos ) {
        stayOnPos = stayOnPos ? true : false
        if ( this.options.url && ( this.options.id || !this.options.checkId ) ) {
            let uri = new URI( this.options.url );
            uri.setSearch( {
                id: this.options.id,
                asDialog: true
            } );
            uri.setSearch( this.options.otherOptions );
            if ( this.options.post ) {
                this._post( stayOnPos, uri );
            } else {
                this._get( stayOnPos, uri );
            }
        } else {
            $( '<h3>' ).text( 'Не передан URL или id заказа' ).appendTo( this.element );
        }
    },
    _createUpdateButt: function () {
        this.element.append( $( '<a>' )
                .addClass( 'cntrbutton update hidden-print' )
                .attr( 'title', 'Обновиь' )
                .append( $( '<span>' ).addClass( 'glyphicon glyphicon-refresh' ) )
                .click( {self: this}, function ( e ) {
                    e.preventDefault();
                    e.data.self._reloadData( true );
                } ) );
    },
    _createEditButt: function () {
        if ( $( '[data-key=' + this.options.id + ']' ).length ) {
            let eButt = $( '[data-key=' + this.options.id + ']' ).find( '[title="Изменить"]' );
            if ( eButt.length ) {
                this.element.append( $( '<a>' )
                        .addClass( 'cntrbutton edit hidden-print' )
                        .attr( 'title', 'Изменить' )
                        .append( $( '<img>' ).attr( {
                            src: '/pic/button_main_page/j_redaktirovat.png',
                            height: 60
                        } ) )
                        .click( {self: this}, function ( e ) {
                            e.preventDefault();
                            e.data.self.close();
                            eButt.trigger( 'click' );
                        } ) );
            }
        }
    },
    print: function () {
        let mywindow = window.open( '', 'my div', 'height=900,width=700' );
        console.log( this );
        mywindow.document.write( '<html><head><title>' + 'Print' + '</title>' );
        $.each( $( '[href*="bootstrap"],[href*="newstyle"]' ), function () {
            mywindow.document.write( '<link href="' + $( this ).attr( 'href' ) + '" rel="' + $( this ).attr( 'rel' ) + '">' );
        } );
        mywindow.document.write( '</head><body >' );
        //table-left-tekhnikal
        mywindow.document.write( $( '<div>' ).append( $( '<div>' ).addClass( 'tecnikals' ).append( this.element.find( '#table-left-tekhnikal' ).clone() ) ).html() );
        $.each( $( '[src*="jquery.js"]' ), function () {
            mywindow.document.write( '<script src="' + $( this ).attr( 'src' ) + '"></script>' );
        } );
        mywindow.document.write( '<script src="/js/printTecnikals.js"></script>' );
        mywindow.document.write( '</body></html>' );
        setTimeout( function () {
            mywindow.document.close();
            setTimeout( function () {
                //mywindow.close();
            }, 400 );

        }, 400 );
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        //mywindow.print();
//        mywindow.close();

    },
    /*
     print: function () {
     let uri = new URI( this.options.url ), self = this;
     let locl = new URI( document.location );
     uri.setSearch( {
     id: this.options.id,
     asDialog: false
     } );
     $.each( this.options.getOptionToPrint, function ( k, v ) {
     uri.setSearch( k, v );
     } );
     uri.origin( locl.origin() );
     console.log( locl.origin() );
     console.log( uri.toString() );
     $( 'body' ).css( 'cursor', 'wait' );
     let tmp = $( '<iframe>' ).attr( {
     src: ( uri.toString() + '&embedded=false' ),
     style: "visibility: hidden",
     //            style:"position:absolute;top:100px;left:100px;",
     width: 00,
     height: 00,
     } ).appendTo( 'body' );
     tmp.on( 'load', function () {
     console.log( 'loaded' );
     let win = tmp[0].contentWindow || tmp[0];
     let iBody = $( win.document ).children( 'html' ).children( 'body' );
     console.log( iBody );
     iBody.html( self.element.html() );
     let header = $( '<head>' ).append( $( '<title>' ).text( 'Заказ №' + self.options.id ) ).insertBefore( iBody );
     setTimeout( function () {
     console.log( "tmp.contents().find( '.table2-small-block' ).height()", tmp.contents().find( '.table2-small-block' ).height() );
     console.log( "tmp.contents().find( '#view-files' + self.options.id ).children( 'div' ).height()", tmp.contents().find( '#view-files' + self.options.id ).children( 'div' ).height() );
     let h = 1058 - ( tmp.contents().find( '#table-left-tekhnikal' ).height() - tmp.contents().find( '#view-files' + self.options.id ).children( 'div' ).height() );
     tmp.contents().find( '#table-left-tekhnikal' ).height( 1102 );
     let vf = tmp.contents().find( '#view-files' + self.options.id );
     vf.children( 'div' ).css( 'height', h + 'px' );//.children().attr( 'height', h + 'px' );
     let wW = vf.children( 'div' ).width() - 5;
     if ( vf.children( 'div' ).children().height() >= vf.children( 'div' ).children().width() ) {
     vf.children( 'div' ).children( 'img' ).attr( {height: ( h ) + 'px', width: 'auto'} );
     } else {
     vf.children( 'div' ).children( 'img' ).attr( {height: ( ( h ) * ( wW / vf.children( 'div' ).children().width() ) ) + 'px', width: 'auto'} );
     }
     vf.children( 'div' ).children( 'iframe' ).attr( {height: ( h + 10 ) + 'px'} );
     win.print();
     setTimeout( function () {
     $( 'body' ).css( 'cursor', 'auto' );
     //tmp.remove();
     }, 1000 );
     }, 500 );
     } );

     },
     * */
    _createPrintButt: function () {
        this.element.append( $( '<a>' )
                .addClass( 'cntrbutton print hidden-print' )
                .attr( 'title', 'Печать' )
                //.append($('<span>').addClass('glyphicon glyphicon-print'))
                .append( $( '<img>' ).attr( {
                    src: '/pic/button_main_page/j_printer.png',
                    height: 60
                } ) )
                .click( {self: this}, function ( e ) {
                    console.log( 'print' );
                    e.preventDefault();
                    e.data.self.print();
                } ) );
    },
    _dateIsGet: function ( answ ) {
        this._hideBunner();
        if ( $.type( answ ) === 'object' )
            this.element.empty().append( answ );
        else
            this.element.empty().html( answ );
    },
    open: function () {
        this._super();
    },
    close: function () {
        this._super();
        this.element.remove();
        this.uiDialog.remove();
    },

};

( function ( $ ) {
    $.widget( "custom.viewDialog", $.custom.maindialog, $.extend( {}, view_dialog ) );
}( jQuery ) );