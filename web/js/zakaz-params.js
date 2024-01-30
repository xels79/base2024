/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var zakaz_params = {
    colors_tbl: null,
    post_p_tbl: null,
    colors_menu: [
        {'label': '0', linkOptions: {'data-key': 0}},
        {'label': 'CMYK', linkOptions: {'data-key': 1}},
        {'label': '1', linkOptions: {'data-key': 2}},
        {'label': '2', linkOptions: {'data-key': 3}},
        {'label': '3', linkOptions: {'data-key': 4}},
        {'label': '4', linkOptions: {'data-key': 5}},
        {'label': '5', linkOptions: {'data-key': 6}},
        {'label': '6', linkOptions: {'data-key': 7}},
        {'label': 'CMYK+1', linkOptions: {'data-key': 12}},
    ],
    _zakaz_params_pre_comment: function ( tab ) {
        var self = this;
        var body = $( '<tbody>' )
                .append( $( '<tr>' )
                        .append( $( '<th>' ).text( 'Вызов на печать:' ).addClass( 'allways-on' ) ).append( $( '<td>' ).addClass( 'allways-on' ).append( $.fn.renderDropDown( false, [
                    {label: 'Нет', linkOptions: {'data-key': 0}},
                    {label: 'Да', linkOptions: {'data-key': 1}},
                ], {
                    input_name: this._createFieldName( 'post_print_call_to_print' ),
                    input_id: 'call_to_print',
                    afterChange: function ( dt ) {
                        console.log( this );
                        if ( $( this ).attr( 'data-key' ) == '0' ) {
                            body.children( ':first-child' ).addClass( 'is-off' );
                            body.children( ':first-child' ).children( 'td' ).children( 'input' ).addClass( 'disabled' ).attr( 'readonly', true ).val( '' );
                        } else {
                            body.children( ':first-child' ).removeClass( 'is-off' );
                            body.children( ':first-child' ).children( 'td' ).children( 'input' ).removeClass( 'disabled' ).removeAttr( 'readonly' );
                        }
                    }
                } ) ) )
                        .append( $( '<th>' ).text( 'Имя:' ) )
                        .append( $( '<td>' ).append( this._createInput( 'post_print_agent_name', {tabindex: this.options.tabIndex++, readonly: this.options.form.fields.post_print_call_to_print == 0} ).addClass( this.options.form.fields.post_print_call_to_print == 0 ? 'disabled' : null ).val( this.options.form.fields.post_print_agent_name ) ) )
                        .append( $( '<th>' ).text( 'Тел:' ) )
                        .append( $( '<td>' ).append( this._createInput( 'post_print_agent_phone', {tabindex: this.options.tabIndex++, readonly: this.options.form.fields.post_print_call_to_print == 0} ).addClass( this.options.form.fields.post_print_call_to_print == 0 ? 'disabled' : null ).val( this.options.form.fields.post_print_agent_phone ) ) )
                        .append( $( '<th>' ).text( 'Фирма:' ) )
                        .append( $( '<td>' ).append( this._createInput( 'post_print_firm_name', {tabindex: this.options.tabIndex++, readonly: this.options.form.fields.post_print_firm_name == 0} ).addClass( this.options.form.fields.post_print_call_to_print == 0 ? 'disabled' : null ).val( this.options.form.fields.post_print_firm_name )/*$('<input>').attr({id:'post_print_agent_firm',readonly:'readonly'})*/ ) )
                        );
        if ( !this.options.form.fields.post_print_call_to_print ) {
            body.children( ':first-child' ).addClass( 'is-off' );
        }
        ;
        console.log( 'zakaz_params_draw_loaded_colors', $( '#gtoup-control-zak_id' ).children( '.activeDD' ).children( 'input' ) );
        var tbl = $( '<table>' ).addClass( 'table table-info-part table-info-part1' ).append( body );
        return $( '<div>' ).addClass( 'dialog-control-group-inline' ).append( tbl );
    },
    _zakaz_params_post_comment_uflak: function ( tab, termalLift ) {
        let body = $( '<tbody>' );
        let comentVal=this.options.form.fields.note ? this.options.form.fields.note : '';
        termalLift=termalLift?termalLift:false;
        console.log('_zakaz_params_post_comment_uflak',!this.options.z_id,termalLift);
        if (!comentVal && !this.options.z_id && termalLift){
            comentVal='Сито №40';
        }
        body.append( $( '<tr>' )
                .append( $( '<th>' ).text( 'Примечания к печати:' ).width( 200 ) )
                .append( $( '<td>' ).attr( 'colspan', 2 ).append( $( '<textarea>' ).attr( {
                    name: this._createFieldName( 'note' ),
                    rows:"6"
                } ).val( comentVal ) ) ) );
        body.append( $( '<tr>' )
                .append( $( '<th>' ).addClass( 'allways-on' ).text( 'Резка:' ) )
                .append( $( '<td>' ).width( 120 ).addClass( 'allways-on' ).append( $.fn.renderDropDown( false, [
                    {label: 'Нет', linkOptions: {'data-key': 0}},
                    {label: 'Да', linkOptions: {'data-key': 1}},
                ], {
                    dropup: true,
                    input_name: this._createFieldName( 'post_print_rezka' ),
                    input_id: 'postprint-rezka',
                    afterChange: function () {
                        if ( $( this ).attr( 'data-key' ) == '0' ) {
                            //body.children(':last-child').addClass('is-off');
                            //body.children(':last-child').children('td').children('input').attr('disabled','disabled').val('');
                        } else {
                            //body.children(':last-child').removeClass('is-off');
                            //body.children(':last-child').children('td').children('input').removeAttr('disabled');
                        }
                    }
                } ) ) )
                .append( $( '<td>' ).append( this._createInput( 'post_print_rezka_text' ).val( this.options.form.fields.post_print_rezka_text ) ) )
                );
        if ( !this.options.form.fields.post_print_rezka ) {
//            body.children(':last-child').addClass('is-off');
//            body.children(':last-child').children('td').children('input').attr('disabled','disabled').val('');
        }
        ;
        let tbl = $( '<table>' ).addClass( 'table table-info-part' ).append( body );
        tab.append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).width( '100%' ).append( tbl ) );

    },
    _zakaz_params_post_comment_pPaket: function ( tab ) {
        //alert('_productCategory: '+this._productCategory);
        console.log( '_zakaz_params_post_comment_pPaket' );
        let body = $( '<tbody>' )
                .append( $( '<tr>' ).addClass( this.options.form.fields.post_print_uf_lak ? '' : 'is-off' )
                        .append( $( '<td>' ).addClass( 'allways-on' ).attr( 'colspan', 3 ).append( $( '<div>' ).radioButton( {
                            values: {
                                0: 'Цвет по пантону',
                                1: 'Цвет примерный',
                                2: 'Цвет по образцу'
                            }, //post_print_color_fit
                            disabledItems: [ ],
                            defaultValue: this.options.form.fields.post_print_color_fit,
                            name: this._createFieldName( 'post_print_color_fit' )
                        } ) ) )
                        )
                .append( $( '<tr>' ).append( $( '<td>' ).attr( 'colspan', 3 ).html( "&nbsp;" ) ) )
                .append( $( '<tr>' ).addClass( this.options.form.fields.post_print_uf_lak ? '' : 'is-off' )
                        .append( $( '<th>' ).addClass( 'allways-on' ).text( 'Распаковка' ) ).append( $( '<td>' ).addClass( 'allways-on' ).append( $.fn.renderDropDown( false,
                        [
                            {label: 'Нет', linkOptions: {'data-key': 0}},
                            {label: 'Да', linkOptions: {'data-key': 1}},
                        ], {
                    input_name: this._createFieldName( 'post_print_uf_lak' ),
                    input_id: 'uf-lak',
                    afterChange: function ( t ) {
                        let td = $( this ).parent().parent().parent().parent().parent();
                        if ( $( this ).attr( 'data-key' ) == '0' ) {
                            if ( !td.parent().hasClass( 'is-off' ) ) {
                                m_alert( 'Внимание', 'На вкладке <b style="color:#AA0000;">"Исполнитель"</b> не забудте убрать <b style="color:#AA0000;">"Распаковку"</b>', true, false );
                            }
                            $( '#Zakaz-post_print_uf_lak_text' ).val( '' );
                            td.parent().addClass( 'is-off' );
                            td.next().children( 'input' ).attr( 'readonly', true ).val( '0.00' );
                        } else {
                            if ( td.parent().hasClass( 'is-off' ) ) {
                                m_alert( 'Внимание', 'На вкладке <b style="color:#AA0000;">"Исполнитель"</b> не забудте добавить <b style="color:#AA0000;">"Распаковку"</b>', true, false );
                            }
                            td.parent().removeClass( 'is-off' );
                            td.next().children( 'input' ).removeAttr( 'readonly' );
                        }
                    }
                } ) ) )
                        .append( $( '<td>' ).append( this._createInput( 'print_unpacking_coast', {
                            tabindex: this.options.tabIndex++,
                            readonly: this.options.form.fields.post_print_uf_lak ? false : true
                        } ).val( this.options.form.fields.print_unpacking_coast ) ) )
                        )
                .append( $( '<tr>' )
                        .append( $( '<th>' ).addClass( 'allways-on' ).text( 'Упаковка' ) ).append( $( '<td>' ).addClass( 'allways-on' ).append( $.fn.renderDropDown( false,
                        [
                            {label: 'Нет', linkOptions: {'data-key': 0}},
                            {label: 'Да', linkOptions: {'data-key': 1}},
                        ], {
                    input_name: this._createFieldName( 'post_print_thermal_lift' ),
                    input_id: 'thermal-lift',
                    afterChange: function () {
                        let td = $( this ).parent().parent().parent().parent().parent();
                        if ( $( this ).attr( 'data-key' ) == '0' ) {
                            if ( !td.parent().hasClass( 'is-off' ) ) {
                                m_alert( 'Внимание', 'На вкладке <b style="color:#AA0000;">"Исполнитель"</b> не забудте убрать <b style="color:#AA0000;">"Упаковку"</b>', true, false );
                            }
                            $( '#Zakaz-post_print_thermal_lift_text' ).val( '' );
                            td.parent().addClass( 'is-off' );
                            td.next().children( 'input' ).attr( 'readonly', true ).val( '0.00' );
                        } else {
                            if ( td.parent().hasClass( 'is-off' ) ) {
                                m_alert( 'Внимание', 'На вкладке <b style="color:#AA0000;">"Исполнитель"</b> не забудте добавить <b style="color:#AA0000;">"Упаковку"</b>', true, false );
                            }
                            td.parent().removeClass( 'is-off' );
                            td.next().children( 'input' ).removeAttr( 'readonly' );
                        }
                    }
                } ) ) )
                        .append( $( '<td>' ).append( this._createInput( 'print_packing_coast', {
                            readonly: this.options.form.fields.post_print_thermal_lift ? false : true
                        } ).val( this.options.form.fields.print_packing_coast ) ) )
                        //.append($('<td>').attr('colspan',3))
                        )
                .append( $( '<tr>' )
                        .append( $( '<th>' ).text( 'Примечания к печати:' ) )
                        .append( $( '<td>' ).attr( 'colspan', 3 ).append( $( '<textarea>' ).attr( {
                            name: this._createFieldName( 'note' )
                        } ).val( this.options.form.fields.note ? this.options.form.fields.note : '' ) ) )
                        );
        //.append($('<tr>').append($('<td>').attr('colspan',3).html("&nbsp;")));
        let tbl = $( '<table>' ).addClass( 'table table-info-part' ).append( body );
        tab.append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).css( 'width', '100%' ).append( tbl ) );

    },
    _zakaz_params_post_comment: function ( tab ) {

        console.log( '_zakaz_params_post_comment', this );
        console.log( '_zakaz_params_post_comment', $( '#Zakaz-production_id' ).attr( 'data-category' ), this._productCategory );
        let isSuvenir = false;
        if ( $.type( $( '#Zakaz-production_id' ).attr( 'data-category' ) ) !== 'undefined' )
            isSuvenir = $( '#Zakaz-production_id' ).attr( 'data-category' ) === '2';
        else
            isSuvenir = this._productCategory === 2;
        let footer = $( '<tfoot>' );
        let body = $( '<tbody>' )
                .append( $( '<tr>' ).addClass( this.options.form.fields.post_print_uf_lak ? '' : 'is-off' )
                        .append( $( '<td>' ).addClass( 'allways-on' ).attr( 'colspan', 3 ).append( $( '<div>' ).radioButton( {
                            values: {
                                0: 'Цвет по пантону',
                                1: 'Цвет примерный',
                                2: 'Цвет по образцу'
                            }, //post_print_color_fit
                            disabledItems: [ ],
                            defaultValue: this.options.form.fields.post_print_color_fit,
                            name: this._createFieldName( 'post_print_color_fit' )
                        } ) ) )
                        )
                .append( $( '<tr>' ).append( $( '<td>' ).attr( 'colspan', 3 ).html( "&nbsp;" ) ) )
                .append( $( '<tr>' ).addClass( this.options.form.fields.post_print_thermal_lift ? '' : 'is-off' )
                        .append( $( '<th>' ).addClass( 'allways-on' ).text( isSuvenir ? 'Распаковка' : 'Уф-лак:' ) ).append( $( '<td>' ).addClass( 'allways-on' ).append( $.fn.renderDropDown( false,
                        isSuvenir ? [
                            {label: 'Нет', linkOptions: {'data-key': 0}},
                            {label: 'Да', linkOptions: {'data-key': 1}},
                        ] : [
                    {label: 'Нет', linkOptions: {'data-key': 0}},
                    {label: '1+0', linkOptions: {'data-key': 1}},
                    {label: '1+1', linkOptions: {'data-key': 2}},
                    {label: '0+1', linkOptions: {'data-key': 3}},
                ], {
                    input_name: this._createFieldName( 'post_print_uf_lak' ),
                    input_id: 'uf-lak',
                    afterChange: function ( t ) {
                        let td = $( this ).parent().parent().parent().parent().parent();
                        if ( $( this ).attr( 'data-key' ) == '0' ) {
                            if ( isSuvenir && !td.parent().hasClass( 'is-off' ) ) {
                                m_alert( 'Внимание', 'На вкладке <b style="color:#AA0000;">"Исполнитель"</b> не забудте убрать <b style="color:#AA0000;">"Распаковку"</b>', true, false );
                            }
                            $( '#Zakaz-post_print_uf_lak_text' ).val( '' );
                            td.parent().addClass( 'is-off' );
                            td.next().children( 'input' ).attr( 'readonly', true ).val( isSuvenir ? '0.00' : '' );
                        } else {
                            if ( isSuvenir && td.parent().hasClass( 'is-off' ) ) {
                                m_alert( 'Внимание', 'На вкладке <b style="color:#AA0000;">"Исполнитель"</b> не забудте добавить <b style="color:#AA0000;">"Распаковку"</b>', true, false );
                            }
                            td.parent().removeClass( 'is-off' );
                            td.next().children( 'input' ).removeAttr( 'readonly' );
                        }
                    }
                } ) ) )
                        .append( $( '<td>' ).append( this._createInput( isSuvenir ? 'print_unpacking_coast' : 'post_print_uf_lak_text', {
                            tabindex: this.options.tabIndex++,
                            readonly: this.options.form.fields.post_print_uf_lak ? false : true
                        } ).val( isSuvenir ? ( this.options.form.fields.print_unpacking_coast ) : ( this.options.form.fields.post_print_uf_lak ? this.options.form.fields.post_print_uf_lak_text : '' ) ) ) )
                        )
                .append( $( '<tr>' )
                        .append( $( '<th>' ).addClass( 'allways-on' ).text( isSuvenir ? 'Упаковка' : 'Термоподъем:' ) ).append( $( '<td>' ).addClass( 'allways-on' ).append( $.fn.renderDropDown( false,
                        isSuvenir ? [
                            {label: 'Нет', linkOptions: {'data-key': 0}},
                            {label: 'Да', linkOptions: {'data-key': 1}},
                        ] : [
                    {label: 'Нет', linkOptions: {'data-key': 0}},
                    {label: '1+0', linkOptions: {'data-key': 1}},
                    {label: '1+1', linkOptions: {'data-key': 2}},
                    {label: '0+1', linkOptions: {'data-key': 3}},
                ], {
                    input_name: this._createFieldName( 'post_print_thermal_lift' ),
                    input_id: 'thermal-lift',
                    afterChange: function () {
                        let td = $( this ).parent().parent().parent().parent().parent();
                        if ( $( this ).attr( 'data-key' ) == '0' ) {
                            if ( isSuvenir && !td.parent().hasClass( 'is-off' ) ) {
                                m_alert( 'Внимание', 'На вкладке <b style="color:#AA0000;">"Исполнитель"</b> не забудте убрать <b style="color:#AA0000;">"Упаковку"</b>', true, false );
                            }
                            $( '#Zakaz-post_print_thermal_lift_text' ).val( '' );
                            td.parent().addClass( 'is-off' );
                            td.next().children( 'input' ).attr( 'readonly', true ).val( isSuvenir ? '0.00' : '' );
                        } else {
                            if ( isSuvenir && td.parent().hasClass( 'is-off' ) ) {
                                m_alert( 'Внимание', 'На вкладке <b style="color:#AA0000;">"Исполнитель"</b> не забудте добавить <b style="color:#AA0000;">"Упаковку"</b>', true, false );
                            }
                            td.parent().removeClass( 'is-off' );
                            td.next().children( 'input' ).removeAttr( 'readonly' );
                        }
                    }
                } ) ) )
                        .append( $( '<td>' ).append( this._createInput( isSuvenir ? 'print_packing_coast' : 'post_print_thermal_lift_text', {
                            readonly: this.options.form.fields.post_print_thermal_lift ? false : true
                        } ).val( isSuvenir ? ( this.options.form.fields.print_packing_coast ) : ( this.options.form.fields.post_print_thermal_lift ? this.options.form.fields.post_print_thermal_lift_text : '' ) ) ) )
                        //.append($('<td>').attr('colspan',3))
                        )
                .append( $( '<tr>' )
                        .append( $( '<th>' ).text( 'Примечания к печати:' ) )
                        .append( $( '<td>' ).attr( 'colspan', 3 ).append( $( '<textarea>' ).attr( {
                            name: this._createFieldName( 'note' )
                        } ).val( this.options.form.fields.note ? this.options.form.fields.note : '' ) ) )
                        );

        if ( !isSuvenir ) {
            body.append( $( '<tr>' ).append( $( '<td>' ).attr( 'colspan', 3 ).html( "&nbsp;" ) ) );
            body.append( $( '<tr>' )
                    .append( $( '<th>' ).addClass( 'allways-on' ).text( 'Резка:' ) )
                    .append( $( '<td>' ).addClass( 'allways-on' ).append( $.fn.renderDropDown( false, [
                        {label: 'Нет', linkOptions: {'data-key': 0}},
                        {label: 'Да', linkOptions: {'data-key': 1}},
                    ], {
                        dropup: true,
                        input_name: this._createFieldName( 'post_print_rezka' ),
                        input_id: 'postprint-rezka',
                        afterChange: function () {
                            if ( $( this ).attr( 'data-key' ) == '0' ) {
//                                    body.children(':last-child').addClass('is-off');
//                                    body.children(':last-child').children('td').children('input').attr('disabled','disabled').val('');
                            } else {
//                                    body.children(':last-child').removeClass('is-off');
//                                    body.children(':last-child').children('td').children('input').removeAttr('disabled');
                            }
                        }
                    } ) ) )
                    .append( $( '<td>' ).attr( 'colspan', 1 ).append( this._createInput( 'post_print_rezka_text' ).val( this.options.form.fields.post_print_rezka_text ) ) )
                    );
            if ( !this.options.form.fields.post_print_rezka ) {
//                body.children(':last-child').addClass('is-off');
            }
            ;
        }
        let tbl = $( '<table>' ).addClass( 'table table-info-part' ).append( body );
        tab.append( $( '<div>' ).addClass( 'dialog-control-group-inline' ).css( 'width', '100%' ).append( tbl ) );
    },
    _zakaz_params_tab: null,
    zakaz_params_redraw: function () {
        this._zakaz_params_tab.empty();
        this.zakaz_params_row( this._zakaz_params_tab );
        $( '#zakaz_printcut_prod_info' ).val( $( '#Zakaz-production_id' ).next().children( 'input:first-child' ).val() );
    },
    _zakaz_params_generate_suvenir_table: function ( tab ) {
        if ( $.type( this.options.form.fields.colors ) === 'string' )
            this.options.form.fields.colors = JSON.parse( this.options.form.fields.colors );
        console.log( '_zakaz_params_generate_suvenir_table', this.options.form.fields.colors );
        let tbl = $( '<table>' ).addClass( 'table table-colors table-colors2' ).append( $( '<tbody>' ) ), self = this;
        tbl.append( $( '<tr>' )
                .append( $( '<th>' ).attr( 'rowspan', 7 ).append( $( '<span>' ).text( 'Печать:' ) ) )
                .append( $( '<td>' ).append( $( '<input>' ).attr( {
                    name: this._createFieldName( 'colors' ) + '[face_id_txt]',
                    placeholder: 'Место нанесения'
                } ).val( this.options.form.fields.colors.face_id_txt ? this.options.form.fields.colors.face_id_txt : '' ) ) )
                .append( $( '<td>' ).append( $.fn.renderDropDown( false, this.colors_menu, {
//                    dropup:true,
                    input_name: this._createFieldName( 'colors' ) + '[face_id]',
                    input_id: 'colors_face',
                    afterChange: function () {
                        self._zakaz_params_draw_loaded_colors_2();
                    },
                    parentWin: {
                        offset: tab.parent().offset(),
                        scroll: tab.parent().scroll(),
                        h: tab.parent().height(),
                    }
                } ) ) )
                );
        tbl.append( $( '<tr>' ).append( $( '<td>' ).attr( 'colspan', 2 ).append( $( '<span>' ).addClass( 'btn' ).text( '+' ).click( function () {
            console.log( $( this ).parent().next() );
            if ( $( this ).parent().parent().next().attr( 'style' ) ) {
                $( this ).parent().parent().next().removeAttr( 'style' );
                $( this ).parent().parent().next().next().removeAttr( 'style' );
            }
        } ) ) ) );
        tbl.append( $( '<tr>' ).css( ( typeof this.options.form.fields.colors.back_id === 'undefined' || parseInt( this.options.form.fields.colors.back_id ) < 1 )
                && ( typeof this.options.form.fields.colors.side_id === 'undefined' || parseInt( this.options.form.fields.colors.side_id ) < 1 )
                && ( typeof this.options.form.fields.colors.clips_id === 'undefined' || parseInt( this.options.form.fields.colors.clips_id ) < 1 ) ? {display: 'none'} : {} )
                .append( $( '<td>' ).append( $( '<input>' ).attr( {
                    name: this._createFieldName( 'colors' ) + '[back_id_txt]',
                    placeholder: 'Место нанесения'
                } ).val( this.options.form.fields.colors.back_id_txt ? this.options.form.fields.colors.back_id_txt : '' ) ) )
                .append( $( '<td>' ).append( $.fn.renderDropDown( false, this.colors_menu, {
//                    dropup:true,
                    input_name: this._createFieldName( 'colors' ) + '[back_id]',
                    input_id: 'colors_back',
                    afterChange: function ( ) {
                        let p = $( this ).parent( ).parent( ).parent( ).parent( ).parent( ).parent( );
                        if ( $( this ).attr( 'data-key' ) == '0' && p.next( ).next( ).attr( 'style' ) ) {
                            p.css( 'display', 'none' );
                            p.next( ).css( 'display', 'none' );
                        }
                        self._zakaz_params_draw_loaded_colors_2( );
                    }
                } ) ) )
                );
        tbl.append( $( '<tr>' ).css( ( typeof this.options.form.fields.colors.back_id === 'undefined' || parseInt( this.options.form.fields.colors.back_id ) < 1 )
                && ( typeof this.options.form.fields.colors.side_id === 'undefined' || parseInt( this.options.form.fields.colors.side_id ) < 1 )
                && ( typeof this.options.form.fields.colors.clips_id === 'undefined' || parseInt( this.options.form.fields.colors.clips_id ) < 1 ) ? {display: 'none'} : {} ).append( $( '<td>' ).attr( 'colspan', 2 ).append( $( '<span>' ).addClass( 'btn' ).text( '+' ).click( function () {
            console.log( $( this ).parent().next() );
            if ( $( this ).parent().parent().next().attr( 'style' ) ) {
                $( this ).parent().parent().next().removeAttr( 'style' );
                $( this ).parent().parent().next().next().removeAttr( 'style' );
            }
        } ) ) ) );
        tbl.append( $( '<tr>' ).css( ( typeof this.options.form.fields.colors.side_id === 'undefined' || parseInt( this.options.form.fields.colors.side_id ) < 1 )
                && ( typeof this.options.form.fields.colors.clips_id === 'undefined' || parseInt( this.options.form.fields.colors.clips_id ) < 1 ) ? {display: 'none'} : {} )
                .append( $( '<td>' ).append( $( '<input>' ).attr( {
                    name: this._createFieldName( 'colors' ) + '[side_id_txt]',
                    placeholder: 'Место нанесения'
                } ).val( this.options.form.fields.colors.side_id_txt ? this.options.form.fields.colors.side_id_txt : '' ) ) )
                .append( $( '<td>' ).append( $.fn.renderDropDown( false, this.colors_menu, {
//                    dropup:true,
                    input_name: this._createFieldName( 'colors' ) + '[side_id]',
                    input_id: 'colors_side',
                    afterChange: function ( ) {
                        let p = $( this ).parent( ).parent( ).parent( ).parent( ).parent( ).parent( );
                        if ( $( this ).attr( 'data-key' ) == '0' && p.next( ).next( ).attr( 'style' ) ) {
                            p.css( 'display', 'none' );
                            p.next( ).css( 'display', 'none' );
                        }
                        self._zakaz_params_draw_loaded_colors_2( );
                    }
                } ) ) )
                );
        tbl.append( $( '<tr>' ).css( ( typeof this.options.form.fields.colors.side_id === 'undefined' || parseInt( this.options.form.fields.colors.side_id ) < 1 )
                && ( typeof this.options.form.fields.colors.clips_id === 'undefined' || parseInt( this.options.form.fields.colors.clips_id ) < 1 ) ? {display: 'none'} : {} ).append( $( '<td>' ).attr( 'colspan', 2 ).append( $( '<span>' ).addClass( 'btn' ).text( '+' ).click( function () {
            console.log( $( this ).parent().next() );
            if ( $( this ).parent().parent().next().attr( 'style' ) ) {
                $( this ).parent().parent().next().removeAttr( 'style' );
            }
        } ) ) ) );
        tbl.append( $( '<tr>' ).css( typeof this.options.form.fields.colors.clips_id === 'undefined' || parseInt( this.options.form.fields.colors.clips_id ) < 1 ? {display: 'none'} : {} )
                .append( $( '<td>' ).append( $( '<input>' ).attr( {
                    name: this._createFieldName( 'colors' ) + '[clips_id_txt]',
                    placeholder: 'Место нанесения'
                } ).val( this.options.form.fields.colors.clips_id_txt ? this.options.form.fields.colors.clips_id_txt : '' ) ) )
                .append( $( '<td>' ).append( $.fn.renderDropDown( false, this.colors_menu, {
//                    dropup:true,
                    input_name: this._createFieldName( 'colors' ) + '[clips_id]',
                    input_id: 'colors_clips',
                    afterChange: function () {
                        if ( $( this ).attr( 'data-key' ) == '0' ) {
                            let p = $( this ).parent().parent().parent().parent().parent().parent();
                            p.css( 'display', 'none' );
                        }
                        self._zakaz_params_draw_loaded_colors_2();
                    }
                } ) ) )
                );
        return tbl;
    },
    _zakaz_params_generate_uflak_table: function ( tab, isTermalLift ) {
        console.log( '_zakaz_params_generate_uflak_table' );
        let tbl = $( '<table>' ).width( '100%' ).addClass( 'table table-colors' ), self = this;
        let body = $( '<tbody>' );
        if ( $.type( this.options.form.fields.colors ) === 'string' )
            this.options.form.fields.colors = JSON.parse( this.options.form.fields.colors );
        body.append( $( '<tr>' ).addClass( 't-padding-border' )
                .append( $( '<th>' ).text( 'Печать:' ) )
                .append( $( '<td>' ).width( 48 ) )
                .append( $( '<td>' ) )
                .append( $( '<th>' ).text( 'Подложка:' ) )
                .append( $( '<td>' ) )
                .append( $( '<td>' ).css( 'min-width', '100px' ) )
                .append( $( '<td>' ).css( 'min-width', '10px' ) )
                .append( $( '<td>' ).addClass( 'padd bord bl' ).text( 'Ламинация:' ) )
                .append( $( '<td>' ).addClass( 'bord' ).append( $.fn.renderDropDown( false, [
                    {label: 'Нет', linkOptions: {'data-key': 0}},
                    {label: 'Да', linkOptions: {'data-key': 1}},
                ], {
                    input_name: this._createFieldName( 'colors' ) + '[lam1_id]',
                    input_id: 'lam1_id',
                    input_val: this.options.form.fields.colors.lam1_id,
                    left: true,
                    afterChange: function () {
                        console.log( 'chg' );
                        if ( $( this ).attr( 'data-key' ) === '0' ) {
                            $( '#lam2_id' ).parent().css( 'visibility', 'hidden' );
                            $( '#' + self._createFieldId( 'lam_tex' ) ).css( 'visibility', 'hidden' ).attr( 'disabled', true );
                        } else {
                            $( '#lam2_id' ).parent().removeAttr( 'style' );
                            $( '#' + self._createFieldId( 'lam_tex' ) ).css( 'visibility', 'visible' ).removeAttr( 'disabled' );
                        }
                    }
                } ) ) )
                .append( $( '<td>' ).addClass( 'br bord' ).append( $.fn.renderDropDown( false, [
                    {label: '1+0', linkOptions: {'data-key': 0}},
                    {label: '1+1', linkOptions: {'data-key': 1}},
                    {label: '0+1', linkOptions: {'data-key': 2}},
                ], {
                    input_name: this._createFieldName( 'colors' ) + '[lam2_id]',
                    input_id: 'lam2_id',
                    input_val: this.options.form.fields.colors.lam2_id,
                    left: true,
                    css: {
                        visibility: !this.options.form.fields.colors.lam1_id || this.options.form.fields.colors.lam1_id == 0 ? 'hidden' : 'visible'
                    }
                } ) ) )
                );
        //Zakaz[colors][face_values][0]
        //Zakaz[colors][face_id]
        //back_id
        body.append(
                $( '<tr>' ).addClass( 't-padding-border' )
                .append( $( '<td>' ).attr( 'colspan', 2 ).append( $.fn.renderDropDown( false, [
                    {label: '0', linkOptions: {'data-key': 0}},
                    {label: '1', linkOptions: {'data-key': 1}},
                ], {
                    input_name: this._createFieldName( 'colors' ) + '[face_id]',
                    input_id: 'face_id',
                    input_val: this.options.form.fields.colors.face_id,
                    afterChange: function () {
                        console.log( $( this ).attr( 'data-key' ) );
                        if ( $( this ).attr( 'data-key' ) === '0' ) {
                            $( '#' + self._createFieldId( 'face_values0' ) ).attr( 'disabled', true ).val( '' );
                        } else {
                            $( '#' + self._createFieldId( 'face_values0' ) ).removeAttr( 'disabled' );
                        }
                    }
                } ) ) )
                .append( $( '<td>' ).append( $( '<input>' ).attr( {
                    type: 'text',
                    name: this._createFieldName( 'colors' ) + '[face_values][0]',
                    id: this._createFieldId( 'face_values0' ),
                    disabled: !this.options.form.fields.colors.face_id || this.options.form.fields.colors.face_id == 0 ? true : false
                } ).val( this.options.form.fields.colors.face_values ? this.options.form.fields.colors.face_values[0] : '' ) ) )
                .append( $( '<td>' ).append( $.fn.renderDropDown( false, [
                    {label: 'Нет', linkOptions: {'data-key': 0}},
                    {label: 'Да', linkOptions: {'data-key': 1}},
                ], {
                    input_name: this._createFieldName( 'colors' ) + '[back_id]',
                    input_id: 'back_id',
                    input_val: this.options.form.fields.colors.back_id,
                    afterChange: function () {
                        if ( $( this ).attr( 'data-key' ) === '0' ) {
                            $( '#' + self._createFieldId( 'back_id' ) ).attr( 'disabled', true ).val( '' );
                        } else {
                            $( '#' + self._createFieldId( 'back_id' ) ).removeAttr( 'disabled' );
                        }
                    }

                } ) ) )
                .append( $( '<td>' ).append( $( '<input>' ).attr( {
                    type: 'text',
                    name: this._createFieldName( 'colors' ) + '[back_values][0]',
                    id: self._createFieldId( 'back_id' ),
                    disabled: !this.options.form.fields.colors.back_id || this.options.form.fields.colors.back_id == 0 ? true : false
                } ).val( this.options.form.fields.colors.back_values ? this.options.form.fields.colors.back_values[0] : '' ) ) )
                .append( $( '<td>' ) )
                .append( $( '<td>' ) )
                .append( $( '<td>' ).addClass( 'bl' ) )
                .append( $( '<td>' ).addClass( 'br' ).attr( 'colspan', 2 ).append( $( '<input>' ).css( 'width', '100%' ).attr( {
                    type: 'text',
                    name: this._createFieldName( 'colors' ) + '[lam_text]',
                    id: self._createFieldId( 'lam_tex' ),
                } ).css( {
                    visibility: !this.options.form.fields.colors.lam1_id || this.options.form.fields.colors.lam1_id == 0 ? 'hidden' : 'visible'
                } ).val( this.options.form.fields.colors.lam_text ? this.options.form.fields.colors.lam_text : '' ).css( 'margin', '0' ) ) )

                );
        body.append(
                $( '<tr>' ).addClass( 't-padding-border' )
                .append( $( '<td>' ).css( 'text-align', 'center' ).attr( 'colspan', 2 ).text( '+' ) )
                .append( $( '<td>' ) )
                .append( $( '<td>' ).css( 'text-align', 'center' ).text( '+' ) )
                .append( $( '<td>' ).attr( 'colspan', 2 ) )
                .append( $( '<td>' ) )
                .append( $( '<td>' ).addClass( 'br bl' ).attr( 'colspan', 3 ) )
                );
        body.append(
                $( '<tr>' ).addClass( 't-padding-border' )
                .append( $( '<td>' ).attr( 'colspan', 2 ).append( $.fn.renderDropDown( false, [
                    {label: '0', linkOptions: {'data-key': 0}},
                    {label: '1', linkOptions: {'data-key': 1}},
                ], {
                    input_name: this._createFieldName( 'colors' ) + '[side_id]',
                    input_id: 'side_id',
                    input_val: this.options.form.fields.colors.side_id,
                    afterChange: function () {
                        if ( $( this ).attr( 'data-key' ) === '0' ) {
                            $( '#' + self._createFieldId( 'side_values0' ) ).attr( 'disabled', true ).val( '' );
                        } else {
                            $( '#' + self._createFieldId( 'side_values0' ) ).removeAttr( 'disabled' );
                        }
                    }
                } ) ) )
                .append( $( '<td>' ).append( $( '<input>' ).attr( {
                    type: 'text',
                    name: this._createFieldName( 'colors' ) + '[side_values][0]',
                    id: self._createFieldId( 'side_values0' ),
                    disabled: !this.options.form.fields.colors.side_id || this.options.form.fields.colors.side_id == 0 ? true : false
                } ).val( this.options.form.fields.colors.side_values ? this.options.form.fields.colors.side_values[0] : '' ) ) )
                .append( $( '<td>' ).append( $.fn.renderDropDown( false, [
                    {label: 'Нет', linkOptions: {'data-key': 0}},
                    {label: 'Да', linkOptions: {'data-key': 1}},
                ], {
                    input_name: this._createFieldName( 'colors' ) + '[clips_id]',
                    input_id: 'clips_id',
                    input_val: this.options.form.fields.colors.clips_id,
                    afterChange: function () {
                        if ( $( this ).attr( 'data-key' ) === '0' ) {
                            $( '#' + self._createFieldId( 'clips_values0' ) ).attr( 'disabled', true ).val( '' );
                        } else {
                            $( '#' + self._createFieldId( 'clips_values0' ) ).removeAttr( 'disabled' );
                        }
                    }
                } ) ) )
                .append( $( '<td>' ).append( $( '<input>' ).attr( {
                    type: 'text',
                    name: this._createFieldName( 'colors' ) + '[clips_values][0]',
                    id: self._createFieldId( 'clips_values0' ),
                    disabled: !this.options.form.fields.colors.clips_id || this.options.form.fields.colors.clips_id == 0 ? true : false
                } ).val( this.options.form.fields.colors.clips_values ? this.options.form.fields.colors.clips_values[0] : '' ) ) )
                .append( $( '<td>' ) )
                .append( $( '<td>' ) )
                .append( $( '<td>' ).css( 'text-align', 'center' ).addClass( 'bl' ).text( 'Способ печати:' ) )
                .append( $( '<td>' ).addClass( 'br' ).attr( 'colspan', 2 ).append( $.fn.renderDropDown( false, [
                    {label: 'Нет', linkOptions: {'data-key': 0}},
                    {label: 'Цифра', linkOptions: {'data-key': 1}},
                    {label: 'Офсет', linkOptions: {'data-key': 2}},
                    {label: 'Шёлкография', linkOptions: {'data-key': 3}},
                ], {
                    input_name: this._createFieldName( 'colors' ) + '[medtodofprint_id]',
                    input_id: 'medtodofprint_id',
                    input_val: this.options.form.fields.colors.medtodofprint_id ? this.options.form.fields.colors.medtodofprint_id : 0,
                    left: true
                } ).css( 'margin', '0 0 0 auto' ) ) )
                );
        body.append(
                $( '<tr>' ).addClass( 't-padding-border' )
                .append( $( '<th>' ).attr( 'colspan', 2 ).text( 'Верный угол:' ) )
                .append( $( '<td>' ).append( $.fn.renderDropDown( false, [
                    {label: 'Нет', linkOptions: {'data-key': 0}},
                    {label: 'Да', linkOptions: {'data-key': 1}},
                ], {
                    input_name: this._createFieldName( 'colors' ) + '[ang_id]',
                    input_id: 'ang_id',
                    input_val: this.options.form.fields.colors.ang_id
                } ) ) )
                .append( $( '<td>' ).attr( 'colspan', 3 ).append( $( '<input>' ).attr( {
                    type: 'text',
                    name: this._createFieldName( 'colors' ) + '[ang_values][0]',
                    style: 'width:100%'
                } ).val( this.options.form.fields.colors.ang_values ? this.options.form.fields.colors.ang_values[0] : '' ) ) )
                .append( $( '<td>' ) )
                .append( $( '<td>' ).css( 'text-align', 'center' ).addClass( 'bl bord' ).text( 'Кол-во листов:' ) )
                .append( $( '<td>' ).addClass( 'br bord' ).attr( 'colspan', 2 ).css( 'text-align', 'right' ).append( $( '<input>' ).attr( {
                    type: 'text',
                    id: 'uflak-tirage',
                    //name:this._createFieldName('colors')+'[ang_values][0]',
//                    style:'width:100%'
                } ).val( self.options.form.fields.number_of_copies ? ( parseInt( self.options.form.fields.number_of_copies ) + 100 ) : 0 ).css( 'margin', '0 0 0 auto' ) ) )
                )
        tbl.append( body );
        return tbl;
    },
    _zakaz_params_generate_standart_table: function ( tab ) {
		console.log('_zakaz_params_generate_standart_table');
        let tbl = $( '<table>' ).addClass( 'table table-colors' ).append( $( '<tbody>' ) ), self = this;
        tbl.append( $( '<tr>' )
                .append( $( '<th>' ).attr( 'rowspan', 3 ).append( $( '<span>' ).text( 'Печать:' ) ) )
                .append( $( '<th>' ).text( 'Пантоны:' ) )
                .append( $( '<td>' ).append( $.fn.renderDropDown( false, this.colors_menu, {
//                    dropup:true,
                    input_name: this._createFieldName( 'colors' ) + '[face_id]',
                    input_id: 'colors_face',
                    afterChange: function () {
                        self._zakaz_params_draw_loaded_colors();
                    },
                    parentWin: {
                        offset: tab.parent().offset(),
                        scroll: tab.parent().scroll(),
                        h: tab.parent().height(),
                    }
                } ) ) )
                );
        tbl.append( $( '<tr>' ).append( $( '<td>' ).attr( 'colspan', 2 ).text( '+' ) ) );
        tbl.append( $( '<tr>' )
                .append( $( '<th>' ).text( 'Пантоны:' ) )
                .append( $( '<td>' ).append( $.fn.renderDropDown( false, this.colors_menu, {
//                    dropup:true,
                    input_name: this._createFieldName( 'colors' ) + '[back_id]',
                    input_id: 'colors_back',
                    afterChange: function () {
                        self._zakaz_params_draw_loaded_colors();
                    }
                } ) ) )
                );
        return tbl;
    },
    zakaz_params_addPakUnPak: function () {

    },
    zakaz_params_row: function ( tab ) {
        this._zakaz_params_tab = tab;
        let isSuvenir = false, isUflak = false, isTermalLift = false;
        if ( $.type( $( '#Zakaz-production_id' ).attr( 'data-category' ) ) !== 'undefined' ) {
            isSuvenir = $( '#Zakaz-production_id' ).attr( 'data-category' ) === '2';
            isUflak = $( '#Zakaz-production_id' ).attr( 'data-category' ) === '3';
            isTermalLift = $( '#Zakaz-production_id' ).attr( 'data-category' ) === '5';
        } else {
            isSuvenir = this._productCategory === 2;
            isUflak = this._productCategory === 3;
            isTermalLift = this._productCategory === 5;
        }
        let prod_info = $( '<input>' ).addClass( 'dialog-form-control' ).width( 220 ).attr( {disabled: true, id: 'zakaz_printcut_prod_info', readonly: 'readonly'} ).val( '' );
        let copies_info = $( '<input>' ).addClass( 'dialog-form-control' ).width( 100 ).attr( {disabled: true, id: 'zakaz_printcut_copies_info', readonly: 'readonly'} ).val( $( '#Zakaz-number_of_copies' ).val() );
        let copies_info2 = $( '<input>' ).addClass( 'dialog-form-control' ).width( 100 ).attr( {disabled: true, id: 'zakaz_printcut_copies_info1', readonly: 'readonly'} ).val( $( '#Zakaz-number_of_copies1' ).val() );
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
        //_zakaz_params_generate_uflak_table
        let tbl = isSuvenir ? this._zakaz_params_generate_suvenir_table( tab ) : ( isUflak || isTermalLift ? this._zakaz_params_generate_uflak_table( tab, isTermalLift ) : this._zakaz_params_generate_standart_table( tab ) );
        this.colors_tbl = tbl.children( 'tbody' );
        console.log( this.options.form );

        tab.append( $( '<div>' ).addClass( 'dialog-row params' ).append( this._zakaz_params_pre_comment( tab ) ) );
        tab.append( $( '<div>' ).addClass( 'dialog-row params' ).append( $( '<div>' ).addClass( 'dialog-control-group' ).width( isUflak || isTermalLift ? 'auto' : '' ).append( tbl ) ) );
        if ( this._productCategory === 1 ) {
            tab.append( $( '<div>' ).addClass( 'dialog-row params' ).append( this._zakaz_params_post_comment_pPaket( tab ) ) );
        } else {
            tab.append( $( '<div>' ).addClass( 'dialog-row params' ).append( isUflak || isTermalLift ? this._zakaz_params_post_comment_uflak( tab, isTermalLift ) : this._zakaz_params_post_comment( tab ) ) );
        }
        if ( this.options.form.fields.colors ) {
            if ( $.type( this.options.form.fields.colors ) !== 'object' && $.type( this.options.form.fields.colors ) !== 'array' )
                this.options.form.fields.colors = JSON.parse( this.options.form.fields.colors );
            if ( $.type( this.options.form.fields.colors.face_id ) !== 'undefined' ) {
                let face_id = this.options.form.fields.colors.face_id;
                $( '#colors_face' )
                        .val( face_id )
                        .next().text( this._zakaz_params_find_in_ul( $( '#colors_face' ).next().next().children( 'ul' ), face_id ) );
            }
            if ( $.type( this.options.form.fields.colors.back_id ) !== 'undefined' ) {
                let back_id = this.options.form.fields.colors.back_id;
                $( '#colors_back' )
                        .val( back_id )
                        .next().text( this._zakaz_params_find_in_ul( $( '#colors_back' ).next().next().children( 'ul' ), back_id ) );
            }
            if ( $.type( this.options.form.fields.colors.side_id ) !== 'undefined' ) {
                let side_id = this.options.form.fields.colors.side_id;
                $( '#colors_side' )
                        .val( side_id )
                        .next().text( this._zakaz_params_find_in_ul( $( '#colors_side' ).next().next().children( 'ul' ), side_id ) );
            }
            if ( $.type( this.options.form.fields.colors.clips_id ) !== 'undefined' ) {
                let clips_id = this.options.form.fields.colors.clips_id;
                $( '#colors_clips' )
                        .val( clips_id )
                        .next().text( this._zakaz_params_find_in_ul( $( '#colors_clips' ).next().next().children( 'ul' ), clips_id ) );
            }
            if ( !isUflak && !isTermalLift ) {
                if ( isSuvenir )
                    this._zakaz_params_draw_loaded_colors_2(isTermalLift);
                else
                    this._zakaz_params_draw_loaded_colors();
            }
        } else {
            this.options.form.fields.colors = {};
        }
        $( '#call_to_print' )
                .val( this.options.form.fields.post_print_call_to_print )
                .next().text( this._zakaz_params_find_in_ul( $( '#call_to_print' ).next().next().children( 'ul' ), this.options.form.fields.post_print_call_to_print ) );
        $( '#uf-lak' )
                .val( this.options.form.fields.post_print_uf_lak )
                .next().text( this._zakaz_params_find_in_ul( $( '#uf-lak' ).next().next().children( 'ul' ), this.options.form.fields.post_print_uf_lak ) );
        $( '#thermal-lift' )
                .val( this.options.form.fields.post_print_thermal_lift )
                .next().text( this._zakaz_params_find_in_ul( $( '#thermal-lift' ).next().next().children( 'ul' ), this.options.form.fields.post_print_thermal_lift ) );
        $( '#postprint-rezka' )
                .val( this.options.form.fields.post_print_rezka )
                .next().text( this._zakaz_params_find_in_ul( $( '#postprint-rezka' ).next().next().children( 'ul' ), this.options.form.fields.post_print_rezka ) );
        if ( isSuvenir )
            this.zakaz_params_addPakUnPak();
    },
    _zakaz_params_draw_loaded_colors_2: function () {
        console.log( 'zakaz_params_draw_loaded_colors', this.options.form.fields.colors );
        let cnt_f = parseInt( $( '#colors_face' ).val() );
        let cnt_b = parseInt( $( '#colors_back' ).val() );
        let cnt_s = parseInt( $( '#colors_side' ).val() );
        let cnt_c = parseInt( $( '#colors_clips' ).val() );
        cnt_f = isNaN( cnt_f ) ? 0 : cnt_f;
        cnt_b = isNaN( cnt_b ) ? 0 : cnt_b;
        cnt_s = isNaN( cnt_s ) ? 0 : cnt_s;
        cnt_c = isNaN( cnt_c ) ? 0 : cnt_c;
        if ( cnt_f > 1 ) {
            if ( cnt_f > 10 )
                cnt_f -= 10;
            else
                cnt_f -= 1;
        }
        if ( cnt_b > 1 ) {
            if ( cnt_b > 10 )
                cnt_b -= 10;
            else
                cnt_b -= 1;
        }
        if ( cnt_s > 1 ) {
            if ( cnt_s > 10 )
                cnt_s -= 10;
            else
                cnt_s -= 1;
        }
        if ( cnt_c > 1 ) {
            if ( cnt_c > 10 )
                cnt_c -= 10;
            else
                cnt_c -= 1;
        }
        let cnt = cnt_f >= cnt_b ? cnt_f : cnt_b;
        let cnt2 = cnt_s >= cnt_c ? cnt_s : cnt_c;
        cnt = cnt >= cnt2 ? cnt : cnt2;
        if ( cnt < 1 )
            cnt = 0;
        if ( $.type( this.options.form.fields.colors ) !== 'object' )
            this.options.form.fields.colors = {};
        if ( $.type( this.options.form.fields.colors.face_values ) !== 'array' )
            this.options.form.fields.colors.face_values = [ ];
        if ( $.type( this.options.form.fields.colors.back_values ) !== 'array' )
            this.options.form.fields.colors.back_values = [ ];
        if ( $.type( this.options.form.fields.colors.side_values ) !== 'array' )
            this.options.form.fields.colors.side_values = [ ];
        if ( $.type( this.options.form.fields.colors.clips_values ) !== 'array' )
            this.options.form.fields.colors.clips_values = [ ];
        let cF = this.options.form.fields.colors.face_values;
        let cB = this.options.form.fields.colors.back_values;
        let cS = this.options.form.fields.colors.side_values;
        let cC = this.options.form.fields.colors.clips_values;
        this.colors_tbl.children( ':nth-child(2n+1)' ).each( function () {
            console.log( $( this ).index(), this );
            let n = 3;
            if ( $( this ).index() !== 0 )
                n--;
            while ( $( this ).children( ).length > n )
                $( this ).children( ':last-child' ).remove();
        } );
        if ( cnt )
            this.colors_tbl.children( ':nth-child(2n)' ).append( $( '<td>' ).attr( 'colspan', cnt ) );
        for ( let i = 0; i < cnt; i++ ) {
            this.colors_tbl.children( ':nth-child(1)' ).append( i < cnt_f ? $( '<td>' ).append( $( '<input>' ).attr( {
                type: 'text',
                name: this._createFieldName( 'colors' ) + '[face_values][' + i + ']',
                tabindex: this.options.tabIndex++,
            } ).change( {self: this}, function ( e ) {
                e.data.self.options.form.fields.colors.face_values[$( this ).parent().index() - 3] = $( this ).val();
            } ).val( cF.length > i ? cF[i] : '' ).enterAsTab() ) : $( '<td>' ) );
            this.colors_tbl.children( ':nth-child(3)' ).append( i < cnt_b ? $( '<td>' ).append( $( '<input>' ).attr( {
                type: 'text',
                tabindex: this.options.tabIndex++,
                name: this._createFieldName( 'colors' ) + '[back_values][' + i + ']',
            } ).change( {self: this}, function ( e ) {
                e.data.self.options.form.fields.colors.back_values[$( this ).parent().index() - 2] = $( this ).val();
            } ).val( cB.length > i ? cB[i] : '' ).enterAsTab() ) : $( '<td>' ) );
            this.colors_tbl.children( ':nth-child(5)' ).append( i < cnt_s ? $( '<td>' ).append( $( '<input>' ).attr( {
                type: 'text',
                tabindex: this.options.tabIndex++,
                name: this._createFieldName( 'colors' ) + '[side_values][' + i + ']',
            } ).change( {self: this}, function ( e ) {
                e.data.self.options.form.fields.colors.side_values[$( this ).parent().index() - 2] = $( this ).val();
            } ).val( cS.length > i ? cS[i] : '' ).enterAsTab() ) : $( '<td>' ) );
            this.colors_tbl.children( ':nth-child(7)' ).append( i < cnt_c ? $( '<td>' ).append( $( '<input>' ).attr( {
                type: 'text',
                tabindex: this.options.tabIndex++,
                name: this._createFieldName( 'colors' ) + '[clips_values][' + i + ']',
            } ).change( {self: this}, function ( e ) {
                e.data.self.options.form.fields.colors.clips_values[$( this ).parent().index() - 2] = $( this ).val();
            } ).val( cC.length > i ? cC[i] : '' ).enterAsTab() ) : $( '<td>' ) );
            if ( cF.length < i )
                cF[i] = '';
            if ( cB.length < i )
                cB[i] = '';
            if ( cS.length < i )
                cS[i] = '';
            if ( cC.length < i )
                cC[i] = '';
//            this.colors_tbl.append(tr);
        }
    },

    _zakaz_params_draw_loaded_colors: function () {
        console.log( 'zakaz_params_draw_loaded_colors', this.options.form.fields.colors );
        let cnt_f = parseInt( $( '#colors_face' ).val() );
        let cnt_b = parseInt( $( '#colors_back' ).val() );
        cnt_f = isNaN( cnt_f ) ? 0 : cnt_f;
        cnt_b = isNaN( cnt_b ) ? 0 : cnt_b
        if ( cnt_f > 1 ) {
            if ( cnt_f > 10 )
                cnt_f -= 10;
            else
                cnt_f -= 1;
        }
        if ( cnt_b > 1 ) {
            if ( cnt_b > 10 )
                cnt_b -= 10;
            else
                cnt_b -= 1;
        }
        let cnt = cnt_f >= cnt_b ? cnt_f : cnt_b;
        if ( cnt < 1 )
            cnt = 0;
        if ( $.type( this.options.form.fields.colors ) !== 'object' )
            this.options.form.fields.colors = {};
        if ( $.type( this.options.form.fields.colors.face_values ) !== 'array' )
            this.options.form.fields.colors.face_values = [ ];
        if ( $.type( this.options.form.fields.colors.back_values ) !== 'array' )
            this.options.form.fields.colors.back_values = [ ];
        let cF = this.options.form.fields.colors.face_values;
        let cB = this.options.form.fields.colors.back_values;
        while ( this.colors_tbl.children( ':first-child' ).children().length > 3 ) {
            this.colors_tbl.children( ':first-child' ).children( ':last-child' ).remove();
        }
        while ( this.colors_tbl.children( ':last-child' ).children().length > 2 ) {
            this.colors_tbl.children( ':last-child' ).children( ':last-child' ).remove();
        }
        if ( this.colors_tbl.children( ':nth-child(2)' ).children().length > 1 )
            this.colors_tbl.children( ':nth-child(2)' ).children( ':last-child' ).remove();
        if ( cnt )
            this.colors_tbl.children( ':nth-child(2)' ).append( $( '<td>' ).attr( 'colspan', cnt ) );
        for ( let i = 0; i < cnt; i++ ) {
//            let tr=$('<tr>');
            this.colors_tbl.children( ':first-child' ).append( i < cnt_f ? $( '<td>' ).append( $( '<input>' ).attr( {
                type: 'text',
                name: this._createFieldName( 'colors' ) + '[face_values][' + i + ']',
                tabindex: this.options.tabIndex++,
            } ).change( {self: this}, function ( e ) {
                e.data.self.options.form.fields.colors.face_values[$( this ).parent().index() - 3] = $( this ).val();
            } ).val( cF.length > i ? cF[i] : '' ).enterAsTab() ) : $( '<td>' ) );
            this.colors_tbl.children( ':last-child' ).append( i < cnt_b ? $( '<td>' ).append( $( '<input>' ).attr( {
                type: 'text',
                tabindex: this.options.tabIndex++,
                name: this._createFieldName( 'colors' ) + '[back_values][' + i + ']',
            } ).change( {self: this}, function ( e ) {
                e.data.self.options.form.fields.colors.back_values[$( this ).parent().index() - 2] = $( this ).val();
            } ).val( cB.length > i ? cB[i] : '' ).enterAsTab() ) : $( '<td>' ) );
            if ( cF.length < i )
                cF[i] = '';
            if ( cB.length < i )
                cB[i] = '';
//            this.colors_tbl.append(tr);
        }
    },
    _zakaz_params_find_in_ul: function ( ul, key ) {
        let rVal = false;
        $.each( ul.children(), function () {
            rVal = $( this ).children().attr( 'data-key' ) == key ? $( this ).children().text() : false;
            return rVal === false ? true : false;
        } );
        return rVal ? rVal : '';
    },
    _zakaz_params_find_li_in_ul: function ( ul, key ) {
        let rVal = false;
        $.each( ul.children(), function () {
            rVal = $( this ).children().attr( 'data-key' ) == key ? $( this ) : false;
            return rVal === false ? true : false;
        } );
        return rVal ? rVal : $();
    },
    _Tech_tab: function ( tab ) {
        if ( !this.options.viewRowUrl ) {
            tab.append( $( '<p>' ).text( 'Не указан URL для технички' ) );
            return;
        }
        let uri = new URI( this.options.viewRowUrl ), self = this;

        uri.addSearch( 'technicals', 'true' );
        uri.addSearch( 'id', this.options.z_id );
        uri.addSearch( 'asDialog', true );
        if ( this.options.isDisainer ) {
            uri.addSearch( 'isDisainer', true );
        }
        tab.append( $( '<p>' ).text( uri, toString() ) );
        $.post( uri.toString() ).done( function ( answ ) {
            tab.html( answ );
            if ( self.options.parent ) {
                self.options.parent.tVAGCAP( tab );
            }
        } );
    }
};