/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom.navigateKey", $.Widget,$.extend({
        _create: function() {
            this._super();
            this.element.keydown({self:this},this.__navigateKey);
        },
        __navigateKeyUpDown:function(key,el,setToFirst,repeat){
            let ind=$(el).parent().index();
            let tr=repeat?repeat:$(el).parent().parent();
            setToFirst=setToFirst===true?true:false;
            if (key==='ArrowDown'){
                tr=tr.next();
            }else{
                tr=tr.prev();
            }
            if (tr.length && (tr.children().length>ind || (setToFirst && tr.children().length>0))){
                let el2=$(tr.children().get(setToFirst?0:ind)).children('input');
                if (el2.length){
                    el2.trigger('focus');
                    return true;
                }else if(!repeat){
                    return this.__navigateKeyUpDown(key,el,setToFirst,tr);
                }
            }else{
                return false;
            }
        },
        __navigateKeyLeftRight:function(key,el,event){
            let td=$(el).parent();
            if (el.selectionStart === el.selectionEnd){
                if (!el.selectionStart&&key==='ArrowLeft'){
                    td.prev().children('input').trigger('focus').navigateKey('cursorEnd');
                    event.preventDefault();
                }else if(el.selectionStart===$(el).val().length && key==='ArrowRight'){
                    let inp=td.next()
                        .children('input')
                        .trigger('focus')
                        .navigateKey('cursorStart');
                    event.preventDefault();
                    if (inp.length) 
                        return true;
                    else
                        return false;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        },
        __navigateTab:function(key,el,event){
            if (!this.__navigateKeyLeftRight('ArrowRight',el,event)){
                if (!this.__navigateKeyUpDown('ArrorDown',el,true)){
                    this.element.parent().parent().parent().children(':first-child').children().each(function(){
                        if ($(this).children('input').length){
                            $(this).children('input').trigger('focus');
                            return false;
                        }
                    });
                }
            }
        },
        __navigateKey: function(e){
            let self=e.data.self;
            switch (e.key){
                case 'ArrowUp':case 'ArrowDown':
                    self.__navigateKeyUpDown(e.key,this);
                    e.preventDefault();
                case 'ArrowLeft':case 'ArrowRight':
                    self.__navigateKeyLeftRight(e.key,this,e);
                break;
                case 'Enter':
                    self.__navigateTab(e.key,this,e);
                break;
            }
        },
        cursorEnd:function(){
            let lng=this.element.val().length;
            this.element.get(0).selectionEnd=lng;
            this.element.get(0).selectionStart=lng;
        },
        cursorStart:function(){
            this.element.get(0).selectionEnd=0;
            this.element.get(0).selectionStart=0;
        }
    }));
}( jQuery ) );
