/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var block_visitki = {
    __drawTypeBlock0: function ( tbd, num ) {
        console.log( '__drawTypeBlock0', tbd, num );
        let dopId = num ? num : '';
        let self = this;
        tbd.parent().removeAttr( 'class' );
        tbd.parent().addClass( 'block-body typeBlock0' );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Блоков с листа:' ) ).append( $( '<span>' ).append( this._createInput( 'blocks_per_sheet' + dopId, {readonly: 'readonly'}, {
            change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ]
        } ).val( this.options.form.fields['blocks_per_sheet' + dopId] ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Формат блока:' ) ).append( $( '<span>' ).append( this._createInput( 'format_printing_block' + dopId, {readonly: 'readonly'} ).val( this.options.form.fields['format_printing_block' + dopId] ).change( {self: this, dopId: dopId}, function ( e ) {
            $( this ).val( self.__proceedStringToSizeArray( $( this ).val() ) );
            self._blockRecalMainParam.call( this, e );
        } ) ) ).append( $( '<span>' ).append( $( '<img>' ).attr( {src: 'pic/k1.png', title: 'Расчитать', id: 'blockRecalButton' + dopId} ).click( {self: this, dopId: dopId}, this._blockFormatter0 ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Шт. в блоке:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_products_in_block' + dopId, {readonly: 'readonly'}, {
            change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ],
        } ).val( this.options.form.fields['num_of_products_in_block' + dopId] ).onlyNumeric() ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Кол-во блоков:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_printing_block' + dopId, {readonly: 'readonly'} ).val( this.options.form.fields['num_of_printing_block' + dopId] ) ) ) );
    },
    _blockAreaGetParam: function ( val ) {
        switch ( val ) {
            case 0:
                return {c: 2, r: 2};
                break;
            case 1:
                return {c: 4, r: 2};
                break;
            case 2:
                return {c: 5, r: 2};
                break;
            case 3:
                return {c: 6, r: 2};
                break;
            case 4:
                return {c: 3, r: 2};
                break;
            case 5:
                return {c: 1, r: 2};
                break;
            default:
                return {c: 0, r: 0};
        }
        ;
    },
    __vizitky_dt: null,
    __cutterData: null,
    _blockAreaClick: function ( e ) {
        let param = e.data.self._blockAreaGetParam( parseInt( $( this ).attr( 'data-key' ) ) ), productSZ = e.data.productSZ;
        let self = e.data.self;
        let dopId = e.data.dopId;
        let info = $( '#bf-info' ).length ? $( '#bf-info' ):$( '<h2>' ).attr( {
            id: 'bf-info'
        } ).css( {'z-index' : 10} ).append( $( '<span>' ).text( 'Формат блока:' ) ).append( $( '<span>' ) ).appendTo( $( this ).parent().parent().parent().parent() );
        $( 'area' ).each( function () {
            let data = $( this ).mouseout().data( 'maphilight' ) || {};
            data.alwaysOn = false;
            $( this ).data( 'maphilight', data ).trigger( 'alwaysOn.maphilight' );
            $( this ).removeAttr( 'data-selected' );
        } );
        let data = $( this ).mouseout().data( 'maphilight' ) || {};
        data.alwaysOn = !data.alwaysOn;
        $( this ).data( 'maphilight', data ).trigger( 'alwaysOn.maphilight' );
        $( this ).attr( 'data-selected', true );
        info.children( ':last-child' ).text( ( param.c * productSZ[0] + 6 + 2 * ( param.c - 1 ) ) + '*' + ( param.r * productSZ[1] + 6 + 2 * ( param.r - 1 ) ) + 'мм' );
        console.log( {top: info.parent().height() / 2 - info.height() / 2, left: info.parent().width() / 2 - info.width() / 2} );
        info.css( {top: info.parent().height() - info.height() - 30, left: info.parent().width() / 2 - 30} );
        $( '#zakaz_material_format_block0' ).maindialog( 'showBunner', 'Загрузка' );
        $.post( self.options.liffCutterUrl, {
            type_id: $( '[name="Zakaz[materials][0][type_id]"]' ).val(),
            mat_id: $( '[name="Zakaz[materials][0][mat_id]"]' ).val(),
            format_printing_block: ( param.c * productSZ[0] + 6 + 2 * ( param.c - 1 ) ) + '*' + ( param.r * productSZ[1] + 6 + 2 * ( param.r - 1 ) ),
            data: {
                mm: true,
                pW: 450
            }
        } ).done( function ( dt ) {
            console.log( dt );
            if ( self.__blockIsOpen ) {
                if ( !dt.error && ( !dt.status || dt.status == 200 ) ) {
                    $( '#zakaz_material_format_block0' ).maindialog( 'hideBunner' );
                    self.__vizitky_dt = dt;
                    let block = self._blockProceed( dopId );
                    let variant = block.block.var ? block.block.var : ( dt.data2.pcs > dt.data1.pcs ? 2 : 1 );
                    block.block.var = variant;
                    self.__cutterData = {data: dt, variant: variant};
                    self._blockProceed( dopId, block );
                    self.__cutterShouBody( variant, dt, $( '.cutter-info' ), true, function ( variant ) {
                        this.__cutterData.variant = variant;
                    }, dopId );
                } else {
                    $( '#zakaz_material_format_block0' ).maindialog( 'hideBunner' );
                    m_alert( 'Ошибка', dt.errorText ? dt.errorText : ( dt.error ? dt.error : ( dt.message ? dt.message : '' ) ), true, false );
                }
            }
        } ).fail( function ( er1 ) {
            if ( $.type( er1.responseJSON ) === 'object' )
                m_alert( er1.responseJSON.name, er1.responseJSON.message, true, false );
            else
                m_alert( "Ошибка сервера", er1.responseText, true, false );
        } );
    },
    _blockFormatter0: function ( e ) {
        let self = e.data.self, productSZ = [ ];
        let dopId = e.data.dopId ? e.data.dopId : '';
        if ( !$( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][type_id]"]' ).length || !$( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][mat_id]"]' ) ) {
            m_alert( 'Ошибка', 'Не выбран материал0', true, false );
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
            console.log( 'productSZ', productSZ );
            if ( productSZ.length === 2 && productSZ[0] > productSZ[1] ) {
                let tmpSz = productSZ[1];
                productSZ[1] = productSZ[0];
                productSZ[0] = tmpSz;
            }
            console.log( 'productSZ', productSZ );
            if ( productSZ.length !== 2 || !productSZ[0] || !productSZ[1] ) {
                m_alert( 'Ошибка', 'Неверное значение размер изделия<br>Должно быть <i>"<b>Ш*В</b>"</i>', true, false, function () {
                    $( '[href="#tab-pane-zakaz-page0"' ).trigger( 'click' );
                    $( '#Zakaz-product_size' ).focus();
                } );
                return;
            }
            let d = $( '<div>' ).attr( {
                id: 'zakaz_material_format_block0',
                title: 'Формат блока',
            } );
            let firstD = $( '<div class="select-side">' ).appendTo( d );
            firstD.append( $( '<img>' ).attr( {
                src: 'pic/vizitki-3.png',
                height: 430,
                usemap: '#bkf_select'
            } ).css( {'z-index': 11} ).on( 'load', function () {
                let info = $( '#bf-info' );
//                $().ready()
                if ( info.length )
                    info.css( {top: info.parent().height() - info.height() - 30, left: info.parent().width() / 2 - 30} );
                d.parent().css( 'top', $( document ).height() / 2 - d.parent().height() / 2 );
            } ) );
            d.append( $( '<div class="cutter-info">' ) );
            let clickData = {self: self, productSZ: productSZ, dopId: dopId};
            firstD.append( $( '<p>' ).css( {'z-index': 12} ).append( $( '<map>' ).attr( 'name', 'bkf_select' )
                    .append( $( '<area>' ).attr( {
                        shape: 'rect',
                        coords: '2,57,132,130',
                        nohref: '',
                        'data-key': 0
                    } ).click( clickData, self._blockAreaClick ) )
                    .append( $( '<area>' ).attr( {
                        shape: 'rect',
                        coords: '2,278,132,428',
                        nohref: '',
                        'data-key': 1
                    } ).click( clickData, self._blockAreaClick ) )
                    .append( $( '<area>' ).attr( {
                        shape: 'rect',
                        coords: '142,2,273,188',
                        nohref: '',
                        'data-key': 2
                    } ).click( clickData, self._blockAreaClick ) )
                    .append( $( '<area>' ).attr( {
                        shape: 'rect',
                        coords: '142,208,273,428',
                        nohref: '',
                        'data-key': 3
                    } ).click( clickData, self._blockAreaClick ) )
                    .append( $( '<area>' ).attr( {
                        shape: 'rect',
                        coords: '2,150,132,260',
                        nohref: '',
                        'data-key': 4
                    } ).click( clickData, self._blockAreaClick ) )
                    .append( $( '<area>' ).attr( {
                        shape: 'rect',
                        coords: '2,2,132,38',
                        nohref: '',
                        'data-key': 5
                    } ).click( clickData, self._blockAreaClick ) )
                    ) );

            d.maindialog( {
                minimizable: false,
                modal: true,
                width: 950,
                height: 'auto',
                buttons: [
                    {
                        text: "Ok",
                        icon: "ui-icon-heart",
                        click: function () {
                            let el = $( '[data-selected="true"]' );
                            if ( el.length ) {
                                let param = self._blockAreaGetParam( parseInt( el.attr( 'data-key' ) ) );
                                console.log( ( param.c * productSZ[0] + 6 + 2 * ( param.c - 1 ) ) + '*' + ( param.r * productSZ[1] + 6 + 2 * ( param.r - 1 ) ) );
                                if ( self.__vizitky_dt !== null ) {
                                    //Zakaz-material_block_format
                                    let block = self._blockProceed( dopId );
                                    console.log( 'okClick', self.__cutterData );
                                    if ( self.__cutterData
                                            && self.__cutterData.data
                                            && self.__cutterData.data['data' + self.__cutterData.variant]
                                            && self.__cutterData.data['data' + self.__cutterData.variant].lifSizes.wn > self.__cutterData.data['data' + self.__cutterData.variant].lifSizes.hn ) {
                                        block.block.blockWidth = ( param.r * productSZ[1] + 6 + 2 * ( param.r - 1 ) );
                                        block.block.blockHeight = ( param.c * productSZ[0] + 6 + 2 * ( param.c - 1 ) );
                                        //$( '#Zakaz-format_printing_block' + dopId ).val( ( param.r * productSZ[1] + 6 + 2 * ( param.r - 1 ) ) + '*' + ( param.c * productSZ[0] + 6 + 2 * ( param.c - 1 ) ) );
                                    } else {
                                        block.block.blockWidth = ( param.c * productSZ[0] + 6 + 2 * ( param.c - 1 ) );
                                        block.block.blockHeight = ( param.r * productSZ[1] + 6 + 2 * ( param.r - 1 ) );

                                    }
                                    block.block.var = block.block.var || 1;
                                    self._blockProceed( dopId, block );
                                    $( '#Zakaz-num_of_products_in_block' + dopId ).val( param.c * param.r );
                                    $( '#Zakaz-format_printing_block' + dopId ).val( ( param.c * productSZ[0] + 6 + 2 * ( param.c - 1 ) ) + '*' + ( param.r * productSZ[1] + 6 + 2 * ( param.r - 1 ) ) );
                                    //$( '#Zakaz-format_printing_block' + dopId ).val( block.block.blockWidth > block.block.blockHeight ? ( block.block.blockHeight + '*' + block.block.blockWidth ) : ( block.block.blockWidth + '*' + block.block.blockHeight ) );
                                    let name = 'data' + $( '#cutter-radio-inp' ).val();
                                    $( '#Zakaz-blocks_per_sheet' + dopId ).val( self.__vizitky_dt[name].pcs );
                                    if ( self._totalCount( dopId ) ) {
                                        let cnt = Math.ceil( self._totalCount( dopId ) / $( '#Zakaz-num_of_products_in_block' + dopId ).val() );
                                        if ( cnt < 1 )
                                            cnt = 1;
                                        cnt += Math.ceil( ( cnt / 100 ) * 20 );
                                        let lifCnt = Math.ceil( cnt / parseInt( $( '#Zakaz-blocks_per_sheet' + dopId ).val() ) );
                                    }
                                    d.maindialog( "close" );
                                    $( '#Zakaz-format_printing_block' + dopId ).trigger( 'change' );
                                } else {
                                }

//                                    }
//                                });
                            }

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
                    if ( e.data && $.isFunction( e.data.afterEnd ) ) {
                        e.data.afterEnd.call( self );
                    }
                },
                open: function () {
                    $( this ).parent().addClass( 'block-format-popup block-format-popup2' );
                    self.__blockIsOpen = true;
                    $( 'img[usemap]' ).maphilight();
                    $( 'area' ).each( function ( id, el ) {
                        let param = self._blockAreaGetParam( parseInt( $( this ).attr( 'data-key' ) ) );
                        console.log( ( param.c * productSZ[0] + 6 + 2 * ( param.c - 1 ) ) + '*' + ( param.r * productSZ[1] + 6 + 2 * ( param.r - 1 ) ) );
                        console.log( $( '#Zakaz-format_printing_block' + dopId ).val() );
                        let w = param.c * productSZ[0] + 6 + 2 * ( param.c - 1 );
                        let h = param.r * productSZ[1] + 6 + 2 * ( param.r - 1 );
                        if ( w + '*' + h === $( '#Zakaz-format_printing_block' + dopId ).val() || h + '*' + w === $( '#Zakaz-format_printing_block' + dopId ).val() ) {
                            $( this ).trigger( 'click', {self: self, productSZ: productSZ, doId: dopId} );
                        }
                    } );
                }
            } );
        }
    },
    _visitki_addClick: function ( mat, sruct ) {
        console.log( '_visitki_addClick', mat, sruct );
    }
};