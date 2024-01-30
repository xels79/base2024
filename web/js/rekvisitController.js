/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//alert('Rekvizit');
let Rekvizit_main = {
    tinyMCEPostEditIsOpen: false,
    options: {
        title: 'Реквизиты',
        listUrl: '',
        saveFileUrl: '',
        removeUrl: '',
        getfileUrl: '',
        sendUrl: '',
        addTabUrl: '',
        removeTabUrl: '',
        renameTabUrl: '',
        fNamePerfix: 'doc',
        height: 400,
        width: 800,
        tabNames: [ ]
    },
    _fileAbort: false,
    _create: function ( ) {
        this._super( );
        this.uiDialog.addClass( 'view-dialog' );
        this.element.addClass( 'rekvizit-dialog' );
        this._reload( );
    },
    _addFileClick: function ( e ) {
        $( '#' + e.data.panelId + '-file-input' ).trigger( 'click' );
    },
    _createItemTR: function ( firmKey, index, page, item, page ) {
        let tr = $( '<tr>' );
        let uri = new URI( this.options.getfileUrl );
        uri.setSearch( {
            firmKey: firmKey,
            index: index, //i
            fNamePerfix: this.options.fNamePerfix
        } );
        tr.append( $( '<td>' ).append( $( '<span>' ).text( item.name ) ) );
        tr.append( $( '<td>' ).append( $( '<a>' ).attr( {
            href: uri.toString( )
        } ).addClass( 'btn btn-default' ).text( 'Скачать' ) ) );
        tr.append( $( '<td>' ).append( $( '<a>' ).addClass( 'btn btn-default' ).text( 'Отправить' ).click( {self: this, firmKey: firmKey, page: page, index: index}, this._send ) ) );
        tr.append( $( '<td>' ).append( $( '<a>' ).addClass( 'btn btn-default' ).text( 'Удалить' ).click( {self: this, firmKey: firmKey, page: page, fName: item.name, index: index}, this._remove ) ) );
        return tr;
    },
    _drawTabContent: function ( firmKey, items, panelId, page ) {
        let tb = $( '<tbody>' ), self = this;
        let tf = $( '<tfoot>' ).append( $( '<tr>' ).append( $( '<td>' ).attr( 'colspan', 4 ).append( $( '<button>' ).addClass( 'btn btn-success' ).click( {
            panelId: panelId,
            firmKey: firmKey,
            page: page,
            self: this
        }, this._addFileClick ).append( $( '<span>' ).addClass( 'glyphicon glyphicon-plus' ) ) ) ) );
        let iLn = $.isArray( items ) ? items.length : Object.keys( items ).length;
        for ( let i = 0; i < iLn; i++ ) {
            if ( items[i].name !== 'NOTREAD' ) {
                tb.append( this._createItemTR( firmKey, i, page, items[i], page ) );
            }
        }
        return $( '<table>' ).addClass( 'table' ).append( tb ).append( tf );
    },
    _remove: function ( e ) {
        let firmKey = e.data.firmKey, self = e.data.self, index = e.data.index;
        let fName = e.data.fName;
        console.log( {firmKey: firmKey, index: index, fNamePerfix: self.options.fNamePerfix} );
        if ( self.options.removeUrl ) {
            m_alert( 'Внимание', 'Удалить файл: "' + fName + '"', function () {
                self.showBunner( 'Загрузка' );
                $.post( self.options.removeUrl, {firmKey: firmKey, index: index, fNamePerfix: self.options.fNamePerfix} ).done( function ( data ) {
                    self.hideBunner( );
                    if ( data.status !== 'ok' ) {
                        m_alert( 'Ошибка Сервера', data.errorText, true, false );
                    } else {
                        self._reload( e.data.page );
                    }
                } ).fail( function ( d, d2, d3, d4 ) {
                    self.hideBunner( );
                    if ( d.responseJSON && d.responseJSON.message && d.responseJSON.name && d.responseJSON.status ) {
                        m_alert( d.responseJSON.name, 'Статус: ' + d.responseJSON.status + '<br>' + d.responseJSON.message, 'Закрыть', false );
                    }
                } );
            } );
        } else {
            console.error( 'Не передан removeUrl' );
        }
    },
    _startTinyMCEToEditPost: function ( callBack ) {
        this.tinyMCEPostEditIsOpen = true;
        tinymce.init( {
            selector: 'textarea#mail-mailbody', // change this value according to your HTML
            plugins: [
                'searchreplace table help lists advlist image imagetools print pagebreak',
                'spellchecker responsivefilemanager link directionality responsivefilemanager filemanager',
                'hr visualblocks code'
            ],
            menubar: false, //'file edit insert view format table tools help',
            toolbar: 'undo redo | styleselect | bold italic |alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor | responsivefilemanager  | spellchecker | image | link',
            language: 'ru',
            save_onsavecallback: function ( ) {
                console.log( 'Saved' );
            },
//            width: 500,
            height: 250,
            resize: false,
            spellchecker_language: 'ru',
            spellchecker_rpc_url: '/spellchecker/spellchecker.php',
//            image_prepend_url: "http://base.asterionspb.ru/",
            relative_urls: false,
            remove_script_host: false,
//            document_base_url: "http://www.base.asterionspb.ru/",
            setup: function ( editor ) {
                editor.on( 'init', function ( e ) {
                    console.log( 'Editor was initialized.' );
                    //tinymce.activeEditor.setContent( zametkaContent );
                    if ( $.isFunction( callBack ) ) {
                        callBack.call();
                    }
                } );
            },
            image_advtab: true,
            external_filemanager_path: "/responsive_filemanager/filemanager/",
            filemanager_title: "Проводник (Responsive Filemanager v9)",
            external_plugins: {
                "filemanager": "/responsive_filemanager/filemanager/plugin.min.js",
                "responsivefilemanager": "/responsive_filemanager/tinymce/plugins/responsivefilemanager/plugin.min.js",
            },
        } );

    },
    _formSubmit: function ( e ) {
        let self = e.data.self, dialog = e.data.dialog;
        tinymce.activeEditor.hide( );
        tinyMCE.remove( );
        this.tinyMCEPostEditIsOpen = false;
        let fd = new FormData( $( this ).get( 0 ) );
        //append
        fd.append( 'fNamePerfix', self.options.fNamePerfix );
        e.preventDefault( );
        dialog.maindialog( 'showBunner', 'Отправляю' );
        $.ajax( {
            type: 'post',
            url: $( this ).attr( 'action' ),
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            forceSync: false,
            success: function ( data ) {
                dialog.maindialog( 'hideBunner' );
                if ( data.status === 'is send' ) {
                    dialog.maindialog( 'close' );
                    m_alert( 'Внимание', 'Сообщение отправлено', true, false );
                } else if ( data.status === 'edit' ) {
                    dialog.empty( ).html( data.html );
                    self._startTinyMCEToEditPost( function () {
                        dialog.parent().offset( {left: dialog.parent().offset().left, top: ( $( window ).height( ) - dialog.parent().height() ) / 2} );
                    } );
                    $( '#file-send-form' ).submit( {self: self, dialog: dialog}, self._formSubmit );
                } else {
                    m_alert( 'Ошибка Сервера', data.errorText, true, false );
                }
            },
            error: function ( d, d2, d3, d4 ) {
                dialog.maindialog( 'close' );
                if ( d.responseJSON && d.responseJSON.message && d.responseJSON.name && d.responseJSON.status ) {
                    m_alert( d.responseJSON.name, 'Статус: ' + d.responseJSON.status + '<br>' + d.responseJSON.message, 'Закрыть', false );
                } else if ( d.responseJSON && d.responseJSON.message && d.responseJSON.name && d.status ) {
                    m_alert( d.responseJSON.name, 'Статус: ' + ( d.statusText ? d.statusText : d.status ) + '<br>' + d.responseJSON.message, 'Закрыть', false );
                }
            },
        } );
    },
    _showForm: function ( html, fName ) {
        let dialog = $( '<div>' ).appendTo( 'body' );
        let self = this;
        dialog.maindialog( {
            title: 'Отправить файл (' + fName + ')...',
            modal: true,
            width: 800,
            afterInit: function ( d ) {
                console.log( this, d );
                $( this ).html( html );
                $( '#file-send-form' ).submit( {self: self, dialog: dialog}, self._formSubmit );
                d.uiDialog.offset( {left: d.uiDialog.offset().left, top: ( $( window ).height( ) - d.uiDialog.height() ) / 2} );
                self._startTinyMCEToEditPost( function () {
                    d.uiDialog.offset( {left: d.uiDialog.offset().left, top: ( $( window ).height( ) - d.uiDialog.height() ) / 2} );
                } );
            },
            beforeClose: function ( ) {
                if ( self.tinyMCEPostEditIsOpen ) {
                    tinyMCE.remove( );
                    this.tinyMCEPostEditIsOpen = false;
                }
                dialog.remove( );
            }
        } );
    },
    _send: function ( e ) {
        let firmKey = e.data.firmKey, self = e.data.self, index = e.data.index;
        let fName = $( this ).parent( ).parent( ).children( ':first-child' ).text( );
        if ( self.options.sendUrl ) {
            self.showBunner( 'Загрузка' );
            $.post( self.options.sendUrl, {firmKey: firmKey, index: index, fNamePerfix: self.options.fNamePerfix} ).done( function ( data ) {
                self.hideBunner( );
                if ( data.status === 'edit' ) {
                    self._showForm( data.html, fName );
                } else if ( data.status !== 'is send' ) {
                    m_alert( 'Ошибка Сервера', data.errorText, true, false );
                } else {
                    self._reload( e.data.page );
                }
            } ).fail( function ( d, d2, d3, d4 ) {
                self.hideBunner( );
                if ( d.responseJSON && d.responseJSON.message && d.responseJSON.name && d.responseJSON.status ) {
                    m_alert( d.responseJSON.name, 'Статус: ' + d.responseJSON.status + '<br>' + d.responseJSON.message, 'Закрыть', false );
                }
            } );
        } else {
            console.error( 'Не передан sendUrl' );
        }
    },
    _renameAnimation: function ( {el, firmKey, index = 0, incrWidth = 20, height = 20, onDoneCallBack = null} ){
        let self = this;
        $( el ).css( 'position', 'relative' );
        let a = $( el ).children( ':first-child' );
        let oldVal = a.text();
        let width = $( el ).width() + ( $( el ).width() / 100 * incrWidth );
        let abort = false;
        console.log( el, a );
        let inp = $( '<input>' ).attr( {
            type: 'text'
        } ).val( oldVal ).css( {
            width: width < 120 ? 120 : width,
            height: height,
            position: 'absolute',
            //padding: '2px'
        } ).focusout( function () {
            $( el ).removeAttr( 'style' );
            $( this ).remove();
            let newVal = $.trim( $( this ).val() );
            if ( newVal.length && newVal !== oldVal ) {
                if ( !abort ) {
                    let tm = setInterval( function () {
                        if ( a.attr( 'style' ) ) {
                            a.removeAttr( 'style' );
                        } else {
                            a.css( 'visibility', 'hidden' );
                        }
                    }, 300 );
                    $.post( self.options.renameTabUrl, {firmKey: firmKey, id: index, name: newVal} ).done( function ( answ ) {
                        clearInterval( tm );
                        if ( answ.status === 'ok' ) {
                            a.text( newVal );
                            if ( a.attr( 'title' ) && newVal.length > 42 )
                                a.attr( 'title', newVal );
                            if ( $.isFunction( onDoneCallBack ) ) {
                                onDoneCallBack.call( self, newVal );
                            }
                        } else {
                            new mDialog.m_alert( {
                                headerText: 'Ошибка сервера',
                                content: answ.errorText,
                                okClick: true,
                                cancelClick: false,
                            } );
                        }
                    } );
                }
            }
        } ).keydown( function ( e ) {
            if ( e.keyCode === 27 || e.keyCode === 13 ) {
                if ( e.keyCode === 27 )
                    abort = true;
                $( this ).blur();
            }
        } ).contextmenu( function ( e ) {
            e.stopPropagation();
            e.preventDefault();
        } ).focus( function () {
            this.select();
        } );
        //a.hide();

        $( el ).append( inp );
        inp.css( 'top', ( $( el ).height() - inp.height() ) / 2 );
        let shift = inp.width() - $( el ).width();
        inp.css( 'left', -1 * shift );
        if ( $( el ).parent().offset().left > inp.offset().left ) {
            inp.css( 'z-index', 90000 );
            inp.offset( {
                left: $( el ).parent().offset().left - 5,
                top: inp.offset().top
            } );
        }
        inp.trigger( 'focus' );
    },
    _drawTabs: function ( dt, page ) {
        let mc = $( '<div>' ).addClass( 'main-content' ), self = this;
        page = $.type( page ) !== 'undefined' ? page : 0;
        this.element.empty( ).append( mc );
        let navs = $( '<ul>' ).addClass( 'nav nav-tabs' ).appendTo( mc );
        let tabContent = $( '<div>' ).addClass( 'tab-content' ).appendTo( mc );
        console.log( dt );
        let countTabs = Object.keys( dt.firms ).length;
        //for ( val in Object.keys( dt.firms ) ) {
        $.each( Object.keys( dt.firms ), function ( key, val ) {
            let panelId = 'tab-pane-rekvizit-' + val + self.uuid;
            let nav = $( '<li>' ).append( $( '<a>' ).text( self.options.tabNames[val] ? self.options.tabNames[val] : dt.firms[val].name ).attr( {
                'data-toggle': 'tab',
                href: '#' + panelId
            } ).click( function ( e ) {
                $( this ).tab( 'show' );
            } ) ).appendTo( navs );
            let tab = $( '<div>' ).attr( {id: panelId} ).addClass( 'tab-pane' ).append( self._drawTabContent( dt.firms[val].id, dt.firms[val].items, panelId, val ) ).appendTo( tabContent );
            if ( $.type( page ) === 'undefined' || !page || page === '0' ) {
                page = dt.firms[val].id;
            }
            nav.contextmenu( {self: self}, function ( e ) {
                e.preventDefault();
                let el = this;
                if ( self.options.fNamePerfix !== 'doc' ) {
                    let dd_options = {
                        posX: e.clientX,
                        posY: e.clientY,
                        items: [ {
                                label: 'Удалить вкладку',
                                click: function () {
                                    if ( !self.options.removeTabUrl ) {
                                        console.warn( 'Не передан removeTabUrl' );
                                        return;
                                    }
                                    m_alert( 'Внимание', 'Вкладка "' + dt.firms[val].name + '" будет удалена.<br>Действие не отменить.', {
                                        'label': 'Продолжить',
                                        click: function () {
                                            $.post( self.options.removeTabUrl, {firmKey: dt.firms[val].id, fNamePerfix: self.options.fNamePerfix} ).done( function ( answ ) {
                                                if ( answ.status !== 'ok' ) {
                                                    m_alert( 'Ошибка сервера', answ.errorText, 'Закрыть', false );
                                                    self.hideBunner();
                                                } else {
                                                    self._reload();
                                                }
                                            } );
                                            self.showBunner( 'Удаляем вкладку...' );
                                        }
                                    }, 'Отмена' );
                                }
                            }
                        ]};
                    if ( self.options.renameTabUrl ) {
                        let el = this;
                        dd_options.items.push( {
                            label: 'Переименовать вкладку...',
                            click: function () {
                                self._renameAnimation( {firmKey: dt.firms[val].id, el: el} );
                            }
                        } );
                    }
                    if ( countTabs - 1 === $( this ).index() ) {
                        dd_options.items.push( {
                            label: 'Добавить вкладку...',
                            click: function () {
                                self._addTabClick.call( el, e )
                            }
                        } );
                    }
                    dropDown( dd_options );
                }
            } );
            tab.append( $( '<form>' ).attr( {
                id: panelId + '-form',
                method: 'post'
            } )
                    .append( $( '<input>' ).attr( {
                        type: 'file',
                        id: panelId + '-file-input',
                        name: 'add_file',
                        accept: 'image/*,application/pdf,application/msword,application/vnd.ms-excel'
                    } ).css( 'display', 'none' ) ).change( {self: self, page: dt.firms[val].id}, function ( e ) {
                console.log( $( this ).parent( ) )
                self._sendData( panelId, dt.firms[val].id )
            } )
                    .append( $( '<input>' ).attr( {
                        type: 'text',
                        name: 'firmId'
                    } ).css( 'display', 'none' ).val( dt.firms[val].id ) )
                    );
            if ( dt.firms[val].id == page ) {
                nav.addClass( 'active' );
                tab.addClass( 'active' );
            }
        }
        );
        if ( self.options.fNamePerfix !== 'doc' && !countTabs ) {
            navs.append( $( '<li>' ).append( $( '<a>' ).append( $( '<span>' ).addClass( 'glyphicon glyphicon-plus' ) ).addClass( 'btn btn-success' ).attr( {
                href: '#'
            } ).click( {self: self}, self._addTabClick ) ) );
        }
    },
    _addTabClick: function ( e ) {
        let self = e.data.self;
        e.preventDefault();
        //addTabUrl
        m_alert( 'Добавить вкладку', '<div class="input-group" style="margin: 10px 140px 10px 10px;">'
                + '<span class="input-group-addon">Укажите название вкладки:</span>'
                + '<input type="text" class="form-control" id="new-tab-name" placeholder="Название">'
                + '</div>', {
                    'label': 'Добавить',
                    click: function () {
                        if ( !$( '#new-tab-name' ).val() ) {
                            $( '#new-tab-name' ).trigger( 'focus' );
                            return false;
                        }
                        if ( !self.options.addTabUrl ) {
                            console.warn( 'Не передан addTabUrl' );
                            return;
                        }
                        $.post( self.options.addTabUrl, {tabName: $( '#new-tab-name' ).val(), fNamePerfix: self.options.fNamePerfix} ).done( function ( answ ) {
                            if ( answ.status !== 'ok' ) {
                                m_alert( 'Ошибка сервера', answ.errorText, 'Закрыть', false );
                            } else {
                                self._reload( answ.page );
                            }
                        } );
                    }
                }, 'Отмена' );
    },
    _reload: function ( page ) {
        let self = this;
        if ( !this.options.listUrl ) {
            console.error( 'rekvizit: непередан listUrl' );
            return;
        }
        this.showBunner( 'Загрузка' );
        $.post( this.options.listUrl, {fNamePerfix: self.options.fNamePerfix} ).done( function ( dt ) {
            self._drawTabs( dt, page );
            self.hideBunner( );
        } );
    },
    _sendData: function ( panelId, page ) {
        let fd = new FormData( $( '#' + panelId + '-form' ).get( 0 ) ), self = this;
        console.log( fd );
        fd.append( 'fNamePerfix', self.options.fNamePerfix );
        if ( this.options.saveFileUrl ) {
            this.showBunner( 'Сохраняю файл' );
            $.ajax( {
                type: 'post',
                url: this.options.saveFileUrl,
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                forceSync: false,
                complete: function ( ) {
                    self.hideBunner( );
                },
                success: function ( data ) {
                    if ( data.status === 'ok' ) {
                        self._reload( page );
                    } else {
                        m_alert( 'Ошибка Сервера', data.errorText, true, false );
                    }
                },
                xhr: function ( ) {
                    let xhrobj = $.ajaxSettings.xhr( );
                    if ( xhrobj.upload ) {
                        xhrobj.upload.addEventListener( 'progress', function ( event ) {
                            console.log( event );
                            let position = event.loaded || event.position;
                            if ( !self._fileAbort ) {
                                //self._setStatus(it,position);
                                console.log( Math.round( position / event.total * 100 ) + '%' );
                                let progress = $( '#save-progress' );
                                if ( !progress.length ) {
                                    progress = $( '<progress>' ).attr( {
                                        max: 100,
                                        id: 'save-progress'
                                    } );
                                    $( '.bunner' ).children( ).empty( ).append( '<div>Сохраняю...</div>' ).css( 'text-align', 'center' ).append( progress );
                                }
                                progress.attr( 'value', Math.round( position / event.total * 100 ) );
                                progress.text( 'Сохраняем ' + Math.round( position / event.total * 100 ) + '%' );
                            } else {
                                xhrobj.abort( );
                            }
                        }, false );
                    }
                    return xhrobj;
                },
            } );
        } else {
            console.error( 'rekvizit: непередан saveFileUrl' );
        }
    }
};
( function ( $ ) {
    $.widget( "custom.rekvizit", $.custom.maindialog, $.extend( {}, Rekvizit_main ) );
}( jQuery ) );
( function ( $ ) {
    $.widget( "custom.rekvizitOpen", $.custom.openButton, {_className: 'rekvizit'} );
}( jQuery ) );