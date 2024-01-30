/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


let __filter_like = {
    _backG: null,
    _selected: null,
    _input: null,
    options: {
        onUpdate: null, //Если были изменения вызовет указанную функцию
        //Формат:Массив [окружение, функция]
        like: {},
    },
    _oldV: '',
    _timer: 0,
    _create: function () {
        this._super();
        this.element.addClass( 'm_filter like' );
        this._informer = $( '<div>' );//.text('Нет');
        this.element.append( this._informer );
        if ( $.type( this.options.like ) !== 'array' && $.type( this.options.like ) !== 'object' ) {
            console.error( 'm_filter_ike - не верное значение опции list' );
        }
        this._input = $( '<input>' ).attr( {
            'type': 'text',
            placeholder: 'Нет',
        } ).val( '' );
        this._informer.append( this._input );
        if (this.options.asDate){
            this._input.change({self: this}, this._dateModeChange);
            this._input.datepicker();
        }else{
            this._input.keyup( {self: this}, this._keyUp );
            this._input.focusout( {self: this}, this._keyUp );
        }
        this._oldV = this._input.val();
        console.log( 'Filter_like - list:', this.options.like );
    },
    _dateModeChange:function(e){
        let self=e.data.self;
        if ( self._oldV === $( this ).val() )
            return;
        self._oldV = $( this ).val();
        if ( $.type( self.options.onUpdate ) === 'array' && self.options.onUpdate.length > 1 && $.isFunction( self.options.onUpdate[1] ) ) {
            self.options.onUpdate[1].call( self.options.onUpdate[0], [ self, self._afterUpdate ],true);
        }else{
            if ($.isFunction( self.options.onUpdate)){
                self.options.onUpdate.call(this,true);
            }
        }
        
    },
    _keyUp: function ( e ) {
        let self = e.data.self;
        if ( self._oldV === $( this ).val() )
            return;
        self._oldV = $( this ).val();
        if ( self._timer ) {
            clearTimeout( self._timer );
            self._timer = 0;
        }
        self._timer = setTimeout( function () {
            self._timer = 0;
            if ( $.type( self.options.onUpdate ) === 'array' && self.options.onUpdate.length > 1 && $.isFunction( self.options.onUpdate[1] ) ) {
                self.options.onUpdate[1].call( self.options.onUpdate[0], [ self, self._afterUpdate ],true );
            }
        }, 500 );
    },
    update: function () {
        this._afterUpdate();
    },
    _afterUpdate: function () {
        if (this.options.asDate) return;
        let index = this.element.parent().index();
        let row = this.element.parent().parent().next();
        while ( row.length ) {
            var td = $( row.children().get( index ) );
            console.log( td );
            if ( !td.attr( 'data-text' ) )
                td.attr( 'data-text', td.text() );
            var txt = td.attr( 'data-text' ), fndTxt = this._input.val(), txtM = txt.toUpperCase();
            var pos = txtM.indexOf( fndTxt.toUpperCase() );
            td.empty();
            td.html( txt.substr( 0, pos ) + '<b>' + txt.substr( pos, fndTxt.length ) + '</b>' + txt.substr( pos + fndTxt.length ) );
            row = row.next();
        }
    },
    value: function () {
        return this._input.val();
    },
    hasSelected: function () {
        return this._input.val() !== '';
    }
};
( function ( $ ) {
    $.widget( "custom.m_filter_like", $.custom.m_filter_base, $.extend( {}, __filter_like ) );
}( jQuery ) );

