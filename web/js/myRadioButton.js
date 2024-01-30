/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
let mRButtDefId = 1, mRButtDefInputId = 1;
( function ( $ ) {
    $.widget( "custom.radioButton", {
        _inp: null,
        options: {
            wrapClass: 'm-radio', //Имя CSS класса по умолчанию
            itemTagName: 'span', //Имя тэга для каждого элемента
            values: {}, //Набор ключ:значение
            defaultValue: 0, //Значение по умолчанию
            id: false, //id - обёртки
            inputId: false, //id - невидимого input
            name: '', //Параметр name для input
            onChange: null, //Функция которая выполница при изменение значения
            onClick: null, //Функция которая выполница при нажатие кнопки мыши
            useInput: false, //Если = true буднт использован INPUT
            elementsInLine: 0, //Количесто элементов в строке
            disabledItems: [ ], //Заблокированные элементы
            disabled: false, //Заблокировать
            disabledTitle: 'Блокирован'  //Текст при блокировки элемента
        },
        _destroy: function () {
            console.log( 'wDestroy' );
        },
        setItemDisabled: function ( val ) {
            let self = this;
            if ( $.type( val ) === 'array' || $.type( val ) === 'object' ) {
                $.each( val, function ( id, el ) {
                    self.options.disabledItems[self.options.disabledItems.length] = el;
                } );
            } else {
                this.options.disabledItems[this.options.disabledItems.length] = val;
            }
            this._chceckDisabled();
        },
        unsetItemDisabled: function ( val ) {
            let newV = [ ];
            for ( let i in this.options.disabledItems ) {
                if ( this.options.disabledItems[i] != val )
                    newV[i] = this.options.disabledItems[i];
            }
            this.options.disabledItems = newV;
            this._chceckDisabled();
        },
        _chceckDisabled: function () {
            let vals = this.options.disabledItems, self = this;
            if ( this.options.elementsInLine ) {
                $.each( this.element.children( 'div' ), function () {
                    $.each( $( this ).children( self.options.itemTagName ), function () {
//                        console.log($(this).children(':first-child'));
                        $( this ).unbind( 'click' );
                        if ( vals.indexOf( $( this ).attr( 'data-key' ) ) > -1 ) {
                            $( this ).addClass( 'disabled' );
                            if ( $( this ).hasClass( 'on' ) ) {
                                $( this ).removeClass( 'on' );
                                self._inp.val( null );
                            }
                        } else {
                            $( this ).removeClass( 'disabled' ).bind( 'click', {self: self}, self._itClick );
                        }
                    } );
                } );
            } else {
                $.each( this.element.children( this.options.itemTagName ), function () {
//                    console.log($(this).children(':first-child'));
                    $( this ).unbind( 'click' );
                    if ( vals.indexOf( $( this ).attr( 'data-key' ) ) > -1 ) {
                        $( this ).addClass( 'disabled' );
                        if ( $( this ).hasClass( 'on' ) ) {
                            $( this ).removeClass( 'on' );
                            self._inp.val( null );
                        }
                    } else {
                        $( this ).removeClass( 'disabled' ).bind( 'click', {self: self}, self._itClick );
                    }
                } );
            }
        },
        _create: function () {
            let self = this, elCount = 0;

            this._super();
            this._inp = $( '<input>' ).attr( 'type', 'hidden' );
            if ( this.options.id === false ) {
                this.options.id = 'radioButton' + mRButtDefId++;
            }
            if ( this.options.inputId === false ) {
                this.options.inputId = 'radioButtonInput' + mRButtDefInputId++;
            }
            this.element.addClass( this.options.wrapClass ).attr( 'id', this.options.id );
            this._inp.attr( {
                id: this.options.inputId,
                name: this.options.name
            } ).val( this.options.defaultValue );
            let apEl = !this.options.elementsInLine ? this.element : $( '<div>' ).appendTo( this.element );
            $.each( this.options.values, function ( id, el ) {
                if ( self.options.useInput ) {
                    let opt = {type: 'radio', name: self.options.name};
                    if ( id == self.options.defaultValue ) {
                        opt.checked = 'checked';
                    }
                    let inp = $( '<input>' ).val( id ).attr( opt );/*.click({self:self},self._itClick)*/
                    let tmp = $( '<' + self.options.itemTagName + '>' ).attr( 'data-key', id ).appendTo( apEl )
                            .append( inp )
                            .append( $( '<span>' ).text( $.type( el ) === 'object' ? el.label : el ) );
                    if ( id == self.options.defaultValue ) {
                        tmp.addClass( 'on' );
//                        if ($.isFunction(self.options.onClick)) self.options.onClick.call(inp.get(0),id);
                    }
                } else {
                    let tmp = $( '<' + self.options.itemTagName + '>' ).attr( 'data-key', id )/*.click({self:self},self._itClick)*/.appendTo( apEl ).append( $( '<span>' ).text( $.type( el ) === 'object' ? el.label : el ) );
                    if ( id == self.options.defaultValue ) {
                        tmp.addClass( 'on' );
                        self._inp.val( id );
                        if ( $.isFunction( self.options.onChange ) )
                            self.options.onChange.call( self._inp, id );
//                        if ($.isFunction(self.options.onClick)) self.options.onClick.call(tmp.get(0),id);
                    }
                }
                elCount++;
                if ( self.options.elementsInLine && elCount == self.options.elementsInLine ) {
                    self.element.append( apEl );
                    apEl = $( '<div>' ).appendTo( self.element );
                    elCount = 0;
                }
            } );
            if ( !self.options.useInput ) {
                this._inp.change( function () {
                    if ( $.isFunction( self.options.onChange ) )
                        self.options.onChange.call( this, $( this ).val() );
                } );
                this.element.append( this._inp );
            }
            this._chceckDisabled();
        },
        option: function ( key, val ) {
            this._super( key, val );
            if ( key === 'disabled' ) {
                if ( val ) {
                    this.element.attr( 'title', this.options.disabledTitle );
                } else {
                    this.element.removeAttr( 'tite' );
                }
            }
        },
        _itClick: function ( e ) {
            if ( e.data.self.options.disabled ) {
                return;
            }
            if ( this.tagName === 'INPUT' ) {
                let val = $( this ).val();
                if ( $.type( e.data.self.options.values[val] ) === 'object' && $.isFunction( e.data.self.options.values[val].click ) ) {
                    if ( e.data.self.options.values[val].click.call( this, val ) === false )
                        return;
                }
                $( this ).parent().parent().children().removeClass( 'on' );
                $( this ).parent().parent().parent().children().children().removeClass( 'on' );
                $( this ).parent().addClass( 'on' );
                if ( $.isFunction( e.data.self.options.onClick ) )
                    e.data.self.options.onClick.call( this, val );
                if ( $.isFunction( e.data.self.options.onChange ) )
                    e.data.self.options.onChange.call( this, val );
            } else {
                let val = $( this ).attr( 'data-key' );
                if ( $.type( e.data.self.options.values[val] ) === 'object' && $.isFunction( e.data.self.options.values[val].click ) ) {
                    if ( e.data.self.options.values[val].click.call( this, val ) === false )
                        return;
                }

                if ( !$( this ).hasClass( 'on' ) ) {
                    if ( e.data.self.options.elementsInLine ) {
                        $( this ).parent().parent().children().each( function () {
                            $( this ).children().removeClass( 'on' );
                        } );

                    } else {
                        $( this ).parent().children( e.data.self.options.itemTagName ).removeClass( 'on' );
                    }
                    e.data.self._inp.val( $( this ).attr( 'data-key' ) );
                    $( this ).addClass( 'on' );
                    if ( $.isFunction( e.data.self.options.onClick ) )
                        e.data.self.options.onClick.call( this, val );
                    if ( $.isFunction( e.data.self.options.onChange ) )
                        e.data.self.options.onChange.call( this, val );
                }
            }
        },
        clickEmulate: function () {
            this.element.find( '.on' ).removeClass( 'on' ).trigger( 'click' );
        },
        value: function ( val ) {
            if ( typeof val != "undefined" ) {
                let i = 1, self = this;
                if ( val == -1 ) {
                    self.element.find( '.on' ).removeClass( 'on' );
                } else {
                    $.each( this.options.values, function ( id, el ) {
                        if ( id == val ) {
                            self._inp.val( val );
                            self.element.find( '.on' ).removeClass( 'on' );
                            //self.element.children(self.options.itemTagName).removeClass('on');
                            console.log( self.element.children( self.options.itemTagName + ':nth-child(' + i + ')' ) );
                            self.element.children( self.options.itemTagName + ':nth-child(' + i + ')' ).trigger( 'click' );
                            //self.element.children(':first-child').children(self.options.itemTagName+':nth-child('+i+')').trigger('click');//.addClass('on');
                            //if ($.isFunction(self.options.onChange)) self.options.onChange.call(self._inp,val);
                            return false;
                        }
                        i++;
                    } );
                }
                return val;
            } else {
                return this._inp.val();
            }
        }
    } )
    // дальнейшая реализация плагина
}( jQuery ) );


