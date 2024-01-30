/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

let Zametki_main = {
    _closeAnyWay: false,
    _zametkaName: '',
    _zametkaId: 0,
    options: {
        renameZametkaUrl: '',
        uploadMCIUrl: '',
        imageBasePathUrl: '',
        width: 900
    },
    _askZametkaName: function ( callBack, dialog ) {
        let self = this;
        let tmpD = $( '<div>' ).attr( {
            id: 'zametka-edit-dialog',
            title: 'Внимание',
        } );
        tmpD.append( $( '<label>' ).text( 'Название заметки' ) )
                .append( $( '<input>' ).attr( {
                    type: 'text',
                    id: 'zametkaName',
                    style: "margin-left:10px;"
                } ) )
                .appendTo( 'body' )
                .maindialog( {
                    minimizable: false,
                    modal: true,
                    width: 500,
                    height: 200,
                    buttons: [
                        {
                            text: "Сохранить",
                            icon: "ui-icon-heart",
                            click: function ( ) {
                                let zName = $.trim( $( '#zametkaName' ).val( ) );
                                if ( zName.length ) {
                                    $( this ).maindialog( "close" );
                                    //self._saveZametka( firmKey, page, zName, tinyMCE.activeEditor.getContent( ), zametkaId, dialog, true );
                                    self._zametkaName = zName;
                                    dialog.maindialog( 'headerText', zName );
                                    callBack.call( self, zName );
                                } else {
                                    if ( !tmpD.children( 'small' ).length )
                                        tmpD.append( $( '<small style="color:red;">Укажите название</small>' ) )
                                    $( '#zametkaName' ).val( zName ).trigger( 'focus' );
                                }
                            }
                        },
                        {
                            text: "Отмена",
                            icon: "ui-icon-heart",
                            click: function ( ) {
                                $( this ).maindialog( "close" );
                                callBack.call( self, false );
                            }
                        }
                    ],
                    open: function ( ) {
                        $( this ).parent( ).addClass( 'alert-maindialog' );
                        $( '#zametkaName' ).keyup( function ( ) {
                            if ( $( this ).val( ).length ) {
                                tmpD.children( 'small' ).remove( );
                            }
                        } );
                    },
                    close: function ( ) {
                        tmpD.remove( );
                    }
                } );
    },
    _saveZametka: function ( tabKey, page, zametkaName, zametkaContent, zametkaId, dialog, andClose ) {
        let self = this;
        if ( !this._zametkaName && !this._zametkaId ) {
            this._askZametkaName( function ( name ) {
                if ( name ) {
                    self.__saveZametka( tabKey, page, name, zametkaContent, this._zametkaId, dialog, andClose );
                }
            }, dialog );
        } else {
            this.__saveZametka( tabKey, page, this._zametkaName, zametkaContent, this._zametkaId, dialog, andClose );
        }
    },
    __saveZametka: function ( tabKey, page, zametkaName, zametkaContent, zametkaId, dialog, andClose ) {
        let self = this;
        if ( !this.options.saveFileUrl ) {
            new mDialog.m_alert( {
                headerText: 'Ошибка',
                content: 'Сохранение невозможно',
                okClick: 'Закрыть'
            } );
            return;
        }
        andClose = andClose ? andClose : false;
        dialog.maindialog( 'showBunner', 'Сохраняю' );
        tinymce.activeEditor.hide( );
        if ( !this.options.saveFileUrl ) {
            dialog.maindialog( 'hideBunner' );
            console.log( 'Не передан saveFileUrl' );
            return;
        }
        let param = {
            tabKey: tabKey,
            zametkaName: zametkaName,
            zametkaContent: zametkaContent
        };
        if ( zametkaId )
            param.id = zametkaId;
        $.post( this.options.saveFileUrl, param ).done( function ( answ ) {
            dialog.maindialog( 'hideBunner' );
            tinymce.activeEditor.show( );
            if ( answ.status === 'ok' ) {
                if ( andClose ) {
                    self._closeAnyWay = true;
                    dialog.maindialog( 'close' );
                } else {
                    self._zametkaId = answ.id;
                    self._closeAnyWay = false;
                }
            } else {
                self._closeAnyWay = false;
                new m_alert( 'Ошибка сохранения', answ.errorText, true, false );
            }
        } );
    },
    _editText: function ( param ) {
        let {firmKey, page, zametkaName = '', zametkaContent = '', zametkaId = null} = param;
        let d = $( '<div>' )
                .attr( {
                    id: 'zametka-edit-dialog',
                    title: zametkaName ? zametkaName : 'Новая заметка',
                } );
        //.html( zametkaContent );
        let self = this
        this._zametkaName = zametkaName;
        this._zametkaId = zametkaId;
        this._closeAnyWay = false;
        d.maindialog( {
            minimizable: false,
            modal: true,
            width: 1024,
            height: 682,
            buttons: [
                {
                    text: "Сохранить",
                    icon: "ui-icon-heart",
                    click: function ( ) {
                        let dialog = $( this );
                        self._saveZametka( firmKey, page, this._zametkaName, tinyMCE.activeEditor.getContent( ), this._zametkaId, dialog );
                    }
                },
                {
                    text: "Закрыть",
                    icon: "ui-icon-heart",
                    click: function ( ) {
                        $( this ).maindialog( "close" );
                    }
                }
            ],
            beforeClose: function ( ) {
                if ( tinymce.activeEditor.isDirty( ) && !self._closeAnyWay && self.options.saveFileUrl ) {
                    new mDialog.m_alert( {
                        headerText: 'Внимание',
                        content: 'Все не сохраненные данные будут потерены',
                        okClick: {
                            label: 'Отменить изменения',
                            click: function ( ) {
                                self._closeAnyWay = true;
                                d.maindialog( 'close' );
                            },
                            class: 'btn btn-red'
                        },
                        canselClick: 'Вернуться',
                        midButton: {
                            label: 'Сохранить и выйти',
                            click: function () {
                                self._saveZametka( firmKey, page, this._zametkaName, tinyMCE.activeEditor.getContent( ), this._zametkaId, d, true );
                            },
                            class: 'btn btn-blue'
                        }
                    } );
                    return false;
                } else if ( !self.options.saveFileUrl && !self._closeAnyWay ) {
                    new mDialog.m_alert( {
                        headerText: 'Внимание',
                        content: 'Сохранение невозможно.<br>Все не сохраненные данные будут потерены',
                        okClick: {
                            label: 'Закрыть',
                            click: function ( ) {
                                self._closeAnyWay = true;
                                d.maindialog( 'close' );
                            },
                        },
                    } );
                    return false;
                }
            },
            close: function ( ) {
                d.remove( );
                tinyMCE.remove( );
                self._reload( firmKey );
            },
            open: function ( ) {
                $( this ).parent( ).addClass( 'zametka-edit' );
                $( this ).append( $( '<div>' ).addClass( 'zametka-content' ).attr( 'id', 'tini-content' ) );
                $( document ).off( 'focus', document );
                $( document ).focus( function ( e ) {
                    e.stopPropagation();
                } );
                tinymce.init( {
                    selector: 'div#tini-content', // change this value according to your HTML
                    plugins: [
                        'searchreplace table help lists advlist image imagetools print pagebreak',
                        'spellchecker responsivefilemanager link directionality responsivefilemanager filemanager',
                        'hr visualblocks code'
                    ],
                    menubar: 'file edit insert view format table tools help',
                    toolbar1: 'print | responsivefilemanager | spellchecker | paste |  searchreplace | image | link | visualblocks | code',
                    toolbar2: 'undo redo | styleselect | bold italic |alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | ltr rtl | pagebreak ',
                    language: 'ru',
                    save_onsavecallback: function ( ) {
                        console.log( 'Saved' );
                    },
                    width: '100%',
                    height: 550,
                    resize: false,
                    spellchecker_language: 'ru',
                    spellchecker_rpc_url: '/spellchecker/spellchecker.php',
                    image_list: [
                        {title: 'My image 1', value: 'pic/blocked2.png'},
                        {title: 'My image 2', value: 'pic/europe_flags_flag_16997.png'},
                        {title: 'My image 2', value: 'pic/bluray-disc.png'}
                    ],
                    setup: function ( editor ) {
                        editor.on( 'init', function ( e ) {
                            console.log( 'Editor was initialized.' );
                            tinymce.activeEditor.setContent( zametkaContent );
                        } );
                    },
                    image_advtab: true,
                    relative_urls: false,
                    remove_script_host: false,
                    external_filemanager_path: "/responsive_filemanager/filemanager/",
                    filemanager_title: "Проводник (Responsive Filemanager v9)",
                    external_plugins: {
                        "filemanager": "/responsive_filemanager/filemanager/plugin.min.js",
                        "responsivefilemanager": "/responsive_filemanager/tinymce/plugins/responsivefilemanager/plugin.min.js",
                    },
                } );
            }
        } );
    },
    _addFileClick: function ( e ) {
        e.data.self._editText.call( e.data.self, e.data );
    },
    _createItemTR: function ( firmKey, index, page, item, page ) {
        let tr = $( '<tr class="zametki-row">' ), self = this;
        let uri = new URI( this.options.getfileUrl );
        uri.setSearch( {
            firmKey: firmKey,
            index: index, //i
            fNamePerfix: this.options.fNamePerfix
        } );
        let spanName = $( '<span>' ).text( item.name );
        if ( item.name.length > 42 )
            spanName.attr( 'title', item.name );
        tr.append( $( '<td>' ).append( spanName ) );
        let bBlock = $( '<div>' ).addClass( 'btn-group btn-group-xs' ).attr( 'role', 'group' );
        bBlock.append( $( '<a>' ).addClass( 'btn btn-default' ).text( 'Открыть' ).click( function ( ) {
            if ( !self.options.getfileUrl ) {
                console.warn( 'Не передан getfileUrl' );
            }
            self.showBunner( 'Загрузка' );
            $.post( self.options.getfileUrl, {id: item.id} ).done( function ( answ ) {
                self.hideBunner( );
                if ( answ.status == 'ok' ) {
                    self._editText( {
                        firmKey: firmKey,
                        page: page,
                        zametkaName: item.name,
                        zametkaContent: answ.content,
                        zametkaId: item.id
                    } );
                } else {
                    m_alert( 'Ошибка сервера', answ.errorText, true, false );
                }
            } );
        } ) );
        if ( this.options.renameZametkaUrl ) {
            bBlock.append( $( '<a>' ).addClass( 'btn btn-default' ).text( 'Переименовать' ).click( {self: this, firmKey: firmKey, page: page, index: item.id, onDoneCallBack: function ( newName ) {
                    item.name = newName
                }}, this._rename ) );
        }
        bBlock.append( $( '<a>' ).addClass( 'btn btn-default' ).text( 'Отправить' ).click( {self: this, firmKey: firmKey, page: page, index: item.id}, this._send ) );
        bBlock.append( $( '<a>' ).addClass( 'btn btn-default' ).text( 'Удалить' ).click( {self: this, firmKey: firmKey, page: page, fName: item.name, index: item.id}, this._remove ) );
        tr.append( $( '<td>' ).css( 'max-width', 445 ).append( bBlock ) );
        return tr;
    },
    _rename: function ( e ) {
        let self = e.data.self;
        e.data.el = $( this ).parent().parent().prev();
        e.data.incrWidth = 0;
        e.data.height = 30;
        e.data.self._renameAnimation( e.data );
    }
};
( function ( $ ) {
    $.widget( "custom.zametki", $.custom.rekvizit, $.extend( {}, Zametki_main ) );
}( jQuery ) );
( function ( $ ) {
    $.widget( "custom.zametkiOpen", $.custom.openButton, {_className: 'zametki'} );
}( jQuery ) );