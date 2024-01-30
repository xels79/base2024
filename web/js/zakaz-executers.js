/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var zakaz_executers = {
    ddButton: null,
    _exexuterCreateDDGroup: function ( name, value, requestUrl, otherParam, onReady ) {
        let rVal = $( '<div>' ).addClass( 'activeDD invisible' ), inp = $( '<input>' ).attr( {tabindex: this.options.tabIndex++} ), self = this;
        rVal.append( $( '<input>' ).attr( {
            name: name,
            type: 'hidden',
            value: value
        } ) );
        rVal.append( inp );
        $.fn.dropInfo( 'Запрос списка для "' + name + '" value:' + value, 'info' );
        let tm = performance.now();

        this.busy++;
        inp.activeDD( {
            requestUrl: requestUrl,
            otherParam: otherParam,
            appendTo: this.element.parent(),
            onClickOrChange: function () {
                $( this ).prev().val( $( this ).attr( 'data-key' ) );
                if ( $( this ).val().length > 10 ) {
                    $( this ).attr( 'title', $( this ).val() );
                } else {
                    $( this ).removeAttr( 'title' );
                }
                console.log( '_exexuterCreateDDGroup', this );
                if ( $( this ).val() === 'Уф-лак' && self._productCategory === 3 ) {
                    let id = $( this ).parent().parent().prev().children( 'input' ).val();
                    let thisEl = this;
                    self._executersAddRow( {
                        pod_id: id,
                        onReady: function ( dd ) {
                            let el = $( thisEl ).parent().parent().parent().prev().children( ':nth-child(3)' ).children( 'div' ).children( 'input:last-child' );
                            tmp = $( this ).activeDD( 'findByText', 'Матрица' );
                            if ( tmp ) {
                                $( this ).prev().val( tmp.value );
                                $( this ).attr( 'data-key', tmp.value );
                                $( this ).val( tmp.label );
                                dd.options.oldVal = tmp.value;
                                if ( $( this ).val().length > 10 ) {
                                    $( this ).attr( 'title', $( this ).val() );
                                } else {
                                    $( this ).removeAttr( 'title' );
                                }
                            }

                        }
                    }, $( this ).parent().parent().parent().parent().children().length ).appendTo( $( this ).parent().parent().parent().parent() );
                }
            },
            strictly: true,
            bigLoaderPicUrl: this.options.bigLoaderPicUrl,
            onReady: function ( dd ) {
                let tmp = $( this ).activeDD( 'findByValue', value );
                $.fn.dropInfo( 'Список для "' + name + '" получен время запроса: ' + ( performance.now() - tm ).toFixed( 2 ) + 'ms', 'success' );
                if ( tmp ) {
                    inp.prev().val( tmp.value );
                    inp.attr( 'data-key', tmp.value );
                    inp.val( tmp.label );
                    dd.options.oldVal = tmp.value;
                    if ( inp.val().length > 10 ) {
                        inp.attr( 'title', $( this ).val() );
                    } else {
                        inp.removeAttr( 'title' );
                    }
                    $.fn.dropInfo( 'Значение по умолчанию для "' + name + '"=' + tmp.label + ' (' + tmp.value + ')' );
                }
                self._busyDown();
                if ( $.isFunction( onReady ) ) {
                    onReady.call( this, dd );
                }
            }
        } );
        return rVal;
    },
    _executersChangeStatus: function ( e ) {
        let self = e.data.self, tsEl = e.data.el.parent().parent(), tbody = tsEl.parent();
        e.data.el.popoverN( 'hide' );
        let frst = tbody.children( 'tr:nth-child(2)' );
        console.log( e.data.el, tsEl, frst );
        //return;
        tsEl.insertBefore( frst );
        console.log( frst.children( ':first-child' ).children( 'p' ) );
        frst.children( ':first-child' ).children( 'p' ).popoverN( {
            content: $( '<a>' )
                    .text( 'Сделать основным' )
                    .click( {self: self, el: frst.children( ':first-child' ).children( 'p' )}, self._executersChangeStatus ),
        } ).text( 'Доп.' ).popoverN( 'enable' ).prev().val( 0 );

        tbody.children( 'tr:nth-child(2)' ).children( ':first-child' ).children( 'p' )
                .text( 'Основ.' )
                .removeAttr( 'data-original-title' )
                .removeAttr( 'title' )
                .popoverN( 'disable' ).prev().val( 1 );
    },
    _executersAddRow: function ( row, count ) {
        let nRow = $.extend( {}, {
            id: null,
            pod_id: 0,
            manager_id: 0,
            coast: 0,
            payment: 0,
            zakaz_id: this.options.form.fields.id ? this.options.form.fields.id : 0,
            isMain: $.type( row.isMain ) === 'undefined' ? ( count - 1 ? 0 : 1 ) : row.isMain,
            workType: 0
        }, row );
//        console.log(row,nRow);
        let rVal = $( '<tr>' ).attr( 'data-key', count - 1 ), self = this;
        //$().mouseenter()
        let spanTypeInfo = $( '<p>' ).text( nRow.isMain == 1 ? 'Основ.' : 'Доп.' );
        if ( nRow.isMain != 1 )
            spanTypeInfo.popoverN( {
                content: $( '<a>' )
                        .text( 'Сделать основным' )
                        .click( {self: this, el: spanTypeInfo}, this._executersChangeStatus ),
            } );
        let td1 = $( '<td>' )
                .append( $( '<input>' ).attr( {
                    type: 'hidden',
                    name: this.options.form.name + '[podryad][' + ( count - 1 ) + '][isMain]',
                    value: nRow.isMain
                } ) )
                .append( spanTypeInfo );
        td1.append( $( '<input>' ).attr( {type: 'hidden', value: nRow.id, name: this.options.form.name + '[podryad][' + ( count - 1 ) + '][id]'} ) )
        rVal.append( td1 );
        let td2 = $( '<td>' )
                .append( $( '<input>' ).attr( {
                    type: 'hidden',
                    name: this.options.form.name + '[podryad][' + ( count - 1 ) + '][pod_id]',
                    value: nRow.pod_id
                } ) )
                .append( $( '<img>' ).attr( {src: this.options.bigLoaderPicUrl} ) );
        rVal.append( td2 );
        let td4 = $( '<td>' ).append( this._exexuterCreateDDGroup(
                this.options.form.name + '[podryad][' + ( count - 1 ) + '][workType]',
                nRow.workType,
                this.options.smallTableListUrl,
                {tableName: 'worktypes'},
                row.onReady
                ) );
        rVal.append( td4 );
        console.log( 're_print', this.options.form.fields.re_print );
        let td5 = $( '<td>' ).append( $( '<div>' ).addClass().append( $( '<input>' ).attr( {
            value: this.options.form.fields.re_print == 0 ? Math.round( parseFloat( nRow.coast ), 2 ) : 0,
            name: this.options.form.name + '[podryad][' + ( count - 1 ) + '][coast]',
            tabindex: this.options.form.fields.re_print != 0 ? -1 : this.options.tabIndex++,
            readonly: this.options.form.fields.re_print != 0
        } )
                .change( {self: this}, this._recalculateAll ).onlyNumeric() ).append( $( '<span>' ).addClass( 'add-on' ).text( 'руб.' ) ) );
        rVal.append( td5 );
        let td6 = $( '<td>' ).append( $( '<div>' ).addClass().append( $( '<input>' ).attr( {
            value: Math.round( parseFloat( nRow.payment ), 2 ),
            name: this.options.form.name + '[podryad][' + ( count - 1 ) + '][payment]',
            tabindex: this.options.tabIndex++
        } ).change( {self: this}, this._recalculateAll ).onlyNumeric() ).append( $( '<span>' ).addClass( 'add-on' ).text( 'руб.' ) ) );
        rVal.append( td6 );
        let td7 = $( '<td>' ).append( $( '<div>' ).addClass().append( $( '<p>' )
                .text( nRow.coast - nRow.payment ) )
                .append( $( '<span>' ).addClass( 'add-on' ).text( 'руб.' ) ) );
        rVal.append( td7 );
        let td8 = $( '<td>' ).append( $( '<div>' ).addClass()
                .append( $( '<input>' ).attr( {
                    value: nRow.date_info ? nRow.date_info : '',
                    name: this.options.form.name + '[podryad][' + ( count - 1 ) + '][date_info]',
                    tabindex: this.options.tabIndex++
                } ).css( 'min-width', 81 ).datepicker() ) );
//                .text('0'))
//                .append($('<span>').addClass('add-on').text('руб2.')));;
        rVal.append( td8 );
        if ( this.options.getpodinfo ) {
            let tm = performance.now();
            $.fn.dropInfo( 'Запрос параметров фирмы с id=' + nRow.pod_id, 'info' );
            self.busy++;
            $.post( this.options.getpodinfo, {id: nRow.pod_id} ).done( function ( answ ) {
                $.fn.dropInfo( 'Ответ получен для фирмы id=' + nRow.pod_id + ' время запроса: ' + ( performance.now() - tm ).toFixed( 2 ) + 'ms', 'success' );
                if ( answ.status === 'error' ) {
                    $.fn.dropInfo( 'Ошибка: ' + answ.errorText, 'danger' );
                } else {
//                    console.log(answ);
                    td2.children( 'img' ).remove();
                    td2.append( $( '<span>' ).text( answ.value.mainName ) );
                }
                self._busyDown();
            } );
        }
        rVal.append( $( '<td>' ).append( $( '<a>' )
                .addClass( 'btn' )
                .append( $( '<button>' ).attr( {class: 'b-button b-minus b-small'} ).text( 'X' ) )
                .click( {self: this}, this._executersRemoveRow ) ) )
        return rVal;
    },
    _executersconfigureDD: function () {
        if ( this.ddButton )
            if ( this.ddButton.prev().children( 'tbody' ).children().length > 4 ) {
                this.ddButton.addClass( 'dropup' );
            } else {
                this.ddButton.removeClass( 'dropup' );
            }
    },
    _executersRemoveRow: function ( e ) {
        let tr = $( this ).parent().parent(), self = e.data.self;
        let id = tr.children( ':first-child' ).children( 'input[name*="id"]' ).val();
        console.log( tr.index() );
        if ( id.length && id != '0' ) {
            id = parseInt( id );
        } else {
            id = 0;
        }
        e.preventDefault();
        m_alert( 'Внимание', 'Удалить строку?', function () {
            if ( tr.index() === 1 ) {
                let nTd = tr.next().children( ':first-child' );
                nTd.children( ':first-child' )
                        .val( 1 )
                        .next().text( 'Основ.' );
            }
            tr.remove();
            if ( id )
                self._addToRemove( 'podryad', id );
            self.__recalculateExecutersOther();
            self._executersconfigureDD();
        } );
    },
    _executersDrawLoaded: function () {
        let self = this;
        let tb = $( '#executers_table' ).children( 'tbody' );
        $.each( this.options.form.fields.podryad, function () {
            tb.append( self._executersAddRow( this, tb.children().length ) );
        } );
        this.__recalculateExecutersOther();
        this._executersconfigureDD();
    },
    _executersClick: function ( e ) {
        console.log( 'click', $( this ).children( 'a' ).attr( 'value' ) );
        e.data.table.append( e.data.self._executersAddRow( {
            pod_id: $( this ).children( 'a' ).attr( 'value' )
        }, e.data.table.children( 'tbody' ).children().length ) );
        e.data.self.__recalculateExecutersOther();
        e.data.self._executersconfigureDD();
    },
    _exexTab: null,
    _executers: function ( tab ) {
        this._exexTab = tab;
        let prod_info = $( '<input>' ).addClass( 'dialog-form-control' ).width( 220 ).attr( {disabled: true, id: 'zakaz_executers_prod_info', readonly: 'readonly'} ).val( '' );
        let copies_info = $( '<input>' ).addClass( 'dialog-form-control' ).width( 100 ).attr( {disabled: true, id: 'zakaz_executers_copies_info', readonly: 'readonly'} ).val( $( '#Zakaz-number_of_copies' ).val() );
        let copies_info2 = $( '<input>' ).addClass( 'dialog-form-control' ).width( 100 ).attr( {disabled: true, id: 'zakaz_executers_copies_info1', readonly: 'readonly'} ).val( $( '#Zakaz-number_of_copies1' ).val() );
        tab.append( $( '<div>' )
                .css( {
                    position: 'relative'
                } )
                .append( $( '<a>' ).addClass( 'site_href' ).attr( {
                    href: 'http://asterionspb.ru/',
                    target: '_blank'
                } ).append( $( '<img>' ).attr( {
                    src: '/pic/button_main_page/J_cena.png',
                    height: 30
                } ) ) )
                .append( $( '<div>' ) )
                .append( $( '<div>' )
                        .css( 'width', '85%' )
                        .append( $( '<div>' )
                                .addClass( 'dialog-control-group-inline' )
                                .append( $( '<label>' ).text( 'Продукция:' ) )
                                .append( prod_info ) )
                        .append( $( '<div>' )
                                .addClass( 'dialog-control-group-inline' )
                                .append( $( '<label>' ).text( 'Тираж:' ) )
                                .append( copies_info )
                                .append( $( '<span>' ).addClass( 'add-on' ).text( 'шт.' ) )
                                .append( copies_info2 )
                                .append( $( '<span>' ).addClass( 'add-on' ).text( 'шт.' ) ) )
                        )
                .append( $( '<div>' ) )
                //.append($('<div>').css('width','50'))
                );
        let mainT = $( '<tbody>' );
        $( '<table>' ).addClass( 'table' ).append( mainT ).appendTo( tab );
        let tbd = $( '<tbody>' ), tbl = $( '<table>' ).addClass( 'table table-hover podryad-info' ).attr( {id: 'executers_table'} ).append( tbd );
        tbd.append( $( '<tr>' )
                .append( $( '<th>' ) )
                .append( $( '<th>' ).text( 'Фирма исполнит.' ) )
//                .append($('<th>').text('Ответственный'))
                .append( $( '<th>' ).text( 'Вид работы' ) )
                .append( $( '<th>' ).text( 'Стоимость' ) )
                .append( $( '<th>' ).text( 'Выплаты' ) )
                .append( $( '<th>' ).text( 'Прибыль' ) )
                .append( $( '<th>' ).text( 'Дата' ) )
                .append( $( '<th>' ) )
                );
//        tab.append(tbl);
        let a = $( '<a>' )
                .attr( {
                    href: '#',
                    'data-toggle': 'dropdown',
                    class: 'dropdown-toggle',
                    'aria-haspopup': true,
                    'aria-expanded': false,
                    id: 'executers_select'
                } )
                .append( $( '<button>' ).addClass( 'b-button b-plus b-small' ).text( '+' ) );
        let ul = $.fn.createUlForDropDownFromActiveRecDate( this.executersList, {valueName: 'firm_id', labelName: 'mainName'} )
                .attr( {'aria-labelledby': 'executers_select'} );
        ul.children().click( {self: this, table: tbl}, this._executersClick );
        this.ddButton = $( '<div>' ).addClass( 'dropdown' )
                .append( a )
                .append( ul );
//        tab.append(this.ddButton);
        mainT.append( $( '<tr>' ).append( $( '<td>' ).attr( 'colspan', 11 )
                .append( tbl )
                .append( this.ddButton ) ) );
        ////////Низ//////////
        mainT
                .append( $( '<tr>' ).addClass( 'bord first-b' )
                        .append( $( '<th>' ).attr( 'colspan', 2 ) )
                        .append( $( '<th>' ).text( 'Сумма' ) )
                        .append( $( '<th>' ).text( 'Выплаты' ) )
                        .append( $( '<th>' ).text( 'Прибыль' ) )
                        .append( $( '<td>' ).addClass( 'mid' ) )
                        .append( $( '<th>' ).attr( 'colspan', 2 ) )
                        .append( $( '<th>' ).text( 'Сумма' ) )
                        .append( $( '<th>' ).text( 'Выплаты' ) )
                        .append( $( '<th>' ).text( 'Прибыль' ) )
                        )
                .append( this.__renderRow( 'exec_speed', 'Срочность:', 'exec_delivery', 'Доставка:' ) )
                .append( this.__renderRow( 'exec_markup', 'Наценка:', 'exec_transport', 'Транспорт:' ) )
                .append( this.__renderRow( 'exec_bonus', 'Бонус:', 'exec_transport2' ) )
                .append( $( '<tr>' ).addClass( 'bord last-b' )
                        .append( $( '<th>' ).text( 'Итого:' ) )
                        .append( $( '<th>' ).text( 'Стоимость:' ) )
                        .append( $( '<td>' ).append( $( '<p>' ).text( 0 ).attr( {id: 'exec_bottom_summ'} ) ) )
                        .append( $( '<th>' ).text( 'Выплаты:' ) )
                        .append( $( '<td>' ).append( $( '<p>' ).text( 0 ).attr( {id: 'exec_bottom_payments'} ) ) )
                        .append( $( '<td>' ).addClass( 'mid' ) )
                        .append( $( '<th>' ).text( 'Прибыль:' ) )
                        .append( $( '<td>' ).append( $( '<p>' ).text( 0 ).attr( {id: 'exec_bottom_profit'} ) ).attr( 'colspan', 2 ) )
                        .append( $( '<th>' ).text( 'Сверхприбыль:' ) )
                        .append( $( '<td>' ).append( $( '<p>' ).text( 0 ).attr( {id: 'exec_bottom_super_profit'} ) ) )
                        );
        //new NEWFUNCTION.FixTable(tbl.get(0));
    },
    __recalculateExecutersOther: function ( callRecalculateAll ) {
        let summ = 0.0;
        let payments = 0.0;
        callRecalculateAll = callRecalculateAll === false ? false : true;
        $.each( this._exexTab.find( '[name*="summ"],[name*="coast"]' ), function () {
            let tmp = parseFloat( $( this ).val() );
            tmp = !isNaN( tmp ) ? tmp : 0;
            summ += tmp;
        } );
        $.each( this._exexTab.find( '[name*="payment"]' ), function () {
            let tmp = parseFloat( $( this ).val() );
            tmp = !isNaN( tmp ) ? tmp : 0;
            payments += tmp;
        } );
//        console.log(this._exexTab.find('[name*="summ"],[name*="coast"]'),summ);
        $( '#exec_bottom_summ' ).text( Math.round( summ, 2 ) );
        $( '#exec_bottom_payments' ).text( Math.round( payments, 2 ) );
        $( '#exec_bottom_profit' ).text( Math.round( summ - payments, 2 ) );
        if ( callRecalculateAll )
            this.__recalculateAll( false );
    },
    __renderGroup: function ( tr, name, comment, disabled ) {
        comment = comment ? comment : false;
        if ( comment )
            tr.append( this.__renderButtt( name, comment ) );
        else
            tr.append( $( '<td>' ).attr( 'colspan', 2 ) );
        tr.append( $( '<td>' ).append( this.__renderOneInput( name + '_summ', disabled || this.options.form.fields.re_print != 0 ) ) );
        tr.append( $( '<td>' ).append( this.__renderOneInput( name + '_payment', disabled ) ) );
        tr.append( $( '<td>' ).append( $( '<p>' ).attr( {
            id: 'Zakaz-' + name + '_profit'
        } ).text( disabled ? 0 : ( Math.round( this.options.form.fields[name + '_summ'], 2 ) - Math.round( this.options.form.fields[name + '_payment'], 2 ) ) ).addClass( disabled ? 'off' : '' ) ) );
    },
    __renderRow: function ( name1, comment1, name2, comment2 ) {
        let tr = $( '<tr>' ).addClass( 'bord' ).attr( 'role', 'calculate' );
        this.__renderGroup( tr, name1, comment1, !this.options.form.fields[name1] );
        tr.append( $( '<td>' ).addClass( 'mid' ) );
        this.__renderGroup( tr, name2, comment2, typeof this.options.form.fields[name2] === 'undefined' ? !this.options.form.fields['exec_transport'] : !this.options.form.fields[name2] );
        return tr;
    },
    __renderOneInput: function ( name, disabled ) {
        let self = this;
        let inp = this._createInput( name, {type: 'text',tabindex:this.options.tabIndex++} ).val( disabled ? 0 : Math.round( this.options.form.fields[name], 2 ) );
        if ( disabled ) {
            inp.addClass( 'off' ).attr( 'readonly', 'readonly' );
        }
        inp.focusout( function () {
            let spend, summ, isSumm = $( this ).attr( 'name' ).indexOf( 'summ' ) > -1;
            console.log( $( this ).attr( 'name' ).indexOf( 'summ' ) > 0 ? 'summ' : 'payment' );
            if ( isSumm ) {
                summ = parseFloat( $( this ).val() );
                spend = parseFloat( $( this ).parent().next().children( 'input' ).val() );
            } else {
                spend = parseFloat( $( this ).val() );
                summ = parseFloat( $( this ).parent().prev().children( 'input' ).val() );
            }
            summ = !isNaN( summ ) ? summ : 0;
            spend = !isNaN( spend ) ? spend : 0;
            if ( isSumm ) {
                $( this ).parent().next().next().children( 'p' ).text( Math.round( summ - spend, 2 ) );
            } else {
                $( this ).parent().next().children( 'p' ).text( Math.round( summ - spend, 2 ) );
            }
            self.__recalculateExecutersOther();
        } );
        return inp;
    },
    __renderButtt: function ( name, txt, colsp ) {
        let self = this;
        colsp = colsp ? colsp : 2;
        return $( '<td>' ).attr( 'colspan', colsp )
                .append( $( '<a>' ).addClass( 'btn ' + ( this.options.form.fields[name] ? 'btn-success' : 'btn-default' ) ).text( txt ).click( function () {
                    let inp = $( this ).next();
                    let par = $( this ).parent();
                    if ( inp.val() == '1' ) {
                        inp.val( 0 );
                        $( this ).removeClass( 'btn-success' ).addClass( 'btn-default' );
                        for ( let i = 0; i < 3; i++ ) {
                            par = par.next();
                            par.children( 'input' ).addClass( 'off' ).val( 0 );
                            par.children( 'p' ).addClass( 'off' ).text( 0 );
                        }
                        if ( inp.attr( 'name' ) === 'Zakaz[exec_transport]' ) {
                            par = $( this ).parent().parent().next().children( ':nth-child(' + ( $( this ).parent().index() + 1 ) + ')' );
                            for ( let i = 0; i < 3; i++ ) {
                                par = par.next();
                                par.children( 'input' ).addClass( 'off' ).val( 0 );
                                par.children( 'p' ).addClass( 'off' ).text( 0 );
                            }
                        }
                        self.__recalculateExecutersOther();
                    } else {
                        inp.val( 1 );
                        $( this ).removeClass( 'btn-default' ).addClass( 'btn-success' );
                        for ( let i = 0; i < 3; i++ ) {
                            par = par.next();
                            if ( self.options.form.fields.re_print == 0 || i != 0 )
                                par.children( 'input,p' ).removeClass( 'off' ).removeAttr( 'readonly' );

                        }
                        if ( inp.attr( 'name' ) === 'Zakaz[exec_transport]' ) {
                            par = $( this ).parent().parent().next().children( ':nth-child(' + ( $( this ).parent().index() + 1 ) + ')' );
                            for ( let i = 0; i < 3; i++ ) {
                                par = par.next();
                                if ( self.options.form.fields.re_print == 0 || i != 0 )
                                    par.children( 'input,p' ).removeClass( 'off' ).removeAttr( 'readonly' );
                            }
                        }
                    }
                } ) )
                .append( this._createInput( name, {type: 'hidden'} ).val( this.options.form.fields[name] ) )
    }
};
