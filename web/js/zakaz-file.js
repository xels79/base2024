/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var zakaz_file = {
    _zakazFAction: null,
    _desFAction: null,
    _viewImg: null,
    _zakazList: null,
    _desList: null,
    _progress: null,
    _zBorderT: null,
    _dBorderT: null,
    _zFileToLoad: [ ],
    _dFileToLoad: [ ],
    _jqXHRCount: 0,
    _totalSz: 0,
    _totalDone: 0,
    _loadedFileList: {des: [ ], main: [ ]},
    _onAllFileComlete: null,
    _xhr: [ ],
    _fileAbort: null,
    destroy: function () {
        this._super();
        if ( this._zakazList ) {
            this._zakazList.children().each( function () {
                $( this ).children().each( function () {
                    $( this ).remove()
                } );
                $( this ).remove();
            } );
            this._zakazList.remove();
        }
        if ( this._desList ) {
            this._desList.children().each( function () {
                $( this ).children().each( function () {
                    $( this ).remove()
                } );
                $( this ).remove();
            } );
            this._desList.remove();
        }
        while ( this._xhr.length ) {
            let tmp = this._xhr.pop();
            delete tmp;
        }
        while ( this._zFileToLoad.length ) {
            let tmp = this._zFileToLoad.pop();
            tmp.barL.remove();
            tmp.bar.remove();
            delete tmp;
        }
        while ( this._dFileToLoad.length ) {
            let tmp = this._dFileToLoad.pop();
            tmp.barL.remove();
            tmp.bar.remove();
            delete tmp;
        }
        this._zFileToLoad = null;
        this._dFileToLoad = null;
        this._xhr = [ ];
        this._zakazList = null;
        this._desList = null;
        if ( this._sendInterval ) {
            clearInterval( this._sendInterval );
        }
    },
    zakaz_file: function ( tab ) {
        this._zakazFAction = $( '<div>' ).addClass( 'f-action' ).html( 'Файлы<br> заказчика<br> сюда' );
        this._desFAction = $( '<div>' ).addClass( 'f-action' ).html( 'Файлы<br> дизайнера<br> сюда' );
        this._viewImg = $( '<img>' );
        this._zakazList = $( '<div>' ).addClass( 'list-group' );
        this._desList = $( '<div>' ).addClass( 'list-group' );
        this._progress = $( '<div>' ).append( $( '<div>' ).addClass( 'progress-label' ) );
        this.options.fileuploadurl = this.options.fileuploadurl ? this.options.fileuploadurl : null;
        let tbody = $( '<tbody>' );
        let self = this;
        tab.append( $( '<table>' ).addClass( 'table' ).append( tbody ) );

        tbody.append( $( '<tr>' )
                .append( $( '<th>' ).text( 'Заказчик:' ) )
                .append( $( '<th>' ).text( 'Загрузка:' ) )
                .append( $( '<th>' ).text( 'Дизайнер:' ) )
                )
                .append( $( '<tr>' )
                        .append( $( '<td>' ).attr( 'rowspan', 3 ).append( this._zakazFAction ) )
                        .append( $( '<td>' ).append( this._progress ) )
                        .append( $( '<td>' ).attr( 'rowspan', 3 ).append( this._desFAction ) ) )
                .append( $( '<tr>' )
                        .append( $( '<th>' ).text( 'Просмотр:' ) )
                        )
                .append( $( '<tr>' )
                        .append( $( '<td>' ).attr( 'rowspan', 2 ).append( $( '<div>' ).addClass( 'img-view' ).append( this._viewImg ) ) )
                        )
                .append( $( '<tr>' )
                        .append( $( '<td>' ).append( this._zakazList ) )
                        .append( $( '<td>' ).append( this._desList ) ) );
        this._progress.progressbar( {value: 0} );
        let obf = $( '.f-action' );
        obf.on( 'dragenter', function ( e ) {
            e.stopPropagation();
            e.preventDefault();
            $( this ).removeClass( 'd-done' ).addClass( 'd-enter' );
        } );
        obf.on( 'dragover', function ( e ) {
            e.stopPropagation();
            e.preventDefault();
        } );
        obf.on( 'dragleave', function ( e ) {
            e.stopPropagation();
            e.preventDefault();
            $( this ).removeClass( 'd-enter' );
        } );
        tab.append( $( '<input>' ).attr( {
            id: 'open-file-click-button',
            type: 'file',
            multiple: 'multiple',
            //accept:'image/*,application/pdf,application/cdr,application/ai,application/excel,application/x-excel,application/msword,application/octet-stream'

        } ).css( 'display', 'none' ).change( {self: this}, function ( e ) {
            console.log( 'change', $( this ).get( 0 ).files );
            if ( $( this ).get( 0 ).files.length ) {
                if ( $( this ).attr( 'data-for' ) === 'zakaz' ) {
                    self._dropAddFileToLoad( $( this ).get( 0 ).files, true );
                } else {
                    self._dropAddFileToLoad( $( this ).get( 0 ).files, false );
                }
            }
        } ) );
        this._zakazFAction.on( 'drop', {self: this}, this._dropZakazFile );
        this._zakazFAction.on( 'click', {self: this}, function ( e ) {
            $( '#open-file-click-button' ).attr( 'data-for', 'zakaz' )
            $( '#open-file-click-button' ).trigger( 'click' );
            self.showBunner( 'Открываем Выбор файлов' );
            setTimeout( function () {
                self.hideBunner();
            }, 1500 );
        } );
        this._desFAction.on( 'drop', {self: this}, this._dropDesFile );
        this._desFAction.on( 'click', {self: this}, function ( e ) {
            $( '#open-file-click-button' ).attr( 'data-for', 'des' )
            $( '#open-file-click-button' ).trigger( 'click' );
            self.showBunner( 'Открываем Выбор файлов' );
            setTimeout( function () {
                self.hideBunner();
            }, 1500 );
        } );
        $( document ).on( 'dragenter', function ( e ) {
            e.stopPropagation();
            e.preventDefault();
        } );
        $( document ).on( 'dragover', function ( e ) {
            e.stopPropagation();
            e.preventDefault();
        } );
        $( document ).on( 'drop', function ( e ) {
            e.stopPropagation();
            e.preventDefault();
        } );
        this._drawLoadedInfo( 'main', '_zakazList' );
        this._drawLoadedInfo( 'des', '_desList' );
        if ( $.type( this.options.tempFileList ) === 'object' ) {
            if ( this.options.tempFileList.main ) {
                this._drawLoadedInfo( 'main', '_zakazList', true );
            }
            if ( this.options.tempFileList.des ) {
                this._drawLoadedInfo( 'des', '_desList', true );
            }
        }

    },
    _drawLoadedInfo: function ( perfix, targetUl, newFile ) {
        newFile = newFile ? newFile : false;
        let self = this, search = newFile ? this.options.tempFileList : this._loadedFileList;
        let sz = 20;
        $.each( search[perfix], function ( index, val ) {
            let param = '&idZipResorch=' + index + '&id=' + ( self.options.z_id ? self.options.z_id : 0 );
            if ( newFile && self.options.form.fields.tmpName )
                param += '&tmpName=' + self.options.form.fields.tmpName;
            let a = $( '<a>' ).attr( {
                href: self.options.getFileUrl ? self.options.getFileUrl + param : '#',
                'data-file-name': val
            } ).on( 'click', function ( e ) {
                console.log( e );
                if ( !$( this ).attr( 'data-download' ) ) {
                    e.preventDefault();
                } else {
                    $( this ).removeAttr( 'data-download' );
                    let tmpA = $( '<a>' ).attr( {
                        href: self.options.getFileUrl ? self.options.getFileUrl + param : '#',
                        download: 'download',
                        'data-file-name': val
                    } ).css( 'dispaly', 'none' ).appendTo( 'body' );
                    tmpA.get( 0 ).click();
                    tmpA.remove();
                }
                return true;
            } );
            if ( val < sz )
                a.text( val );
            else {
                a.text( val.substr( 0, sz ) + ' ...' );
                a.attr( 'title', val );
            }

            let div = ( $( '<div>' ).addClass( 'list-group-item' + ( newFile ? ' bg-warning' : '' ) )
                    .append( a ) );
            if ( newFile ) {
                div.attr( 'title', 'Не сохранен!' );
                div.append( $( '<a>' ) );
            } else {
//                div.append($('<a>').addClass('btn btn-danger').append($('<span>').addClass('glyphicon glyphicon-remove')).click({
//                    self:self,
//                    idZipResorch:index
//                },self._fileRemove));
            }
            div.contextmenu( {self: self, idZipResorch: index, tmpName: newFile && self.options.form.fields.tmpName ? self.options.form.fields.tmpName : null, }, function ( e ) {
                e.preventDefault();
                if ( self.options.isCopy ) {
                    return;
                }
                new dropDown( {
                    posX: e.clientX,
                    posY: e.clientY,
                    items: [
                        {
                            label: 'Просмотр',
                            click: function () {
                                if ( e.data.self.options.publishfileFileUrl ) {
                                    let opt = {idZipResorch: e.data.idZipResorch};
                                    if ( e.data.tmpName )
                                        opt.tmpName = e.data.tmpName;
                                    if ( e.data.self.options.z_id )
                                        opt.id = e.data.self.options.z_id;
                                    let size = null;
                                    let ajax = $.ajax( {
                                        url: e.data.self.options.publishfileFileUrl,
                                        type: "POST",
                                        data: opt,
                                        success: function ( answ ) {
                                            console.log( answ );
                                            e.data.self._progress.progressbar( 'value', 0 );
                                            e.data.self._progress.children( '.progress-label' ).text( '' );
                                            if ( answ.status === 'ok' ) {
                                                if ( answ.url ) {
                                                    let url = answ.url;
                                                    if ( $.inArray( answ.ext, [ 'doc', 'pdf', 'docx', 'ai', 'ppt', 'pptx', 'txt', 'xls', 'xlsx', 'psd', 'zip', 'rar', 'fb2' ] ) > -1 ) {
                                                        console.log( 'rc', url );
                                                        console.log( location.origin );

                                                        $( '.img-view' ).empty().append( $( '<iframe>' ).attr( {
                                                            src: 'https://docs.google.com/viewer?url=' + encodeURIComponent( location.origin + url ) + '&a=bi&embedded=true',
                                                            //style:"width:100%; height:220px;"
                                                        } ) );
                                                    } else if ( $.inArray( answ.ext, [ 'img', 'bmp', 'png', 'gif', 'jpg', 'ico' ] ) > -1 ) {
                                                        $( '.img-view' ).empty().append( $( '<img>' ).attr( {
                                                            src: url,
                                                        } ) );
                                                    } else {
                                                        $( '.img-view' ).empty().append( $( '<h3>' ).html( 'Просмотр<br>Не поддерживается!' ) );
                                                    }
                                                } else if ( answ.img_blob ) {
                                                    $( '.img-view' ).empty().append( $( '<img>' ).attr( {
                                                        src: 'data:image/' + answ.ext + ';base64,' + answ.img_blob,
                                                    } ) );
                                                }
                                            } else {
                                                if ( answ.errorText ) {
                                                    console.error( answ.errorText, answ );
                                                } else {
                                                    console.error( 'Неизвестная ошибка', answ );
                                                }
                                            }
                                        },
                                        xhr: function () {
                                            let xhr = new window.XMLHttpRequest();
                                            xhr.addEventListener( "progress", function ( evt ) {
                                                if ( size === null ) {
                                                    let tmp = xhr.getAllResponseHeaders().toLowerCase(), head = 'test-len:';
                                                    let pos1 = tmp.indexOf( head );
                                                    if ( pos1 > -1 ) {
                                                        let found = false, i = 0, start = pos1 + head.length + 1;
                                                        while ( start + i < tmp.length && !found ) {
                                                            if ( tmp.charCodeAt( start + i ) < 48 || tmp.charCodeAt( start + i ) > 57 )
                                                                found = true;
                                                            else
                                                                i++;
                                                        }
                                                        if ( found ) {
                                                            let val = tmp.substr( start, i );
                                                            if ( $.isNumeric( val ) ) {
                                                                size = parseInt( val );
                                                                console.log( 'testLen: ' + size + 'kb' );
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    let percentComplete = Math.round( evt.loaded / size * 100 );
                                                    // делать что-то...
                                                    e.data.self._progress.progressbar( 'value', percentComplete );
                                                    e.data.self._progress.children( '.progress-label' ).text( percentComplete + '%' );

//                                                    console.log(percentComplete);
                                                }
                                            }, false );
                                            return xhr;
                                        },
                                    } );
                                    //$.post(e.data.self.options.publishfileFileUrl,opt).done();
                                } else {
                                    console.warn( 'zakz-file', 'Не задан publishfileFileUrl' );
                                }
                            }
                        },
                        {
                            'label': 'Скачать',
                            click: function () {
                                a.attr( 'data-download', 'download' )
                                a.trigger( 'click' );
                            }
                        },
                        {
                            'label': 'Отправить',
                            click: function () {
                                self._mailFile.call( self, a.get( 0 ), {
                                    idZipResorch: index,
                                    fName: val
                                } );
                            }
                        },
                        'separator',
                        {
                            label: 'Удалить',
                            click: function () {
                                self._fileRemove.call( a.get( 0 ), {data: {
                                        self: self,
                                        idZipResorch: index,
                                        fName: val
                                    }} );
                            }
                        }
                    ],
                } );

            } );
            self[targetUl].append( div );
        } );
    },
    _fileRemove: function ( e ) {
        let self = e.data.self, it = $( this ), txt = e.data.fName ? e.data.fName : it.text(), idZipResorch = e.data.idZipResorch;
        if ( self.options.z_id ) {
            if ( self.options.form.fields.tmpName ) {
                if ( self.options.toRemoveUrl ) {
                    m_alert( 'Внимание', 'Удалить файл "' + txt + '"?', function () {
                        $.post( self.options.toRemoveUrl, {
                            id: self.options.z_id,
                            tmpName: self.options.form.fields.tmpName,
                            idZipResorch: idZipResorch
                        } ).done( function ( answ ) {
                            console.log( '_fileRemove->answ', answ );
                            if ( answ.status === 'ok' )
                                it.parent().remove();
                        } );
                    }, true );
                } else
                    console.warn( '_fileRemove', 'Не указан toRemoveUrl' );
            } else
                console.warn( '_fileRemove', 'Не указан временный каталог.' );
        } else
            console.warn( '_fileRemove', 'новый заказ нечего тут удалять - нажми отмену' );
    },
    _dropDefault: function ( obj, e, izZ ) {
        let letN = izZ ? '_zBorderT' : '_dBorderT';
        e.stopPropagation();
        e.preventDefault();
        obj.removeClass( 'd-enter' ).addClass( 'd-done' );
        if ( this[letN] ) {
            clearTimeout( this[letN] );
        }
        this[letN] = setTimeout( function () {
            this[letN] = null;
            obj.removeClass( 'd-done' );
        }, 1000 );
        console.log( e.originalEvent.dataTransfer.files );
        return e.originalEvent.dataTransfer.files;
    },
    _mailFile: function ( el, data ) {
        console.log( el, data );
        let self = this, it = $( el ), txt = data.fName ? data.fName : it.text(), uri;
        let dialog = $( '<div>' );
        let postOpt = {
            idZipResorch: data.idZipResorch
        };
        if ( this.options.z_id ) {
            if ( this.options.form.fields.tmpName ) {
                if ( this.options.mailFileUrl ) {
                    uri = new URI( this.options.mailFileUrl );
                    postOpt.id = this.options.z_id;
                    postOpt.tmpName = this.options.form.fields.tmpName;
                    uri.setQuery( postOpt );
                    console.log( 'is send', postOpt, uri.toString() );
                    this.showBunner( 'Работа с почтой' );
                    $.post( uri.toString() ).done( function ( answ ) {
                        console.log( answ );
                        if ( answ.status === 'edit' ) {
                            dialog.appendTo( 'body' );
                            dialog.maindialog( {
                                minimizable: false,
                                width: 550,
                                modal: true,
                                title: 'Отправить файл ' + txt,
                                afterInit: function ( d ) {
                                    d.uiDialog.addClass( 'zakaz-send-file' );
                                    dialog.html( answ.html );
                                    //file-send-form
                                    d.uiDialog.css( {'top': $( document ).height() / 2 - d.uiDialog.height() / 2} );
                                    $( '#file-send-form' ).submit( {dialog: dialog, self: self}, self._mailFileSubmit );
                                    $( '#file_send_cansel' ).click( function () {
                                        dialog.maindialog( 'close' )
                                    } );
//                                    $('#mail-toemail').attr('autocomplete','fake-name-disable-autofill');
                                    $( '#mail-toemail' ).keyup( {dialog: dialog, self: self}, self._mailFileSelectAddress );
                                    $( '#mail-toemail' ).click( {dialog: dialog, self: self}, self._mailFileSelectAddress );
                                    dialog.parent().click( {self: self, dialog: dialog}, self._mailDialogClick );
                                    dialog.find( 'input:not([name="Mail[toEmail]"])' ).focus( {self: self, dialog: dialog}, self._mailDialogClick );
                                    //mailGetZkazchikEmails
                                },
                                beforeClose: function () {
                                    dialog.remove();
                                    self.hideBunner();
                                }
                            } );
                        }
                    } );
                } else
                    console.warn( '_mailFile', 'Не указан mailFileUrl' );
            } else
                console.warn( '_mailFile', 'Не указан временный каталог.' );
        } else
            console.warn( '_mailFile', 'Новый заказ. Невозможно отправить, пока не сохранил.' );

    },
    _mailLiClick: function ( e ) {
        console.log( 'dialog click', e );
        $( '#mail-toemail' ).val( $( this ).attr( 'data-value' ) );
        $( '#files_send_addres_list' ).css( 'display', 'none' );
    },
    _mailDialogClick: function ( e ) {
        console.log( e );
        console.log( 'target: ' + e.originalEvent.target.tagName );
        let doAction = true;
        if ( e.originalEvent.path && !( e.originalEvent.path.length > 1 && ( ( e.originalEvent.path[0].tagName === 'P' && e.originalEvent.path[1].tagName === 'LI' ) || e.originalEvent.path[0].tagName === 'LI' ) ) )
            $( '#files_send_addres_list' ).css( 'display', 'none' );
        else if ( e.originalEvent.target.tagName !== 'LI' )
            $( '#files_send_addres_list' ).css( 'display', 'none' );
    },
    _mailAddToList: function ( items, like ) {
        let ul = $( '#files_send_addres_list' ).children( 'ul:first-child' );
        let self = this;
        $.each( items, function () {
            let li = $( '<li>' );
            if ( !like && like != 0 ) {
                li.append( $( '<p>' ).text( this.name ) );
                li.append( $( '<p>' ).text( this.mail ) );
            } else {
                let likeC = like.toLowerCase(), nameC = this.name.toLowerCase();
                let mailC = this.mail.toLowerCase();
                let name = nameC.indexOf( likeC ) > -1 ? this.name.substring( 0, nameC.indexOf( likeC ) ) + '<span>' + this.name.substr( nameC.indexOf( likeC ), likeC.length ) + '</span>' + this.name.substr( nameC.indexOf( likeC ) + likeC.length ) : this.name;
                let mail = mailC.indexOf( likeC ) > -1 ? this.mail.substring( 0, mailC.indexOf( likeC ) ) + '<span>' + this.mail.substr( mailC.indexOf( likeC ), likeC.length ) + '</span>' + this.mail.substr( mailC.indexOf( likeC ) + likeC.length ) : this.mail;
                li.append( $( '<p>' ).html( name ) );
                li.append( $( '<p>' ).html( mail ) );
            }
            li.attr( 'data-value', this.mail );
            li.click( {self: self, it: this}, self._mailLiClick );
            ul.append( li )
        } );
    },
    _mailFileSelectAddress: function ( e ) {
        let self = e.data.self, firmId = $( '#Zakaz-zak_id' ).val();
        let el = this;
        if ( e.type === 'click' ) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            console.log( 'stopped' );
        }
        $( this ).removeAttr( 'readonly' );
        if ( !self.options.mailGetZkazchikEmails ) {
            console.warn( 'Не передан mailGetZkazchikEmails' );
            return;
        }
        $.post( self.options.mailGetZkazchikEmails, {firmId: firmId, like: $( el ).val()} ).done( function ( answ ) {
            console.log( answ );
            console.log( {left: $( el ).offset().left, top: $( el ).offset().top + $( el ).height()} );
            if ( !$( '#files_send_addres_list' ).length ) {
                $( '<div>' )
                        .attr( 'id', 'files_send_addres_list' )
                        .append( $( '<ul>' ) )
                        .addClass( 'filesSAL' )
                        .appendTo( e.data.dialog )
                        .width( $( el ).outerWidth() - 2 )
                        .offset( {left: $( el ).offset().left, top: $( el ).offset().top + $( el ).outerHeight() - 2} );
            }
            $( '#files_send_addres_list' ).children( 'ul' ).empty();
            self._mailAddToList( answ.list1, $( el ).val() );
            self._mailAddToList( answ.list2, $( el ).val() );
            if ( $( '#files_send_addres_list' ).children( 'ul' ).children().length )
                $( '#files_send_addres_list' ).css( 'display', 'block' );
            else
                $( '#files_send_addres_list' ).css( 'display', 'none' );
        } );
    },
    _mailFileSubmit: function ( e ) {
        let self = e.data.self, dialog = e.data.dialog;
        let fd = new FormData( $( this ).get( 0 ) );
        e.preventDefault();
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
                    dialog.empty().html( data.html );
                    $( '#file-send-form' ).submit( {self: self, dialog: dialog}, self._mailFileSubmit );
                    $( '#file_send_cansel' ).click( function () {
                        dialog.maindialog( 'close' )
                    } );
                    dialog.find( 'input:not([name="Mail[toEmail]"])' ).focus( {self: self, dialog: dialog}, self._mailDialogClick );
                    $( '#mail-toemail' ).keyup( {dialog: dialog, self: self}, self._mailFileSelectAddress );
                    $( '#mail-toemail' ).click( {dialog: dialog, self: self}, self._mailFileSelectAddress );
                    dialog.parent().css( {'top': $( document ).height() / 2 - dialog.parent().height() / 2} );
                } else {
                    m_alert( 'Ошибка Сервера', data.errorText, true, false );
                }
            },

        } );
    },
    _setStatus: function ( it, position ) {
        let self = this, percent;
        if ( position < 0 ) {
            it.barL.children( ':last-child' ).text( 'готов' );
            if ( it.file ) {
                if ( it.position < it.file.size ) {
                    self._totalDone += it.file.size - it.position;
                    it.position = it.file.size;
                    if ( !self._jqXHRCount )
                        $( '#load-info' ).remove();
                }
            } else {
                if ( it.position < it.blob.size ) {
                    self._totalDone += it.blob.size - it.position;
                    it.position = it.blob.size;
                    if ( !self._jqXHRCount )
                        $( '#load-info' ).remove();
                }
            }
        } else {
            it.bar.progressbar( 'value', position );
            if ( position <= ( it.file ? it.file.size : it.blob.size ) ) {
                self._totalDone += position - it.position;
                it.position = position;
            }
        }
        percent = Math.ceil( ( self._totalDone / self._totalSz ) * 100 );
        this._progress.progressbar( 'value', percent );
        this._progress.children( '.progress-label' ).text( percent + '%' );
        this._zakaz_file_info_badge( percent + '%', false );
//        console.log(self._totalSz,self._totalDone,percent);
    },
    _showDopBunnerMess: function ( all ) {
        let tmp = $( '.bunner-opacity' );
        if ( tmp.length ) {
            let span = tmp.find( 'span' );
            if ( !span.length ) {
                span = $( '<span>' );
                tmp.append( span );
            }
            let cnt;
            if ( all ) {
                cnt = Math.ceil( ( this._totalDone / this._totalSz ) * 100 ) + '%';
            } else {
                cnt = this._jqXHRCount;
            }
            span.text( ' (заверш. файл. оп. ост.: ' + cnt + ')' );
        }
    },
    _sendFile: function ( it ) {
        let self = this;
        let fd = new FormData();
        console.log( '_senFile', it );
        fd.append( 'tmpName', $( '#zakaz-tmpname' ).val() );
        if ( it.blob && it.fileName ) {
            fd.append( 'file', it.blob, it.fileName );
        } else if ( it.file ) {
            fd.append( 'file', it.file );
        } else {
            console.warn( 'Файл не предан!', it );
            if ( !$.isFunction( self._fileAbort ) ) {
                if ( self._jqXHRCount < 3 && ( self._zFileToLoad.length || self._dFileToLoad.length ) )
                    self._checkAndSend();
                self._setStatus( it, -1 );
                if ( !self._jqXHRCount && $.isFunction( self._onAllFileComlete ) )
                    self._onAllFileComlete.call( self );
            } else if ( !self._jqXHRCount ) {
                self._fileAbort.call( self );
            }
        }
        fd.append( 'isDes', it.izZ ? 'false' : 'true' );
        if ( this.z_id )
            fd.append( 'id', this.z_id );
        this._xhr[this._jqXHRCount++] = $.ajax( {
            xhr: function () {
                let xhrobj = $.ajaxSettings.xhr();
                if ( xhrobj.upload ) {
                    xhrobj.upload.addEventListener( 'progress', function ( event ) {
                        let position = event.loaded || event.position;
                        if ( !self._fileAbort ) {
                            self._setStatus( it, position );
                            if ( self._onAllFileComlete )
                                self._showDopBunnerMess( true );
                        } else {
                            xhrobj.abort();
                            self._showDopBunnerMess();
                        }
                    }, false );
                }
                return xhrobj;
            },
            url: self.options.fileuploadurl,
            type: "POST",
            contentType: false,
            processData: false,
            cache: false,
            data: fd,
            complete: function () {
                self._jqXHRCount--;
                console.log( '_sendFile:complete', self._jqXHRCount );
                if ( !$.isFunction( self._fileAbort ) ) {
                    if ( self._jqXHRCount < 3 && ( self._zFileToLoad.length || self._dFileToLoad.length ) )
                        self._checkAndSend();
                    self._setStatus( it, -1 );
                    if ( !self._jqXHRCount && $.isFunction( self._onAllFileComlete ) )
                        self._onAllFileComlete.call( self );
                } else if ( !self._jqXHRCount ) {
                    self._fileAbort.call( self );
                }
            },
            success: function ( data ) {
                console.log( '_sendFile:success', data );
            }
        } );
    },
    _checkAndSend: function () {
        console.log( '_checkAndSend', this._zFileToLoad, this._dFileToLoad );
        let zFTLCount = this._zFileToLoad.length;
        let dFTKCount = this._dFileToLoad.length;
        if ( this._jqXHRCount < 3 && this.options.fileuploadurl ) {
            if ( zFTLCount ) {
                zFTLCount--;
                this._sendFile( this._zFileToLoad.shift() );
                if ( this._jqXHRCount < 3 && ( zFTLCount || dFTKCount ) )
                    this._checkAndSend();
            } else if ( dFTKCount ) {
                dFTKCount--;
                this._sendFile( this._dFileToLoad.shift() );
                if ( this._jqXHRCount < 3 && ( zFTLCount || dFTKCount ) )
                    this._checkAndSend();
            }
        }
    },
    _dropAddFileToLoad: function ( files, izZ ) {
        let arrN = izZ ? '_zFileToLoad' : '_dFileToLoad';
        let letN = izZ ? '_zakazList' : '_desList';
        let self = this, sz = 17;
        $.each( files, function ( k, v ) {
            let it = $( '<a>' ).attr( {
                class: 'list-group-item',
                'data-file-name': v.name,
                href: '#'
            } ), txt, ttl = false;
            if ( v.name.length < sz )
                txt = v.name;
            else {
                txt = v.name.substr( 0, sz ) + ' ...';
                //it.attr('title',v.name);
                ttl = v.name
            }
            let pBarL = $( '<div>' ).addClass( 'progress-label' ).append( $( '<span>' ).text( v.name ) ).append( $( '<span>' ).text( '0%' ) );
            let pBar = $( '<div>' ).append( pBarL );
            pBar.progressbar( {
                value: 0,
                max: v.size,
                change: function () {
                    let val = pBar.progressbar( "value" ) || 0;
//                            console.log(val);
                    if ( $.type( val ) === 'string' )
                        pBarL.children( ':last-child' ).text( val );
                    else
                        pBarL.children( ':last-child' ).text( Math.ceil( ( val / v.size ) * 100 ) + '%' );
                }
            } );
            if ( ttl )
                pBar.attr( 'title', ttl );
            self[letN].append( it
                    .append( pBar )
                    );
            if ( !self._jqXHRCount ) {
                self._totalSz = 0;
                self._totalDone = 0;
            }
            self._totalSz += v.size;
            if ( v.blob ) {
                self[arrN][self[arrN].length] = {
                    blob: v.blob,
                    fileName: v.name,
                    item: it,
                    bar: pBar,
                    barL: pBarL,
                    position: 0,
                    izZ: izZ
                };
            } else {
                self[arrN][self[arrN].length] = {
                    file: v,
                    item: it,
                    bar: pBar,
                    barL: pBarL,
                    position: 0,
                    izZ: izZ
                };
            }
            self._checkAndSend();
        } );
    },
    _dropZakazFile: function ( e ) {
        let self = e.data.self;
        console.log( 'zakaz-file' );
        let obj = $( this );
        self._dropAddFileToLoad( self._dropDefault( obj, e, true ), true );
        ;
    },
    _dropDesFile: function ( e ) {
        let self = e.data.self;
        console.log( 'des-file' );
        let obj = $( this );
        self._dropAddFileToLoad( self._dropDefault( obj, e, false ), false );
    },
    _zakaz_file_info_badge: function ( val, add ) {
        let bage = $( '#load-info' );
        if ( !bage.length ) {
            bage = $( '<span>' ).attr( {
                class: 'badge',
                id: 'load-info'
            } );
            if ( add ) {
                add.append( bage );
            }
        }
        bage.text( val );
    },
    _zakaz_file_init: function () {
        $( '.nav-tabs' ).on( 'hide.bs.tab', {self: this}, function ( e ) {
            let self = e.data.self;
            let id = $( e.target ).attr( 'href' );
            console.log( e.target.hash, $( e.target ) );
            if ( id !== '#tab-pane-zakaz-page5' )
                return;
            let badg = $( e.target ).children( '.badge' );
            if ( self._totalSz > self._totalDone ) {
                self._zakaz_file_info_badge( Math.ceil( ( self._totalDone / self._totalSz ) * 100 ) + '%', $( e.target ) );
            }
        } ).on( 'shown.bs.tab', function ( e ) {
            let id = $( e.target ).attr( 'href' );
            console.log( e.target.hash, $( e.target ) );
            if ( id !== '#tab-pane-zakaz-page5' )
                return;
            $( '#load-info' ).remove();
        } );
    }
};