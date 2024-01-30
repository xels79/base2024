/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


let zakaz_list = {
    _attention: null,
    _stageLevels: [ 'Согласование', 'У дизайнера', 'Печать', 'Готов', 'Сдан', 'С ошибкой' ],
    _showAttentionColumn: false,
    _editCollName: 'stage',
    _lastZakaz: null,
    options: {
        setColumnsUrl: null,
        getOneRawUrl: null,
        changeRowUrl: null,
        removeRowUrl: null,
        copyRowUrl: null,
        viewRowUrl: null,
        canEditStage: false,
        canEditZakaz: false,
        canEditOtherOrder: true,
        stageLevels: null,
        showTechnikalCol:true,
        publishfileFileUrl: null,
        userId: 0
    },
    _create: function () {
        let uri = new URI( window.location.href );
        let params = URI.parseQuery( uri.query() );
        //let pCnt=Math.ceil(this._count/this._fieldParams.pageSize);
        this._super();
        if ( this.options.stageLevels )
            this._stageLevels = this.options.stageLevels;
        $( '#show-reprint' )
                .click( {self: this}, this._showReprintClick )
        if ( params.page )
            this._requstDate( this._firstStart, {page: params.page} );
        else
            this._requstDate( this._firstStart );
    },

    /*
     * Пеключить режим отоброжения заказов с перепечаткой
     * @param {event} e
     * @return {undefined}
     */
    _showReprintClick: function ( e ) {
        e.preventDefault();
        console.log( $( this ).children( 'img' ).attr( 'src' ) );
        let tmp = $( this ).children( 'img' ).attr( 'src' );
        $( this ).children( 'img' ).attr( 'src', $( this ).children( 'img' ).attr( 'data-nexturl' ) )
        $( this ).children( 'img' ).attr( 'data-nexturl', tmp );
        $( this ).attr( 'data-reprint', $( this ).attr( 'data-reprint' ) === 'true' ? 'false' : 'true' );
        e.data.self.update();
    },
    /*
     * Обновление ряда
     * @param {int} id - идентификатор
     * @param {object} newVals - новые значения
     * @returns {undefined}
     */
    _updateRaw: function ( id, newVals, callBackAfter ) {
        newVals = newVals ? newVals : {};
        if ( this.options.getOneRawUrl ) {
            let tr = this.__showBunnerAtRowAndGetIt( id ), self = this;
            $.post( this.options.getOneRawUrl, {id: id, update: newVals} ).done( function ( answ ) {
                console.log( answ );
                self._lastZakaz = answ.lastZakaz ? answ.lastZakaz : null;
                if ( answ.status == 'ok' ) {
                    if ( answ.attention ) {
                        self._attention[answ.row.id] = answ.attention;
                    } else {
                        if ( self._attention[answ.row.id] )
                            delete self._attention[answ.row.id];
                    }
                    if ( answ.hidden ) {
                        self._hidden[answ.row.id] = answ.hidden;
                    }
                    tr.replaceWith( self._generateDateRow( answ.row, 0, answ.hidden ) );
                    if ( $.isFunction( callBackAfter ) )
                        callBackAfter();
                }
            } ).fail( function ( answ ) {
                new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                console.error( answ );
            } );
        } else {
            window.location.reload( true );
        }
    },
    _zakazChangeClick: function ( e ) { //Кнопки изменить заказ
        let id = parseInt( $( this ).parent().parent().attr( 'data-key' ) );
        let self = e.data.self;
        id = !isNaN( id ) ? id : 0;
        console.log( '_zakazChangeClick', id );
        console.log( document.def_opt );
        let d = $( '#zakaz_dialog' );
        if ( !d.length ) {
            d = $.fn.creatTag( 'div', {
                id: 'zakaz_dialog'
            } );
            $( 'body' ).append( d );
            d.zakazAddEditController( $.extend( {}, {addCustomerOptions: self.options.addCustomerOptions}, window.def_opt, {z_id: id, close: function () {
                    let doOpenTecnicals = $( window.dialogSelector ).zakazAddEditController( 'doOpenTecnicals' );
                    $( window.dialogSelector ).zakazAddEditController( 'destroy' );
                    $( window.dialogSelector ).remove();
                    self._updateRaw( id, {}, () => {
                        if ( doOpenTecnicals ) {
                            self._tBody.children( '[data-key=' + id + ']' ).children( '.technikal' ).children( '[title="Техничка"]' ).trigger( 'click' );
                        }
                    } );
                }} ) );
        }
        if ( !d.zakazAddEditController( 'isOpen' ) )
            d.zakazAddEditController( 'open' );
    },
    _zakazRemoveClick: function ( e ) {
        let id = parseInt( $( this ).parent().parent().attr( 'data-key' ) );
        let self = e.data.self;
        if ( isNaN( id ) )
            id = parseInt( e.data.id );
        id = !isNaN( id ) ? id : 0;
        if ( id ) {
            m_alert( 'Внимание', '<h2 style="color:red;">Действие отменить невозможно!</h2><p>Удалить заказ №' + id + ' ?</p>', function () {
                $.post( self.options.removeRowUrl, {id: id} ).done( function ( answ ) {
                    console.log( answ );
                    self.update();
                    if ( answ.menu && answ.label ) {
                        $( '.nav-mess-main' ).children( '.dropdown-menu' ).empty().html( $( answ.menu ).html() );
                        $( '.nav-mess-main' ).children( '.dropdown-toggle' ).empty().html( answ.label + '<b class="caret"></b>' );
                        $( '.stored-z' )
                                .click( window.storedZClick )
                                .contextmenu( window.storedZContextMenu );
                        if ( $.isFunction( window.storedZContextMenu ) ) {
                            $( '.stored-z-remove-all' ).click( window.storedZContextMenu );
                        }
                        $.fn.enablePopover();
                    }
                } ).fail( function ( answ ) {
                    new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                    console.error( answ );
                } );
            }, true );
        } else {
            console.warn( '_zakazRemoveClick:Номер заказа не определён' );
        }
    },
    _technicalsViewAfterGetCompliteAndPaste: function ( dialog, dialogObject ) {
        let self = this;

        console.log( dialog.find( '.dropdown-menu' ).find( 'a' ) );
        dialog.find( '.dropdown-menu' ).find( 'a' ).click( function ( e ) {
            e.preventDefault();
            console.log( this );
            let url = $( this ).attr( 'data-url' );
            let imgs = $( '.tImg-view' ), img;
            if ( imgs.children().length > 1 ) {
                imgs.children( ':first-child' ).remove();
            }
            if ( $.inArray( $( this ).attr( 'data-ext' ), [ 'doc', 'pdf', 'docx', 'ai', 'ppt', 'pptx', 'txt', 'xls', 'xlsx', 'psd', 'zip', 'rar', 'fb2' ] ) > -1 ) {
                console.log( 'rc', url );
                console.log( location.origin );
                img = ( $( '<iframe>' ).attr( {
                    src: 'https://docs.google.com/viewer?url=' + encodeURIComponent( location.origin + url ) + '&a=bi&embedded=true',
                } ) );
            } else if ( $.inArray( $( this ).attr( 'data-ext' ), [ 'img', 'bmp', 'png', 'gif', 'jpg', 'ico' ] ) > -1 ) {
                img = $( '<img>' ).attr( {
                    src: url,
                } );
            } else {
                img = $( '<h3>' ).html( 'Просмотр<br>Не поддерживается!' );
            }
            if ( imgs.children().length ) {
                imgs.children( ':first-child' ).addClass( 'img1' ).removeClass( 'img2' );
                img.addClass( 'img2' );
            }
            img.appendTo( imgs );
            if ( dialogObject ) {
                dialogObject.showBunner();
                img.on( 'load', function () {
                    dialogObject.hideBunner();
                    dialogObject._afterFirstStartComlite();
                } );
                img.ready( function () {
                    dialogObject._afterFirstStartComlite();
                } );
            }
        } );
    },
    _viewDialogFileInsert: function ( el, id ) {
        let cnt = $( '#view-files' + id );
//        if (cnt.children().length===2){
//            cnt.children(':first-child').remove();
//        }
        cnt.children( ':first-child' ).remove();
        cnt.empty().append( $( '<div>' ).append( el ) );
        if ( cnt.children().length > 1 ) {
            cnt.children().addClass( 'half' );
        }
    },
    _viewDialogFile: function ( el ) {
        let uri = new URI( $( el ).attr( 'href' ) ), self = this, search = uri.search( true );
        let size = null;
        console.log( search );
        if ( !this.options.publishfileFileUrl ) {
            console.error( 'Не задано publishfileFileUrl' );
            return;
        }
        let opt = {idZipResorch: search.idZipResorch, id: search.id};
        if ( search.asJPG )
            opt.asJPG = search.asJPG;
        let pbar = $( '<div>' )
                .addClass( 'p-bar' )
                .appendTo( $( el ).parent() )
                .height( $( el ).height() )
                .width( 0 )
                .offset( {
                    left: $( el ).offset().left + 1,
                    top: $( el ).offset().top + 1
                } );
        console.log( $( el ) );
        pbar.append( $( el ).children( 'p:first-child' ).clone() );
        $.ajax( {
            url: this.options.publishfileFileUrl,
            type: "POST",
            data: opt,
            success: function ( answ ) {
                console.log( answ );
                pbar.remove();
                if ( answ.status === 'ok' ) {
                    if ( answ.url ) {
                        let url = answ.url;
                        if ( $.inArray( answ.ext, [ 'doc', 'pdf', 'docx', 'ai', 'ppt', 'pptx', 'txt', 'xls', 'xlsx', 'psd', 'zip', 'rar', 'fb2' ] ) > -1 ) {
                            console.log( 'rc', url );
                            console.log( location.origin );

                            self._viewDialogFileInsert( $( '<iframe>' ).attr( {
                                src: 'https://docs.google.com/viewer?url=' + encodeURIComponent( location.origin + url ) + '&a=bi&embedded=true',
                                //style:"width:100%; height:220px;"
                            } ), search.id );
                        } else if ( $.inArray( answ.ext, [ 'img', 'bmp', 'png', 'gif', 'jpg', 'ico' ] ) > -1 ) {
                            self._viewDialogFileInsert( $( '<img>' ).attr( {
                                src: url,
                            } ), search.id );
                        } else {
                            self._viewDialogFileInsert( $( '<h3>' ).html( 'Просмотр<br>Не поддерживается!' ), search.id );
                        }
                    } else if ( answ.img_blob ) {
                        self._viewDialogFileInsert( $( '<img>' ).attr( {
                            src: 'data:image/' + answ.ext + ';base64,' + answ.img_blob,
                        } ), search.id );
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
                        pbar.offset( {
                            left: $( el ).offset().left + 1,
                            top: $( el ).offset().top + 1
                        } );
                        pbar.width( ( $( el ).width() - 2 ) / 100 * percentComplete );
                    }
                }, false );
                return xhr;
            },
        } );

    },
    _viewDialogFileRC: function ( e ) {
        let self = e.data.self;
        let el = this;
        console.log( e );
        new dropDown( {
            posX: e.clientX,
            posY: e.clientY,
            items: [
                {
                    label: 'Показать',
                    click: function () {
                        self._viewDialogFile( el );
                    }
                },
                {
                    label: 'Скачать',
                    click: function ( e ) {
                        console.log( el );
                        $( el ).attr( 'data-download', 'download' );
                        $( el ).trigger( 'click' );
                    }
                }
            ],
        } );
        e.preventDefault();

    },
    _zakazViewClick: function ( e ) {
        let id = parseInt( $( this ).parent().parent().attr( 'data-key' ) );
        let self = e.data.self;
        let uri = new URI( window.location.href );
        let tmp = URI.parseQuery( uri.query() );
        let page = $.type( tmp.page ) !== 'undefined' ? tmp.page : 0;
        console.log( e );
        id = !isNaN( id ) ? id : 0;
        if ( id && !e.ctrlKey ) {
            let tmp = $( '[zakaz-num=' + id + ']' );
            if ( tmp.length ) {
                tmp.viewDialog( 'moveToTop' ).viewDialog( 'restore' );
            } else {
                $.custom.viewDialog( {
                    id: id,
                    url: self.options.viewRowUrl,
                    showEditButton: true,
                    afterGetCompliteAndPaste: function () {
                        this.find( '.list-group-item' ).click( function ( e ) {
                            if ( $( this ).attr( 'href' ) )
                                window.open( $( this ).attr( 'href' ), '_blank' );
                            e.preventDefault();
                        } ).each( function () {
                            if ( $( this ).children( ':first-child' ).children( ':first-child' ).width() > $( this ).width() ) {
                                $( this ).addClass( 'cut' );
                                $( this ).attr( 'title', $( this ).children( ':first-child' ).children( ':first-child' ).text() );
                            }
                        } );
                    }
                } );
            }
        } else if ( id && e.ctrlKey ) {
            e.preventDefault();
            uri = new URI( self.options.viewRowUrl );
            uri.addSearch( 'id', id );
            console.log( uri.toString() );
            window.open( uri.toString(), '_blank' );
        } else {
            console.warn( '_zakazRemoveClick:Номер заказа не определён' );
        }
    },
    _attentionColl: function ( id ) {
        /*
         * Вывод предупредительной колонки
         */
        let rVal = $( '<div class="resize-cell">' ).addClass( 'technikal' ).css({
                            'max-width': '30px',
                            'min-width': '30px',
                            'width': '30px'
                        });
        if ( this._attention && this._attention[id] ) {
            rVal.append( $( '<a>' )
                    .addClass( 'text-danger' )
                    .append( $( '<span>' ).addClass( 'glyphicon glyphicon-alert' ) )
                    .attr( {
                        href: '#',
                        title: this._attention[id]
                    } )
                    .click( function ( e ) {
                        e.preventDefault();
                    } ) );
        }
        return rVal;
    },
    _technikalColl: function ( id ) { //Калонка управления
        let rVal = $( '<div class="resize-cell">' ).addClass( 'technikal' ).css({
                'max-width': '67px',
                'min-width': '67px',
                'width': '67px'
        });
        let remove = $( '<a>' )
                .append( $( '<spna>' ).addClass( 'glyphicon glyphicon-trash' ) )
                .attr( {
                    href: '#',
                    title: 'Удалить'
                } ).click( {self: this}, this._zakazRemoveClick );
        let edit = $( '<a>' )
                .append( $( '<spna>' ).addClass( 'glyphicon glyphicon-pencil' ) )
                .attr( {
                    href: '#',
                    title: 'Изменить'
                } ).click( {self: this}, this._zakazChangeClick );
        let view = $( '<a>' )
                .append( $( '<spna>' ).addClass( 'glyphicon glyphicon-eye-open' ) )
                .attr( {
                    href: '#',
                    title: 'Просмотр'
                } ).click( {self: this}, this._zakazViewClick );
        let technicals = $( '<a>' )
                .append( $( '<spna>' ).addClass( 'glyphicon glyphicon-eye-open' ) )
                .attr( {
                    href: '#',
                    title: 'Техничка'
                } ).click( {self: this}, this._technicalsViewClick );
//        let copy=$('<a>')
//                .append($('<spna>').addClass('glyphicon glyphicon-duplicate'))
//                .attr({
//                    href:'#',
//                    title:'Скопировать'
//                }).click({self:this},this._zakazViewClick);
        //removeRowUrl:null,
        if ( this._hidden[id].fileCount ) {
            rVal.append( $( '<span>' ).addClass( 'glyphicon glyphicon-paperclip' ).attr( 'title', this._hidden[id].fileCount + ' - ' + $.fn.endingNums( this._hidden[id].fileCount, [ 'файл', 'файла', 'файлов' ] ) ).tooltip() );
        } else {
            rVal.append( $( '<span>' ).addClass( 'glyphicon' ) );
        }
        if ( this.options.viewRowUrl ) {
            rVal.append( technicals );//.append(view);
        }
        if ( this.options.canEditZakaz ) {
            if ( this.options.canEditOtherOrder || this.options.userId == this._hidden[id]['ourmanager_id'] )
                rVal.append( edit );
        }
//        if (this.options.copyRowUrl&&this.options.canEditZakaz){
//            rVal.append(copy);
//        }
//        if (this.options.removeRowUrl&&this.options.canEditZakaz){
//            rVal.append(remove);
//        }
        return rVal;
    },
    _generateStageSelect: function ( val ) { //Состояние заказа
        console.log( '_generateStageSelect', val );
        let select = $( '<select>' ).attr( {
//            class:'form-control',
            name: 'stage',
            back: val ? val : 0
        } );
        let stageText = "";
        for ( let i = 0; i < this._stageLevels.length; i++ ) {
            let opt = $( '<option>' ).attr( 'value', i ).text( this._stageLevels[i] );
            let comp = parseInt( val );
            comp = isNaN( comp ) ? 0 : comp;
            if ( i == comp ) {
                opt.attr( {'selected': true} );
                select.val( comp );
            }
            select.append( opt );
        }
        select.change( {self: this}, this._stageChange );
        return select;
    },
    _stageChange: function ( e ) {//Изменемие состояния заказа
        let el = this, idZak = $( this ).parent().parent().attr( 'data-key' );
        m_alert( 'Внимание', '<p>Изменить состояние заказа №' + idZak + '</p><p>C <em>"' + e.data.self._stageLevels[parseInt( $( el ).attr( 'back' ) )] + '"</em> на <em>"' + e.data.self._stageLevels[parseInt( $( el ).val() )] + '"</em></p>', function () {
            $( el ).attr( 'back', $( el ).val() );
            if ( e.data.self.options.changeRowUrl ) {
                let tr = e.data.self.__showBunnerAtRowAndGetIt( idZak ), stage = parseInt( $( el ).val() );
                stage = isNaN( stage ) ? 0 : stage;
                $.post( e.data.self.options.changeRowUrl, {id: idZak, row: {stage: stage}} ).done( function ( answ ) {
                    console.log( answ );
                    if ( answ.status == 'ok' ) {
                        tr.replaceWith( e.data.self._generateDateRow( answ.row, 0, answ.hidden ) );
                    }
                } ).fail( function ( answ ) {
                    new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                    console.error( answ );
                } );
            } else {
                m_alert( 'Информация', '<p>Невозможно сохранить.</p><p>Не задан <em>changeRowUrl</em></p>', true, false );
            }
        }, function () {
            $( el ).val( $( el ).attr( 'back' ) );
        } );
    },
    _generateDateRow: function ( dt, rowNumber, hidden ) { //Новый ряд dt=Содержимое
        let cnt = this._tHeader.children( ':first-child' ).children().length;
        let self = this;
        let newCnt = 1, rVal = $( '<div class="resize-row">' )
                .attr( 'data-key', dt.id );
        let fldOpt=this._allFieldsWidthByName();
        let fldOptKey=Object.keys(fldOpt);
        if ( this._showAttentionColumn )
            rVal.append( this._attentionColl( dt.id ) )
        rVal.append( this._technikalColl( dt.id ) );
        $.each( dt, function ( key, val ) {
            if ( newCnt < cnt ) {
                let td = $( '<div class="resize-cell">' );
//                console.log(self.options);
                if ($.inArray(key,fldOptKey)>-1){
                    td.css({
                        'max-width': fldOpt[key]+'px',
                        'min-width': fldOpt[key]+'px',
                        'width': fldOpt[key]+'px'
                    });
                }
                with ( self.options ) {
                    if ( key === 'stageText' && canEditStage && ( userId == self._hidden[dt.id]['ourmanager_id'] || canEditOtherOrder ) && self._hidden ) {//canEditOtherOrder
                        td.append( self._generateStageSelect( hidden ? hidden.stage : self._hidden[dt.id].stage ) );
                        td.addClass('resize-center');
                        if ( self._fieldParams && self._fieldParams.colors && self._fieldParams && self._fieldParams.colors.stage ) {
                            td.css( 'background-color', self._fieldParams.colors.stage[hidden ? hidden.stage : self._hidden[dt.id].stage] );
                        }
                    } else if ( key === 'id' ) {
                        if ( val == self._lastZakaz ) {
                            self._tBody.find( '.last-zakaz' ).removeClass( 'last-zakaz' );
                            self._tBody.children( '[title]' ).removeAttr( 'title' );
                            rVal.addClass( 'last-zakaz' ).attr( 'title', 'Последний редактированный заказ' );
                        }
                        if ( self._hidden[val]['is_express'] )
                            td.addClass( 'speed' ).attr( 'title', 'Срочно' + ( self._hidden[val]['re_print'] ? ' - перепечатка' : '' ) ).tooltip( {container: 'body'} );
                        //console.log( self._hidden[val], '_generateDateRow' );
                        if ( self._hidden[val]['re_print'] ) {
                            if ( !self._hidden[val]['is_express'] ) {
                                td.css( {
                                    color: '#f0f0f0',
                                    'background-color': '#191919'
                                } );
                                td.attr( 'title', 'Перепечатка' ).tooltip( {container: 'body'} );
                            }
                        }
                        td.html( val );
                    } else
                        td.html( val );
//                    if ( newCnt === cnt - ( self._showAttentionColumn ? 3 : 2 ) )
//                        td.addClass( 'last' ); //Устоновить последнюю колонку
                }
                rVal.append( td );
            }
            newCnt++;
        } );
//        rVal.append( $( '<div class="resize-cell">' ) );
        rVal.contextmenu( {self: this}, function ( e ) {
            let self = e.data.self;
            //console.log( e );
            let td = $( e.target );
            while ( td.length && td.get( 0 ).tagName !== 'TD' ) {
                td = td.parent();
            }
            if ( !td.length )
                td = $( e.target );
            if ( td.index() > 0 && td.index() < $( this ).children().length - 2 ) {
                e.preventDefault();
                let el = this;
                dropDown( {
                    posX: e.clientX,
                    posY: e.clientY,
                    afterInit: function ( menu ) {
                        console.log( $( window ).height(), menu.content.offset().top, menu.content.outerHeight() );
                        if ( menu.content.offset().top + menu.content.outerHeight() > $( window ).height() ) {
                            let top = e.clientY - menu.content.outerHeight();
                            let h = top < 0 ? $( window ).height() - e.clientY : 250;
                            menu.content.css( 'max-height', Math.abs( h ) );
                            top = e.clientY - menu.content.outerHeight();
                            //top=5;
                            menu.content.css( {
                                top: top,
                                overflow: 'hidden',
                                'overflow-y': 'auto'
                            } );
                        }
                    },
                    items: [
                        {header: 'Заказ №' + $( this ).attr( 'data-key' )},
                        'separator',
                        {
                            label: 'Изменить',
                            disabled: self._showAttentionColumn ? ( $( el ).children( ':nth-child(2)' ).children( '[title="Изменить"]' ).length ? false : true ) : ( $( el ).children( ':first-child' ).children( '[title="Изменить"]' ).length ? false : true ),
                            click: function () {
                                console.log( self );
                                console.log( $( el ).children( ':first-child' ).children( '[title="Изменить"]' ) );
                                if ( self._showAttentionColumn )
                                    $( el ).children( ':nth-child(2)' ).children( '[title="Изменить"]' ).trigger( 'click', {self: self} );
                                else
                                    $( el ).children( ':first-child' ).children( '[title="Изменить"]' ).trigger( 'click', {self: self} );
                            }
                        },
                        {
                            label: 'Техничка',
                            click: function () {
                                if ( self._showAttentionColumn )
                                    $( el ).children( ':nth-child(2)' ).children( '[title="Техничка"]' ).trigger( 'click', {self: self} );
                                else
                                    $( el ).children( ':first-child' ).children( '[title="Техничка"]' ).trigger( 'click', {self: self} );
                            }
                        },
                        'separator',
                        {
                            label: 'Копировать',
                            click: function () {
                                self._zakazCopy.call( self, el );
                            }
                        },
                        {
                            label: 'Перепечатка',
                            click: function () {
                                let id = parseInt( td.parent().attr( 'data-key' ) );
                                self._zakazCopy.call( self, el, !isNaN( id ) ? id : 0 );
                            }
                        },
                        'separator',
                        {
                            label: 'Удалить',
                            click: function () {
                                e.data.id = td.parent().attr( 'data-key' );
                                self._zakazRemoveClick( e );
                            }
                        },
                    ]
                } );
            }
        } );
        return rVal;
    },
    _firstStart: function ( answ, onEnd ) { //Вывод первых колонок в пустую таблицу
        this.element.find( '.bunn' ).remove();
        this._lastZakaz = answ.lastZakaz ? answ.lastZakaz : null;
        if ( answ.colOptions ) {
            if ( answ.attention )
                this._attention = answ.attention;
            this._showAttentionColumn = answ.colOptions.showAttentionColumn ? true : false;
            this._drawCols( true );
            this._drawContent.call( this );
            this._drawFooter.call( this );
            if ( $.type( onEnd ) === 'array' && onEnd.length > 1 && $.isFunction( onEnd[1] ) ) {
                onEnd[1].call( onEnd[0], answ );
            }

        } else
            console.warn( 'zakazListController._firstStart()', 'Сервер не передал параметры колонок' );
    },
    _secondStart: function ( answ, onEnd ) { //Вывод следующей стр
        this._tBody.children( ).remove();
        if ( answ.colOptions ) {
            if ( answ.attention )
                this._attention = answ.attention;
            this._showAttentionColumn = answ.colOptions.showAttentionColumn ? true : false;
            this._drawContent();
            this._drawFooter();
            if ( $.type( onEnd ) === 'array' && onEnd.length > 1 && $.isFunction( onEnd[1] ) ) {
                onEnd[1].call( onEnd[0], answ );
            } else if ( $.isFunction( onEnd ) ) {
                onEnd.call( this.element.get( 0 ), answ );
            }
        } else
            console.warn( 'zakazListController._secondStart', 'Сервер не передал параметры колонок' );
    },
    _zakazCopy: function ( el, rePrint ) {
        let id = parseInt( $( el ).attr( 'data-key' ) );
        let self = this;
        id = !isNaN( id ) ? id : 0;
        console.log( '_zakazChangeClick', id );
        console.log( document.def_opt );
        let d = $( '#zakaz_dialog' );
        if ( !d.length ) {
            d = $.fn.creatTag( 'div', {
                id: 'zakaz_dialog'
            } );
            $( 'body' ).append( d );
            d.zakazAddEditController( $.extend( {}, {addCustomerOptions: self.options.addCustomerOptions}, window.def_opt, {
                z_id: id,
                isCopy: id,
                copyAsReprint: rePrint ? rePrint : false,
                close: function () {
                    $( window.dialogSelector ).zakazAddEditController( 'destroy' );
                    $( window.dialogSelector ).remove();
                    self.update();
                },
                afterSave: function ( dt ) {
                    self._lastZakaz = dt.lastZakaz;
                }

            } ) );
        }
        if ( !d.zakazAddEditController( 'isOpen' ) )
            d.zakazAddEditController( 'open' );
    },
    findZakaz: function ( num, notOpen ) {
        notOpen = notOpen === true ? true : false;
        if ( num ) {
            let self = this;
            this.options.otherRequestOptions.findZakaz = num;
            this.update( function () {
                let page = parseInt( self._tFooter.find( '.active' ).children().attr( 'data-key' ) );
                console.log( 'findZakaz', page, self._tFooter.find( '.active' ).children() );
                if ( !isNaN( page ) ) {
                    let uri = new URI( window.location.href );
                    uri.setSearch( 'page', page );
                    window.history.pushState( {page: page}, null, uri.href() );
                }
                if ( !notOpen ) {
                    $( this ).children( '.resize-tbody' ).children( '[data-key=' + num + ']' ).children( '.technikal' ).children( '[title="Изменить"]' ).trigger( 'click' );
                } else {
                    if ( !isNaN( page ) ) {
                        self._updateRaw( num );
                    }
                }
            } );
            this.options.otherRequestOptions.findZakaz = null;
        }
    }
};

( function ( $ ) {
    $.widget( "custom.zakazListController", $.custom.resizebleTable, $.extend( {}, zakaz_list ) );
}( jQuery ) );