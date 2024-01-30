/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var keyboard={
    _target:null,
    _create:function(){
        this._super();
        this.element.attr('tabindex','-1');
        this.element.find('button')
                .click({self:this},this._click)
                .focusin({self:this},this._focusin);
        this.element.focusout({self:this},this._focusout);
    },
    _focusin:function(e){
        var self=e.data.self;
        var rt=$(e.relatedTarget);
        if (rt.length){
            console.log(rt[0].tagName);
            if (!self._target&&rt[0].tagName==='INPUT'){
                self._target=rt;
                console.log(rt);
            }
        }
    },
    _focusout:function(e){
        var self=e.data.self;
        if ($(e.target).attr('data-label')!=='keyboard'||$(e.relatedTarget).attr('data-label')!=='keyboard'){
            self._target=null;

            console.log(e);
        }
    },
    _click:function(e){
        var self=e.data.self;
        if (self._target){
            console.log($(this).text());
            self._target.val(self._target.val()+$(this).text());
        }
    },
};
(function( $ ) {
    $.widget( "custom.keyboard", $.Widget,keyboard);
}( jQuery ) );
$(document).ready(function(){
    $('.keyboard').keyboard();
});