/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var block_block = {
    __drawTypeBlock1: function ( tbd, num ) {
        let self = this;
        let dopId = num ? num : '';
        let key = ( dopId === '' ? '0' : ( '' + dopId ) );

        console.log( '__drawTypeBlock1', tbd, num );
        if ( this._productCategory === 0 ) {
            tbd.parent().removeAttr( 'class' );
            tbd.parent().addClass( 'block-body typeBlock1' );
            let blockInfo = $( '#Zakaz-material_block_format' + dopId ).length && $( '#Zakaz-material_block_format' + dopId ).val() !== '' ? JSON.parse( $( '#Zakaz-material_block_format' + dopId ).val() ) : {};
            if ( $.type( blockInfo.zapas ) === 'undefined' ) {
                blockInfo.zapas = this._totalCount( dopId ) / 100 * 20;
            }
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Блоков с листа:' ) ).append( $( '<span>' ).append( this._createInput( 'blocks_per_sheet' + dopId, {}, {
                change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ]
            } ).val( this.options.form.fields['blocks_per_sheet' + dopId] ) ) ).append( $( '<span>' ).append( $( '<img>' ).attr( {src: 'pic/k1.png', title: 'Расчитать', id: 'blockRecalButton' + dopId} ).click( {self: this, dopId: dopId}, this._liffCutter ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Формат блока:' ) ).append( $( '<span>' ).append( this._createInput( 'format_printing_block' + dopId, {} ).val( $.fn.formatProceed( this.options.form.fields['format_printing_block' + dopId] ) ).change( {self: this, dopId: dopId}, function ( e ) {
                let tmpSzBase = self.__proceedStringToSizeArray( $.fn.formatProceed( $( this ).val() ), true );
                let tmpSz = self.__proceedStringToSizeArray( $.fn.formatProceed( $( '#Zakaz-product_size' ).val() ), true );
                if ( $.type( tmpSz ) === 'array' && $.type( tmpSzBase ) === 'array' && tmpSz.length === tmpSzBase.length ) {
                    if ( ( tmpSz[0] > tmpSzBase[0] || tmpSz[1] > tmpSzBase[1] ) && ( tmpSz[1] > tmpSzBase[0] || tmpSz[0] > tmpSzBase[1] ) ) {
                        let el = this;
                        m_alert( 'Внимание!', 'Размер блока меньше размера изделия (' + tmpSz[0] + '*' + tmpSz[1] + ')', {
                            label: 'Закрыть',
                            click: function () {
                                $( el ).trigger( 'focus' );
                            }
                        }, false );
                    } else {
                        $( this ).val( self.__proceedStringToSizeArray( $.fn.formatProceed( $( this ).val() ) ) );
                        $( '#Zakaz-num_of_products_in_block'+key ).val( 0 );
                        console.log( e.relatedTarget );
                        self._blockRecalMainParam.call( this, e );
                    }
                } else {
                    $( this ).val( self.__proceedStringToSizeArray( $.fn.formatProceed( $( this ).val() ) ) );
                    $( '#Zakaz-num_of_products_in_block'+key ).val( 0 );
                    console.log( e.relatedTarget );
                    self._blockRecalMainParam.call( this, e );
                }
            } ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Шт. в блоке:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_products_in_block' + dopId, {}, {
                change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ],
            } ).val( this.options.form.fields['num_of_products_in_block' + dopId] ).onlyNumeric() ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Кол-во блоков:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_printing_block' + dopId, {} ).val( this.options.form.fields['num_of_printing_block' + dopId] ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Запас:' ) ).append( $( '<span>' ).append( this._createInput( '' + dopId, {id: 'zapasId' + dopId}, {
                change: [ {self: this, dopId: dopId}, function ( e ) {
                        let blockInfo = $( '#Zakaz-material_block_format' + dopId ).length && $( '#Zakaz-material_block_format' + dopId ).val() !== '' ? JSON.parse( $( '#Zakaz-material_block_format' + dopId ).val() ) : {};
                        blockInfo.zapas = parseInt( $( this ).val() );
                        if ( isNaN( blockInfo.zapas ) )
                            blockInfo.zapas = 0;
                        $( '#Zakaz-material_block_format' + dopId ).val( JSON.stringify( blockInfo ) );
                        self._blockRecalMainParam.call( this, e );
                    } ],
            } ).val( blockInfo.zapas ).onlyNumeric() ) ) );
            if ( $( '#Zakaz-material_block_format' + dopId ).length ) {
                $( '#Zakaz-material_block_format' + dopId ).val( JSON.stringify( blockInfo ) );//
            }
        } else if ( this._productCategory === 3 || this._productCategory === 4 || this._productCategory === 5 ) {
            tbd.parent().removeAttr( 'class' );
            tbd.parent().addClass( 'block-body typeBlock1_1' );
            tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Коментарий:' ) ) ).append( $( '<div>' ).append( $( '<span>' ).append( $( '<textarea>' ).attr( {
                name: this._createFieldName( 'colors' ) + '[rem][' + num + ']',
                rows: 5,
            } ).css( {width: '100%'} ).val( this.options.form.fields.colors.rem && this.options.form.fields.colors.rem[num] ? this.options.form.fields.colors.rem[num] : '' ) ) ) );
        } else {
            tbd.parent().removeAttr( 'class' );
            tbd.parent().addClass( 'block-body ypeBlock1' );
            tbd.append( $( '<div>' ).append( $( '<div>' ) ).append( $( '<span>' ).text( 'Блоков с листа:' ) ).append( $( '<span>' ).append( this._createInput( 'blocks_per_sheet' + dopId, {}, {
                change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ]
            } ).val( this.options.form.fields['blocks_per_sheet' + dopId] ) ) ).append( $( '<span>' ).append( $( '<img>' ).attr( {src: 'pic/k1.png', title: 'Расчитать', id: 'blockRecalButton' + dopId} ).click( {self: this, dopId: dopId}, this._liffCutter ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<span>' ) ).append( $( '<span>' ).text( 'Формат блока:' ) ).append( $( '<span>' ).append( this._createInput( 'format_printing_block' + dopId, {} ).val( this.options.form.fields['format_printing_block' + dopId] ).change( {self: this, dopId: dopId}, function ( e ) {
                $( this ).val( self.__proceedStringToSizeArray( $( this ).val() ) );
                self._blockRecalMainParam.call( this, e );
            } ) ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ) ).append( $( '<span>' ).text( 'Шт. в блоке:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_products_in_block' + dopId, {}, {
                change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ],
            } ).val( this.options.form.fields['num_of_products_in_block' + dopId] ).onlyNumeric() ) ) );
            tbd.append( $( '<div>' ).append( $( '<div>' ) ).append( $( '<span>' ).text( 'Кол-во блоков:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_printing_block' + dopId, {} ).val( this.options.form.fields['num_of_printing_block' + dopId] ) ) ) );
        }
    },
};