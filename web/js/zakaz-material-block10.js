/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var block_paket_p = {
    __drawTypeBlock10: function ( tbd, ind ) {
        tbd.parent().removeAttr( 'class' );
        tbd.parent().addClass( 'block-body nastroiki-dlya-ppaketov' );
        let self = this;
        console.log( 'block_paket_p', ind, tbd );
        ind = $.type( ind ) !== 'undefined' ? ind : 0;
        let key = ind ? ( '' + ind ) : '';
        let total = parseInt( self._totalCount( key ) );
        let zapas = this.options.form.fields['block_zapas' + key] ? this.options.form.fields['block_zapas' + key] : 0;
        total = !isNaN( total ) ? total : 0;
        zapas = !isNaN( zapas ) ? zapas : 0;
        let lifCnt = total + zapas;

        tbd.append( $( '<div>' ).addClass( 'border' ).append( $( '<div>' ).text( 'Цвет:' ) ).append( $( '<span>' ).append( $( '<input>' ).attr( {
            id: 'block-color-info' + key,
            readonly: 'readonly',
            tabindex:this.options.tabIndex++
        } ).val( tbd.parent().prev().find( '[data-material-color]' ).text() ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Формат блока:' ) ).append( $( '<span>' ).append( this._createInput( 'format_printing_block' + key, {readonly: 'readonly',tabindex:this.options.tabIndex++} ).val( tbd.parent().prev().find( '[data-material-size]' ).text() ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Запас:' ) ).append( $( '<span>' )
                .append( this._createInput( 'block_zapas' + key ,{tabindex:this.options.tabIndex++}).val( zapas )
                .change( {self: this, index: ind}, this.__block10Recalc ) )
                ) );
        tbd.append( $( '<div>' )
                .append( $( '<div>' ).text( 'Кол-во блоков:' ) )
                .append( $( '<span>' ).append( this._createInput( 'num_of_printing_block' + key, {readonly: 'readonly',tabindex:this.options.tabIndex++}).val( this.options.form.fields['num_of_printing_block' + key] ? this.options.form.fields['num_of_printing_block' + key] : lifCnt ) ) )
                .append( $( '<span>' ).append( $( '<input>' ).attr( {
                    type: 'hidden',
                    id: 'blockRecalButton' + key
                } ).click( {self: this, index: ind}, self.__block10Recalc ) ) ) );
        this.__block10Recalc( {data: {self: this, index: ind}} );
    },
    ___block10Recalc_hasShown: false,
    ___block10Recalc: function ( ind, key, e ) {
        if ( !$( '#Zakaz-num_of_printing_block' + key ).length )
            return;
        let self = e.data.self;
        let total = parseInt( self._totalCount( key ) );
        let zapas = parseInt( $( '#Zakaz-block_zapas' + key ).val() );
        if ( isNaN( zapas ) ) {
            zapas = self.options.form.fields['block_zapas' + key] ? self.options.form.fields['block_zapas' + key] : 0;
        }
        total = !isNaN( total ) ? total : 0;
        zapas = !isNaN( zapas ) ? zapas : 0;
        let lifCnt = total + zapas;
        self.options.form.fields['num_of_printing_block' + key] = lifCnt;
        $( '#Zakaz-num_of_printing_block' + key ).val( lifCnt );
        if ( $( '[name="Zakaz[materials][' + ind + '][count]"]' ).length && !self.___block10Recalc_hasShown ) {
            if ( parseInt( $( '[name="Zakaz[materials][' + ind + '][count]"]' ).val() ) < lifCnt ) {
                self.___block10Recalc_hasShown = true;
                console.log( 'open for ', ind );
                m_alert( 'Ошибка при заполнение', 'Указано недостаточное количество материала.<br>По расчёту требуется ' + lifCnt + ' ' + $.fn.endingNums( lifCnt, [ 'пакет', 'пакета', 'пакетов' ] ) + ' ', {
                    label: 'Авто',
                    click: function () {
                        self.___block10Recalc_hasShown = false;
                        $( '[name="Zakaz[materials][' + ind + '][count]"]' ).val( lifCnt ).trigger( 'change' );
                    }
                }, {
                    label: 'Редактировать',
                    click: function () {
                        self.___block10Recalc_hasShown = false;
                        $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                        $( '[name="Zakaz[materials][' + ind + '][count]"]' ).trigger( 'focus' )
//                        .on( 'focusout', function () {
//                            $( this ).off( 'focusout' );
//                            e.data.self.__block11Recalc( e );
//                        } );
                    }} );
            } else if ( parseInt( $( '[name="Zakaz[materials][' + ind + '][count]"]' ).val() ) > ( lifCnt + 1 ) ) {
                self.___block10Recalc_hasShown = true;
                console.log( 'open for ', ind );
                m_alert( 'Ошибка при заполнение', 'Указано избыточное количество материала.<br>По расчёту требуется ' + lifCnt + ' ' + $.fn.endingNums( lifCnt, [ 'пакет', 'пакета', 'пакетов' ] ), {
                    label: 'Авто',
                    click: function () {
                        ___block10Recalc_hasShown = false;
                        $( '[name="Zakaz[materials][' + ind + '][count]"]' ).val( lifCnt ).trigger( 'change' );
                    }
                }, {
                    label: 'Редактировать',
                    click: function () {
                        self.___block10Recalc_hasShown = false;
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
    __block10Recalc: function ( e ) {
        console.log( '__block10Recalc' );
        e.data.self.___block10Recalc( 0, "", e );
        e.data.self.___block10Recalc( 1, "1", e );
    }
};

