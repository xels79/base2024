/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Модульконструктор */

var block_constuktor = {
    __blockCurW: 0, __blockCurH: 0, __blockDelay: null, __blockFormatCutterResult: null, __blockIsOpen: false,
    __cutterData2: null,
    _blockFormatterRecalk: function ( self, block, productSZ, dopId, key ) {
        //let work_key=key;
        block = $( '#Zakaz-material_block_format' + dopId ).val() ? ( JSON.parse( $( '#Zakaz-material_block_format' + dopId ).val() ) ) : {};
        $( this ).blockFormat( 'option', 'CText', parseInt( $( '#bf_markers' ).val() ) );
        $( this ).blockFormat( 'option', 'DText', parseInt( $( '#bf_distance' ).val() ) );
        $( this ).blockFormat( 'recalculate', false );
        $( '#bf_pcs_in_block' ).val( $( this ).blockFormat( 'getPCS' ) );
        if ( $( '#mt-cutting' ).attr( 'data-key' ) === 'on' ) {
            $( '#bf_corner_distance' ).removeAttr( 'readonly' ).val( $( '#bf_corner_distance' ).val() != 0 ? $( '#bf_corner_distance' ).val() : 7 );
            $( '.bfTable' ).addClass( 'shift' );
            if ( !$( '#cutting-info' ).length ) {
                $( '.bfTable' ).append( $( '<p>' ).attr( {id: 'cutting-info'} ) );
            }
            if ( !$( '#cutting-info2' ).length ) {
                $( '.bfTable' ).append( $( '<p>' ).attr( {id: 'cutting-info2'} ) );
            }
            $( '#cutting-info,#cutting-info2' ).text( $( '#bf_corner_distance' ).val() );
        } else {
            $( '.bfTable' ).removeClass( 'shift' );
            $( '#bf_corner_distance' ).attr( 'readonly', true ).val( 0 );
            $( '#cutting-info,#cutting-info2' ).remove();
        }
        self.__blockCurW = $( this ).blockFormat( 'getColCount' ) * productSZ[0] + 2 * parseInt( $( '#bf_markers' ).val() ) + parseInt( $( '#bf_distance' ).val() ) * ( $( this ).blockFormat( 'getColCount' ) - 1 );
        self.__blockCurH = $( this ).blockFormat( 'getRowCount' ) * productSZ[1] + 2 * parseInt( $( '#bf_markers' ).val() ) + parseInt( $( '#bf_distance' ).val() ) * ( $( this ).blockFormat( 'getRowCount' ) - 1 );
        self.__blockCurH += parseInt( $( '#bf_corner_distance' ).val() );
        self.__blockCurW += parseInt( $( '#bf_corner_distance' ).val() );

        $( '#mt-width-info' ).text( self.__blockCurW + 'мм' );
        $( '#mt-width-info' ).css( {left: $( this ).width() / 2 - $( '#mt-width-info' ).width() / 2 + $( this ).prev().width()} );
        $( '#mt-height-info' ).text( self.__blockCurH + 'мм' );
        $( '#mt-height-info' ).css( {top: $( this ).height() / 2 - $( '#mt-height-info' ).height() / 2} );
        $( '#bf_block_size' ).val( self.__blockCurW + '*' + self.__blockCurH );
        //Zakaz-material_block_format
        let variant = block.block ? ( block.block.var ? block.block.var : 1 ) : 1;
        let swap = false;
        if ( self.__cutterData
                && self.__cutterData.data
                && self.__cutterData.data['data' + self.__cutterData.variant]
                && self.__cutterData.data['data' + self.__cutterData.variant].lifSizes.wn > self.__cutterData.data['data' + self.__cutterData.variant].lifSizes.hn ) {
            swap = true;
        }
        block.block = {
            col: $( this ).blockFormat( 'getColCount' ),
            row: $( this ).blockFormat( 'getRowCount' ),
            c: parseInt( $( '#bf_markers' ).val() ),
            d: parseInt( $( '#bf_distance' ).val() ),
            cutting: $( '#mt-cutting' ).attr( 'data-key' ),
            corner_distance: $( '#bf_corner_distance' ).val(),
            blockWidth: self.__blockCurW, //swap ? self.__blockCurH : self.__blockCurW,
            blockHeight: self.__blockCurH, //swap ? self.__blockCurW : self.__blockCurH,
            var : variant
        };
        self._blockProceed( dopId, block );
        //$('#Zakaz-material_block_format').val(JSON.stringify(block));
        if ( self.__blockDelay ) {
            clearTimeout( self.__blockDelay );
        }
        $( '#bk_load' ).remove();
        $( '#bf_from_liff' ).parent().append( $( '<img>' ).attr( {
            id: 'bk_load',
            src: '/pic/loader-hor.gif',
            width: '80px',
            height: '30px'
        } ).css( {left: $( '#bf_from_liff' ).position().left + 2, top: -3} ) );
        self.__blockDelay = setTimeout( function () {
            self.__blockDelay = null;
            if ( self.__blockIsOpen )
                $.post( self.options.liffCutterUrl, {
                    type_id: $( '[name="Zakaz[materials][' + key + '][type_id]"]' ).val(),
                    mat_id: $( '[name="Zakaz[materials][' + key + '][mat_id]"]' ).val(),
                    format_printing_block: $( '#bf_block_size' ).val(),
                    data: {
                        mm: true,
                        pW: 310
                    }
                } ).done( function ( dt ) {
                    console.log( dt );
                    if ( self.__blockIsOpen ) {
                        if ( dt.error || ( dt.status && dt.status != 200 && dt.status !== 'ok' ) ) {
                            m_alert( dt.errorHead ? dt.errorHead : 'Ошибка', dt.errorText ? dt.errorText : ( dt.error ? dt.error : ( dt.message ? dt.message : '' ) ), true, false );
                            return block;
                        }
                        self.__blockFormatCutterResult = dt;
                        $( '#bk_load' ).remove();
                        let blockT = self._blockProceed( dopId );
                        blockT.block = blockT.block || {};
                        let shStart = blockT.block.var ? blockT.block.var : ( self.__blockFormatCutterResult.data1.pcs >= self.__blockFormatCutterResult.data2.pcs ? 1 : 2 );
                        self._blockProceed( dopId, blockT );
                        self.___block2_show_result( shStart, dopId );
                        self.__cutterData2 = {data: dt, variant: variant};
                        self.__cutterShouBody( shStart, dt, $( '.mat_cutter-content' ), true, function ( val ) {
                            console.log( this );
                            this.___block2_show_result( val, dopId );
                            this.__cutterData2.variant = variant;
                        }, dopId );
                    }
                } ).fail( function ( er1 ) {
                    if ( $.type( er1.responseJSON ) === 'object' )
                        m_alert( er1.responseJSON.name, er1.responseJSON.message, true, false );
                    else
                        m_alert( "Ошибка сервера", er1.responseText, true, false );
                } );
            ;
        }, 400 );
        return block;
    },
    ___block2_show_result: function ( variant, dopId ) {
        let bf_from_liff
        if ( $.type( variant ) === 'undefined' ) {
            bf_from_liff = parseInt( this.__blockFormatCutterResult.data1.pcs >= this.__blockFormatCutterResult.data2.pcs ? this.__blockFormatCutterResult.data1.pcs : this.__blockFormatCutterResult.data2.pcs );
        } else {
            bf_from_liff = parseInt( this.__blockFormatCutterResult['data' + variant].pcs );
        }
        $( '#bf_from_liff' ).val( bf_from_liff );
        $( '#bf_from_liff' ).next().text( $.fn.endingNums( bf_from_liff, [ 'блок', 'блока', 'блоков' ] ) );
        //mt-liff-cout
        if ( this._totalCount( dopId ) ) {
            let nOC = this._totalCount( dopId ), cnt = Math.ceil( ( nOC + nOC / 100 * 20 ) / $( '#bf_pcs_in_block' ).val() );
//                            let cnt=Math.ceil(parseInt($('#Zakaz-number_of_copies').val())/$('#bf_pcs_in_block').val());
            if ( cnt < 1 )
                cnt = 1;
//                            cnt+=Math.ceil((cnt/100)*20);
            let lifCnt = Math.ceil( cnt / parseInt( $( '#bf_from_liff' ).val() ) );
            $( '#mt-liff-cout' ).val( lifCnt );
            $( '#mt-liff-cout' ).next().text( $.fn.endingNums( lifCnt, [ 'лист', 'листа', 'листов' ] ) );
        }
    },
    _blockFormatter: function ( e ) {
        console.log( '_blockFormatter' );
        let self = e.data.self, productSZ = [ ];
        let dopId = e.data.dopId ? e.data.dopId : '';
        let key = ( dopId === '' ? '0' : ( '' + dopId ) );

        let d = $( '<div>' )
                .attr( {
                    id: 'zakaz_material_format_block',
                    title: 'Формат блока',
                } );
        let  forBack = 1;
        d.append( $( '<h4>' ).append( $( '<span>' ).text( 'Размер готового изделия: ' ) ).append( $( '<span>' ).attr( {id: 'mat_header_info'} ) ) );
        let inf = $( '<div class="mat_blockf-info">' ).append( $( '<div>' ) ).append( $( '<div>' ).append( $( '<div>' ) ) ).appendTo( d );
        let butt = $( '<div class="mat_blockf-button">' ).appendTo( d );
        let cont = $( '<div class="mat_blockf-content">' ).appendTo( d );
        let cutter = $( '<div class="mat_cutter-content">' );
        let wt = $( '<div>' );
        if ( !$( '[name="Zakaz[materials][' + key + '][type_id]"]' ).length || !$( '[name="Zakaz[materials][' + key + '][mat_id]"]' ) ) {
            m_alert( 'Ошибка', 'Не выбран материал2', true, false );
            return;
        }
        if ( !$( '#Zakaz-product_size' ).val() ) {
            m_alert( 'Ошибка', 'Не указан размер изделия', true, false, function () {
                $( '[href="#tab-pane-zakaz-page0"' ).trigger( 'click' );
                $( '#Zakaz-product_size' ).focus();
            } );
            return;
        } else {
            productSZ = self.__proceedStringToSizeArray( $( '#Zakaz-product_size' ).val(), true );
            if ( productSZ.length !== 2 || !productSZ[0] || !productSZ[1] ) {
                productSZ = self.__proceedStringToSizeArray( $.fn.formatProceed( $( '#Zakaz-product_size' ).val() ), true );
                if ( productSZ.length !== 2 || !productSZ[0] || !productSZ[1] ) {
                    m_alert( 'Ошибка', 'Неверное значение размер изделия<br>Должно быть <i>"<b>Ш*В</b>"</i>', true, false, function () {
                        $( '[href="#tab-pane-zakaz-page0"' ).trigger( 'click' );
                        $( '#Zakaz-product_size' ).focus();
                    } );
                    return;
                } else {
                    productSZ[0] = parseInt( productSZ[0] );
                    productSZ[1] = parseInt( productSZ[1] );
                }
            }
        }
        if ( self.options.getonematinfo ) {
            self._showBunner( 'Загрузка' );
            $.post( self.options.getonematinfo, {
                type_id: $( '[name="Zakaz[materials][' + key + '][type_id]"]' ).val(),
                mat_id: $( '[name="Zakaz[materials][' + key + '][mat_id]"]' ).val(),
            } ).done( function ( dt ) {
                console.log( dt );
                if ( !dt.error && ( !dt.status || dt.status == 200 ) ) {
                    let block = $( '#Zakaz-material_block_format' + dopId ).val() ? ( JSON.parse( $( '#Zakaz-material_block_format' + dopId ).val() ) ) : {};
                    d.appendTo( 'body' );
                    $( '#mat_header_info' ).text( productSZ[0] + '*' + productSZ[1] + 'мм' );
                    inf.children( ':first-child' ).append( $( '<div>' ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'Кол-во шт. в блоке:' ) ).append( $( '<input>' ).attr( {
                        id: 'bf_pcs_in_block',
                        readonly: 'readonly',
                        value: '12'
                    } ) ).append( $( '<span>' ).text( '' ) ) ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'материал:' ) ).append( $( '<input>' ).attr( {
                        id: 'bf_lif_size',
                        readonly: 'readonly',
                        value: dt[0] + '*' + dt[1],
                    } ) ).append( $( '<span>' ).text( 'мм' ) ) ) );
                    inf.children( ':first-child' ).append( $( '<div>' ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'Размер блока:' ) ).append( $( '<input>' ).attr( {
                        id: 'bf_block_size',
                        readonly: 'readonly',
                        value: '110*200'
                    } ) ).append( $( '<span>' ).text( 'мм' ) ) ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'c листа:' ) ).append( $( '<input>' ).attr( {
                        id: 'bf_from_liff',
                        readonly: 'readonly',
                        value: '10'
                    } ) ).append( $( '<span>' ).text( 'блоков' ) ) ) );

                    inf.children( ':first-child' ).append( $( '<div>' ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'Столбцы:' ) ).append( $( '<input>' ).attr( {
                        id: 'mt-col-info',
                        //readonly: 'readonly',
                        value: '4',
                        class: 'small'
                    } ).focusin( function () {
                        forBack = $( this ).val();
                    } ).focusout( function () {
                        if ( forBack !== $( this ).val() ) {
                            if ( wt.blockFormat( 'setCol', $( this ).val() ) !== true ) {
                                m_alert( 'Действие невозможно', 'Неверное значение', true, false );
                            }
                        }
                    } ) ).append( $( '<button>' ).text( '+' ).click( function () {
                        if ( self.__blockCurW + productSZ[0] + 2 * parseInt( $( '#bf_markers' ).val() ) + parseInt( $( '#bf_distance' ).val() ) + parseInt( $( '#bf_corner_distance' ).val() ) < dt[0] ) {
                            wt.blockFormat( 'addCol', false );
                            $( '#mt-col-info' ).val( wt.blockFormat( 'getColCount' ) );
                        } else {
                            m_alert( 'Действие невозможно', 'Размер превысит ширину листа', true, false );
                        }
                    } ) ).append( $( '<button>' ).text( '-' ).click( function () {
                        wt.blockFormat( 'removeCol', false );
                        $( '#mt-col-info' ).val( wt.blockFormat( 'getColCount' ) );
                    } ) ) ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).css( 'padding-left', 10 ).append( $( '<label>' ).css( 'min-width', 96 ).text( 'Кол-во.:' ) ).append( $( '<input>' ).attr( {
                        id: 'mt-liff-cout',
                        readonly: 'readonly',
                        value: '4',
                    } ) ).append( $( '<span>' ).text( 'листов' ) ) ) );
                    inf.children( ':first-child' ).append( $( '<div>' ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'Строки:' ) ).append( $( '<input>' ).attr( {
                        id: 'mt-row-info',
                        //readonly: 'readonly',
                        value: '4',
                        class: 'small'
                    } ).focusin( function () {
                        forBack = $( this ).val();
                    } ).focusout( function () {
                        if ( forBack !== $( this ).val() ) {
                            if ( wt.blockFormat( 'setRow', $( this ).val() ) !== true ) {
                                m_alert( 'Действие невозможно', 'Неверное значение', true, false );
                            }
                        }
                    } ) ).append( $( '<button>' ).text( '+' ).click( function () {
                        if ( self.__blockCurH + productSZ[1] + 2 * parseInt( $( '#bf_markers' ).val() ) + parseInt( $( '#bf_distance' ).val() ) + parseInt( $( '#bf_corner_distance' ).val() ) < dt[1] ) {
                            wt.blockFormat( 'addRow', false );
                            $( '#mt-row-info' ).val( wt.blockFormat( 'getRowCount' ) );
                        } else {
                            m_alert( 'Действие невозможно', 'Размер превысит высоту листа', true, false );
                        }
                    } ) ).append( $( '<button>' ).text( '-' ).click( function () {
                        wt.blockFormat( 'removeRow', false );
                        $( '#mt-row-info' ).val( wt.blockFormat( 'getRowCount' ) );
                    } ) ) ) );
                    inf.children( ':first-child' ).append( $( '<div>' ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'Вырубка:' ) ).append( $( '<input>' ).attr( {
                        id: 'mt-cutting',
                        class: 'small',
                        type: 'checkbox',
                        'data-key': $.type( block.block ) === 'object' && block.block.cutting === 'on' ? 'on' : 'off',
                        checked: $.type( block.block ) === 'object' && block.block.cutting === 'on' ? true : false,
                    } ).click( function () {
                        if ( $( this ).attr( 'data-key' ) === 'on' ) {
                            $( this ).attr( 'data-key', 'off' );
                            $( '#bf_markers' ).val( 3 );
                            $( '#bf_distance' ).val( 2 );
                        } else {
                            $( this ).attr( 'data-key', 'on' );
                        }
                        block = self._blockFormatterRecalk.call( wt[0], self, block, productSZ, dopId, key );
                    } ) ) ) );
                    inf.children( ':last-child' ).children( ':first-child' ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'Дополнительные параметры' ) ) );
                    inf.children( ':last-child' ).children( ':first-child' ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'Расстояние между изделиями:' ) ).append( $( '<input>' ).attr( {
                        id: 'bf_distance',
                        //readonly:'readonly',
                        value: $.type( block.block ) === 'object' && block.block.d ? block.block.d : '2',
                        class: 'small'
                    } ).css( 'border-color', 'rgb(162, 0, 56)' ).focusout( function () {
                        block = self._blockFormatterRecalk.call( wt[0], self, block, productSZ, dopId, key );
                    } ).onlyNumeric() ).append( $( '<span>' ).text( 'мм' ) ) );
                    inf.children( ':last-child' ).children( ':first-child' ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'Метки:' ) ).append( $( '<input>' ).attr( {
                        id: 'bf_markers',
                        //readonly:'readonly',
                        value: $.type( block.block ) === 'object' && block.block.c ? block.block.c : '3',
                        class: 'small'
                    } ).css( 'border-color', 'rgb(0, 243, 180)' ).focusout( function () {
                        block = self._blockFormatterRecalk.call( wt[0], self, block, productSZ, dopId, key );
                    } ).onlyNumeric() ).append( $( '<span>' ).text( 'мм' ) ) );
                    inf.children( ':last-child' ).children( ':first-child' ).append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( $( '<label>' ).text( 'Расстояние до верного угла:' ) ).append( $( '<input>' ).attr( {
                        id: 'bf_corner_distance',
                        readonly: 'readonly',
                        value: $.type( block.block ) === 'object' && block.block.corner_distance ? block.block.corner_distance : 0,
                        class: 'small'
                    } ).focusout( function () {
                        block = self._blockFormatterRecalk.call( wt[0], self, block, productSZ, dopId, key );
                    } ).onlyNumeric() ).append( $( '<span>' ).text( 'мм' ) ) );

                    cont.append( $( '<div>' ).append( $( '<p>' ).attr( {id: 'mt-width-info'} ) ) )
                            .append( $( '<div>' ).addClass( 'blockFormat' ).append( $( '<div>' ).append( $( '<p>' ).attr( 'id', 'mt-height-info' ) ) ).append( wt ) );
                    d.maindialog( {
                        minimizable: false,
                        modal: true,
                        width: 1250,
                        height: 680,
                        buttons: [
                            {
                                text: "Ok",
                                icon: "ui-icon-heart",
                                click: function () {
                                    $( '#Zakaz-format_printing_block' + dopId ).val( $( '#bf_block_size' ).val() );
                                    $( '#Zakaz-blocks_per_sheet' + dopId ).val( $( '#bf_from_liff' ).val() );
                                    //$( '#Zakaz-material_block_format' + dopId ).val( JSON.stringify( block ) );
                                    $( '#Zakaz-num_of_products_in_block' + dopId ).val( $( '#bf_pcs_in_block' ).val() ).trigger( 'change' );
                                    $( this ).maindialog( "close" );
                                }
                            },
                            {
                                text: "Отмена",
                                icon: "ui-icon-heart",
                                click: function () {
                                    $( this ).maindialog( "close" );
                                }
                            }
                        ],
                        close: function () {
                            self.__blockIsOpen = false;
                            d.remove();
                            self._hideBunner();
                            if ( $.isFunction( e.data.afterEnd ) )
                                e.data.afterEnd.call( self );
                        },
                        open: function () {
                            $( this ).parent().addClass( 'block-format-popup' );
                            self.__blockIsOpen = true;
                            wt.blockFormat( {
                                row: $.type( block.block ) === 'object' ? block.block.row : 1,
                                col: $.type( block.block ) === 'object' ? block.block.col : 1,
                                maxRow: 20,
                                maxCol: 20,
                                w: productSZ[0],
                                h: productSZ[1],
                                bW: 650,
                                bH: 250,
                                CText: $( '#bf_markers' ).val(),
                                DText: $( '#bf_distance' ).val(),
                                afterUpdate: function () {
                                    block = self._blockFormatterRecalk.call( this, self, block, productSZ, dopId, key );
                                }
                            } );
                            $( '#mt-col-info' ).val( wt.blockFormat( 'getColCount' ) );
                            $( '#mt-row-info' ).val( wt.blockFormat( 'getRowCount' ) );
                            console.log( 'wt', wt.position() );
                        }
                    } );
                    cont.append( cutter );
                } else {
                    m_alert( dt.errorHead ? dt.errorHead : 'Ошибка', dt.errorText, true, false, function () {
                        self._hideBunner();
                    } );
                }
                console.log( d );
            } ).fail( function ( er1 ) {
                if ( $.type( er1.responseJSON ) === 'object' )
                    m_alert( er1.responseJSON.name, er1.responseJSON.message, true, false );
                else
                    m_alert( "Ошибка сервера", er1.responseText, true, false );
            } );
            ;
        }
    },
    __drawTypeBlock2: function ( tbd, num ) {
        let self = this;
        let dopId = num ? num : '';
        let key = ( dopId === '' ? '0' : ( '' + dopId ) );
        tbd.parent().removeAttr( 'class' );
        tbd.parent().addClass( 'block-body typeBlock2' );
        console.log( '__drawTypeBlock2', tbd, num );
        if ( this._productCategory === 0 || this._productCategory === 3 || this._productCategory === 5 ) {
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Блоков с листа:' ) ).append( $( '<span>' ).append( this._createInput( 'blocks_per_sheet' + dopId, {readonly: 'readonly'}, {
                change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ]
            } ).val( this.options.form.fields['blocks_per_sheet' + dopId] ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Формат блока:' ) ).append( $( '<span>' ).append( this._createInput( 'format_printing_block' + dopId, {readonly: 'readonly'} ).val( this.options.form.fields['format_printing_block' + dopId] ).change( {self: this}, function () {
                $( this ).val( self.__proceedStringToSizeArray( $( this ).val() ) );
            } ) ) ).append( $( '<span>' ).append( $( '<img>' ).attr( {src: 'pic/k1.png', title: 'Расчитать', id: 'blockRecalButton' + dopId} ).click( {self: this, dopId: dopId}, this._blockFormatter ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Шт. в блоке:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_products_in_block' + dopId, {readonly: 'readonly'}, {
                change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ]
            } ).val( this.options.form.fields['num_of_products_in_block' + dopId] ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Кол-во блоков:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_printing_block' + dopId, {readonly: 'readonly'} ).val( this.options.form.fields['num_of_printing_block' + dopId] ) ) ) );

        } else {
            tbd.append( $( '<div>' ).append( $( '<div>' ) ).append( $( '<span>' ).text( 'Блоков с листа:' ) ).append( $( '<span>' ).append( this._createInput( 'blocks_per_sheet' + dopId, {readonly: 'readonly'}, {
                change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ]
            } ).val( this.options.form.fields['blocks_per_sheet' + dopId] ) ) ).append( $( '<span>' ).append( $( '<img>' ).attr( {src: 'pic/k1.png', title: 'Расчитать'} ).click( {self: this, dopId: dopId}, this._liffCutter ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ) ).append( $( '<span>' ).text( 'Формат блока:' ) ).append( $( '<span>' ).append( this._createInput( 'format_printing_block' + dopId, {readonly: 'readonly'} ).val( this.options.form.fields['format_printing_block' + dopId] ).change( {self: this, dopId: dopId}, function () {
                $( this ).val( self.__proceedStringToSizeArray( $( this ).val() ) );
            } ) ) ).append( $( '<span>' ).append( $( '<img>' ).attr( {src: 'pic/k1.png', title: 'Расчитать', id: 'blockRecalButton' + dopId} ).click( {self: this, dopId: dopId}, this._blockFormatter ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ) ).append( $( '<span>' ).text( 'Шт. в блоке:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_products_in_block' + dopId, {readonly: 'readonly'}, {
                change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ]
            } ).val( this.options.form.fields['num_of_products_in_block' + dopId] ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ) ).append( $( '<span>' ).text( 'Кол-во блоков:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_printing_block' + dopId, {readonly: 'readonly'} ).val( this.options.form.fields['num_of_printing_block' + dopId] ) ) ) );

        }
    },

};

