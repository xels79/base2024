/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

let pechatnics_tmp = {
    options: {
        isProizvodstvo: false,
        petchatniAddUrl: null,
        petchatnikRemoveUrl: null,
        petchatnikToTomorowUrl: null,
        petchatnikReadyUrl: null,
        pechatnikTable: {},
        dateOptions: {
            month: 'long',
            day: '2-digit',
            weekday: 'long',
            timezone: 'UTC',
        }
    },
    __pechatniks: [ ],
    /*
     *
     * @param {type} callB
     * @param {type} options
     * @param {type} onEnd
     * @returns {undefined}
     */
    _requstDate: function ( callB, options, onEnd ) {
        console.log( 'pechatnics', this.options.pechatnikTable );
        let self = this;
        this._super( function ( answ, onEnd2 ) {
            console.log( 'pechatnics', answ );
            if ( $.isPlainObject( answ.pechatnikTable ) || $.isArray( answ.pechatnikTable ) ) {
                self.options.pechatnikTable = answ.pechatnikTable;
            }
            if ( $.isArray( answ.pechatiks ) ) {
                self.__pechatniks = answ.pechatiks;
            }
            if ( $.isFunction( callB ) )
                callB.call( self, answ, onEnd2 );
            self.__drawPTable();
        }, options, onEnd );
    },
    _c_menu: {
        0: 0,
        1: 4,
        2: 1,
        3: 2,
        4: 3,
        5: 4,
        6: 5,
        7: 6,
        12: 5
    },
    _uf_term_text: {
        0: {label: '', val: 0},
        1: {label: '1+0', val: 1},
        2: {label: '1+1', val: 2},
        3: {label: '0+1', val: 1},
    },
    /*
     * Контекстное меню над печатником
     * @param {eventType} e
     * @returns {undefined}
     */
    __contMenu: function ( e ) {
        e.preventDefault();
        $( this ).addClass( 'select' );
        let el = this;
        let removeSelect = true;
        let items = [
            {
                header: !e.data.ready ? ( e.data.val.name + ' - Заказ №' + e.data.val.z_id ) : ( 'Зак.№' + e.data.val.z_id + ' отпеч.: ' + e.data.ready.toLocaleString( "ru", {
                    month: '2-digit',
                    day: '2-digit',
                    year: 'numeric',
                    timezone: 'UTC',
                } ) )
            },
            {
                label: 'Вернуть в нераспределенные',
                click: function () {
                    removeSelect = false;
                    m_alert( 'Внимание', 'Вернуть заказ №' + e.data.val.z_id + ' в нераспределённые?', {
                        lebel: 'Да',
                        click: function () {
                            if ( e.data.self.options.petchatnikRemoveUrl ) {
                                $.post( e.data.self.options.petchatnikRemoveUrl, {z_id: e.data.val.z_id} ).done( function ( answ ) {
                                    if ( answ.status !== 'ok' ) {
                                        m_alert( 'Ошибка сервера', answ.errorText, {
                                            label: 'Закрыть',
                                            click: function () {
                                                e.data.self.update();
                                            }
                                        }, false );
                                    } else {
                                        e.data.self.update();
                                    }
                                } );
                            } else {
                                console.warn( 'Не передан petchatnikRemoveUrl' );
                            }
                        }
                    }, 'Нет', function () {
                        $( el ).removeClass( 'select' );
                    } );
                }
            }
        ];
        if ( e.data.canToTomorow && !e.data.ready ) {
            let dt = new Date();
            dt.setDate( dt.getDate() + 1 );
            if ( dt.getDay() === 6 ) {
                dt.setDate( dt.getDate() + 2 );
            } else if ( dt.getDay() === 0 ) {
                dt.setDate( dt.getDate() + 1 );
            }
            items[items.length] = {
                label: e.data.self.__computeNextWorkDayText( null, 'Переместить на ' ),
                click: function () {
                    removeSelect = false;
                    m_alert( 'Внимание', 'Переместить заказ №' + e.data.val.z_id + e.data.self.__computeNextWorkDayText( null, ' на ' ) + ' ?', {
                        lebel: 'Да',
                        click: function () {
                            if ( e.data.self.options.petchatnikToTomorowUrl ) {
                                $.post( e.data.self.options.petchatnikToTomorowUrl, {z_id: e.data.val.z_id, date: dt.toLocaleString( "ru", {
                                        month: '2-digit',
                                        day: '2-digit',
                                        year: 'numeric',
                                        timezone: 'UTC',
                                    } )} ).done( function ( answ ) {
                                    if ( answ.status !== 'ok' ) {
                                        m_alert( 'Ошибка сервера', answ.errorText, {
                                            label: 'Закрыть',
                                            click: function () {
                                                e.data.self.update();
                                            }
                                        }, false );
                                    } else {
                                        console.log( answ );
                                        e.data.self.update();
                                    }
                                } );
                            } else {
                                console.warn( 'Не передан petchatnikToTomorowUrl' );
                            }
                        }
                    }, 'Нет', function () {
                        $( el ).removeClass( 'select' );
                    } );
                }
            };
        }
        if ( !e.data.ready ) {
            items[items.length] = 'separator';
            items[items.length] = {
                label: 'Отпечатан',
                click: function () {
                    removeSelect = false;
                    m_alert( 'Внимание', 'Заказ №' + e.data.val.z_id + ' отметить отпечатан?', {
                        lebel: 'Да',
                        click: function () {
                            if ( e.data.self.options.petchatnikReadyUrl ) {
                                $.post( e.data.self.options.petchatnikReadyUrl, {z_id: e.data.val.z_id} ).done( function ( answ ) {
                                    if ( answ.status !== 'ok' ) {
                                        m_alert( 'Ошибка сервера', answ.errorText, {
                                            label: 'Закрыть',
                                            click: function () {
                                                e.data.self.update();
                                            }
                                        }, false );
                                    } else {
                                        console.log( answ );
                                        e.data.self.update();
                                    }
                                } );
                            } else {
                                console.warn( 'Не передан petchatnikToTomorowUrl' );
                            }
                        }
                    }, 'Нет', function () {
                        $( el ).removeClass( 'select' );
                    } );
                }
            }
        }
        new dropDown( {
            posX: e.clientX,
            posY: e.clientY,
            items: items,
            beforeClose: function ( e2 ) {
                if ( removeSelect )
                    $( el ).removeClass( 'select' );
            }
        } );
    },
    /*
     * Содержимое таблицы печатник
     * @param {jQuery} T Таблица
     * @param {Object} val
     * @param {bool} canToTomorow
     * @returns {undefined}
     */
    __drawPTableContent: function ( T, val, canToTomorow ) {
        let self = this;
        let totalProcat = 0;
        let hasCont = false;
        let maxCol = 0;

        console.log( '__drawPTableContent' );
        $.each( val, function ( key, val ) {
            let dt = val.ready ? new Date( val.ready ) : false;
            let tr = ( $( '<tr>' ).attr( 'data-key', key ) ).contextmenu( {
                self: self,
                val: val,
                canToTomorow: canToTomorow,
                ready: dt
            }, self.__contMenu );
            let curCol = 0;
            let isUfLak = val.category == 3;
            hasCont = true;
            $.each( self._tPHeaders, function () {
                let td = $( '<td>' );
                let tmpO = val['colors'] ? JSON.parse( val['colors'] ) : {};
                let colors = self._c_menu[tmpO.face_id] ? self._c_menu[tmpO.face_id] : 0, colorsText = '';
                let uf = self._uf_term_text[val.uf_lak ? val.uf_lak : 0].val;
                let termo = self._uf_term_text[val.thermal_lift ? val.thermal_lift : 0].val;
                curCol++;
                if ( isUfLak ) {
                    colors = 0;
                    uf = ( tmpO.face_id == 1 ? 1 : 0 ) + ( tmpO.side_id == 1 ? 1 : 0 );
                    colorsText += tmpO.face_id == 1 ? '1' : '0';
                    colorsText += '+';
                    colorsText += tmpO.side_id == 1 ? '1' : '0';
                } else {
                    colorsText += tmpO.face_id == 1 ? 'CMYK' : ( tmpO.face_id == 12 ? 'CMYK+1' : ( self._c_menu[tmpO.face_id] ? self._c_menu[tmpO.face_id] : '0' ) );
                    colorsText += '+';
                    colorsText += tmpO.back_id == 1 ? 'CMYK' : ( tmpO.back_id == 12 ? '(CMYK+1)' : ( self._c_menu[tmpO.back_id] ? self._c_menu[tmpO.back_id] : '0' ) );
                }
                colors += self._c_menu[tmpO.back_id] ? self._c_menu[tmpO.back_id] : 0;
                if ( $.inArray( this.cNm, Object.keys( val ) ) > -1 ) {
                    if ( this.cNm === 'colors' ) {
                        if ( !isUfLak )
                            td.text( colorsText );
                    } else if ( this.cNm === 'thermal_lift' || this.cNm === 'uf_lak' ) {
                        if ( this.cNm === 'uf_lak' && isUfLak ) {
                            td.text( colorsText );
                        } else {
                            td.text( self._uf_term_text[val[this.cNm] ? val[this.cNm] : 0].label );
                        }
                    } else if ( this.cNm === 'z_time' ) {
                        td.text( val[this.cNm] );
                        if ( val[this.cNm] !== '00:00' ) {
                            td.addClass( 'time' );
                        }
                    } else {
                        td.text( val[this.cNm] );
                    }
                } else if ( this.cNm === 'prokat' ) {
                    let tir = isUfLak
                            ? ( ( val.number_of_copies ? parseInt( val.number_of_copies ) : 0 ) + ( val.number_of_copies1 ? parseInt( val.number_of_copies1 ) : 0 ) )
                            : ( ( val.num_of_printing_block ? parseInt( val.num_of_printing_block ) : 0 ) + ( val.num_of_printing_block1 ? parseInt( val.num_of_printing_block1 ) : 0 ) );
                    let prokat = ( colors + uf + termo ) * tir;
                    console.log( 'prokat #' + val.z_id, tir + ' colors:' + colors + ' uf:' + uf + ' termo:' + termo + ' prokat:' + prokat );
                    td.text( prokat );
                    totalProcat += prokat;
                } else {
                    td.text( val[this.cNm] );
                }
                tr.append( td );

            } );
            if ( maxCol < curCol )
                maxCol = curCol;
            if ( val.ready ) {
                tr.children().addClass( 'ready' );
                tr.attr( 'title', 'Прока готов ' + dt.toLocaleString( "ru", {
                    month: '2-digit',
                    day: '2-digit',
                    year: 'numeric',
                    timezone: 'UTC',
                } ) );
            }
            T.append( tr );
        } );
        if ( hasCont ) {
            T.append( $( '<tr>' ).addClass( 'summ' )
                    .append( $( '<td>' ).attr( 'colspan', maxCol - 1 ).text( 'Всего прокатов:' ) )
                    .append( $( '<td>' ).text( totalProcat ) ) );
        }
    },
    _tPHeaders: [
        {label: '№', cNm: 'z_id'},
        {label: 'Время', cNm: 'z_time'},
        {label: 'Продукция', cNm: 'production_text'},
        {label: 'Наименование', cNm: 'product_name_text'},
        {label: 'Тираж', cNm: 'number_of_copies'},
        {label: 'Тираж2', cNm: 'number_of_copies1'},
        {label: 'Цвет', cNm: 'colors'},
        {label: 'Уф', cNm: 'uf_lak'},
        {label: 'Термо', cNm: 'thermal_lift'},
        {label: 'Прокаты', cNm: 'prokat'},
    ],
    /*
     * Заголовки таблицы
     *
     * @returns {pechatnics_tmp.__drawPTableHeader.h|$|_$}
     */
    __drawPTableHeader: function () {
        let h = $( '<tr>' );
        $.each( this._tPHeaders, function () {
            let th = $( '<th>' ).text( this.label );
            if ( this.width )
                th.css( {
                    width: this.width
                } )
            h.append( th );
        } );
        return h;
    },
    /*
     * Выводит день в контейнер
     * @param {$} cnt контейнер
     * @param {Object} day содержимое
     * @param {string} dayName название дня
     * @param {bool} canToTomorow
     * @returns {undefined}
     */
    __drawPDay: function ( cnt, day, dayName, canToTomorow ) {
        let self = this;
        if ( Object.keys( day ).length )
            cnt.append( $( '<h4>' ).text( dayName ) );
        $.each( day, function ( key, val ) {
            let T = $( '<table>' ), cntT = $( '<div>' ).addClass( 'row' );
            let tB = $( '<tbody>' ).appendTo( T );
            tB.append( self.__drawPTableHeader() );
            self.__drawPTableContent( tB, val, canToTomorow );
            cntT.append( $( '<h4>' ).text( key ) ).append( T );
            cnt.append( cntT );
        } );
    },
    /*
     * Вычисляет текст для след. лня
     * @param {Date} dt Дата или null
     * @param {String} preText Текс преставка
     * @returns {String}
     */
    __computeNextWorkDayText: function ( dt, preText ) {
        if ( !dt )
            dt = new Date();
        dt.setDate( dt.getDate() + 1 );
        let tomorowText = '';
        if ( dt.getDay() === 6 ) {
            dt.setDate( dt.getDate() + 2 );
            tomorowText = ( !preText ? 'П' : ( preText + 'п' ) ) + 'осле выходных - ';
        } else if ( dt.getDay() === 0 ) {
            dt.setDate( dt.getDate() + 1 );
            tomorowText = ( !preText ? 'П' : ( preText + 'п' ) ) + 'осле завтра - ';
        } else {
            tomorowText = ( !preText ? 'З' : ( preText + 'з' ) ) + 'автра - ';
        }
        return tomorowText + dt.toLocaleString( "ru", {
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            timezone: 'UTC',
        } );
    },
    /*
     * Выводит печатников в контейнер #p_cont
     * @returns {undefined}
     */
    __drawPTable: function () {
        let dt = new Date();
        $( '#p_cont' ).empty();
        if ( $.isPlainObject( this.options.pechatnikTable ) ) {
            if ( $.isPlainObject( this.options.pechatnikTable.today ) ) {
                this.__drawPDay( $( '<div class="day" id="today_p">' ).appendTo( $( '#p_cont' ) ), this.options.pechatnikTable.today, 'Сегодня - ' + dt.toLocaleString( "ru", {
                    weekday: 'short',
                    day: '2-digit',
                    month: 'short',
                    timezone: 'UTC',
                } ), true );
            }
            if ( $.isPlainObject( this.options.pechatnikTable.tomorow ) ) {
                this.__drawPDay( $( '<div class="day" id="tomorow_p">' ).appendTo( $( '#p_cont' ) ), this.options.pechatnikTable.tomorow, this.__computeNextWorkDayText( dt ), false );
            }
        }
    },
    __generateSubMenuPechatniks: function ( zID, dt ) {
        ;
        let self = this;
        if ( !dt ) {
            dt = new Date();
            dt.setDate( dt.getDate() + 1 );
            if ( dt.getDay() === 6 ) {
                dt.setDate( dt.getDate() + 2 );
            } else if ( dt.getDay() === 0 ) {
                dt.setDate( dt.getDate() + 1 );
            }
        }
        let rVal = [
            {header: dt.toLocaleString( "ru", self.options.dateOptions )},
            'separator',
        ];
        console.log( 'day', dt.getDay() );
        for ( let i in this.__pechatniks ) {
            rVal[rVal.length] = {
                label: this.__pechatniks[i].name,
                click: function () {
                    self._addToPechatnikDialog.call( self, {
                        p_name: self.__pechatniks[i].name,
                        p_id: self.__pechatniks[i].id,
                        z_id: zID,
                        z_date: dt.toLocaleString( "ru", {
                            month: '2-digit',
                            day: '2-digit',
                            year: 'numeric',
                            timezone: 'UTC',
                        } )
                    } );
                }
            };
        }
        return rVal;
    },
    _addToPechatnikSend: function ( opt ) {
        let self = this;
        if ( this.options.petchatniAddUrl ) {
            $.post( this.options.petchatniAddUrl, opt ).done( function ( answ ) {
                console.log( answ );
                if ( answ.status !== 'ok' ) {
                    m_alert( 'Ошибка сохранения', answ.errorText, 'Закрыть', false );
                } else {
                    self.update();
                }
            } );
        } else {
            console.warn( 'Не передан petchatniAddUrl' );
        }
    },
    _addToPechatnikDialog: function ( opt ) {
        let self = this;
        m_alert( 'Добавить заказ №' + opt.z_id + ' к печатнику ' + opt.p_name, '', {
            label: 'Да',
            click: function () {
                opt.p_time = $( '#pechatnik-time' ).val();
                console.log( opt );
                self._addToPechatnikSend.call( self, opt );
            }
        }, 'Нет', null, function ( el ) {
            let d = new Date();
            this.body.append(
                    $( '<div>' ).addClass( 'input-group' )
                    .append( $( '<span>' ).addClass( 'input-group-addon' ).text( 'Время' ) )
                    .append( $( '<input>' ).addClass( 'form-control' ).attr( {
                        type: 'text',
                        id: 'pechatnik-time',
                    } ).css( {
                        width: '66px'
                    } ).val( '00:00' ) )
                    );
            $( '#pechatnik-time' ).timePicker();
        } );
    },
    _generateSubMenuWithDate: function ( zID ) {
        let d = new Date();
        let rVal = [
            {header: 'Укажите дату'},
            'separator',
        ];
        let options = {
            month: 'long',
            day: '2-digit',
            weekday: 'long',
            timezone: 'UTC',
        };
        d.setDate( d.getDate() + 1 );

        for ( let i = 0; i < 6; i++ ) {
            d.setDate( d.getDate() + 1 );
            rVal[rVal.length] = {
                label: d.toLocaleString( "ru", options ),
                items: this.__generateSubMenuPechatniks( zID, d )
            };
        }
        return rVal;
    },
    __checkZakazHasPechastnikDay ( vals, zID, allowed ) {
        if ( !allowed )
            return false;
        $.each( vals, function ( k, val ) {
            $.each( val, function ( k2, val2 ) {
                allowed = zID != val2.z_id;
                return allowed;
            } );
            return allowed;
        } );
        return allowed;
    },
    __checkZakazHasPechastnik: function ( zID ) {
        if ( $.isPlainObject( this.options.pechatnikTable ) ) {
            return this.__checkZakazHasPechastnikDay( this.options.pechatnikTable.ready, zID, this.__checkZakazHasPechastnikDay( this.options.pechatnikTable.today, zID, this.__checkZakazHasPechastnikDay( this.options.pechatnikTable.tomorow, zID, true ) ) );
        } else
            return true;
    },
    __rightClickOptions: function ( zID, stage ) {
        if ( this.options.isProizvodstvo ) {
            if ( stage < 6 ) {
                if ( this.__checkZakazHasPechastnik( zID ) )
                    return [
                        {header: 'Печатники'},
                        {
                            label: this.__computeNextWorkDayText( null, 'На ' ),
                            items: this.__generateSubMenuPechatniks( zID )
                        },
                                //                {
                                //                    label:'На другой день',
                                //                    items:this._generateSubMenuWithDate(zID)
                                //                }
                    ];
                else
                    return [ ];
            } else {
                return [ ];
            }
        } else {
            return [ ];
        }
    },
    _checkPechatnikReady: function ( zId ) {
        let rVal = false;
        if ( $.isPlainObject( this.options.pechatnikTable ) && $.isPlainObject( this.options.pechatnikTable.ready ) ) {
            Object.values( this.options.pechatnikTable.ready ).forEach( val => {
                if ( !rVal ) {
                    Object.values( val ).forEach( subVal => {
                        rVal = rVal || subVal.z_id == zId;
                    } );
                }
            } );
        }
        return rVal;
    },
    _generateDateRow: function ( val, rawNumber ) {
        let rVal = this._super( val, rawNumber );
        console.log( val, rawNumber );
        if ( this._checkPechatnikReady( val.id ) ) {
            console.log( 'match' );
            rVal.addClass( 'ready' );
        }

        this._technikalColl( val.id ).insertBefore( rVal.children( ':first-child' ) );
        return rVal;
    },
    _technikalColl: function ( id ) { //Калонка управления
        let rVal = $( '<div class="resize-cell">' ).addClass( 'technikal hand' );
        let self = this;
        let technicals = $( '<a>' )
                .append( $( '<spna>' ).addClass( 'glyphicon glyphicon-eye-open' ) )
                .attr( {
                    href: '#',
                    title: 'Техничка'
                } ).click( {self: this}, function ( e ) {
            console.log( $( this ).parent() );
            self._doubleClick.call( $( this ).parent().parent().get( 0 ), e );
        } );
        if ( this.options.viewRowUrl ) {
            rVal.append( technicals );//.append(view);
        }
        rVal.css({
            'max-width': '67px',
            'min-width': '67px',
            'width': '67px'
        });
        return rVal;
    },
    _firstStart: function ( answ, onEnd ) { //Вывод первых колонок в пустую таблицу
        this.element.find( '.bunn' ).remove();
        if ( answ.colOptions ) {
            if ( this._isFirstStart ) {
                this._drawCols( true );
                this._isFirstStart = false;
            }
            this._drawContent();
            this._drawFooter();
            if ( $.type( onEnd ) === 'array' && onEnd.length > 1 && $.isFunction( onEnd[1] ) ) {
                onEnd[1].call( onEnd[0] );
            }
        } else
            console.warn( 'materialToOrder._firstStart()', 'Сервер не передал параметры колонок' );
    },
    _print: function ( idText, hText ) {
        let main_cnt = $( '<div class="pecahtnik-print">' );
        let cont = $( '<div class="page">' ).appendTo( main_cnt );
        let maxH = 500;//Пикселей
        let curH = 0;
        let cntEl = 0;
        $.each( $( idText ).children( '.row' ), function () {
            if ( curH + $( this ).outerHeight() > maxH ) {
                if ( !cntEl ) {
                    cont.append( $( this ).clone() ).appendTo( main_cnt );
                    cont = $( '<div class="page">' );
                    curH = 0;
                } else {
                    cntEl = 0;
                    curH = $( this ).outerHeight();
                    cont.appendTo( main_cnt );
                    cont = $( '<div class="page">' );
                    cont.append( $( this ).clone() );
                }
            } else {
                cntEl++;
                curH += $( this ).outerHeight();
                cont.append( $( this ).clone() );
            }
        } );
        let mywindow = window.open( '', 'my div', 'height=400,width=600' );
        mywindow.document.write( '<html><head><title>' + hText + '</title>' );
        $.each( $( '[rel="stylesheet"]' ), function () {
            mywindow.document.write( '<link href="' + $( this ).attr( 'href' ) + '" rel="stylesheet">' );
        } );
        mywindow.document.write( '</head><body >' );
        console.log( $( '<div>' ).append( main_cnt ).html() );
        mywindow.document.write( $( '<div>' ).append( main_cnt ).html() );
        mywindow.document.write( '</body></html>' );
        setTimeout( function () {
            mywindow.document.close();
            setTimeout( function () {
                mywindow.close();
            }, 400 );

        }, 400 );
        //mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        mywindow.print();
//        mywindow.close();
    },
    print: function () {
        let self = this;
        if ( !$( '#tomorow_p' ).length && !$( '#today_p' ).length ) {
            m_alert( 'Сообщение', 'Нечего печатать!', 'Закрыть', false );
            return true;
        }
        if ( $( '#tomorow_p' ).length && $( '#today_p' ).length ) {
            new mDialog.m_alert( {
                headerText: 'Сообщение',
                content: 'Выберете какой день печатать?',
                okClick: {
                    label: 'Печатать на сегодня',
                    click: function () {
                        self._print( '#today_p', 'Сегодня' );
                    }
                },
                midButton: {
                    label: 'Печатать на завтра',
                    click: function () {
                        self._print( '#tomorow_p', 'Завтра' );
                    }
                },
                canselClick: 'Закрыть'
            } );
        } else if ( !$( '#tomorow_p' ).length ) {
            self._print( '#today_p', 'Сегодня' );
        } else {
            self._print( '#tomorow_p', 'Завтра' );
        }
        return true;
    },
};
( function ( $ ) {
    $.widget( "custom.pechatnics", $.custom.dirtZakazController, $.extend( {}, pechatnics_tmp ) );
}( jQuery ) );
