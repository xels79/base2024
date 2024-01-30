/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var resizeble_tr={
    _inUse:null,
    options:{
        min:30,
        max:1250,
        step:10,
        onResizeDone:null
    },
    _create:function(){
        this._super();
        this.element.addClass('m-risizeble');
        var self=this,childCnt=this.element.children().length;
        this.element.children(':not(:last-child)').each(function(){
                if ($(this).index()<childCnt-2&&!$(this).hasClass('technikal')){
                    $(this).append($('<div>')
                            .addClass('m-risizeble-drag')
                            .mousedown({self:self},self._onDragStart)
                        );
                }
            });
        $(document).mousemove({self:this},this._onDrag);
        $(document).mouseup({self:self},self._onDragEnd);
        
    },
    _onDragStart:function(e){
        console.log('dStart');
        var self=e.data.self;
        self._inUse={
                el:$(this).parent(),
                x:e.clientX
        };
    },
    _proceessIncrDecr:function(el,w,dir){
        dir=dir?dir:1;
        el.css({
            'max-width':w+(this.options.step*dir),
            width:w+(this.options.step*dir),
            
        });                
    },
    _processDecr:function(el,w){
        if (w-this.options.step<this.options.min){
            return;
        }
//        if (el.index()<=el.parent().children().length){
            
            var wNext=el.next().css('max-width')?parseInt(el.next().css('max-width')):el.next().width();
            if (isNaN(wNext)) wNext=parseInt(el.next().width());
            
            if (wNext+this.options.step>this.options.max)
                return;
            this._proceessIncrDecr(el.next(),wNext)
//        }
        this._proceessIncrDecr(el,w,-1);

    },
    _processIncr:function(el,w){
        console.log(el.next().width());
        if (w+this.options.step>this.options.max) return;
        if (this.element.children(':last-child').width()<1&&el.index()>=el.parent().children().length-3||el.next().width()<1) return;
//        if (el.index()<=el.parent().children().length){
            var wNext=el.next().css('max-width')?parseInt(el.next().css('max-width')):el.next().width();
            if (isNaN(wNext)) wNext=parseInt(el.next().width());
            if (el.index()<el.parent().children().length-3&&wNext-this.options.step<this.options.min) return;
            this._proceessIncrDecr(el.next(),wNext,-1);
//        }
        this._proceessIncrDecr(el,w);

    },
    _onDrag:function(e){
        var self=e.data.self;
        if (self._inUse){
            var dir=e.clientX>self._inUse.x;
            var w=self._inUse.el.css('max-width')?parseInt(self._inUse.el.css('max-width')):self._inUse.el.width();
            self._inUse.x=e.clientX;
            console.log('dragg dir=',dir);
            if (dir){
                self._processIncr(self._inUse.el,w);
            }else{
                self._processDecr(self._inUse.el,w);
            }
            
        }
    },
    _onDragEnd:function(e){
        var self=e.data.self;
        if (self._inUse){
            console.log('dEnd');
            delete self._inUse;
            self._inUse=null;
            if ($.type(self.options.onResizeDone)==='array'){
                if ($.isFunction(self.options.onResizeDone[0])){
                    if (self.options.onResizeDone.length>1)
                        self.options.onResizeDone[0].call(self.element,self.options.onResizeDone[1]);
                    else
                        self.options.onResizeDone[0].call(self.element);
                }else if (self.options.onResizeDone.length>1 && $.isFunction(self.options.onResizeDone[1])){
                    self.options.onResizeDone[1].call(self.element,self.options.onResizeDone[0]);
                }
            }else if ($.isFunction(self.options.onResizeDone))
                self.options.onResizeDone.call(self.element);
        }
    }
};

(function( $ ) {
    $.widget( "custom.resizebletr", $.Widget,$.extend({},resizeble_tr));
}( jQuery ) );