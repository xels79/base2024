/*
 * Это файл создан xel_s
 * Для проекта СУБД ver2  *
 */
var _MFDRBUG = false;
let m_filter = {
    options: {
        list: {},
        onClose: null, //Если были изменения вызовет указанную функцию
        //Формат:Массив [окружение, функция]
        textSelectAll: 'Выбрать все',
        textUnSelectAll: 'Отменить все',
        checkValue: '',
        //afterChange:null,
    },
    _selected: null,
    _backG: null,
    _li: null,
    _input:null,
    _hasAClick: false,
    _create: function () {
        this._super();
        this.element.addClass( 'm_filter' );
        this._informer = $( '<div>' ).text( 'Нет' ).click( {self: this, setFocus:true}, this._open );
        this.element.append( this._informer );
        this._button = $( '<div>' )
                .append( $( '<span>' )
                        .addClass( 'glyphicon glyphicon-chevron-down' ) )
                .click( {self: this}, this._open );
        this.element.append( this._button );
        this._selected = [ ];
        $( window ).resize( {self: this}, this._resize );
        let time = performance.now();
        this.options.list = new NEWFUNCTION.sorted2( this.options.list );
        time = performance.now() - time;
        if ( _MFDRBUG ) console.log( 'Время выполнения sorted2 = ', time );
        if ( _MFDRBUG ) console.log( this.options.list );
//        this.options.list.sort();
    },
    check: function ( val ) {
        return this.options.checkValue === val;
    },
    selectedValAsString: function () {
        return JSON.stringify( this._selected );
    },
    _createList: function () {
        let time = performance.now();
        let rVal = $( '<div>' )
                .addClass( 'filter-ul list-group' )
                .attr('tabindex',-1);
        let keys = this.options.list.keys();
        let kL = keys.length;
        let a_first = null;
        let tab=1;
        if ( kL ) {
            a_first = $( '<a>' ).addClass( 'list-group-item' ).attr( {
                href: '#',
                'data-key': '-1',
                tabindex:tab++
            } );
            a_first.text( this.options.textSelectAll );
            a_first.click( {self: this}, this._itemClick );
            rVal.append( a_first );
            a_first = $( '<a>' ).addClass( 'list-group-item' ).attr( {
                href: '#',
                'data-key': '-2',
                tabindex:tab++
            } );
            a_first.text( this.options.textUnSelectAll );
            a_first.click( {self: this}, this._itemClick );
            rVal.append( a_first );
        }
        for ( var i = 0; i < kL; i++ ) {
            let a = $( '<a>' ).addClass( 'list-group-item' ).attr( {
                href: '#',
                'data-key': keys[i],
                tabindex:tab++
            } );
            a.text( this.options.list.get( keys[i] ) );
            a.click( {self: this}, this._itemClick );
            if ( $.inArray( keys[i], this._selected ) > -1 ) {
                a.addClass( 'active' );
            }
            rVal.append( a );
        }
        time = performance.now() - time;
        if ( _MFDRBUG ) console.log( 'Время выполнения _createList = ', time );
        return rVal;
    },
    _listKeyPress:function(e){
        let kC=e.keyCode;
        if (e.key==='ArrowUp'||e.key==='ArrowDown'){
            let m=false;
            let tmp=$(this);
            do{
                tmp=e.key==='ArrowUp'?tmp.prev():tmp.next();
                if (tmp.length && !tmp.hasClass('hidden') && !tmp.hasClass('active')){
                    m=true;
                    e.preventDefault();
                    tmp.trigger('focus');
                }
                m= m || tmp.index()>=$(this).parent().children().length || tmp.index()<1;
            }while(!m);
        }else{
            //e.data.self._input.trigger('keydown');
            if ((kC>46&&kC<91)||(kC>46&&kC<65) ||kC===189 || [
                219,221,186,222,188,190,191,192,32
            ].indexOf(kC)>-1){
                e.preventDefault();
                e.data.self._input.val(e.data.self._input.val()+e.key);
                e.data.self.___doSelection(e.data.self._input,kC);
                if ($(this).hasClass('hidden')){
                    e.data.self._input.trigger('focus');
                }else{
                    $(this).trigger('focus');   
                }
            }else if (kC===8){
                let tmp=e.data.self._input.val();
                e.preventDefault();
                if (tmp.length){
                    tmp=tmp.substr(0,tmp.length-1);
                    e.data.self._input.val(tmp);
                    e.data.self.___doSelection(e.data.self._input,kC);
                    $(this).trigger('focus');
                }
            }
            if ( _MFDRBUG ) console.log(e.keyCode);
        }
    },
    _keyPress: function ( e ) {
        if ( e.keyCode == 27 )
            e.data.self.close();
    },
    _resize: function ( e ) {
        if ( e.data.self._backG || e.data.self._li )
            e.data.self.close();
    },
    ___doSelection:function(el,keyCode){
        let val=$(el).val();
        this._li.children().each(function(){
            let dtKey=$(this).attr('data-key');
            let dtVal=$(this).text();
            let hasMatch=dtKey==='-1'||dtKey==='-2'||$(this).hasClass('active');
            let matchComplitly=false;
            let tmpVal=dtVal.replace(new RegExp(val, 'i'),function(str){
                hasMatch=true;
                if (!matchComplitly && str===dtVal){
                    matchComplitly=true;
                }
                return `<span>${str}</span>`;
            });
            $(this).html(tmpVal);
            if (hasMatch){
                $(this).removeClass('hidden');
                if (matchComplitly && keyCode===13){
                    $(this).trigger('focus');
                    return false;
                }
            }else{
                $(this).addClass('hidden');
            }
        });        
    },
    _doSelection:function(e){
        let self=e.data.self;
        let val=$(this).val();
        if (e.key==='ArrowDown'){
            self._li.children(':first-child').trigger('focus');
            return;
        }
        self.___doSelection.call(self,this,e.keyCode);
    },
    open: function (setFocus) {
        setFocus=setFocus?true:false;
        if ( !this._backG && !this._li ) {
            if (this._input){
                this._input.remove();
            }
            this._input=$('<input>').addClass('m_fiter_input').attr('type','text').css({
                width:this._informer.outerWidth(),
                height:this._informer.outerHeight(),
                left:this._informer.offset().left,
                top:this._informer.offset().top
            })
            .val('').keyup({self:this}, this._doSelection)
            .keydown({self:this},function(e){
                if (e.keyCode===9){
                    e.data.self._li.children(':first-child').trigger('focus');
                    e.preventDefault();
                }
            });
            this._hasAClick = false;
            let offset = this.element.offset();
            $( document ).keydown( {self: this}, this._keyPress );
            offset.top += this.element.outerHeight();
            this._backG = $( '<div>' )
                    .addClass( 'ui-widget-overlay ui-front' )
                    .css( {
                        'z-index': 30000,
                        background: 'transparent'
                    } )
                    .click( {self: this}, this._close );
            this._li = this._createList();
            this._li.css( 'z-index', 30001 );
            this._li.offset( offset );
            this._li.width( this.element.outerWidth() > 200 ? this.element.outerWidth() : 200 );
            $( 'body' ).append( this._li );
            $( 'body' ).append( this._backG );
            $( 'body' ).append( this._input );
            this._setPrompt();
            if (setFocus) this._input.trigger('focus');
            this._li.children().keydown({self:this},this._listKeyPress);
        } else
            console.warn( 'Filter: попытка повторного открытия!' );
    },
    _open: function ( e ) {
        e.data.self.open(e.data.setFocus);
    },
    close: function () {
        $( document ).unbind( 'keydown', this._keyPress );
        this._li.remove();
        delete this._li;
        this._backG.remove();
        delete this._backG;
        this._backG = null;
        this._li = null;
        this._input.remove();
        this._input=null;
        if ( this._hasAClick && $.type( this.options.onClose ) === 'array' ) {
            if ( this.options.onClose.length > 1 && $.isFunction( this.options.onClose[1] ) ) {
                this.options.onClose[1].call( this.options.onClose[0], null, true );
            }
        }
    },
    _close: function ( e ) {
        e.data.self.close();
    },
    _setPrompt: function () {
        var keys = this.options.list.keys();
        var kL = keys.length;
        this._informer.removeAttr( 'title' );
        if ( !this._selected.length ) {
            this._informer.text( 'Нет' );
            this.element.parent().removeClass( 'has-select' );
        } else if ( this._selected.length === 1 ) {
            this.element.parent().addClass( 'has-select' );
            this._informer.text( this.options.list.get( this._selected[0] ) );
        } else {
            var tmp = "Несколько: ";
            for ( var i = 0; i < this._selected.length; i++ ) {
                if ( i )
                    tmp += ', ';
                tmp += '"' + this.options.list.get( this._selected[i] ) + '"';
            }
            this._informer.text( 'Неск...' );
            this._informer.attr( 'title', tmp );
            this.element.parent().addClass( 'has-select' );
        }
        if ( this._li ) {
            if ( this._selected.length == kL )
                this._li.children( '[data-key="-1"]' ).addClass( 'disabled' );
            else
                this._li.children( '[data-key="-1"]' ).removeClass( 'disabled' );
            if ( !this._selected.length )
                this._li.children( '[data-key="-2"]' ).addClass( 'disabled' );
            else
                this._li.children( '[data-key="-2"]' ).removeClass( 'disabled' );
        }
        if ( _MFDRBUG ) console.log( 'Filter - selected:', this._selected );
    },
    _massAction: function ( data_key ) {
        let keys = Object.keys( this.options.list );
        let kL = keys.length;
        let uncheck = data_key == '-2';//$( this ).attr( 'data-key' ) == '-2';
        let self = this;
        this._hasAClick = true;
        if ( kL == this._selected.length || uncheck ) {
            if ( this._li )
                this._li.children( '.active' ).removeClass( 'active' );
            this._selected = [ ];
        } else {
            $.each( this._li.children( ':not(.active):not([data-key="-1"]):not([data-key="-2"])' ), function () {
                if ( $.inArray( $( this ).attr( 'data-key' ), self._selected ) == -1 ) {
                    self._selected[self._selected.length] = $( this ).attr( 'data-key' );
                    $( this ).addClass( 'active' );
                }
            } );
        }
        this._setPrompt();
    },
    _time: 0,
    _itemClick: function ( e ) {
        e.preventDefault();
        e.stopPropagation();

        if ( $( this ).hasClass( 'disabled' ) )
            return;
        if ( e.data.self._time ) {
            clearTimeout( e.data.self._time );
        }
        e.data.self._time = setTimeout( function () {
            e.data.self._time = 0;
            e.data.self.close();
        }, 500 );
        if ( $( this ).attr( 'data-key' ) == '-1' || $( this ).attr( 'data-key' ) == '-2' )
            return e.data.self._massAction.call( e.data.self, $( this ).attr( 'data-key' ) );
        e.data.self._hasAClick = true;
        e.data.self._informer.removeAttr( 'style' );
        e.data.self._informer.width( e.data.self._informer.width() );
        e.data.self._informer.css( 'max-width', e.data.self._informer.width() );
        if ( !$( this ).hasClass( 'active' ) ) {
            $( this ).addClass( 'active' );
            e.data.self._selected[e.data.self._selected.length] = $( this ).attr( 'data-key' );
        } else {
            $( this ).removeClass( 'active' );
            var removeItem = $( this ).attr( 'data-key' );
            e.data.self._selected = $.grep( e.data.self._selected, function ( value ) {
                return value != removeItem;
            } );
        }
        e.data.self._setPrompt();
    },
    hasSelected: function () {
        return Object.keys( this._selected ).length != 0;
    },
    'selected-items-keys': function () {
        return this._selected;
    },
};

( function ( $ ) {
    $.widget( "custom.m_filter", $.custom.m_filter_base, $.extend( {}, m_filter ) );
}( jQuery ) );

