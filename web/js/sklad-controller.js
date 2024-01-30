/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var tabIndex=1;
var _infoBlind={
    _create:function(){
        var self=this;
        this._super();
        var el=this.element;
        setInterval(function(){
            if (el.text())
                if (el.css('visibility')==='hidden'){
                    el.css('visibility','visible');
                }else{
                    el.css('visibility','hidden');
                }
        },200);
    },
};
var _skladColors={
    _back:'',
    options:{
        updateColorsUrl:''
    },
    _create:function(){
        this._super();
        this.element.attr('tabindex',tabIndex);
        tabIndex+=2;
        this.element.focus({self:this},this._enter);
        this.element.click(function(){
            $(this).trigger('focus');
        });
    },
    _enter:function(e){
        var self=e.data.self;
        self._back=$(this).text();
        var inp=$('<input>').attr({
            type:'text',
            tabindex:parseInt($(this).attr('tabindex'))+1
        }).val(self._back).focusout({self:self},self._leave).keydown({self:self},self._keydown).onlyNumeric({allowPoint:true});
        
        
        $(this).empty().append(inp);
        inp.trigger('focus');
        inp[0].setSelectionRange(0, inp.val().length);
//        // сперва получаем позицию элемента относительно документа
//        var scrollTop = inp.offset().top;
//
//        // скроллим страницу на значение равное позиции элемента
//        $(document).scrollTop(scrollTop);
    },
    _leave:function(e){
        var self=e.data.self;
        var datachemikal=self.element.attr('data-table')?self.element.attr('data-table'):'0';
        if ($(this).val()!==self._back){
            self._back=$(this).val();
            if (self.options.updateColorsUrl){
                $('#sklad-info').text('Сохраняю...');
                $.post(self.options.updateColorsUrl,{
                    id:self.element.attr('data-key'),
                    'datacolumnname':self.element.attr('data-column-name'),
                    datachemikal:datachemikal,
                    val:self._back
                }).done(function(dt){
                    $('#sklad-info').text('');
                    if (dt.maxDate && dt.maxTime){
                        var tmpName=(datachemikal==='color'||datachemikal==='other')?'color':datachemikal;
                        $('#'+tmpName+'-max-date').children('span:first-child').text(dt.maxDate+' - '+dt.maxTime);
                    }
                });
            }else
                console.warn('skladColors не задан url сохранения');
        }
        $(this).parent().empty().text(self._back); 
   },
   _keydown:function(e){
        if (e.keyCode===13){
            $(this).trigger('focusout');
        }else if (e.keyCode===27){
            $(this).val(e.data.self._back).trigger('focusout');
        }else if (e.keyCode===39){
            $(this).parent().next().trigger('click');
        }else if (e.keyCode===37){
            $(this).parent().prev().trigger('click');
        }else if (e.keyCode===38){
            var el=$($(this).parent().parent().prev().children().get($(this).parent().index()));
            if (el.length&&el.get(0).tagName==='TD') el.trigger('click');
        }else if (e.keyCode===40){
            var el=$($(this).parent().parent().next().children().get($(this).parent().index()));
            if (el.length&&el.get(0).tagName==='TD') el.trigger('click');
            
        }else
            console.log(e.keyCode);
   }
};
(function( $ ) {
    $.widget( "custom.skladColors", $.Widget,_skladColors);
}( jQuery ) );
(function( $ ) {
    $.widget( "custom.infoBlind", $.Widget,_infoBlind);
}( jQuery ) );
