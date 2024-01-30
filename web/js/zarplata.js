/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

let tabIndex = 1000;
let yearUpdate = function ( e ) {
    let uri = new URI( window.location.href );
    e.preventDefault( );
    if ( !uri.hasSearch( 'year', $( '#zarpalata-yaer' ).val( ) ) ) {
        uri.setSearch( 'year', $( '#zarpalata-yaer' ).val( ) );
        window.location.href = uri.toString( );
        $( this ).parent( ).parent( ).parent( ).parent( ).empty( );
    }
};
$( '#zarplata-get-yaer' ).click( yearUpdate );
$( '#zarpalata-yaer' ).keypress( function ( e ) {
    if ( e.keyCode === 13 ) {
        yearUpdate.call( $( '#zarplata-get-yaer' ).get( 0 ), e );
    }
} );
$( '#zarplata-get-yaer,#zarpalata-yaer,#addMonthButton' ).removeAttr( 'disabled' );
$( '#addMonthButton' ).click( function ( e ) {
    e.preventDefault( );
    $.post( $( this ).attr( 'href' ), {
        'dayCount': $( '#zarplata-day-count' ).val( )
    } ).done( function ( answ ) {
        console.log( answ );
        if ( answ.status !== 'ok' ) {
            let alrt = $( '<div>' ).addClass( 'alert alert-danger fade in' );
            alrt.append( $( '<button>' ).addClass( 'close' ).attr( {
                'data-dismiss': 'alert',
                'type': 'button',
                'aria-hidden': 'true'
            } ).html( '&#215;' ) );
            alrt.append( $( '<p>' ).html( '<strong>Ошибка сервера:</strong>&nbsp' + answ.errorText ) );
            alrt.insertBefore( $( '.zarplata' ).children( ':first-child' ) );
        } else {
            $( this ).parent( ).parent( ).parent( ).parent( ).empty( );
            window.location.reload( );
        }
    } );
} );
$( '#zarplateTabs' ).children( ).children( ).click( function ( e ) {
    console.log( this );
} );
( function ( $ ) {
    $.widget( "custom.zarplataTabsActions", $.Widget, {
        options: {
            actionRemovMonthUrl: '',
            actionRemovYearUrl: '',
            actionAddMonth: '',
            addMonthMenu: [ ]
        },
        _create: function ( ) {
            this._super( );
            if ( this.element.parent().index() === this.element.parent().parent().children().length - 1 ) {
                this.element.contextmenu( {self: this}, this._showMenu );
            } else {
                this.element.contextmenu( function ( e ) {
                    e.preventDefault();
                } );
            }
        },
        _removeMonthItem: function ( mName, month_id ) {
            let self = this;
            return {
                label: 'Удалить месяц "' + mName + '"',
                click: function ( ) {
                    //data-month_id
                    m_alert( 'Внимание', 'Удалить месяц "' + mName + '" за ' + $( '#zarpalata-yaer' ).val( ) + ' год.<br>Действие не обратимо!', {
                        label: 'Продолжить',
                        click: function ( ) {
                            $.post( self.options.actionRemovMonthUrl, {month_id: month_id} ).done( ( answ ) => {
                                if ( answ.status !== 'ok' ) {
                                    m_alert( 'Ошибка сервера', answ.errorText, 'закрыть', false );
                                } else {
                                    window.location.reload( );
                                }
                            } );
                        }
                    }, 'Отмена' );
                }
            };
        },
        _addMonthItem: function ( ) {
            let month = this.options.addMonthMenu[0];
            let aUrl = this.options.actionAddMonth;
            if ( this.options.addMonthMenu.length ) {
                return {
                    label: 'Добавить следующий',
                    click: function () {
                        let inp = $( '<input>' ).addClass( 'form-control' ).attr( {
                            type: 'text',
                            placeholder: "Количество дней"
                        } );
                        m_alert( 'Добавить', '<p>Добавить: ' + month.monthName + ' - ' + month.year + 'г.</p>' +
                                '<div class="input-group" style="margin: 10px 120px 10px 10px;"><span class="input-group-addon">Укажите количество рабочих дней</span><input type="text" class="form-control" placeholder="Количество дней" id="zpDayCountD"></div>', {
                                    label: 'Добавить',
                                    click: function () {
                                        if ( $( '#zpDayCountD' ).val( ) && $( '#zpDayCountD' ).val( ) !== '0' ) {
                                            $.post( aUrl, {
                                                dayCount: $( '#zpDayCountD' ).val( ),
                                                cMonth: month.month,
                                                cYear: month.year
                                            } ).done( function ( answ ) {
                                                console.log( answ );
                                                if ( answ.status !== 'ok' ) {
                                                    m_alert( 'Ошибка сервера', answ.errorText, 'Закрыть', false );
                                                } else {
                                                    $( '.zarplata' ).remove();
                                                    window.location.reload( );
                                                }
                                            } );
                                        } else {
                                            m_alert( 'Внимание', 'Укажите количество', 'Закрыть', false );
                                            return false;
                                        }
                                    }
                                }, 'Отмена', null, function () {
                            $( '#zpDayCountD' ).onlyNumeric();
                        } );
                    }
                };
            } else {
                return {
                    label: 'Добавить следующий',
                    disabled: true
                }
            }
        },
        _showMenu: function ( e ) {
            e.preventDefault( );
            let self = e.data.self;
            let items = [ ];
            let month_id = $( this ).parent( ).attr( 'data-month_id' );
            let mName = $( this ).text( );
            console.log( self.options, e );
            if ( self.options.actionAddMonth ) {
                items[items.length] = self._addMonthItem.call( self );
            }
            if ( self.options.actionRemovMonthUrl ) {
                items[items.length] = self._removeMonthItem.call( self, mName, month_id );
            }
            dropDown( {
                posX: e.clientX,
                posY: e.clientY,
                items: items
            } );
        },
    } );
}( jQuery ) );
let hasF2=null;
( function ( $ ) {
    $.widget( "custom.zarplataActions", $.Widget, {
        _timer: 0,
        options: {
            actionUrl: ''
        },
        _create: function ( ) {
            this._super( );
            this.element.attr('autocomplete', 'off');
            this.element.attr( 'tabindex', tabIndex++ );
            if ( this.element.attr( 'name' ) !== 'comment' ) {
				let opt={};
				if (this.element.attr( 'name' )==='hours' && this.element.attr('data-allow-point')==='true'){
					opt.allowPoint=true;
				}
                this.element.onlyNumeric( opt );
            }
            this.element.focusin(function(){
                $(this).attr('data-oldval',$(this).val())
                hasF2={
                    zKey:$(this).parent().parent().attr('data-zarplata-key'),
                    eI:$(this).parent().index()
                }
            });
            this.element.focusout(function(){
                let oldV=$(this).attr('data-oldval');
                if (typeof(oldV)!=='undefined'){
                    if (oldV!==$(this).val()){
                        $(this).trigger( 'mychange' );
                    }
                }
            });
            this.element.click(function(e){
                e.preventDefault();
                e.stopImmediatePropagation();
            });
            $(document).click(function(e){
                hasF2=null;
            });
            this.element.keydown(function(e){
                if (e.key==='ArrowDown'){
                    let tr=$(this).parent().parent().next();
                    if (tr.length && !tr.hasClass('with-date')){
                        tr=tr.next();
                    }
                    if (tr.length && $(this).parent().index()<tr.children().length){
                        $(tr.children().get($(this).parent().index())).children('input').trigger('focus');
                    }
                    e.preventDefault();
                }else if (e.key==='ArrowUp'){
                    let tr=$(this).parent().parent().prev();
                    if (tr.length && !tr.hasClass('with-date')){
                        tr=tr.prev();
                    }
                    if (tr.length && $(this).parent().index()<tr.children().length){
                        
                        $(tr.children().get($(this).parent().index())).children('input').trigger('focus');
                    }
                    e.preventDefault();
                }else if (e.key==='ArrowLeft'){
                    let _end=false;
                    let par=$(this).parent();
                    let input=null;
                    if (!this.selectionStart){
                        while (!_end){
                            par=par.prev();
                            if (!par.length){
                                _end=true;
                            }else{
                                input=par.children( 'input' );
                                if (input.length) _end=true;
                            }
                        }
                        if (input && input.length) input.trigger( 'focus' );
                        e.preventDefault();
                    }
                }else if (e.key==='ArrowRight'){
                    let _end=false;
                    let par=$(this).parent();
                    let input=null;
                    if (this.selectionStart===$(this).val().length){
                        while (!_end){
                            par=par.next();
                            if (!par.length){
                                _end=true;
                            }else{
                                input=par.children( 'input' );
                                if (input.length) _end=true;
                            }
                        }
                        if (input && input.length) input.trigger( 'focus' );
                        e.preventDefault();
                    }
                }else if (e.key==='Delete'){
                    let str=$(this).val();
                    if (str.length && this.selectionStart<str.length){
                        let oldPos=this.selectionStart;
                        if (this.selectionStart!==this.selectionEnd){
                            str=str.substr(0,this.selectionStart)+str.substr(this.selectionEnd);
                        }else{
                            str=str.substr(0,this.selectionStart)+str.substr(this.selectionStart+1);
                        }
                        $(this).val(str);
                        this.selectionStart=oldPos;
                        this.selectionEnd=oldPos;
                    }
                    e.preventDefault();
                }else if (e.key==='Escape'){
                    let oldV=$(this).attr('data-oldval');
                    if (typeof(oldV)!=='undefined'){
                        $(this).val(oldV);
                    }
                    $(this).removeAttr('data-oldval');
                    $(this).trigger( 'blur' );
                }else {
                    console.log(e.key);
                }


            });
            this.element.enterAsTab( );
            this.element.on("mychange", {self: this}, this._doChange );
            //this.element.change( {self: this}, this._doChange );
        },
        _doChange: function ( e ) {
            let self = e.data.self;
            let z_id = $( this ).parent( ).parent( ).attr( 'data-zarplata-key' );
            $(this).removeAttr('data-oldval');
            if ( !self.options.actionUrl ) {
                console.error( 'zarplataActions._doChange: не передан actionUrl' );
                return;
            } else {
                if ( self._timer ) {
                    clearTimeout( self._timer );
                }
                self._timer = setTimeout( ( ) => {
                    self._timer = 0;
                    $( '[role="data-input"]' ).attr( 'disabled', true );
                    $.post( self.options.actionUrl, {
                        value: $( this ).val( ),
                        dataKey: $( this ).attr( 'name' ),
                        id: z_id,
                        year: $( '#zarpalata-yaer' ).val( )
                    } ).done( function ( answ ) {
                        if ( answ.status !== 'ok' ) {
                            m_alert( 'Ошибка сервера', answ.errorText, 'Закрыть', false );
                        } else {
                            let newTr = $( answ.html ).find( 'tr.with-date' );
                            let summCol = $( answ.html ).find( 'tr.summ' );
                            console.log( self.element.parent( ).parent( ).parent( ) );
                            self.element.parent( ).parent( ).parent( ).find( 'tr.summ' ).remove( );
                            self.element.parent( ).parent( ).parent( ).append( summCol );
                            $( '[data-zarplata-key="' + z_id + '"]' ).replaceWith( newTr );
                            newTr.find( '[role="data-input"]' ).zarplataActions( {
                                actionUrl: self.options.actionUrl
                            } );
                            $( '[role="data-input"]' ).removeAttr( 'disabled' );
                            if (hasF2!==null){
                                let tr=$('[data-zarplata-key='+hasF2.zKey+']');
                                if (tr.length && hasF2.eI<tr.children().length){
                                    let el=$(tr.children().get(hasF2.eI));
                                    el.children( 'input' ).trigger( 'focus' );
                                }
                                hasF2=null;
                            }
                        }
                    } );
                }, 1 );
            }
        },
    } );
}( jQuery ) );
