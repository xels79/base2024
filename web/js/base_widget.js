/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if ( !window.unDef ) {
    window.unDef = 'undefined';
}
$.widget( "custom.baseW", {
    fields: {},
    dialog: null,
    getWidgetName: function () {
        return 'baseW';
    },
    createMessageText: function ( txt ) {
        return this.getWidgetName() + ':' + txt;
    },
    _processOption: function ( key, val ) {
//        console.debug('processOption обрабатываем ключ:',key);
        if ( $.type( this.options[key] ) !== unDef ) {

        } else {
            if ( $.type( val ) !== 'object' ) {
                if ( val !== null )
                    console.error( this.getWidgetName() + ':processOption.' + key, val );
            } else if ( $.type( val.message ) !== 'undefined' && $.type( val['default'] ) === unDef ) {
                console.error( this.getWidgetName() + ':processOption.' + key, val.message );
//                this.options['create']=false;
            } else if ( $.type( val['default'] ) !== unDef ) {
                if ( $.type( val.message ) === unDef )
                    console.debug( this.getWidgetName() + ':processOption.' + key, 'Oбязательное свойство не задано по умолчанию:', val.default );
                else
                    console.debug( this.getWidgetName() + ':processOption.' + key, val.message, val.default );
                this.options[key] = val.default;
            } else
                console.error( this.getWidgetName() + ':processOption.' + key, 'Oбязательное свойство не задано' );
        }
    },
    _checkAllOptions: function () {
        let self = this;
        let allOk = true;
        $.each( this.options, function ( key ) {
            allOk = key in self.fields;
            if ( !allOk )
                console.warn( self.getWidgetName() + ':checkAllOptions."' + key + '" Неизвестное свойство' );
            //return allOk;
        } );
        $.each( this.fields, function ( key, val ) {
            self._processOption( key, val );
            return self.options.create;
        } )
        return allOk;
    },
    _create: function () {
        this.fields.disabled = null;
        this.fields.create = null;
        console.group( this.createMessageText( 'init' ) );
        this.fields = $.extend( {
            requestUpdateParent: {message: 'Функция не заданна', 'default': false},
            afterSubmit: {message: 'Функция не заданна', 'default': false},
            beforeClose: {message: 'Функция не заданна', 'default': false},
            afterInit: {message: 'Функция не заданна', 'default': false},
            loadPicUrl: {message: 'Url картинка кнопки загрузка не задан', 'default': false},
            loadPjaxPicUrl: {message: 'Url картинка кнопки банер загрузки pjax', 'default': false},
            pointPicUrl: {message: 'Url пустая Картинка не задан', 'default': '#'},

        }, this.fields );
        this.options.create = this._checkAllOptions();
        console.debug( this );
        console.groupEnd();
        return this.options.create;
    },
    _setOptions: function ( options ) {
        console.debug( this.getWidgetName() + ':_setOption', options );
    },
    _setOption: function ( key, value ) {
        console.debug( this.getWidgetName() + ':_setOption', key, value );
    },
    getDialog: function () {
        return this.dialog;
    },
    submitBackText: '',
    busy: function ( set ) {
        let btn = $( this.options.form_Id ).find( '[type=submit]' );
        if ( set ) {
            if ( this.dialog.isFadedOut )
                return;
            this.submitBackText = btn.text();
            console.debug( this.createMessageText( 'busy.submitBackText' ), this.submitBackText );
            if ( $.type( this.options.loadPicUrl ) === 'string' )
                btn.empty().append( $.fn.creatTag( 'img', {
                    src: this.options.loadPicUrl,
                    style: {width: '100%'}
                } ) );
            else
                btn.text( 'Загрузка...' );
            this.dialog.fadeOut();
        } else {
//            if ( $.type( this.dialog.isFadedOut ) !== 'undefined' && !this.dialog.isFadedOut )
//                return;
            if ( btn )
                if ( $.type( this.submitBackText ) === 'string' )
                    btn.text( this.submitBackText );
            this.dialog.fadeIn();
        }
    },
    initRemovePicKeys: function () {
        let el = this.dialog.element ? this.dialog.element : this.dialog;
        el.find( '[role=removepic]' ).unbind( 'click' );
        el.find( '[role=removepic]' ).click( {controller: this}, function ( e ) {
            let controller = e.data.controller;
            e.preventDefault();
            console.log( controller.createMessageText( 'removePickKeys' ), input );
            let input = $( this ).parent().find( '>input[type=file]' );
            input.val( null );
            $( this ).parent().find( '>input[type=hidden]' ).remove();
            $( this ).parent().find( 'img' ).attr( 'src', controller.options.pointPicUrl );
            if ( $( this ).attr( 'data-hide-img' ) ) {
                $( this ).parent().find( 'img' ).css( 'visibility', 'hidden' );
            }
            input.parent().prepend( $.fn.creatTag( 'input', {
                type: 'hidden',
                name: $( this ).attr( 'data-form-name' ) + '[' + ( $( this ).attr( 'data-attr-name' ) + 'remove' ) + ']',
                value: 'remove'
            } ) );
            $( this ).css( 'visibility', 'hidden' );
        } );
    },
    ajax: function ( fd, onOk ) {
        this.busy( true );
        let self = this;
        $.ajax( {
            type: 'post',
            url: self.options.requestUrl,
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            forceSync: false,
            complete: function () {
                self.busy( false );
            },
            success: function ( data ) {
//                console.debug(self.createMessageText('submit:succes'),data);
                console.log( data );
                if ( $.type( data['status'] ) != 'udefined' ) {
                    if ( $.isFunction( onOk ) )
                        if ( onOk.call( this, self, data ) === false )
                            return;
                    if ( $.isFunction( self.options.afterSubmit ) )
                        if ( self.options.afterSubmit.call( this, self, data ) === false )
                            return;
                    if ( data.status == 'form' ) {
                        console.debug( self.createMessageText( 'submit:ok' ), data );
                        self.dialog.body.empty().html( data.html );
                        return;
                    } else if ( data.status == 'saved' ) {
                        console.debug( self.createMessageText( 'submit:ok' ), data );
                        if ( $.isFunction( self.options.requestUpdateParent ) )
                            if ( self.options.requestUpdateParent.call( this, self, data ) === false )
                                return;
                        self.dialog.close();
                        delete self;
                        return;
                    } else if ( data.errors ) {
                        console.debug( self.createMessageText( 'submit:error' ), data );
                        let fName = '';
                        if ( data.modelName )
                            fName = data.modelName.toLowerCase();
                        $.each( data.errors, function ( id, val ) {
                            let pId = '#' + fName + '-' + id.toLowerCase();
                            $( pId ).parent().addClass( 'has-error' );
                            let span = $( pId ).parent().next( 'span' );
                            if ( span.length === 0 ) {
                                span = $( pId ).parent().find( '.dialog-error' );
                                if ( span.length === 0 ) {
                                    span = $.fn.creatTag( 'span', {
                                        'class': 'dialog-error'
                                    } );
                                    $( pId ).parent().append( span );
                                }
                            }
                            console.log( span );
                            span.css( 'display', 'block' );
                            span.text( val );
                        } );
                        return;
                    }
                    console.debug( self.createMessageText( 'submit:XZ' ), data );
                } else {
                    console.debug( self.createMessageText( 'submit:XZ(status not set)' ), data );
                }
            },
            error: function ( jqXHR ) {
                self.busy( false );
                self.dialog.body.empty().html( jqXHR.responseText );
                self.dialog.header.children( 'span:first-child' ).text( 'Ошибка загрузки' );
            }
        } );
    }

} );
