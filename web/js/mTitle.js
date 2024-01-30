/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom.mTitle", $.Widget,$.extend({},{
        _x:0,
        _y:0,
        _tm:null,
        options:{
            title:'',
            asHtml:false,
            zIndex:0,
            class:'info-popup',
            delay:800,
            addOnClass:''
        },
        _create:function(){
            this._super();
            this.element.mousemove({self:this},this._mMove);
//            this.element.hover({self:this},this._mHover);
            this.element.mouseleave({self:this},this._mLeave);
            if (!this.options.title){
                if (this.element.atrr('title')){
                    this.options.title=this.element.atrr('title');
                }else if (this.element.atrr('data-title')){
                    this.options.title=this.element.atrr('data-title');
                }
            }
        },
        _mMove:function(e){
            var self=e.data.self;
            self._x=e.pageX;
            self._y=e.pageY;
            if (self._tm) clearTimeout(self._tm);
            self._tm=setTimeout(function(){
                var cnt=$('<div>');
                if ($.type(self.options.title)==='string'){
                    if (self.options.asHtml){
                        cnt.html(self.options.title);
                    }else{
                        cnt.text(self.options.title);
                    }
                }else if ($.isFunction(self.options.title)){
                    if (self.options.asHtml){
                        cnt.html(self.options.title.call(self.element));
                    }else{
                        cnt.text(self.options.title.call(self.element));
                    }                    
                }
                self._tm=null;
                if (self.options.addOnClass) self.element.addClass(self.options.addOnClass);
                m_popUp({
                    posX:self._x-5,
                    posY:self._y-2,
                    width:'auto',
                    zindex:60001,
                    parentControl:self.element,
                    content:cnt,
                    beforeClose:function(){
                        if (self.options.addOnClass) self.element.removeClass(self.options.addOnClass);
                    },
                    options:{
                        class:self.options.class
                    }
            });
            },self.options.delay);

        },
        _mLeave:function(e){
            if (e.data.self._tm) clearTimeout(e.data.self._tm);
            e.data.self._tm=null;
        },
    }));
}( jQuery ) );
