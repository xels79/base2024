/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var _ZMDEBUG=false;

var zakaz_material = {
    zmDialog: null,
    zmCont: null,
    _zakaz_material_init: function () {
        if ( !this.options.isDisainer ) {
            let dOpt = {};
            if ( $.type( $( '#Zakaz-production_id' ).attr( 'data-category' ) ) !== 'undefined' ) {
                dOpt.category = $( '#Zakaz-production_id' ).attr( 'data-category' );
            }
            this.zmDialog.tablesControllerMaterialTypes( $.extend( {}, this.options.materials, dOpt ) );
        }
    },
    _removeLastMaterial: function ( self ) {
        //let self = e.data.self;
        let el = $( this );
        let ind = el.parent().index() - 1;
        if ( $.type( self.options.form.fields.materials[ind] ) !== 'undefined' ) {
            if ( _ZMDEBUG ) console.log( self.options.form.fields.materials.pop() );
        }
        if ( _ZMDEBUG ) console.log( 'remove', el.parent().index(), self.options.form.fields );
        if ( $( '[name="Zakaz[materials][' + el.parent().prev().index() + '][id]"]' ).length ) {
            if ( _ZMDEBUG ) console.log( 'To erase id(' + '[name="Zakaz[materials][' + el.parent().prev().index() + '][id]"]' + '):', $( '[name="Zakaz[materials][' + el.parent().prev().index() + '][id]"]' ).val() );
            self._addToRemove( 'materials', $( '[name="Zakaz[materials][' + el.parent().prev().index() + '][id]"]' ).val() );
        }
        el.parent().prev().remove();
        if ( el.parent().parent().children().length < 2 )
            el.addClass( 'disabled' );
        el.prev().removeClass( 'disabled' );
        el.parent().removeAttr( 'style' );
        self._checkMaterialCount();
        self.__correctControlButtonPos();
    },
    _zakaz_material: function ( tab ) {
        let self = this;
        let isFirst = true;
        let prod_info = $( '<input>' ).addClass( 'dialog-form-control' ).width( 220 ).attr( {disabled: true, id: 'zakaz_material_prod_info', readonly: 'readonly'} ).val( $( '#Zakaz-production_id' ).next().children( 'input:first-child' ).val() );
        let copies_info = $( '<input>' ).addClass( 'dialog-form-control' ).width( 100 ).attr( {disabled: true, id: 'zakaz_material_copies_info', readonly: 'readonly'} ).val( $( '#Zakaz-number_of_copies' ).val() );
        let copies_info2 = $( '<input>' ).addClass( 'dialog-form-control' ).width( 100 ).attr( {disabled: true, id: 'zakaz_material_copies_info1', readonly: 'readonly'} ).val( $( '#Zakaz-number_of_copies1' ).val() );
        let comercInput = $( '<input>' )
                .addClass( 'dialog-form-control' )
                .width( 90 )
                .focusout( {self: this}, function ( e ) {
                    e.data.self._zakaz_material_recalulate();
                } )
                .attr( {
                    id: 'zakaz_material_coast_comerc',
                    tabindex: this.options.tabIndex++,
                    name: 'Zakaz[material_coast_comerc]',
                    title: 'Стоимость продажи материала'
                } ).val( self.options.form.fields['material_coast_comerc'] ? self.options.form.fields['material_coast_comerc'] : '0' ).onlyNumeric( {allowPoint: true} ).tooltip();
        let zakupka = $( '<input>' ).addClass( 'dialog-form-control' ).width( 90 ).attr( {readonly: 'readonly', tabindex: this.options.tabIndex++, id: 'zakaz_material_coast', title: 'Стоимость закуаки материала'} ).val( 0 ).tooltip();
        let pribil = $( '<input>' ).addClass( 'dialog-form-control' ).width( 90 ).attr( {readonly: 'readonly', tabindex: this.options.tabIndex++, id: 'zakaz_material_profit', title: 'Разность между закупкой и продажей. Не сохраняется в базе, а вычисляется!!!'} ).val( 0 ).tooltip();
        if ( this.options.form.fields.re_print )
            comercInput.val( 0 ).attr( 'readonly', 'readonly' );
        if ( _ZMDEBUG ) console.log( this );
        this.zmDialog = $( '#zakaz_material_select_mat' );
        if ( !this.zmDialog.length ) {
            this.zmDialog = $( '<div>' ).attr( 'id', 'zakaz_material_select_mat' );
            $( 'body' ).append( this.zmDialog );
        }
        tab.append( $( '<div>' )
                .append( $( '<div>' ) )
                .append( $( '<div>' )
                        .css( 'width', '80%' )
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
        let a_add = $( '<a>' ).attr( 'id', 'zakaz-select-material' ).addClass( 'b-button b-plus-left b-big btn' ).append( $( '<span>' ).addClass( 'glyphicon glyphicon-plus' ) )
                .click( {self: this}, function ( e ) {
                    let category;
                    if ( $.type( $( '#Zakaz-production_id' ).attr( 'data-category' ) ) !== 'undefined' ) {
                        category = $( '#Zakaz-production_id' ).attr( 'data-category' );
                    }
                    if ( $( '#Zakaz-production_id' ).val( ).length && $( '#Zakaz-production_id' ).val( ) != '0' ) {
                        e.data.self.zmDialog.tablesControllerMaterialTypes( 'open', category );
                    } else {
                        m_alert( 'Ошибка при заполнение', 'Не указан вид продукции', {
                            label: 'Закрыть',
                            click: function ( ) {
                                $( '[href="#tab-pane-zakaz-page0"]' ).trigger( 'click' );
                                $( '#Zakaz-production_id' ).next( ).children( 'input:first-child' ).trigger( 'focus' );
                            }
                        }, false );
                    }

                } );
        let a_rem = $( '<a>' ).addClass( 'btn b-button b-minus-right b-big disabled' ).append( $( '<span>' ).addClass( 'glyphicon glyphicon-remove' ) )
                .attr( 'id', 'Remove-material-Button' )
                .click( {self: this}, function ( e ) {
                    //console.log($());
                    let self = e.data.self;
                    if ( $( this ).parent().parent().children().length > 1 ) {
                        let el = this;
                        if ( $( this ).attr( 'data-notask' ) ) {
                            $( this ).removeAttr( 'data-notask' );
                            self._removeLastMaterial.call( el, self );
                        } else {
                            m_alert( 'Внимание', 'Удалить материал?', function () {
                                self._removeLastMaterial.call( el, self );
                            }, true );
                        }
                    }
                } );
        this.zmCont = $( '<div>' ).append( $( '<span>' ).attr( 'role', 'group' ).addClass( 'btn-group btn-group-justified' ).append( a_add ).append( a_rem ) );

        //!!!!!!!!!!!
        //let tmp_block_id = ( !$( '#Zakaz-production_id' ).attr( 'data-category' ) || $( '#Zakaz-production_id' ).attr( 'data-category' ) == '0' ) ? ( this.options.form.fields.blocks_type ? this.options.form.fields.blocks_type : 0 ) : ( parseInt( $( '#Zakaz-production_id' ).attr( 'data-category' ) ) + 10 );
        //let tmp_block = ( this._productCategory > 1 && this._productCategory != 3 && this._productCategory != 4 ) ? $( '<div>' ).append( this._block_table( tmp_block_id ) ) : null;
//        let tmp_block=this._changeBlockModeAll(this._productCategory,this.zmCont.children().length-1);
        tab.append( $( '<div>' ).addClass( 'mat-middle-zone' ).append( this.zmCont ) );//.append( tmp_block ) );

        this.options.materials.but = '#zakaz-select-material';
        this.options.materials.onOkClick = [ {self: this}, this._addClick ];
        this.options.materials.beforeClose = null;
        this.options.materials.materialForm = {
            formName: 'Tablestypes',
            requestUrl: "non",
            removeUrl: "non",
            fields: {
                id: [ 'prim' ],
                name: [ 'strind', 'Название' ],
                structlist: [ function ( el, defVal ) {
                    }, 'Структура' ],
                struct: [ 'string', 'Структура', null, null, 'hide' ]
            }
        };
        /*
        if ( $( '#Zakaz-num_of_products_in_block' ).length && $( '#Zakaz-num_of_products_in_block' ).val() ) {
            $( '#Zakaz-num_of_products_in_block' ).trigger( 'change' );
        }
        if ( $( '#Zakaz-num_of_products_in_block1' ).length && $( '#Zakaz-num_of_products_in_block1' ).val() ) {
            $( '#Zakaz-num_of_products_in_block1' ).trigger( 'change' );
        }
        */
        tab.append( $( '<div>' )
                .addClass( 'info-string' )
                .append( $( '<div>' )
                        .addClass( 'dialog-control-group-inline' )
                        .append( $( '<label>' ).text( 'Итого:' ) ) )
                .append( $( '<div>' )
                        .addClass( 'dialog-control-group-inline' )
                        .append( $( '<label>' ).text( 'Закупка:' ) )
                        .append( zakupka ) )
                .append( $( '<div>' )
                        .addClass( 'dialog-control-group-inline' )
                        .append( $( '<label>' ).text( 'Продажа:' ) )
                        .append( comercInput ) )
                .append( $( '<div>' )
                        .addClass( 'dialog-control-group-inline' )
                        .append( $( '<div>' ).radioButton( {
                            id: 'profitTypeRadio',
                            wrapClass: 'm-radio m-radio2',
                            values: {
                                0: 'сам',
                                1: '+10%',
                                2: '+20%',
                                3: '+50%'
                            },
                            name: 'Zakaz[profit_type]',
                            inputId: 'Zakaz-profit_type',
                            defaultValue: this.options.form.fields.profit_type ? this.options.form.fields.profit_type : 0,
                            disabledItems: [ ],
                            onChange: function ( val ) {
                                if ( _ZMDEBUG ) console.log( val );
                                if ( val == 0 ) {
                                    if ( !self.options.form.fields.re_print )
                                        comercInput.removeAttr( 'readonly' );
                                } else {
                                    comercInput.attr( 'readonly', 'readonly' );
                                    if ( val == 1 ) {
                                        let tmp = parseFloat( zakupka.val() ) + parseFloat( zakupka.val() ) * 0.1;
                                        comercInput.val( tmp );
                                        pribil.val( parseFloat( tmp - parseFloat( zakupka.val() ) ) );
                                    } else if ( val == 2 ) {
                                        let tmp = parseFloat( zakupka.val() ) + parseFloat( zakupka.val() ) * 0.2;
                                        comercInput.val( tmp );
                                        pribil.val( tmp - parseFloat( zakupka.val() ) );
                                    } else {
                                        let tmp = parseFloat( zakupka.val() ) + parseFloat( zakupka.val() ) * 0.5;
                                        comercInput.val( tmp );
                                        pribil.val( tmp - parseFloat( zakupka.val() ) );
                                    }
                                }
                                if ( !isFirst )
                                    self._zakaz_material_recalulate.call( self );
                                //else

                            }
                        } ) ) )
                .append( $( '<div>' )
                        .addClass( 'dialog-control-group-inline' )
                        .append( $( '<label>' ).text( 'Прибыль:' ) )
                        .append( pribil ) )
                .append( this._createInput( 'material_block_format', {type: 'hidden'} ).val( this.options.form.fields.material_block_format ) )
                .append( this._createInput( 'material_block_format1', {type: 'hidden'} ).val( this.options.form.fields.material_block_format1 ) )
                );

        isFirst = false;
//        $('#Zakaz-num_of_products_in_block').change(function(){
//        });
        this.__correctControlButtonPos();
    },
    _createBlockGroup: function ( num ) {
        let self = this;
        let inpG = $( '<div>' );
        let key = num ? '1' : '';
        if ( _ZMDEBUG ) console.log( '_createBlockGroup', this._productCategory2 );
        let values = this._productCategory2;
        let valKeys = Object.keys( this._productCategory2 );
        if ( _ZMDEBUG ) console.log( '_createBlockGroup', this.options.form.fields['blocks_type' + key] ? this.options.form.fields['blocks_type' + key] : ( valKeys.length ? valKeys[0] : 0 ) );
        if ( valKeys ) {
            inpG.radioButton( {
                id: 'blockType' + num,
                wrapClass: 'mradio-input',
                elementsInLine: 3,
                itemTagName: 'div',
                values: values,
                name: 'Zakaz[blocks_type' + key + ']',
                inputId: 'block_type_select' + key,
                disabledItems: [ ],
                defaultValue: ( this.options.form.fields['blocks_type' + key] ? this.options.form.fields['blocks_type' + key] : ( valKeys.length ? valKeys[0] : 0 ) ),
                onClick: function ( val ) {
                    if ( _ZMDEBUG ) console.log( 'onClick' );
                    self._block_table( val, false, num, true );
                }
            } );
        }
        return inpG;
    },
    __proceedStringToSizeArray: function ( val, mode ) {
        if ( $.type( val ) === 'string' ) {
            let tmp = mode ? [ ] : '';
            let pos = 0, i = 0;
            for ( ; i < val.length; i++ ) {
                if ( val.charCodeAt( i ) < 48 || val.charCodeAt( i ) > 57 ) {
                    if ( mode ) {
                        let t1 = parseInt( val.substring( pos, i ) );
                        tmp[tmp.length] = !isNaN( t1 ) ? t1 : 0;
                        pos = i + 1;
                    } else {
                        if ( tmp !== '' )
                            tmp += '*';
                        tmp += val.substring( pos, i );
                        pos = i + 1;
                    }
                }
            }
            if ( pos != i ) {
                if ( mode ) {
                    let t1 = parseInt( val.substring( pos, i ) );
                    tmp[tmp.length] = !isNaN( t1 ) ? t1 : 0;
                } else {
                    if ( tmp !== '' )
                        tmp += '*';
                    tmp += val.substring( pos, i );
                }
            }
            if ( _ZMDEBUG ) console.log( tmp );
            return tmp;
        } else {
            return mode ? [ ] : '';
        }
    },
    __blockRecalMainParam: function ( dopId, showMessage ) {
        let m_count = $( '.mat-middle-zone' ).children( 'div:first-child' ).children().length - 1;
        let self = this;
        if ( !this._totalCount() ) {
            if ( showMessage ) {
                m_alert( 'Ошибка при заполнение', 'Не указан тираж', true, false, function () {
                    $( '[href="#tab-pane-zakaz-page0"]' ).trigger( 'click' );
                    $( '#Zakaz-number_of_copies' ).trigger( 'focus' );
                } );
            } else {
                //$.fn.dropInfo('Ошибка при заполнение - "Не указан тираж"','danger',5000);
            }
        } else if ( m_count > 1 && !this._totalCount( '1' ) ) {
            if ( showMessage ) {
                m_alert( 'Ошибка', 'Не указан тираж2', true, false, function () {
                    $( '[href="#tab-pane-zakaz-page0"' ).trigger( 'click' );
                    $( '#Zakaz-number_of_copies1' ).focus();
                } );
            } else {
                //$.fn.dropInfo('Ошибка при заполнение - "Не указан тираж2"','danger',5000);
            }
        } else if ( !parseFloat( $( '#Zakaz-num_of_products_in_block' + dopId ).val() ) ) {
            if ( _ZMDEBUG ) console.log( $( '#Zakaz-num_of_products_in_block' + dopId ) );
            if ( _ZMDEBUG ) console.log( $( '#Zakaz-num_of_products_in_block' + dopId ).val() );
            if ( showMessage ) {
                m_alert( 'Ошибка при заполнение', 'Количество шт. в блоке' + ( dopId ? ( '(2)' ) : '' ) + ' не может быть 0', true, false, function () {
                    $( '#Zakaz-num_of_products_in_block' + dopId ).trigger( 'focus' );
                } );
            } else {
                $( '#Zakaz-num_of_products_in_block' + dopId ).addClass('has-error');
                //$.fn.dropInfo('Ошибка при заполнение - "Количество шт. в блоке'+(dopId?('(2)'):'')+' не может быть 0"','danger',5000);
            }
        } else {
            let nOC = this._totalCount( dopId ), zapas = $( '#zapasId' + dopId ).length ? parseInt( $( '#zapasId' + dopId ).val() ) : nOC / 100 * 20;
            if ( isNaN( zapas ) )
                zapas = 0;
            let cnt = Math.ceil( ( nOC + zapas ) / $( '#Zakaz-num_of_products_in_block' + dopId ).val() );
            if ( _ZMDEBUG ) console.log( '__blockRecalMainParam', nOC );
            if ( cnt < 1 )
                cnt = 1;
//            cnt+=Math.round((cnt/100)*20),0;
            $( '#Zakaz-num_of_printing_block' + dopId ).val( cnt );
            if ( !parseInt( $( '#Zakaz-blocks_per_sheet' + dopId ).val() ) ) {
                if ( showMessage ) {
                    m_alert( 'Ошибка при заполнение', 'Блоков с листа 0 - странненько', true, false );
                } else {
                    $( '#Zakaz-blocks_per_sheet' + dopId ).addClass('has-error');
                    //$.fn.dropInfo('Ошибка при заполнение - "Блоков с листа 0 - странненько"','danger',5000);
                }
            } else {
                let lifCnt = Math.ceil( cnt / parseInt( $( '#Zakaz-blocks_per_sheet' + dopId ).val() ), 0 );
                $( '#Zakaz-num_of_printing_block' + dopId ).removeClass('has-error');
                $( '#bf_with_a_stock' + dopId ).text( lifCnt );
                $( '#bf_number_of_products' + dopId ).text( ( nOC + zapas ) );
                if ( $( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][count]"]' ).length ) {
                    if ( parseInt( $( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][count]"]' ).val() ) < lifCnt ) {
                        if ( showMessage ) {
                            m_alert( 'Ошибка при заполнение', 'Указано недостаточное количество материала.<br>По расчёту требутеся ' + lifCnt + ' ' + $.fn.endingNums( lifCnt, [ 'лист', 'листа', 'листов' ] ) + ' ', {
                                label: 'Авто',
                                click: function () {
                                    $( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][count]"]' ).val( lifCnt ).trigger( 'change' );
                                    self._zakaz_material_recalulate();
                                }
                            }, {
                                label: 'Редактировать',
                                click: function () {
                                    $( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][count]"]' ).trigger( 'focus' );
                                }
                            } );
                        } else {
                            //$.fn.dropInfo('Ошибка при заполнение - "Указано недостаточное количество материала.<br>По расчёту требутеся '+lifCnt+' '+$.fn.endingNums(lifCnt,['лист','листа','листов'])+'"','danger',5000);
                        }
                    } else if ( parseInt( $( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][count]"]' ).val() ) > ( lifCnt + ( lifCnt / 100 ) * 10 ) ) {
                        if ( showMessage ) {
                            m_alert( 'Ошибка при заполнение', 'Указано избыточное количество материала.<br>По расчёту требутеся ' + lifCnt + $.fn.endingNums( lifCnt, [ 'лист', 'листа', 'листов' ] ), {
                                label: 'Авто',
                                click: function () {
                                    $( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][count]"]' ).val( lifCnt ).trigger( 'change' );
                                    self._zakaz_material_recalulate();
                                }
                            }, {
                                label: 'Редактировать',
                                click: function () {
                                    $( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][count]"]' ).trigger( 'focus' );
                                }
                            } );
                        } else {
                            //$.fn.dropInfo('Ошибка при заполнение - "Указано избыточное количество материала.<br>По расчёту требутеся '+lifCnt+'шт."','danger',5000);
                        }
                    }
                }
            }
        }
    },
    _blockRecalMainParam: function ( e ) {
        let dopId = e.data.dopId ? e.data.dopId : '';
        let showMessage = $.type( e.data.showMessage ) !== 'undefined' ? e.data.showMessage : true;
        if ( _ZMDEBUG ) console.log( '_blockRecalMainParam' );
        $(this).removeClass('has-error');
        e.data.self.__blockRecalMainParam.call( e.data.self, dopId, false );
    },
    _block_table: function ( type, check, num, is_new, is_change ) {
        is_change = $.type( is_change ) !== 'undefined' ? is_change : false;
        let r = $( '<div>' ).addClass( 'block-div' );
        if ( !this.options.form.fields.materials.length && this._productCategory != 2 && !is_new && !is_change )
            return null;
        let tbd = $( '#block-body' + ( !num ? '' : num ) ).length ? $( '#block-body' + ( !num ? '' : num ) ):$( '<div id="block-body' + ( !num ? '' : num ) + '">' ).appendTo( r );
        let self = this;
        if ( this._productCategory === 4 ) {
            type = 1;
            this._productCategory2 = {1: 'Сам'};
        }
        tbd.addClass( 'block-body' );
        is_new = $.type( is_new ) === 'undefined' ? false : true;
        if ( _ZMDEBUG ) console.log( '_block_table', this );
        tbd.children( 'div:not(:first-child)' ).remove();
        if ( check && !Object.keys( this._productCategory2 ).length ) {
            tbd.append( $( '<h3>Калькулятор не настроен</h3>' ) );
            return r;
        }

        if ( !tbd.children().length && type < 10 ) {
            tbd.append( $( '<div>' ).append( this._createBlockGroup( !num ? 0 : num ) ) );
        } else if ( type > 9 ) {
            tbd.children().remove();
            tbd.append( $( '<div>' ).append( $( '<p>' ).css( 'text-align', 'center' ).text( 'Блок' + ( type === 10 ? ' пакет п/э' : ( type === 11 ? ' сувенирка' : '' ) ) ) ).append( this._createInput( 'blocks_type' + ( !num ? '' : num ), {
                type: 'hidden'
            } ).val( type ) ) );//Zakaz[blocks_type]
        }
        if ( $.isFunction( this['__drawTypeBlock' + type] ) ) {
            //let tbd2=$('<tbody>');
            let tmp = $( '<div>' ).addClass( 'mid' ).appendTo( tbd );
            this['__drawTypeBlock' + type].call( this, tmp, ( !num ? 0 : num ) );
            //tbd.append($('<div>').append($('<table>').append(tbd2)));
            //.append(this._createInput('date_of_receipt'+(!num?'':num)).val(this.options.form.fields['date_of_receipt'+(!num?'':num)]).datepicker())
            if ( _ZMDEBUG ) console.log( 'this._productCategory', this._productCategory );
            if ( type < 9 && ( this._productCategory > 1 && this._productCategory != 3 && this._productCategory != 4 && this._productCategory != 5 ) ) {
                tmp.append( $( '<div>' ).append( this._productCategory != 2 ? null : $( '<span>' ).text( 'Кол-во листов:' ).append( $( '<span>' ).attr( {'id': 'bf_with_a_stock' + ( !num ? '' : num )} ).text( '0' ) ) ) );
                tmp.append( $( '<div>' ).append( $( '<span>' ).text( 'Дата поступления' ) ).append( $( '<span>' ).text( 'Кол-во изделий:' ) ).append( $( '<span>' ).attr( {'id': 'bf_number_of_products' + ( !num ? '' : num )} ).text( '0' ) ) );
            } /*else if ( this._productCategory > 1 && this._productCategory != 3 && this._productCategory != 4 ) {
             tmp.append( $( '<div>' ).append( $( '<span>' ).text( 'Дата поступления' ) ) );
             }*/
            let mat_sklad_tmp = this.options.form.fields['material_is_on_sklad' + ( !num ? '' : num )] ? this.options.form.fields['material_is_on_sklad' + ( !num ? '' : num )] : 0;
            let values = {
                0: 'Купить',
                1: 'На складе',
            };
            if ( $( '[name="Zakaz[materials][' + num + '][id]"]' ).length || ( this.options.form.fields.materials && this.options.form.fields.materials[num] ) ) {
                values[2] = {
                    label: 'Сброс',
                    click: function ( val ) {
                        new m_alert( 'Внимание действие отменить невозможно', 'Сбросить параметры заказа/доставки материала?', {
                            'label': 'Да',
                            'click': function () {
                                if ( self.options.toResetMaterialUrl ) {
                                    if ( _ZMDEBUG ) console.log( 'Zakaz[materials][' + num + '][id]] change Material #' + $( '[name="Zakaz[materials][' + num + '][id]"]' ).val() );
                                    $.post( self.options.toResetMaterialUrl, {
                                        id: self.options.z_id,
                                        materialNumber: num
                                    } ).done( function ( answ ) {
                                        if ( answ.error || ( answ.status && ( answ.status != 200 && answ.status !== 'ok' ) ) ) {
                                            m_alert( answ.errorHead ? answ.errorHead : 'Ошибка', answ.errorText ? answ.errorText : ( answ.error ? answ.error : ( answ.message ? answ.message : '' ) ) );
                                            return;
                                        } else {
                                            let onSklad = $( '#on-sklaad' + ( num ? num : '' ) );
                                            onSklad.radioButton( 'value', 0 );
                                            m_alert( 'Информация', 'Параметры заказа/доставки материала сброшены.', true, false );
                                        }
                                    } );
                                } else {
                                    console.warn( 'Не передан toResetMaterialUrl' );
                                }
                            }
                        }, 'Нет' );
                        return false;
                    }
                };
            }
            if ( this._productCategory <= 1 ) {
                if ( this._productCategory < 1 ) {
                    tmp.append( $( '<div>' ).append( this._productCategory != 2 ? null : $( '<span>' ) ).append( $( '<div>' ).text( 'Кол-во листов:' ) ).append( $( '<span>' ).attr( {'id': 'bf_with_a_stock' + ( !num ? '' : num )} ).text( '0' ) ) );
                    tmp.append( $( '<div>' ).append( $( '<div>' ).text( 'Кол-во изделий:' ) ).append( $( '<span>' ).attr( {'id': 'bf_number_of_products' + ( !num ? '' : num )} ).text( '0' ) ) );
                }
                //tbd.append( $( '<div>' ).addClass( 'border b-bottom back-gray' ).append( $( '<span>' ) ) );
            }
            if ( this._productCategory != 3 && this._productCategory != 5) {
                if ( _ZMDEBUG ) console.log( 'with-mater', tbd.parent(), tbd.parent().children(), tbd.parent().children().length );
                let switchSklad = ( $( '<div>' ).addClass( 'on-sklad' )
                        .append( ( $( '<div>' ).radioButton( {
                            id: 'on-sklaad' + ( !num ? '' : num ),
                            values: values,
                            name: 'Zakaz[material_is_on_sklad' + ( !num ? '' : num ) + ']',
                            defaultValue: mat_sklad_tmp,
                        } ) ) ) );
                if ( _ZMDEBUG ) console.log( 'on-sklaad', $( '#on-sklaad' + ( !num ? '' : num ) ), '#on-sklaad' + ( !num ? '' : num ), $( '#on-sklaad' + ( !num ? '' : num ) ).length );
                if ( $( '#on-sklaad' + ( !num ? '' : num ) ).length ) {
                    let oldVal = $( '#on-sklaad' + ( !num ? '' : num ) ).radioButton( 'value' );
                    $( '#on-sklaad' + ( !num ? '' : num ) ).parent().replaceWith( switchSklad );
                    $( '#on-sklaad' + ( !num ? '' : num ) ).radioButton( 'value', oldVal );
                    $( '#radio-supplierType' + num ).radioButton( 'clickEmulate' );

                } else {
                    tmp.append( switchSklad );
                }
            }
            tbd.append( $( '<div>' )
                    .append( $( '<div>' ).text( 'Дата поставки' ) )
                    .append( $( '<span>' ).append( this._createInput( 'date_of_receipt' + ( !num ? '' : num ) ).val( this.options.form.fields['date_of_receipt' + ( !num ? '' : num )] ).datepicker() ) ) )
            if ( $( '#Zakaz-num_of_products_in_block' + ( !num ? '' : num ) ).length && $( '#Zakaz-num_of_products_in_block' + ( !num ? '' : num ) ).val() ) {
                $( '#Zakaz-num_of_products_in_block' + ( !num ? '' : num ) ).trigger( 'change' );
            }
        }
        return r;
    },
    __createBaseOpt: function ( dt, name ) {
        return {
            asHtml: true,
            addOnClass: 'vh_On',
            readInIfPossible: true,
            title: function () {
                if ( $( this ).hasClass( 'hor' ) ) {
                    return ( '<p>&uarr;</p><p><span>' + dt[name].lifSizes.hn + 'мм.</span><span>&larr;' + dt[name].lifSizes.wn + 'мм.&rarr;</span><p><p>&darr;</p>' );
                } else if ( $( this ).hasClass( 'vert' ) ) {
                    return ( '<p>&uarr;</p><p><span>' + dt[name].lifSizes.wn + 'мм.</span><span>&larr;' + dt[name].lifSizes.hn + 'мм.&rarr;</span></p><p>&darr;</p>' );
                } else if ( $( this ).attr( 'data-title' ) ) {
                    return ( $( this ).attr( 'data-title' ) );
                } else {
                    return '';
                }
            }
        };
    },
    __proceedCutterAnswer: function ( dt, num, opt, notMove ) {
        let key = 'data' + num;
        let cnt = $( '<div class="cutter">' ).append( $( '<div>' ).append( $( '<p id="cutInfo' + num + '">' ).text( dt[key].lifSizes.h + 'мм.' ) ) ).append( $( '<div>' ).append( $( '<div>' ).append( $( '<p>' ).text( dt[key].lifSizes.w + 'мм.' ) ) ).append( $( dt[key].tabels ) ) );
        let rVal = $( '<div>' ).append( cnt ).appendTo( this );
        $( '<style>' ).html( dt[key].style ).appendTo( rVal );
        //this.body.append(cnt);
        $( '.vert,.hor' ).each( function () {
            let v = null, h = null, tmp = $( this ).text();
            let htmv = $( '<div>' ).addClass( 'vInf' ).append( $( '<p>' ).html( '<span>&larr;</span><span>' + ( $( this ).hasClass( 'hor' ) ? dt[key].lifSizes.hn : dt[key].lifSizes.wn ) + 'мм.</span><span>&rarr;</span>' ) );
            let htmh = $( '<div>' ).addClass( 'hInf' ).html( '<span>&larr;</span><span>' + ( $( this ).hasClass( 'hor' ) ? dt[key].lifSizes.wn : dt[key].lifSizes.hn ) + 'мм.</span><span>&rarr;</span>' );
            if ( $( this ).width() > 50 ) {
                if ( $( this ).height() > 10 ) {
                    h = htmh;
                }
            }
            if ( $( this ).height() > 50 ) {
                if ( $( this ).width() > 10 ) {
                    v = htmv;
                }
            }
            if ( v ) {
                $( this ).empty();
                $( this ).append( $( '<span>' ).text( tmp ) ).append( v );
                v.children( 'p' ).css( 'top', v.height() / 2 - v.children( 'p' ).height() / 2 );
            }
            if ( h ) {
                if ( !v ) {
                    $( this ).empty().append( $( '<span>' ).text( tmp ) ).append( h );
                    h.css( 'left', $( this ).width() / 2 - h.width() / 2 );
                } else {
                    $( this ).append( h );
                    h.css( 'left', $( this ).width() / 2 - h.width() / 2 + v.width() / 2 );
                    v.css( 'left', $( this ).width() / 2 - v.width() / 2 - h.width() / 2 );
                }
                h.css( 'top', $( this ).height() / 2 - h.height() / 2 );
            } else if ( v ) {
                v.css( 'left', $( this ).width() / 2 - v.width() / 2 - ( !tmp ? 3 : 0 ) );
            }
            if ( !v || !h )
                $( this ).mTitle( opt );
        } );
        //$('.vert,.hor').mTitle(opt);
        opt.addOnClass = 'kCutOn';
        $( '.kCutW' ).mTitle( opt );
        if ( _ZMDEBUG ) console.log( this.parent().height() );
        if ( _ZMDEBUG ) console.log( $( window ).height() );
        if ( notMove !== true )
            this.parent().offset( {top: $( window ).height() / 2 - ( this.parent().height() / 2 )} );
        $( '#cutInfo' + num ).css( 'top', $( '#cutInfo' + num ).parent().height() / 2 - $( '#cutInfo' + num ).height() / 2 + 25 );
        $( '#cutInfo' + num ).css( 'left', -22 );
        return rVal;
    },
    _liffCutter: function ( e ) {
        let self = e.data.self, tm = null, pp = null, tmp = 0;
        let dopId = e.data.dopId ? e.data.dopId : '';
        if ( !self.options.liffCutterUrl ) {
            console.error( 'Резка листа не указан liffCutterUrl' );
        }
        if ( !$( '#Zakaz-format_printing_block' + dopId ).val() ) {
            m_alert( 'Ошибка', 'Не указан формат блока', true, false, function () {
                $( '#Zakaz-format_printing_block' + dopId ).trigger( 'focus' );
            } );
            return;
        }
        if ( !$( '[name="Zakaz[materials][' + ( dopId === '' ? 0 : dopId ) + '][type_id]"]' ).length || !$( '[name="Zakaz[materials][' + ( dopId === '' ? 0 : dopId ) + '][mat_id]"]' ) ) {
            m_alert( 'Ошибка', 'Не выбран материалMain', true, false );
            return;
        }
        $.post( self.options.liffCutterUrl, {
            type_id: $( '[name="Zakaz[materials][' + ( dopId === '' ? 0 : dopId ) + '][type_id]"]' ).val(),
            mat_id: $( '[name="Zakaz[materials][' + ( dopId === '' ? 0 : dopId ) + '][mat_id]"]' ).val(),
            format_printing_block: $( '#Zakaz-format_printing_block' + dopId ).val(),
            data: {
//                wLif:1000,
//                hLif:700,
                mm: true,
                pW: 465
            }
        } ).done( function ( dt ) {
            if ( _ZMDEBUG ) console.log( 'Резка листа ответ сервера', dt );
            if ( dt.error || ( dt.status && dt.status != 200 ) ) {
                m_alert( dt.errorHead ? dt.errorHead : 'Ошибка', dt.errorText ? dt.errorText : ( dt.error ? dt.error : ( dt.message ? dt.message : '' ) ) );
                return;
            }
            m_alert( 'Результат', '', function () {
                let variant = parseInt( $( '#cutter-radio-inp' ).val() );
                variant = !isNaN( variant ) ? variant : 1;
                let tmpBlock = self._blockProceed( dopId );
                tmpBlock.block.var = variant;
                self._blockProceed( dopId, tmpBlock );
                $( '#Zakaz-blocks_per_sheet' + dopId ).val( dt['data' + variant].pcs ).trigger( 'change' );
            }, false, function () {
                if ( _ZMDEBUG ) console.log( '_liffCutter - closed' );
            }, function () {
                let tmpBlock = self._blockProceed( dopId );
                let variant = tmpBlock.block.var;
                if ( !variant ) {
                    if ( $( '#Zakaz-blocks_per_sheet' + dopId ).val().length ) {
                        if ( dt.data2.pcs == $( '#Zakaz-blocks_per_sheet' + dopId ).val() && parseInt( $( '#Zakaz-blocks_per_sheet' + dopId ).val() ) !== 0 && !isNaN( parseInt( $( '#Zakaz-blocks_per_sheet' + dopId ).val() ) ) )
                            variant = 2;
                        else
                            variant = 1;
                    } else {
                        if ( dt.data2.pcs > dt.data1.pcs )
                            variant = 2;
                        else
                            variant = 1;
                    }
                    tmpBlock.block.var = variant;
                    self._blockProceed( dopId, tmpBlock );
                }
                self.__cutterShouBody( variant, dt, this.body, false, function ( v ) {
                    tmpBlock.block.var = v;
                    tmpBlock.block.blockWidth = dt['data' + v].lifSizes.wn;
                    tmpBlock.block.blockHeight = dt['data' + v].lifSizes.hn;
                    self._blockProceed( dopId, tmpBlock );
                }, dopId );
            } );
        } ).fail( function ( er1 ) {
            if ( $.type( er1.responseJSON ) === 'object' )
                m_alert( er1.responseJSON.name, er1.responseJSON.message, true, false );
            else
                m_alert( "Ошибка сервера", er1.responseText, true, false );
        } );

    },
    __cutterShouBody: function ( variant, dt, dialog, notMove, onChange, dopId ) {
        let self = this;
        $( dialog ).empty();
        if ( _ZMDEBUG ) console.log( '__cutterShouBody' );
        if ( typeof ( dopId ) !== 'undefined' ) {
            let tmpBlock = self._blockProceed( dopId );
            tmpBlock.block.var = variant;
            self._blockProceed( dopId, tmpBlock );
        }

        let values = {};
        for ( let i = 1; i < 4; i++ ) {
            if ( $.type( dt['data' + i] ) === 'object' ) {
                if ( i == 3 ) {
                    values[i] = 'Экономич. ( ' + dt['data' + i].pcs + 'шт. )';
                } else {
                    values[i] = 'Вариант' + i + ' ( ' + dt['data' + i].pcs + 'шт. )';
                }
            }
        }
        $( '<div>' ).append( $( '<div>' ).radioButton( {
            id: 'selectPCS',
            values: values,
            name: 'cutter-radio',
            inputId: 'cutter-radio-inp',
            defaultValue: variant,
            disabledItems: [ ],
            onClick: function ( val ) {
                self.__cutterShouBody( parseInt( val ), dt, dialog, notMove, onChange, dopId );
                if ( $.isFunction( onChange ) ) {
                    onChange.call( self, parseInt( val ) );
                }
            }
        } ) ).appendTo( dialog );
        this.__proceedCutterAnswer.call( dialog, dt, variant, this.__createBaseOpt( dt, 'data' + variant ), notMove );
    },
    _zakaz_material_recalulate: function ( callRecalculateAll ) {
        if ( _ZMDEBUG ) console.log( '_zakaz_material_recalulate', this.zmCont );
        let summ = 0;
        let price = 0;
        let hasOurMat = false;
        let hasGlobalChange = false;
        callRecalculateAll = callRecalculateAll !== false ? true : false;
        if ( _ZMDEBUG ) console.log( this.zmCont.find( '[name=mat-summ]' ) );
//        $.each( this.zmCont.find( '[name=mat-summ]' ), function () {
//            let curVal = parseFloat( $( this ).val() );
//            if ( !isNaN( curVal ) )
//                summ += curVal;
//        } );
        $.each( $( '[name*=supplierType]' ), function () {
            if ( $( this ).val() === '2' ) {
                let matSum = $( this ).parent().parent().parent().find( '[name=mat-summ]' );
                let curVal = parseFloat( matSum.val() );
                let curPrice = parseFloat( matSum.attr('data-price-coast') );
                //hasGlobalChange = hasGlobalChange || matSum.attr('data-old-val') !== matSum.val();
                matSum.attr('data-old-val', matSum.val());
                if (curPrice < curVal) curPrice = curVal;
                if ( !isNaN( curVal ) && curVal > 0 ){
                    summ += curVal;
                    price += curPrice;
                }
                hasOurMat = true;
            }
            //hasOurMat = hasOurMat === false ? false : $( this ).val() === '2';
        } );
        if ( _ZMDEBUG ) console.log( summ, price );
        if ( hasOurMat && $( '#profitTypeRadio' ).radioButton( 'value' ) == 0 ) {
            $( '#zakaz_material_coast_comerc' ).removeAttr( 'readonly' );
        } else {
            $( '#zakaz_material_coast_comerc' ).attr( 'readonly', true ).val( 0 );
        }
        if ( hasOurMat )
            $( '#zakaz_material_coast' ).val( Math.ceil( summ ) );
        else
            $( '#zakaz_material_coast' ).val( 0 );
        if (!$( '#zakaz_material_coast_comerc' )[0].hasAttribute('readonly') && (parseFloat( $( '#zakaz_material_coast_comerc' ).val()) === 0 || hasGlobalChange) ){
            $( '#zakaz_material_coast_comerc' ).val( Math.ceil( price<summ?summ:price ) );
        }
        if ( parseFloat( $( '#zakaz_material_coast_comerc' ).val() ) < summ ) {
            if ( _ZMDEBUG ) console.log( '_zakaz_material_recalulate сброс стоимости', parseFloat( $( '#zakaz_material_coast_comerc' ).val() ), '=', summ );
            $( '#zakaz_material_coast_comerc' ).val( Math.ceil( summ ) );
        }
        //profitTypeRadio
        if ( !this.options.form.fields.re_print ) {
            if ( $( '#profitTypeRadio' ).radioButton( 'value' ) > 0 ) {
                if ( $( '#profitTypeRadio' ).radioButton( 'value' ) == '1' ) {
                    $( '#zakaz_material_coast_comerc' ).val( Math.ceil( ( price<summ?summ:price ) + ( price<summ?summ:price ) * 0.1 ) );
                } else if ( $( '#profitTypeRadio' ).radioButton( 'value' ) == '2' ) {
                    $( '#zakaz_material_coast_comerc' ).val( Math.ceil( ( price<summ?summ:price ) + ( price<summ?summ:price ) * 0.2 ) );
                } else {
                    $( '#zakaz_material_coast_comerc' ).val( Math.ceil( ( price<summ?summ:price ) + ( price<summ?summ:price ) * 0.5 ) );
                }
            }
        } else {
            $( '#zakaz_material_coast_comerc' ).val( 0 );
        }
        if ( !this.options.form.fields.re_print ) {
            $( '#zakaz_material_profit' ).val( Math.floor( parseFloat( $( '#zakaz_material_coast_comerc' ).val() ) - ( summ ) ) );
        }
        if ( callRecalculateAll )
            this.__recalculateAll();
    },
    _inputTooltipID: 0,
    _drawOtherInput: function ( cont, vals, baseCoast, updatetime, sType, optBaseCoast, optFrom, rCoast, index ) {
        let self = this;
        let coast = null, count = null;
        let hide = $.type( vals.supplierType ) !== 'undefined' ? (vals.supplierType == 2 ? false : true) : true;
        let inputTooltipID = self._inputTooltipID++;
        let allDone = false;
        if ( _ZMDEBUG ) console.log( '_drawOtherInput', cont.parent().parent() );
        let elIndex = this._productCategory != 2 ? cont.parent().parent().index() : cont.parent().parent().index();
        if ( _ZMDEBUG ) console.log( '_drawOtherInput', this._productCategory != 2 ? cont.parent().parent().parent().parent() : cont );
//        let uDate=null;
//        if (updatetime){
//            uDate=new Date(Date.UTC(updatetime));
//        }
        if ( _ZMDEBUG ) console.log( '_drawOtherInput', [ cont, vals, elIndex ] );
        $.each( vals, function ( key, val ) {
            if ( key !== 'rCoast' && key !== 'coast' && key !== 'count' && key !== 'supplierType' && key !== 'firm_id' && key !== 'updatetime' && key !== 'optBaseCoast' && key !== 'optFrom' ) {
                cont.append( $( '<input>' ).attr( {
                    type: 'hidden',
                    name: self._createFieldName( 'materials' ) + '[' + elIndex + '][' + key + ']'
                } ).val( val ) );
            } else if ( key === 'count' ) {
                let grp = $( '<div>' ).addClass( 'dialog-control-group' );
                grp.append( $( '<label>' ).text( 'Количество:' ) );
                count = $( '<input>' ).attr( {
                    type: 'text',
                    name: self._createFieldName( 'materials' ) + '[' + elIndex + '][' + key + ']',
                    tabindex: self.options.tabIndex++,
                    role: 'data-count' + index
                } ).change( function () {
                    if ( coast && count )
                        if ( !coast.attr( 'disabled' ) ) {
                            cont.children( ':last-child' ).children( ':last-child' )
                                .val( self._toFixed( parseFloat( coast.val() ) * parseInt( count.val() ) ) )
                                .attr('data-price-coast', self._toFixed( parseFloat( coast.attr('data-price') ) * parseInt( count.val() ) ) );
                        }
                    self._zakaz_material_recalulate();
                    if ( optBaseCoast && optFrom ) {
                        if ( parseInt( $( this ).val() ) >= optFrom ) {
                            $( '#base-opt' + index ).radioButton( 'value', 1 );
                        } else {
                            $( '#base-opt' + index ).radioButton( 'value', 0 );
                        }
                    }
                } ).val( val ? val : 0 ).onlyNumeric().enterAsTab();
                ;
                grp.append( count );
                cont.append( grp );
            } else if ( key === 'coast' ) {
                let grp = $( '<div>' ).addClass( 'dialog-control-group' );
                grp.append( $( '<label>' ).text( 'Шт/руб:' ) );
                let popoverTxt = '<p><a id="atooltip' + inputTooltipID
                        + '" style="font-size:1.1em;font-weight: bold;color:#33F;cursor:pointer;">'
                        + (baseCoast)
                        + ' руб./шт.</a> ';
                if ( optBaseCoast && optFrom ) {
                    popoverTxt += '<p><a id="atooltip' + inputTooltipID
                            + '_opt" style="font-size:1.1em;font-weight: bold;color:#33F;cursor:pointer;">'
                            + optBaseCoast
                            + ' руб./';
                    popoverTxt += optFrom;
                    popoverTxt += 'шт.</a>';
                }
                popoverTxt += ' - нажмите чтобы заполнить</p>';
                popoverTxt += ( updatetime ? ( 'Обновлено: ' + updatetime ) : '' );
                let opt = {
                    type: 'text',
                    name: self._createFieldName( 'materials' ) + '[' + elIndex + '][' + key + ']',
                    tabindex: self.options.tabIndex++,
                    'data-coast': (baseCoast),
                    'data-optBaseCoast': optBaseCoast,
                    'data-optFrom': optFrom,
                    'data-price': rCoast,
                    title: 'Стоимость по базе:',
                    'data-content': popoverTxt,
                    role: 'data-coast' + index,
                    readonly: true
                };
                if ( hide )
                    opt.disabled = 'disabled';
                coast = $( '<input>' ).attr( opt ).val( val ? val : 0 ).change( function () {
                    if ( coast && count ) {
                        cont.children( ':last-child' ).children( ':last-child' ).val( self._toFixed( parseFloat( coast.val() ) * parseInt( count.val() ) ) );
                        self._zakaz_material_recalulate();
                    }
                } ).change( function () {
                    if ( coast && count )
                        if ( !coast.attr( 'disabled' ) ) {
                            cont.children( ':last-child' ).children( ':last-child' ).val( self._toFixed( parseFloat( coast.val() ) * parseInt( count.val() ) ) );
                        }
                    self._zakaz_material_recalulate();
                    if ( optBaseCoast ) {
                        let tVal = $( this ).val(), nVal = tVal == baseCoast ? 0 : ( tVal == optBaseCoast ? 1 : -1 );
                        $( this ).parent().next().children( 'div:first-child' ).radioButton( 'value', nVal );
                    }
                } ).onlyNumeric( {allowPoint: true} ).enterAsTab();
//                let tmp = {
//                    l: coast.val().length,
//                    v: coast.val()
//                };
                if ( !self.options.form.fields.re_print && !hide && ( !coast.val().length || coast.val() == '0' ) ) {
                    coast.val( baseCoast );
                }
                let isOn = false;
                coast.popover( {
                    html: true,
                    trigger: 'manual',
                    placement: 'top'
                } );
                coast.on( 'focus', function () {
                    let par = $( this );
                    if (!isOn){
                        coast.popover( 'show' );
                        isOn = true;
                    }
                } );
                $( document ).bind( 'click', function ( e ) {
                    let t = e.target;
                    let id = 'atooltip' + inputTooltipID;
                    if ( isOn ) {
                        if ( _ZMDEBUG ) console.log( t );
                        if ( t ) {
                            if ( $( t ).attr( 'id' ) !== id && coast.attr( 'name' ) !== $( t ).attr( 'name' ) ) {
                                if ( _ZMDEBUG ) console.log( $( t ).attr( 'id' ), id, coast.attr( 'name' ), $( t ).attr( 'name' ) );
                                coast.popover( 'hide' );
                                isOn = false;
                                //                            $(document).unbind('click');
                            }
                        }
                    }
                } );

                coast.on( 'shown.bs.popover', function ( e ) {
                    let nCoast = $( this ).attr( 'data-coast' );
                    let optCoast = $( this ).attr( 'data-optBaseCoast' );
                    $( '#atooltip' + inputTooltipID ).click( function () {
                        coast.val( nCoast );
//                        $(document).unbind('click');
                        if ( _ZMDEBUG ) console.log( 'coast: ' + nCoast );
                        coast.popover( 'hide' );
                        isOn = false;
                        coast.trigger( 'change' );
                    } );
                    $( '#atooltip' + inputTooltipID + '_opt' ).click( function () {
                        coast.val( optCoast );
//                        $(document).unbind('click');
                        if ( _ZMDEBUG ) console.log( 'coast: ' + optCoast );
                        coast.popover( 'hide' );
                        isOn = false;
                        coast.trigger( 'change' );
                    } );
                    coast.blur();
                } );
                grp.append( coast );
                cont.append( grp );
//                inputTooltipID++;
                if ( optBaseCoast ) {
                    grp = $( '<div>' ).addClass( 'dialog-control-group' );
                    grp.append( $( '<div>' ).radioButton( {
                        id: 'base-opt' + index,
                        values: [ 'Розница', 'ОПТ' ],
                        defaultValue: val == baseCoast ? 0 : ( optBaseCoast && val == optBaseCoast ? 1 : -1 ),
                        onChange: function ( id ) {
                            if ( allDone ) {
                                if ( id == 1 ) {
                                    coast.val( optBaseCoast );
                                } else {
                                    coast.val( baseCoast );
                                }
                                if ( coast && count )
                                    if ( !coast.attr( 'disabled' ) ) {
                                        cont.children( ':last-child' ).children( ':last-child' ).val( self._toFixed( parseFloat( coast.val() ) * parseInt( count.val() ) ) );
                                    }
                                self._zakaz_material_recalulate();
                            }
                        }
                    } ) );
                    cont.append( grp );
                }
            } else if ( key === 'firm_id' ) {
                cont.append( $( '<input>' ).attr( {
                    type: 'hidden',
                    name: self._createFieldName( 'materials' ) + '[' + elIndex + '][' + key + ']'
                } ).val( val ) );
            }
        } );
        allDone = true;
    },
    __checkMaterialPostSklad: function ( val, tmp, indx ) {
        let onSklad = $( '#on-sklaad' + ( indx ? indx : '' ) );
        if ( val == '2' ) {
            let oldVal = onSklad.radioButton( 'value' );
            //onSklad.radioButton('setItemDisabled','2');
            onSklad.radioButton( 'unsetItemDisabled', '0' );
            onSklad.radioButton( 'unsetItemDisabled', '1' );
            onSklad.radioButton( 'unsetItemDisabled', '2' );
            onSklad.radioButton( 'value', oldVal );
            if ( onSklad.radioButton( 'value' ) == 2 || onSklad.radioButton( 'value' ) == '' )
                onSklad.radioButton( 'value', 0 );
            if ( _ZMDEBUG ) console.log( '__checkMaterialPostSklad', this );
            tmp.find( '[name *= coast]' ).removeAttr( 'disabled' );
            tmp.find( '[name *= coast]' ).each( function () {
                if ( _ZMDEBUG ) console.log( 'coastFlashing', {
                    el: this,
                    valLenght: $( this ).val().length,
                    val: $( this ).val()

                } );
                if ( !$( this ).val().length || $( this ).val() === '0' )
                    $( this ).val( $( this ).attr( 'data-coast' ) );
            } );
            let tmpSumm = tmp.find( '[name*=coast]' ).attr( 'disabled' ) ? 0 : ( parseFloat( tmp.find( '[name*=coast]' ).val() ) * parseInt( tmp.find( '[name*=count]' ).val() ) );
            tmp.children( ':last-child' ).children( ':last-child' ).val( this._toFixed( tmpSumm ) );
        } else if ( val == '0' ) {
            onSklad.radioButton( 'setItemDisabled', '1' );
            onSklad.radioButton( 'setItemDisabled', '0' );
            onSklad.radioButton( 'setItemDisabled', '2' );
            onSklad.radioButton( 'value', null );
            tmp.find( '[name *= coast]' ).attr( 'disabled', true ).val( 0 );
            tmp.children( ':last-child' ).children( ':last-child' ).val( 0 );
            if ( _ZMDEBUG ) console.log( onSklad.radioButton( 'value' ) );
//            let tmpSumm=tmp.find('[name*=coast]').attr('disabled')?0:(parseFloat(tmp.find('[name*=coast]').val()) * parseInt(tmp.find('[name*=count]').val()));
            tmp.children( ':last-child' ).children( ':last-child' ).val( 0 );
        } else {
            onSklad.radioButton( 'setItemDisabled', '0' );
            onSklad.radioButton( 'setItemDisabled', '1' );
            onSklad.radioButton( 'setItemDisabled', '2' );
            onSklad.radioButton( 'value', null );
            tmp.find( '[name *= coast]' ).attr( 'disabled', true ).val( 0 );
            tmp.children( ':last-child' ).children( ':last-child' ).val( 0 );
        }

    },
    _drawOneMater: function ( vals, lbls, isNew ) {
        let self = this;
        let tmp = $( '<div>' ), lbl_cnt = lbls.length - 1;
        let keys = Object.keys( vals ), v_cnt = keys.length;
        // let text = [ 'Заказчик', 'Испол-ль', 'Наш' ];
        let cont;
        let index;
        isNew = $.type( isNew ) === 'undefined' ? false : isNew;
        //self._productCategory
        if ( _ZMDEBUG ) console.log( '_drawOneMater', {vals, lbls} );
        //if ( this._productCategory != 2 ) {
        cont = this._changeBlockModeAll( this._productCategory, this.zmCont.children().length - 1, isNew );
        //tmp = cont.children( 'tbody' ).children( ':first-child' ).children( ':first-child' );
        tmp = $( '<div>' ).insertBefore( cont.children( ':first-child' ) );
        //cont=$('<div>').append(tmp).addClass('with-mater');
        tmp.addClass( 'with-mater' );
        /*
         optBaseCoast: dialog.selectedVal.optBaseCoast,
         optFrom: dialog.selectedVal.optFrom

         */
        let middle = $( '<div>' ).addClass( 'mid' );
        cont.insertBefore( this.zmCont.children( ':last-child' ) );
        if ( _ZMDEBUG ) console.log( '_drawOneMater', cont, cont.index() );//
        index = cont.index();
        if ( $.type( vals.ids.supplierType ) !== 'undefined' ) {
            let chk = $( '<div>' ).radioButton( {
                name: this._createFieldName( 'materials' ) + '[' + index + '][supplierType]',
                wrapClass: 'mradio-input',
                id: 'radio-supplierType' + index,
                values: {
                    0: 'Заказчик',
                    1: 'Испол-ль',
                    2: 'Наш'
                },
                elementsInLine: 3,
                itemTagName: 'div',
                defaultValue: $.type( vals.ids.supplierType ) !== 'undefined' ? vals.ids.supplierType : 2,
                disabledItems: [ ],
                onClick: function () {
                    self.__checkMaterialPostSklad( $( this ).attr( 'data-key' ), tmp, index );
                    if ( _ZMDEBUG ) console.log( tmp.find( '[name *= coast]' ), tmp.children( ':last-child' ).children( ':last-child' ), $( this ) );
                    self._zakaz_material_recalulate();
                }
            } );
            tmp.append( $( '<div>' )
                    .addClass( 'suppradio' )
                    .append( chk )
                    );
        }
        if ( vals.post ) {
            middle.append( $( '<div>' )
                    .addClass( 'dialog-control-group' )
                    .append( $( '<label>' ).text( 'Поставщик:' ) )
                    .append( $( '<span>' ).addClass( 'dialog-form-control' ).text( vals.post ) ) );
        }
        for ( let i = v_cnt - 1, n = 0; i >= 0; i-- ) {
            if ( _ZMDEBUG ) console.log( '_drawOneMater', tmp.parent().children().length );
            if ( keys[i] !== 'ids' && keys[i] !== 'ids' !== 'id' && keys[i] !== 'post' && keys[i] !== 'baseCoast' && keys[i] !== 'rCoast' && keys[i] !== 'optBaseCoast' && keys[i] !== 'optFrom' && keys[i] !== 'updatetime' ) {
                let grp = $( '<div>' ).addClass( 'dialog-control-group' );
                let lbl = $( '<label>' ).text( n <= lbl_cnt ? ( lbls[n] + ':' ) : 'нет' );
                let span = $( '<span>' ).addClass( 'dialog-form-control' ).text( vals[keys[i]] );
                if ( tmp.parent().children().length === 2 && n <= lbl_cnt ) {
                    if ( _ZMDEBUG ) console.log( '_drawOneMater', vals[keys[i]] );
                    if ( $( '#Zakaz-blocks_type' + ( index ? index : '' ) ).val() === '10' ) {
                        if ( lbls[n] === 'Цвет:' || lbls[n] === 'Цвет' ) {
                            span.attr( 'data-material-color', vals[keys[i]] );
                            if ( !$( '#block-color-info' + ( index ? index : '' ) ).val() ) {
                                $( '#block-color-info' + ( index ? index : '' ) ).val( vals[keys[i]] );
                            }
                        } else if ( lbls[n] === 'Размер:' || lbls[n] === 'Размер листа:' || lbls[n] === 'Формат:' || lbls[n] === 'Размер' || lbls[n] === 'Размер листа' || lbls[n] === 'Формат' ) {
                            span.attr( 'data-material-size', vals[keys[i]] );
                            $( '#Zakaz-format_printing_block' + ( index ? index : '' ) ).val( vals[keys[i]] );
                        }
                    } else if ( $( '#Zakaz-blocks_type' + ( index ? index : '' ) ).val() === '11' ) {
                        if ( lbls[n] == 'Cостав:' || lbls[n] == 'Cостав' ) {
                            span.attr( 'data-material-sostav' + ( index ? index : '' ), vals[keys[i]] );
                            if ( !$( '#block-sostav-info' + ( index ? index : '' ) ).val() ) {
                                $( '#block-sostav-info' + ( index ? index : '' ) ).val( vals[keys[i]] );
                            }
                        }
                    }
                }
                n++;
                grp.append( lbl );
                if ( vals[keys[i]].length > 11 )
                    span.attr( 'title', vals[keys[i]] );
                grp.append( span );
                middle.append( grp );
            }
        }
        if ( _ZMDEBUG ) console.log( 'Вычислени матер', this.zmCont.children( ':last-child' ).children( ':first-child' ) );
        if ( !this._checkMaterialCount() ) {
            this.zmCont.removeAttr( 'style' );
        }
        if ( this.zmCont.children().length > 1 )
            this.zmCont.children( ':last-child' ).children( ':last-child' ).removeClass( 'disabled' );
        let tmpv = $.extend( {}, vals.ids );
        if ( vals.id )
            tmpv.id = vals.id;
        tmp.append( middle );
        this._drawOtherInput( middle, tmpv, vals.baseCoast, $.type( vals.updatetime ) !== 'undefined' ? vals.updatetime : 0, $.type( vals.ids.supplierType ) !== 'undefined' ? vals.ids.supplierType : 2, vals.optBaseCoast, vals.optFrom, vals.rCoast, index );
        if ( _ZMDEBUG )  console.log( 'Вычислени матер', middle.children( '.dialog-control-group' ).last() );
        if ( _ZMDEBUG ) console.log( middle.children( '.dialog-control-group' ).last().children().last().val(), middle.children( '.dialog-control-group' ).last().prev().children().last().val() );
        let tmpSumm  = ( parseFloat( middle.find( '[role=data-count' + index + ']' ).val() ) * parseFloat( middle.find( '[role=data-coast' + index + ']' ).val() ) );
        let tmpPrice = ( parseFloat( middle.find( '[role=data-count' + index + ']' ).val() ) * parseFloat( middle.find( '[role=data-coast' + index + ']' ).attr('data-price') ) );
        if (isNaN(tmpPrice)) tmpPrice = 0;
        if ( _ZMDEBUG ) console.log( $.type( middle.children( ':last-child' ).children( ':last-child' ).attr( 'disabled' ) ) );
        let tmpChk = $.type( middle.children( ':last-child' ).children( ':last-child' ).attr( 'disabled' ) ) !== 'undefined' || vals.ids.supplierType !== '2';
        middle.append( $( '<div>' ).addClass( 'dialog-control-group' )
                .append( $( '<label>' ).text( 'Итог:' ) )
                .append( $( '<input>' ).attr( {
                    type: 'text',
                    name: 'mat-summ',
                    disabled: true,
                    'data-price-coast':tmpPrice
                } ).val( tmpChk ? 0 : ( this._toFixed( tmpSumm ) ) ) )
                .enterAsTab() );
        this._zakaz_material_recalulate();
        let tmp1 = $( '[name="Zakaz[materials][0][supplierType]"]' );
        if ( tmp1.length && tmp1.val() !== '' ) {
            self.__checkMaterialPostSklad( tmp1.val(), tmp, index );
        }
        tmp1 = $( '[name="Zakaz[materials][1][supplierType]"]' );
        if ( tmp1.length && tmp1.val() !== '' ) {
            self.__checkMaterialPostSklad( tmp1.val(), tmp, index );
        }
        this.__correctControlButtonPos();

        tmp.append( tmp.parent().find( '.on-sklad' ) );
        if ( _ZMDEBUG ) console.log( 'on-sklad', tmp.parent().find( '.on-sklad' ), tmp );
        if ( vals.post === 'Заказчик' ) {
            $( '#radio-supplierType' + index ).radioButton( 'value', 0 );
            $( '#radio-supplierType' + index ).radioButton( 'setItemDisabled', '1' );
            $( '#radio-supplierType' + index ).radioButton( 'setItemDisabled', '2' );
        }
    },
    __correctControlButtonPos: function () {
        //if ( _ZMDEBUG ) console.log('__correctControlButtonPos',this.zmCont.children().length);
        if ( this.zmCont.children().length === 3 ) {
            this.zmCont.children( ':last-child' ).removeAttr( 'style' );
        } else if ( this.zmCont.children().length === 2 ) {
            this.zmCont.children( ':last-child' ).css( {
                right: '45%'
            } );
        } else {
            this.zmCont.children( ':last-child' ).css( {
                right: '95%'
            } );
        }
    },
    _addClick: function ( dialog, data ) {
        let self = data.self;
        if ( _ZMDEBUG ) console.log( dialog.selectedVal );
        if ( !dialog.selectedVal ) {
            console.warn( 'Zakaz-material->_addClick', 'Не выбрано значение!' );
        }
        if ( data.self.options.materialInfoUrl ) {
            self._showBunner();
            $.post( data.self.options.materialInfoUrl, {values: dialog.selectedVal} ).done( function ( answ ) {
                if ( _ZMDEBUG ) console.log( answ );
                if ( answ.status && answ.status === 'ok' ) {
                    self._drawOneMater(
                            $.extend( {}, {
                                post: answ.postav.name,
                                baseCoast: dialog.selectedVal.coast ? dialog.selectedVal.coast : 0,
                                optBaseCoast: dialog.selectedVal.optBaseCoast,
                                optFrom: dialog.selectedVal.optFrom,
                                rCoast: dialog.selectedVal.rCoast
                            }, answ.query, {ids: $.extend( {}, dialog.selectedVal, {
                                    count: 0,
                                    coast: dialog.selectedVal.coast ? dialog.selectedVal.coast : 0,
                                    supplierType: 2, } )} ),
                            JSON.parse( answ.value.struct ), true );
                }
                self._hideBunner();
            } ).fail( function ( er1 ) {
                if ( $.type( er1.responseJSON ) === 'object' )
                    m_alert( er1.responseJSON.name, er1.responseJSON.message, true, false );
                else
                    m_alert( "Ошибка сервера", er1.responseText, true, false );
            } );
        } else {
            console.warn( 'Zakaz-material->_addClick', 'Не задан адресс materialInfoUrl' );
        }
    },
    _zakaz_materialDrawLoaded: function () {
        let cnt = this.options.form.fields.materials.length, self = this;
        if ( !cnt )
            return;
        if ( this.options.materialInfoUrl ) {
            this.busy++;
            let tm = performance.now();
            //$.fn.dropInfo('Запрос данных по материалам','info');
            $.post( this.options.materialInfoUrl, {value_lists: this.options.form.fields.materials} ).done( function ( answ ) {
//                if ( _ZMDEBUG ) console.log(answ);
                if ( answ.status && answ.status === 'ok' ) {
                    $.each( answ.list, function ( key, val ) {
                        if ( $.type( val ) === 'object' ) {
                            self._drawOneMater(
                                    $.extend( {}, {
                                        post: val.postav.name,
                                        baseCoast: val.postav.baseCoast ? val.postav.baseCoast : 0,
                                        updatetime: val.postav.updatetime ? val.postav.updatetime : 0,
                                        optBaseCoast: val.postav.optBaseCoast,
                                        optFrom: val.postav.optFrom
                                    }, val.query, {ids: self.options.form.fields.materials[key]} ),
                                    JSON.parse( val.value.struct ), false
                                    );
                        }
                    } );
                }
                //$.fn.dropInfo('Данных по материалам получены время запроса: '+(performance.now()-tm).toFixed(2)+'ms','success');
                self._busyDown();
            } ).fail( function ( er1 ) {
                if ( $.type( er1.responseJSON ) === 'object' )
                    m_alert( er1.responseJSON.name, er1.responseJSON.message, true, false );
                else
                    m_alert( "Ошибка сервера", er1.responseText, true, false );
            } );
        } else {
            console.warn( 'Zakaz-material->_addClick', 'Не задан адресс materialInfoUrl' );
        }
    }
};