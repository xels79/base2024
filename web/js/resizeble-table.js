/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


let resizeble_table = {
    _fieldParams: null,
    _list: null,
    _hidden: null,
    _hidden2: null,
    _currentPage: 0,
    _count: 0,
    _editCollName: null,
    _firstElNum: 1,
    _filters: {},
    _hasFilterColumn: false,
    _sort: {},
    _memColor: [ ],
    _tHeader: null,
    _tBody: null,
    _tFooter: null,
    _tCaption: null,
    _resizeColNumber: -1,
    options: {
        setsizesUrl: null,
        requestUrl: null,
        bigLoaderPicUrl: null,
        getAvailableColumnsUrl: null,
        otherRequestOptions: {}
    },
    _create: function () {
        let self = this;
        this._super();
        this._editCollName = this._editCollName ? this._editCollName : null;
        this._tHeader = this.element.children( '.resize-header' );
        this._tBody = this.element.children( '.resize-tbody' );
        this._tFooter = this.element.children( '.resize-tfooter' );
        this._tCaption = this.element.children( '.resize-caption' );
        if ( this.options.bigLoaderPicUrl ) {
            this._tBody.append( $( '<div class="resize-row bunn">' ).append( $( '<div class="resize-cell">' ).append( $( '<img>' ).attr( {
                src: this.options.bigLoaderPicUrl,
            } ).css( {
                'max-width': '30px',
                'max-height': '30px'
            } ) ) ) );
        } else {
            this._tBody.append( $( '<div class="resize-row bunn">' ).append( $( '<div class="resize-cell">' ).text( 'Загрузка...' ) ) );
        }
        let optBut = $( '<a>' )
                .append( $( '<span>' ).addClass( 'glyphicon glyphicon-cog' ) )
                .attr( 'title', 'Настройки таблицы' )
                .click( {self: this}, function ( e ) {
                    e.data.self._optionsClick( e.data.self._editCollName );
                } );
        this._tCaption.append( optBut );
        let dtPickerOpt = {
            maxDate: '+0d'
        };
        $( window ).on( 'popstate', {self: this}, this._popstate );
        $( window ).mouseup( function () {
            if ( self._resizeColNumber > -1 ) {
                console.log( 'End resize-controls - #' + self._resizeColNumber );
                self._resizeDone.call( self._tHeader.children( ':first-child' ), {self: self} );
                self._triggerLightColToResize( self._resizeColNumber, false );
                self._resizeColNumber = -1;
            }
        } ).mousemove( {self: this}, this._mouseMove );
        $( '#date-from' ).datepicker( dtPickerOpt );
        $( '#date-to' ).datepicker( dtPickerOpt );
        $( '#date-to' ).datepicker( 'setDate', '+0d' );
        $( '#date-to,#date-from' ).on( 'change', {self: this}, this._dateFilterClick );
        $( '#time-reset' ).click( {self: this}, function ( e ) {
            $( '#date-to,#date-from' ).off( 'change' );
            $( '#date-from' ).val( '' );
            $( '#date-to' ).val( '' );
            $( '#date-to' ).datepicker( 'setDate', '+0d' );
            $( '#date-to,#date-from' ).on( 'change', {self: e.data.self}, e.data.self._dateFilterClick );
            e.data.self.update();
        } );

    },
    _dateFilterClick: function ( e ) {
        e.data.self.update.call( e.data.self );
    },

    /*
     * Подготавлевает список колонок для фильтрации
     *
     * @returns {Объект где ключ равен realColName, значени:
     *  массив с ключами выбранных пользователем}
     */
    _prepareFilterParamsForRequest: function () {
        let rV = {showReprint: $( '#show-reprint' ).attr( 'data-reprint' ) === 'true'}, tmp;
        $.each( $( '.m_filter' ), function () {
            if ( $( this ).hasClass( 'like' ) ) {
                tmp = $( this ).m_filter_like( 'value' );
            } else {
                tmp = $( this ).m_filter( 'selected-items-keys' );
            }
            if ( !rV.filters )
                rV.filters = {};
            if ( tmp.length ) {
                rV.filters[$( this ).attr( 'data-realcolname' )] = tmp;
            }
        } );
        return rV;
    },

    _checkFilters: function () {
        let hasFilter = false, filterTr = $( '.filter-tr' );
        if ( filterTr.length ) {
            $( '.m_filter' ).each( function () {
                if ( $( this ).hasClass( 'like' ) )
                    hasFilter = $( this ).m_filter_like( 'hasSelected' );
                else
                    hasFilter = $( this ).m_filter( 'hasSelected' );
                return !hasFilter;
            } );
            if ( hasFilter ) {
                filterTr.addClass( 'filter-has-selected' );
                $( '#filter-reset' ).removeClass( 'disabled' );
            } else {
                filterTr.removeClass( 'filter-has-selected' );
                $( '#filter-reset' ).addClass( 'disabled' );
            }
        }
        return hasFilter;
    },
    /*
     *
     * Запрос данных с сервера и вызов указанной функции
     * в случае успеха
     * @param {function} callB - коллбэк функция
     * @param {object} options - параметры на сервер
     * @returns {undefined}
     */
    _requstDate: function ( callB, options, onEnd ) {
        options = $.type( options ) === 'object' ? options : {};
        let self = this;
        let dateFilter = {
            'date-from': $( '#date-from' ).val() ? $( '#date-from' ).val() : null,
            'date-to': $( '#date-to' ).val() ? $( '#date-to' ).val() : null,
        };
        this._checkFilters();
        if ( this.options.requestUrl ) {
            console.log( '_requestDate:', $.extend( {}, options, this.options.otherRequestOptions, {sort: self._sort}, this._prepareFilterParamsForRequest() ) );
            $.post( this.options.requestUrl, $.extend( {}, dateFilter, options, this.options.otherRequestOptions, {sort: self._sort}, this._prepareFilterParamsForRequest() ) ).done( function ( answ ) {
                console.log( answ );
                self._currentPage = answ.page ? answ.page : 0;
                self._count = $.type( answ.count ) !== 'undefined' ? answ.count : 0;
                if ( answ.stageLevels )
                    self._stageLevels = answ.stageLevels;
                if ( answ.colOptions ) {
                    self._fieldParams = answ.colOptions;
                    self._firstElNum = ( self._currentPage * self._fieldParams.pageSize ) + 1;
                }
                if ( $.type( answ.sortable ) == 'array' )
                    self.sortable = answ.sortable;
                else
                    self.sortable = [ ];
                if ( answ.list ) {
                    self._list = answ.list;
                }
                if ( $.type( answ.filters ) === 'object' ) {
                    self._filters = answ.filters;
                } else {
                    self._filters = {};
                }
                if ( answ.hidden )
                    self._hidden = answ.hidden;
                if ( answ.hidden2 )
                    self._hidden2 = answ.hidden2;
                if ( $.isFunction( callB ) )
                    callB.call( self, answ, function () {
                        if ( $.type( onEnd ) === 'array' && onEnd.length > 1 && $.isFunction( onEnd[1] ) ) {
                            onEnd[1].call( onEnd[0], answ );
                        } else if ( $.isFunction( onEnd ) ) {
                            onEnd.call( this.element ? this.element.get( 0 ) : this, answ );
                        }
//                        let b = NEWFUNCTION.browser();
//                        if ( b === 'Firefox' || b === 'Microsoft edge' )
//                            self.element.colResizableXELS( 'update' );
                    } );
                if ( $.type( answ.inform ) === 'object' ) {
                    m_alert( answ.inform.headerText, answ.inform.text, 'Закрыть', false );
                }
            } ).fail( function ( answ ) {
                new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                console.error( answ );
            } );
        } else {
            console.warn( 'zakazListController._requstDate()', 'Не задана requestUrl' );
        }
    },
    _resizeDone: function ( data ) { //Сохранение размеров по окончанию их изменения
        let self = data.self, allFields = $.extend( {}, self._fieldParams.constant, self._fieldParams.add );
        let key = Object.keys( allFields );
        let child = $( this ).children( '[data-colkey]' );
        let send = {};
        $.each( child, function () {
            send[$( this ).attr( 'data-colkey' )] = {name: $( this ).attr( 'data-colkey' ), width: Math.floor( $( this ).outerWidth() ), label: $( this ).text()};
        } );
        if ( self.options.setsizesUrl ) {
            $.post( self.options.setsizesUrl, $.extend( {}, {options: send}, self.options.otherRequestOptions ) ).done( function ( answ ) {
                console.log( answ );
            } ).fail( function ( answ ) {
                new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                console.error( answ );
            } );
        } else
            console.error( '_resizeDone', 'Не передан setsizesUrl' );
    },
    _allFieldsWidthByName: function () {
        let rVal = {};
        $.each( this._fieldParams.constant, function () {
            rVal[this.name] = this.width;
        } );
        $.each( this._fieldParams.add, function () {
            rVal[this.name] = this.width
        } );
        return rVal;
    },
    _allFields: function () { //Объединяет все колонки
        let allFields = [ ];
        $.each( this._fieldParams.constant, function () {
            allFields[allFields.length] = this
        } );
        $.each( this._fieldParams.add, function () {
            allFields[allFields.length] = this
        } );
        return allFields;
    },
    /*
     * Находит нужный ряд,
     * Заменяет его банром
     * И возвращает чтобы не искать
     * @param {type} id - Идентификатор элемента
     * @returns {$('<div.resize-row>')}
     */
    __showBunnerAtRowAndGetIt: function ( id ) {
        let tr = id ? this._tBody.children( '[data-key=' + id + ']' ) : $( '<div class="resize-row">' ).addClass( 'bunn' ).appendTo( this._tBody );
        //let colCnt=this.element.children('tbody').children().first().children().length;
        if ( this.options.bigLoaderPicUrl ) {
            tr.empty().append( $( '<div class="resize-cell">' )
                    .append( $( '<img>' ).attr( {
                        src: this.options.bigLoaderPicUrl,
                    } ).css( {
                        'max-width': '30px',
                        'max-height': '30px'
                    } ) ).attr( {
            } ) );
        } else {
            tr.empty().append( $( '<div class="resize-cell">' )
                    .text( 'Загрузка...' )
                    .attr( {
                        colspan: Object.keys( this._allFields() ).length
                    } ) );
        }
        return tr;
    },
    __paginatioClick: function ( e ) { //Нажата кнопка пагинации
        e.preventDefault();
        let self = e.data.self, page = this.tagName === 'INPUT' ? $( this ).val() : $( this ).attr( 'data-key' );
        let uri = new URI( window.location.href );
        uri.setSearch( 'page', page );
        //console.log(window.location.hash);
        console.log( URI.parseQuery( uri.query() ) );
        console.log( uri.href() );
//        if ( self._hasFilterColumn ) {
//            while ( self._tBody.children( ).length > 2 )
//                self._tBody.children( ':last-child' ).remove();
//        } else
        self._tBody.children(  ).remove();
        self._tFooter.empty();
        self.__showBunnerAtRowAndGetIt();
        window.history.pushState( {page: page}, null, uri.href() );
    },
    /*
     * Кнопка назад браузера
     * @param {type} e event
     * @returns {undefined}
     */
    _popstate: function ( e ) {
        console.log( e, e.state );
        if ( $.type( e.state ) === 'object' && $.type( e.state.page ) !== 'undefined' ) {
            let self = e.data.self;
//            self.element.children('tbody').children(':not(:first-child)').remove();
//            if ( self._hasFilterColumn ) {
//                while ( self._tBody.children( ).length > 2 )
//                    self._tBody.children( ':last-child' ).remove();
//            } else
            self._tBody.children( ).remove();

            self._tFooter.empty();
            self.__showBunnerAtRowAndGetIt();
            self._requstDate( self._secondStart, {page: e.state.page} );
        }
    },

    _sorterReset: function () {
        //has-sorter
        $( '.has-sorter' ).children( 'a' ).children( 'span:last-child' ).remove();
        delete this._sort;
        this._sort = {};
    },
    _headerClick: function ( e ) {
        let tmp = e.data.self._sort[e.data.name];
        e.data.self._sorterReset();
        if ( tmp ) {
			if (tmp==='DESC'){
				$( this ).children( ':last' ).remove();
				$( this ).append( $( '<span>' ).addClass( 'glyphicon glyphicon glyphicon-sort-by-alphabet' ) );
				e.data.self._sort[e.data.name] = 'ASC';		
			}else{
				$( this ).children( ':last' ).remove();
//            if (tmp=='DESC'){
//                $(this).children(':last').remove();
//                $(this).append($('<span>').addClass('glyphicon glyphicon-sort-by-alphabet'));
//                e.data.self._sort[e.data.name]='ASC';
//            }else if(tmp=='ASC'){
//                $(this).children(':last').remove();
//            }
			}
        } else {
            $( this ).children( ':last' ).remove();
            $( this ).append( $( '<span>' ).addClass( 'glyphicon glyphicon-sort-by-alphabet-alt' ) );
            e.data.self._sort[e.data.name] = 'DESC';
        }
        e.data.self.update();
        console.log( 'sort:', e.data.self._sort );
    },

    /*
     * Заголовки с елементами управления
     * @param {type} tecnical - Вывадить/нет технич колонку
     * @returns {undefined}
     */
    _drawCols: function ( tecnical ) {
        let allFields = this._allFields();
        let tr = $( '<div class="resize-row">' );
        let self = this;
        let totalWidth = 0;
        let filterRaw = null;
        let cnt = 0;
        tecnical = $.type( tecnical ) === 'undefined' ? false : true;
        if ( tecnical ) {
            if ( this._showAttentionColumn ) {
                tr.append( $( '<div class="resize-cell-header">' ).css( {
                    'max-width': '30px',
                    'min-width': '30px',
                    'width': '30px'
                } ).addClass( 'technikal' ) );
                totalWidth += 30;
            }
            tr.append( $( '<div class="resize-cell-header">' ).css( {
                'max-width': '67px',
                'min-width': '67px',
                'width': '67px'
            } ).addClass( 'technikal' ) );
            totalWidth += 75;
        }

        filterRaw = $( '<div class="resize-row">' ).addClass( 'filter-tr' );
        if ( self._showAttentionColumn )
            filterRaw.append( $( '<div class="resize-cell">' ).addClass( 'technikal' ).css( {
                'max-width': '30px',
                'min-width': '30px',
                'width': '30px'
            } ) );
        if ( tecnical )
            filterRaw.append( $( '<div class="resize-cell">' ).addClass( 'technikal' ).css( {
                'max-width': '67px',
                'min-width': '67px',
                'width': '67px'
            } ) );

        $.each( allFields, function () {
            //if (this.name==='empt') return;
            let cssOpt = {
                'max-width': this.width + 'px',
                'min-width': this.width + 'px',
                'width': this.width + 'px'
            };
            let th = $( '<div class="resize-cell-header">' )
                    .css( cssOpt )
                    .attr( {'data-colkey': this.name} );
            totalWidth += parseInt( this.width );
            if ( $.inArray( this.name, Object.keys( self._filters ) ) > -1 ) {//filter
                self._hasFilterColumn = true;
                if ( $.type( self._filters[this.name].content ) === 'array' || $.type( self._filters[this.name].content ) === 'object' ) {
                    filterRaw.append( $( '<div class="resize-cell">' ).css( cssOpt ).append( $( '<div>' ).m_filter( {
                        list: self._filters[this.name].content,
                        onClose: [ self, self.update ]
                    } )
                            .attr( {'data-realColName': self._filters[this.name].realColName} ) ) );
                } else if ( $.type( self._filters[this.name].like ) === 'array' || $.type( self._filters[this.name].like ) === 'object' ) {
                    filterRaw.append( $( '<div class="resize-cell">' ).css( cssOpt ).append( $( '<div>' ).m_filter_like( {
                        list: self._filters[this.name].content,
                        onUpdate: [ self, self.update ]
                    } )
                            .attr( {'data-realColName': self._filters[this.name].realColName} ) ) );

                } else if ( self._filters[this.name].isDate ) {
                    filterRaw.append( $( '<div class="resize-cell">' ).css( cssOpt ).append( $( '<div>' ).m_filter_like( {
                        asDate: true,
                        onUpdate: [ self, self.update ]
                    } )
                            .attr( {'data-realColName': self._filters[this.name].realColName} ) ) );
                }
            } else if ( filterRaw ) {
                filterRaw.append( $( '<div class="resize-cell">' ).css( cssOpt ) );
            } else
                cnt += 1;
            if ( $.inArray( this.name, self.sortable ) > -1 ) {
                th.append( $( '<a>' )
                        .text( this.name === 'firm_id' ? '№' : this.label )
                        .click( {name: this.name, self: self}, self._headerClick )
                        );
                th.addClass( 'has-sorter' );
            } else
                th.text( this.name === 'firm_id' ? '№' : this.label );
            tr.append( th );
        } );
        console.log( this.options );
        if ( allFields.length ) {
            this._tHeader.append( tr );
            if ( this.options.setsizesUrl ) {
                let b = NEWFUNCTION.browser();
                console.log( 'resizeble-table', b );
            } else
                console.warn( 'resizebleTable->_drawCols', 'Не задан setsizesUrl' );
            if ( filterRaw.children().length > 2 ) {
                $( '<div>' ).append( $( '<a id="filter-reset" title="Сбросить фильтры">' )
                        .addClass( 'btn disabled' )
                        .append( $( '<span>' )
                                .addClass( 'glyphicon glyphicon-filter' ) )
                        .click( function ( e ) {
                            console.log( $( '.m_filter:not(.like)' ) );
                            $( '.m_filter:not(.like)' ).m_filter( 'reset' );
                            console.log( $( '.like' ) );
                            $( '.like' ).m_filter_like( 'reset' );
                            self.update(null,true);
                        } )
                        ).insertBefore( this._tCaption.children( ':last-child' ) );
                console.log( 'end_filters_create', this.element );
                this._tHeader.append( filterRaw );
            }
        }
        this._createResizeTouchZone();
        this._tBody.css( 'min-height', this._fieldParams.pageSize * this._tHeader.children( ':first-child' ).outerHeight() );
    },
    /*
     *
     * @param {event} e
     * @returns {undefined}
     */
    _mouseMove: function ( e ) {
        let self = e.data.self;
        if ( self._resizeColNumber > -1 ) {
            let div = self._tHeader.children( ':first-child' ).children( ':nth-child(' + ( self._resizeColNumber + 1 ) + ')' );
            let pos = div.offset();
            if ( e.pageX > pos.left + 10 )
                self._doResizeCol( e.pageX - pos.left );
        }
    },
    /*
     * Выполняет изменение размера колонки в
     * каждом ряду кроме tFooter
     * @param {int} newWidth
     * @returns {undefined}
     */
    _doResizeCol: function ( newWidth ) {
        this.element.find(
                '>div.resize-header>div.resize-row>div:nth-child(' + ( this._resizeColNumber + 1 ) + '),' +
                '>div.resize-tbody>div.resize-row>div:nth-child(' + ( this._resizeColNumber + 1 ) + ')' )
                .width( newWidth ).css( {
            'min-width': newWidth + 'px',
            'max-width': newWidth + 'px'
        } );
    },
    /*
     * Подсвечивает изменяемые колонки
     * @param {int} ind     -индекс колонки
     * @param {bool} isOn   -включить/выключить
     * @returns {undefined}
     */
    _triggerLightColToResize: function ( ind, isOn ) {
        isOn = isOn || false;
        this.element.find(
                '>div.resize-header>div.resize-row>div:nth-child(' + ( ind + 1 ) + '),' +
                '>div.resize-tbody>div.resize-row>div:nth-child(' + ( ind + 1 ) + ')' ).each( function () {
            let d = $( this ).children( '.resize-controls' );
            if ( !d.length ) {
                d = $( '<div class="resize-controls">' );
                $( this ).append( d );
            }
            if ( isOn )
                d.addClass( 'on' );
            else
                d.removeClass( 'on' );

        } );
    },
    /*
     * Добовляем елемент управления к заголовку
     * @returns {undefined}
     */
    _createResizeTouchZone: function () {
        let self = this;
        //_resizeColNumber
        this._tHeader.children( ':first-child' ).children(':not(.technikal)').each( function () {
            let d = $( '<div class="resize-controls">' );
            let ind = $( this ).index();
            $( this ).append( d );
            d.mousedown( function ( e ) {
                e.preventDefault();
                self._resizeColNumber = ind;
                console.log( 'start resize-controls - #' + self._resizeColNumber );
                self._triggerLightColToResize( ind, true )
            } );
        } );
    },
    /*
     * Диалог настроек таблицы: содержимое диалога правая часть
     * @param {string} colN - имя колонки для настройки цвета
     * @returns {$('div')}
     */
    _optionsContent2: function ( colN ) {
        let rVal = $( '<div>' );
        if ( colN ) {
            rVal.append( $( '<h4>' ).text( 'Цвета:' ) );
            let colorsStage = $( '<div>' ).addClass( 'colors-setup' );
            colorsStage.append( $( '<p>' ).text( 'Этап работы' ) );
            for ( let i = 0; i < this['_' + this._editCollName + 'Levels'].length; i++ ) {
                let row = $( '<div>' ).addClass( 'stage-colors' );
                let inp = $( '<input>' ).attr( {type: 'text'} ).cPicker();
                row.append( $( '<span>' ).text( this['_' + this._editCollName + 'Levels'][i] ) );//[this._editCollName]
                if ( this._fieldParams.colors && this._fieldParams.colors[colN] && this._fieldParams.colors[colN][i] ) {
                    inp.val( this._fieldParams.colors[colN][i] );
                }
                row.append( inp );
                colorsStage.append( row );
            }
            rVal.append( colorsStage );
        } else
            console.log( 'optionsContent2', 'Не указанно имя колонки colN' );
        return rVal;
    },
    /*
     * Диалог настроек таблицы: содержимое диалога левая часть
     * @param {type} d
     * @returns {$('<div>')}
     */
    _optionsContent: function ( d ) { //
        let rVal = $( '<div>' ), self = this;
        $.post( this.options.getAvailableColumnsUrl, this.options.otherRequestOptions ).done( function ( answ ) {
            console.log( answ );
            let colsToAdd = [ ], existsCol = [ ];
            if ( answ.availableColumns && answ.options ) {
                rVal.append( $( '<h4>' ).text( 'Колонки:' ) );
                rVal.append( $( '<div>' )
                        .addClass( 'content-header' )
                        .append( $( '<p>' ).text( 'Добавлены' ) )
                        .append( $( '<p>' ).text( 'Доступны' ) ) );
                for ( let i = 0; i < answ.options.constant.length; i++ ) {
                    existsCol[existsCol.length] = {
                        label: answ.options.constant[i].label,
                    };
                    colsToAdd[colsToAdd.length] = {
                        label: answ.options.constant[i].label,
                        class: 'list-group-item ui-state-disabled'
                    };
                }
                for ( let i = 0; i < answ.options.add.length; i++ ) {
                    existsCol[existsCol.length] = {
                        label: answ.options.add[i].label,
                        'data-key': answ.options.add[i].name
                    };
                    if ( answ.options.add[i].name !== 'empt' ) {
                        colsToAdd[colsToAdd.length] = {
                            label: answ.options.add[i].label,
                            'data-key': answ.options.add[i].name
                        };
                        if ( $.inArray( answ.options.add[i].name, Object.keys( answ.availableColumns ) ) > -1 ) {
                            delete answ.availableColumns[answ.options.add[i].name];
                        }
                    }
                }
                $.each( answ.availableColumns, function ( key, val ) {
                    colsToAdd[colsToAdd.length] = {
                        label: val,
                        'data-key': key
                    };
                } );
                console.log( existsCol, colsToAdd );
                rVal.append( $.fn.twoEditableLists( existsCol, colsToAdd, null, {
                    firstListClass: 'list-group to-add'
                }, 'label' ) );
                rVal.append( $( '<h4>' ).text( 'Другие настройки:' ) );
                rVal.append( $( '<div>' )
                        .addClass( 'dialog-control-group-inline' )
                        .append( $( '<label>' ).text( 'Заказов на странице:' ) )
                        .append( $( '<input>' ).val( $.type( answ.options.pageSize ) !== 'undefined' ? answ.options.pageSize : 1800 ).attr( 'id', 'table-options-pagesize' ) )
                        );
                rVal.append( $( '<div>' )
                        .addClass( 'dialog-control-group-inline' )
                        .append( $( '<label>' ).text( 'Показывать предупреждения:' ) )
                        .append( $( '<input>' ).attr( {
                            id: 'table-options-showAttentionColumn',
                            type: 'checkbox',
                            checked: self._showAttentionColumn === true
                        } ).click( function () {
                            if ( $( this ).attr( 'checked' ) )
                                $( this ).removeAttr( 'checked' );
                            else
                                $( this ).attr( 'checked', true );
                        } ) )
                        );
                d.maindialog( 'hideBunner' ).maindialog( 'moveToCenter' );
            }
        } ).fail( function ( answ ) {
            new m_alert( 'Ошибка сервера', answ.responseText, true, false );
            console.error( answ );
        } );
        return rVal;
    },
    /*
     * Диалог настроек таблицы сохранение настроек
     * @param {type} d - объект $ контент диалога
     * @returns {undefined}
     */
    _optionsSave: function ( d ) {
        console.log( d );
        if ( this.options.setColumnsUrl ) {
            let toSend = [ ], colors = {}, self = this;
            colors[this._editCollName] = [ ];
            console.log( d.find( '.to-add' ).children( '[data-key]' ) );
            d.find( '.to-add' ).children( '[data-key]' ).each( function () {
                toSend[toSend.length] = $( this ).attr( 'data-key' );
            } );
            d.find( '.stage-colors' ).each( function () {
                let val = $( this ).children( 'input' ).val();
                colors[self._editCollName][colors[self._editCollName].length] = val ? val : "";
            } );
            console.log( $( '#table-options-showAttentionColumn' ).attr( 'checked' ) );
            $.post( this.options.setColumnsUrl, $.extend( {}, {
                coltoadd: toSend.length ? toSend : 'remove-all',
                colors: colors,
                pageSize: $.isNumeric( $( '#table-options-pagesize' ).val() ) ? parseInt( $( '#table-options-pagesize' ).val() ) : 1800,
                showAttentionColumn: $( '#table-options-showAttentionColumn' ).attr( 'checked' ) === 'checked'
            }, self.options.otherRequestOptions ) ).done( function ( answ ) {
                console.log( answ );
                if ( answ.status === 'ok' ) {
                    d.maindialog( 'close' );
                    window.location.reload( true );
                }
            } ).fail( function ( answ ) {
                new m_alert( 'Ошибка сервера', answ.responseText, true, false );
                console.error( answ );
            } );
        } else {
            console.warn( '_optionsSave', 'Не задан setColumnsUrl' );
        }
    },
    /*
     * Диалог настроек таблицы - поля и тд...
     * @param {string} colN - имя колонки с изменяемым цветом
     * @returns {undefined}
     */
    _optionsClick: function ( colN ) {
        if ( this.options.getAvailableColumnsUrl ) {
            let self = this, d = $( '<div>' )
                    .appendTo( 'body' )
                    .attr( {
                        id: 'zakaz_list_options_dialog',
                        title: 'Настройки таблицы'
                    } );
            d.append( this._optionsContent( d ) )
                    .append( this._optionsContent2( colN ) )
                    .maindialog( {
                        modal: true,
                        resizable: false,
                        close: function () {
                            d.remove();
                        },
                        open: function ( e, ui ) {
                            $( this ).parent().addClass( 'zakaz-list-popup' );
                        },
                        buttons: [
                            {
                                text: 'Сохранить',
                                role: 'save-button',
                                click: function () {
                                    self._optionsSave( d );
                                }
                            },
                            {
                                text: 'Отменить',
                                click: function () {
                                    $( this ).maindialog( 'close' );
                                }
                            }
                        ]
                    } ).maindialog( 'showBunner' );
        } else {
            console.warn( '_optionsClick: Не передан getAvailableColumnsUrl' );
        }
    },
    _drawContent: function () { //Выводит все полученные ряды
        let self = this;
        let rawNumber = this._firstElNum;
        if ( this._list && $.isFunction( this._generateDateRow ) ) {
            $.each( this._list, function ( key, val ) {
                let tr = self._generateDateRow( this, rawNumber++ );
                if ( tr )
                    self._tBody.append( tr );
            } );
            if ( $.isFunction( self._generateTableFooter ) )
                self._generateTableFooter.call( self );
        } else {
            $.each( this._list, function ( key, val ) {
                let tr = $( '<div class="resize-row">' );
                $.each( val, function ( k, v ) {
                    let td = $( '<div class="resize-cell">' ).text( v );
                    if ( k === 'empt' )
                        td.addClass( 'last' );
                    tr.append( td );
                } );
                self._tBody.append( tr );
            } );

        }
        //$('.m_filter').m_filter('redraw');
    },
    _drawFooter: function () {//Выводит footer таблицы
        let pCnt = Math.ceil( this._count / this._fieldParams.pageSize );
        let ft = this._tFooter;
        let elW = 0;
        let maxW = 820;
        let maxElCount = 0;
        let startP = 0, endP = pCnt;
        if ( pCnt < 2 )
            return;
        let list = $( '<ul>' ).addClass( 'pagination' )
                , prev = $( '<li>' )
                .append( $( '<a>' )
                        .attr( {
                            href: '#',
                            'data-key': this._currentPage - 1,
                            title: 'Предыдущая страница'
                        } ).html( '<span aria-hidden="true">&laquo;</span>' ) )
                .appendTo( list )
                , next = $( '<li>' )
                .append( $( '<a>' )
                        .attr( {
                            href: '#',
                            'data-key': this._currentPage + 1,
                            title: 'Следующая страница'
                        } ).html( '<span aria-hidden="true">&raquo;</span>' ) );
        ft.append( $( '<nav>' ).attr( {'aria-label': 'Page navigation'} ).append( list ) );

        let lastPage = $( '<li>' ).append( $( '<a>' ).attr( {
            href: '#',
            'data-key': pCnt - 1,
            title: 'Последняя страница'
        } ).click( {self: this}, this._paginatioClick ).html( '<span aria-hidden="true">&raquo;&raquo;&raquo;</span>' ) );
        let firstPage = $( '<li>' ).append( $( '<a>' ).attr( {
            href: '#',
            'data-key': 0,
            title: 'Первая страница'
        } ).click( {self: this}, this._paginatioClick ).html( '<span aria-hidden="true">&laquo;&laquo;&laquo;</span>' ) );
        if ( !this._currentPage )
            prev.addClass( 'disabled' );
        else
            prev.children( 'a' ).click( {self: this}, this._paginatioClick );
        if ( this._currentPage === pCnt - 1 )
            next.addClass( 'disabled' );
        else
            next.children( 'a' ).click( {self: this}, this._paginatioClick );
        elW = list.children( ':first-child' ).outerWidth( true );
        if ( this._currentPage > 0 ) {
            //list.append( firstPage );
            firstPage.insertBefore( list.children( ':first-child' ) );
        }
        //maxElCount
        maxW -= elW * 2 - firstPage.outerWidth( true ) * 2;
        maxElCount = Math.floor( maxW / elW );
        if ( pCnt > maxElCount ) {
            if ( this._currentPage > maxElCount / 2 ) {
                if ( this._currentPage + Math.floor( maxElCount / 2 ) > pCnt ) {
                    endP = pCnt;
                    startP = this._currentPage - Math.floor( maxElCount / 2 ) - ( this._currentPage + Math.floor( maxElCount / 2 ) - pCnt );
                } else {
                    startP = this._currentPage - Math.floor( maxElCount / 2 );
                    endP = this._currentPage + Math.floor( maxElCount / 2 );
                }
            } else {
                endP = maxElCount - 1
            }
        }
        for ( let i = startP; i < endP; i++ ) {
            let li = $( '<li>' ).appendTo( list );
            if ( this._currentPage === i )
                li.addClass( 'active' );
            li.append( $( '<a>' )
                    .attr( {
                        href: '#',
                        'data-key': i,
                    } )
                    .click( {self: this}, this._paginatioClick )
                    .text( i + 1 ) );
        }
        list.append( next );
        if ( this._currentPage < pCnt - 1 ) {
            list.append( lastPage );
        }
        if ( pCnt > maxElCount ) {
            let dd_m = $( '<ul class="dropdown-menu">' ).attr( {
                'aria-labelledby': 'paginationdropdown',
                role: 'menu'
            } );
            for ( let i = 0; i < pCnt; i++ ) {
                dd_m.append( $( '<li>' ).append( $( '<a class="dropdown-item">' )
                        .attr( {
                            href: '#',
                            'data-key': i,
                        } )
                        .click( {self: this}, this._paginatioClick )
                        .text( 'Стр.: ' + ( i + 1 ) ) ) );
            }
            $( '<li class="dropup">' ).append( $( '<a>' ).attr( {
                id: 'paginationdropdown',
                'data-toggle': "dropdown",
                'aria-haspopup': "true",
                'aria-expanded': "false"
            } ).text( 'Все страницы' ) ).append( dd_m ).appendTo( list );
            list.append( $( '<li class="input-group">' )
                    .append( $( '<input class="form-control">' ).attr( {
                        type: 'number',
                        placeholder: 'Или введите номер'
                    } ).focusout( {self: this}, function ( e ) {
                        $( this ).val( '' );
                    } ).keypress( {self: this}, function ( e ) {
                        if ( e.keyCode === 13 ) {
                            let v = parseInt( $( this ).val() );
                            if ( v < 1 || v > pCnt ) {
                                alert( "Указан не верный номер страницы." );
                                $( this ).val( '' );
                                return;
                            }
                            v--;
                            $( this ).val( v );
                            e.data.self._paginatioClick.call( this, e );
                        }
                    } ) )
                    );
        }
    },
    _paginatioClick: function ( e ) { //Нажата кнопка пагинации
        let self = e.data.self;
        self.__paginatioClick.call( this, e );
        if ( self._secondStart )
            self._requstDate( self._secondStart, {page: this.tagName === 'INPUT' ? $( this ).val() : $( this ).attr( 'data-key' )} );
    },
    update: function ( onEnd, isFilter ) {
        let uri = new URI( window.location.href );
        let tmp = URI.parseQuery( uri.query() );
        let page = $.type( tmp.page ) !== 'undefined' ? tmp.page : 0;
//        if ( this._hasFilterColumn ) {
//            while ( this._tBody.children( ).length > 0 )
//                this._tBody.children( ':last-child' ).remove();
//        } else
        isFilter=isFilter?true:false;
        this._tBody.children( ).remove();
        this._tFooter.empty();
        this.__showBunnerAtRowAndGetIt();
        if ( !onEnd )
            onEnd = [ this, function () {
                    $( '.like' ).m_filter_like( 'update' );
                } ];
        if (isFilter){
            this._requstDate( this._secondStart, {}, onEnd );
        }else{
            this._requstDate( this._secondStart, {page: page}, onEnd );
        }
    },

    _rgb2hex: function ( rgb ) {

        function hex ( x ) {
            return ( "0" + parseInt( x ).toString( 16 ) ).slice( -2 );
        }
        let r = rgb.match( /^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/ );
        let h = '';
        if ( r )
            h = '#' + hex( r[1] ) + hex( r[2] ) + hex( r[3] );
        return h.toUpperCase();

    },
    _mEnter: function ( e ) {
        return;
        let self = e.data.self;//_memColor
        let first = $( self._findFirstRowByKeyVal( $( this ).attr( 'data-key' ) ) );
        let maxH = self._tBody.children( ':first-child' ).children().length - 1;
        let backColor, mColor = null;
        if ( self._rgb2hex( first.children( ':first-child' ).css( 'background-color' ) ) === self._oddColor )
            first.addClass( 'odd' );
        self._tBody.children( '[data-key=' + $( this ).attr( 'data-key' ) + ']' ).children( ':not(.last)' ).each( function () {
            if ( $( this ).index() < maxH ) {
                backColor = $( this ).css( 'background-color' );
                mColor = $( this ).css( 'color' );
                if ( backColor != "rgba(0, 0, 0, 0)" || mColor != "rgba(0, 0, 0, 0)" ) {
                    self._memColor.unshift( {td: $( this ), bcolor: backColor, color: mColor} );
                }
                $( this ).css( 'background-color', 'rgb(182,218,255)' );
            }
        } );
    },
    _mLeave: function ( e ) {
        return;
        let self = e.data.self;
        let first = $( self._findFirstRowByKeyVal( $( this ).attr( 'data-key' ) ) );
        let maxH = self._tBody.children( ':first-child' ).children().length - 1;
        if ( first.hasClass( 'odd' ) ) {
            first.removeClass( 'odd' );
            self._tBody.children( '[data-key=' + $( this ).attr( 'data-key' ) + ']' ).children( ':not(.last)' ).each( function () {
                if ( $( this ).index() < maxH )
                    $( this ).css( 'background-color', self._oddColor );
            } );
        } else {
            self._tBody.children( '[data-key=' + $( this ).attr( 'data-key' ) + ']' ).children( ':not(.last)' ).each(function(){
                //let maxwidth=$(this).css('max-width');
                //let minwidth=$(this).css('min-width');
                let width=$(this).outerWidth(true);
                $(this).removeAttr('style').css({
                        'max-width': width+'px',
                        'min-width': width+'px',
                        'width': width+'px'
                });
            });
            //.removeAttr( 'style' );
        }
//        while ( self._memColor.length ) {
//            let tmp = self._memColor.pop();
//            tmp.td.css( {
//                'background-color': tmp.bcolor,
//                'color': tmp.color
//            } );
//        }
    },
    print: function () {
        return false;
    },

    _technicalsViewClick: function ( e ) {
        let id = parseInt( $( this ).parent().parent().attr( 'data-key' ) );
        let self = e.data.self;
        let uri = new URI( self.options.viewRowUrl );

        uri.addSearch( 'technicals', 'true' );
        console.log( e );
        id = !isNaN( id ) ? id : 0;
        if ( id && !e.ctrlKey ) {
            let tmp = $( '[zakaz-technicals-num=' + id + ']' );
            if ( tmp.length ) {
                tmp.viewDialog( 'moveToTop' ).viewDialog( 'restore' );
            } else {
                $.custom.viewDialog( {
                    id: id,
                    url: uri.toString(),
                    optionName: 'zakaz-technicals-num',
                    minWidth: 1170,
                    showPrintButton: true,
                    showEditButton: true,
                    getOptionToPrint: {technicalsPrin: "true"},
                    afterGetCompliteAndPaste: function ( dialogObject ) {
                        self._technicalsViewAfterGetCompliteAndPaste( this, dialogObject );
                        console.log( self );
                        if ( $( '#is_expressZ' + id ).length ) {
                            console.log( dialogObject );
                            dialogObject.uiDialog.addClass( 'dialog-danger' );
                            if ( dialogObject.uiDialogTitlebar.children( 'span:first-child' ).text().indexOf( 'СРОЧНО' ) === -1 )
                                dialogObject.uiDialogTitlebar.children( 'span:first-child' ).text( dialogObject.uiDialogTitlebar.children( 'span:first-child' ).text() + ' - СРОЧНО!' );
                        } else {
                            dialogObject.uiDialog.removeClass( 'dialog-danger' );
                            let pos = dialogObject.uiDialogTitlebar.children( 'span:first-child' ).text().indexOf( ' - СРОЧНО!' );
                            if ( pos > -1 ) {
                                dialogObject.uiDialogTitlebar.children( 'span:first-child' ).text( dialogObject.uiDialogTitlebar.children( 'span:first-child' ).text().substr( 0, pos ) );
                            }
                        }
                        dialogObject.uiDialog.find( '[data-type="file-to-view"]' ).contextmenu( {self: self, dialogObject: dialogObject}, self._viewDialogFileRC ).click( function ( e ) {
                            if ( !$( this ).attr( 'data-download' ) ) {
                                e.preventDefault();
                            } else {
                                $( this ).removeAttr( 'data-download' );
                                let tmpA = $( '<a>' ).attr( {
                                    href: $( this ).attr( 'href' ),
                                    download: 'download',
                                } ).css( 'dispaly', 'none' ).appendTo( 'body' );
                                tmpA.get( 0 ).click();
                                tmpA.remove();
                            }
                        } );
                    }
                } );
            }
        } else if ( id && e.ctrlKey ) {
            e.preventDefault();
            uri.addSearch( 'id', id );
            console.log( uri.toString() );
            window.open( uri.toString(), '_blank' );
        } else {
            console.warn( '_technicalsViewClick:Номер заказа не определён' );
        }

    },

};


( function ( $ ) {
    $.widget( "custom.resizebleTable", $.Widget, $.extend( {}, resizeble_table ) );
}( jQuery ) );
