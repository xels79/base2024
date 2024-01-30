/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var block_suvenirka = {
    __drawTypeBlock11: function ( tbd, ind ) {
        let self = this;
        ind = $.type( ind ) !== 'undefined' ? ind : 0;
        let key = ind ? ( '' + ind ) : '';
        tbd.parent().removeAttr( 'class' );
        tbd.parent().addClass( 'block-body typeBlock11' );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Состав:' ) ).append( $( '<span>' ).append( $( '<input>' ).attr( {
            id: 'block-sostav-info' + key,
            readonly: 'readonly'
        } ).val( $( '[data-material-sostav]' + key ).text() ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Мест нанесения:' ) ).append( $( '<span>' ).append( this._createInput( 'place_of_application_block' + key ).val( this.options.form.fields.place_of_application_block ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Запас:' ) ).append( $( '<span>' ).append( this._createInput( 'block_zapas' + key ).val( this.options.form.fields.block_zapas ).change( {self: this}, this.__block11Recalc ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Кол-во блоков:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_printing_block' + key, {readonly: 'readonly'} ).change( {self: this}, this.__block11Recalc ).val( $( '#Zakaz-number_of_copies' + key ).val().length ? $( '#Zakaz-number_of_copies' + key ).val() : 0 ) ) ) );
        this.__block11Recalc( {data: {self: this}} );
    },
    ___block11Recalc_hasShown: false,
    ____block11Recalc: function ( ind, key, e ) {
        if ( !$( '#Zakaz-num_of_printing_block' + key ).length )
            return;
        let total = parseInt( e.data.self._totalCount( key ) );
        let zapas = parseInt( $( '#Zakaz-block_zapas' + key ).val() );
        if ( isNaN( zapas ) || ( !zapas && zapas !== 0 ) ) {
            zapas = e.data.self.options.form.fields.block_zapas;
            $( '#Zakaz-block_zapas' + key ).val( e.data.self.options.form.fields['block_zapas' + key] );
        }
        total = !isNaN( total ) ? total : 0;
        zapas = !isNaN( zapas ) ? zapas : 0;
        let lifCnt = total + zapas;
        e.data.self.options.form.fields.num_of_printing_block = lifCnt;
        $( '#Zakaz-num_of_printing_block' + key ).val( lifCnt );
        if ( $( '[name="Zakaz[materials][' + ind + '][count]"]' ).length && !e.data.self.___block11Recalc_hasShown ) {
            if ( parseInt( $( '[name="Zakaz[materials][' + ind + '][count]"]' ).val() ) < lifCnt ) {
                e.data.self.___block11Recalc_hasShown = true;
                m_alert( 'Ошибка при заполнение', 'Указано недостаточное количество материала.<br>По расчёту требуется ' + lifCnt + ' ' + $.fn.endingNums( lifCnt, [ 'штука', 'штуки', 'штук' ] ) + ' ', {
                    label: 'Авто',
                    click: function () {
                        e.data.self.___block11Recalc_hasShown = false;
                        $( '[name="Zakaz[materials][' + ind + '][count]"]' ).val( lifCnt ).trigger( 'change' );
                    }
                }, {
                    label: 'Редактировать',
                    click: function () {
                        e.data.self.___block11Recalc_hasShown = false;
                        $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                        $( '[name="Zakaz[materials][' + ind + '][count]"]' ).trigger( 'focus' )
//                        .on( 'focusout', function () {
//                            $( this ).off( 'focusout' );
//                            e.data.self.__block11Recalc( e );
//                        } );
                    }} );
            } else if ( parseInt( $( '[name="Zakaz[materials][' + ind + '][count]"]' ).val() ) > ( lifCnt + 1 ) ) {
                e.data.self.___block11Recalc_hasShown = true;
                m_alert( 'Ошибка при заполнение', 'Указано избыточное количество материала.<br>По расчёту требуется ' + lifCnt + ' ' + $.fn.endingNums( lifCnt, [ 'штука', 'штуки', 'штук' ] ), {
                    label: 'Авто',
                    click: function () {
                        e.data.self.___block11Recalc_hasShown = false;
                        $( '[name="Zakaz[materials][' + ind + '][count]"]' ).val( lifCnt ).trigger( 'change' );
                    }
                }, {
                    label: 'Редактировать',
                    click: function () {
                        e.data.self.___block11Recalc_hasShown = false;
                        $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                        $( '[name="Zakaz[materials][' + ind + '][count]"]' ).trigger( 'focus' );
//                        .on( 'focusout', function () {
//                            $( this ).off( 'focusout' );
//                            e.data.self.__block11Recalc( e );
//                        } );
                    }} );
            }

        }
    },
    __block11Recalc: function ( e ) {
        console.log( '__block11Recalc' );
        e.data.self.____block11Recalc( 0, "", e );
        e.data.self.____block11Recalc( 1, "1", e );
    }
};

