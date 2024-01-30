/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


let dirt_zakaz_controller={
    _isFirstStart:true,
    _zakazDialog:null,
    _doMouseLeave:true,
    opions:{
        zakazRequestURL:'',
        zakazDirtRemoveURL:'',
        isDisainer:false,
    },
    _create:function(){
        this._super();
        let uri=new URI(window.location.href);
        let params=URI.parseQuery(uri.query());
        if (params.page)
            this._requstDate(this._firstStart,{page:params.page});
        else
            this._requstDate(this._firstStart);        
    },
    _firstStart:function(answ, onEnd){ //Вывод первых колонок в пустую таблицу
        this.element.find('.bunn').remove();
        if (answ.colOptions){
            if (this._isFirstStart){
                this._drawCols();
                this._isFirstStart=false;
            }
            this._drawContent();
            this._drawFooter();
            if ($.type(onEnd)==='array' && onEnd.length>1 && $.isFunction(onEnd[1])){
                onEnd[1].call(onEnd[0]);
            }
        }else
            console.warn('materialToOrder._firstStart()','Сервер не передал параметры колонок');
    },
    _secondStart:function(answ,onEnd){ //Вывод следующей стр
        this._firstStart(answ,onEnd);
    },
    _doubleClick:function(e){
        let self=e.data.self,id=$(this).attr('data-key');
        if (self.options.zakazRequestURL){
            let opt={
                dirtId:id
            };
            if (e.data.saveNow){
                opt.saveNow=true;
            }
            $.post(e.data.self.options.zakazRequestURL,opt).done(function(answ){
                console.log(answ);
                if (!e.data.saveNow){
                    if (!self._zakazDialog){
                        self._zakazDialog=$('<div>').appendTo($('body'));
                        self._zakazDialog.zakazAddEditController($.extend({},window.def_opt,{
                            z_id:null,
                            isDirt:true,
                            sendTmpTimeOut:0,
                            restore:answ.zakaz?answ.zakaz.content:null,
                            restoreProductCategory:answ.productCategory,
                            restoreProductCategory2:answ.productCategory2,
                            close:function(){
                                self._zakazDialog.zakazAddEditController('destroy');
                                self._zakazDialog.remove();
                                delete (self._zakazDialog);
                                self._zakazDialog=null;
                            }
                        }));
                    }else{
                        m_alert('Ошибка','Нельзя открыть обновремнно два заказа.',true,false);
                    }
                }else{
                    if (answ.status==='ok'){
                        m_alert('Сообщение','Заказ сохранён под номером '+answ.id,'Закрыть',false);
                    }else{
                        m_alert('Ошибка',answ.errorText);
                    }
                }
            });

        }else
            console.warn('dirtZakazController','Не указан URL запроса (zakazRequestURL)');
    },
    _rightClick:function(e){
        let zID=parseInt($(this).parent().attr('data-key')),el=$(this).parent().get(0);
        zID=!isNaN(zID)?zID:-1;
        e.preventDefault();
        new dropDown({
            posX:e.clientX,
            posY:e.clientY,
            items:[
                {
                    label:'Открыть',
                    click:function(){
                        e.data.self._doubleClick.call(el,e);
                    },
                },
                {
                    label:'В закзазы',
                    click:function(){
                        e.data.saveNow=true;
                        e.data.self._doubleClick.call(el,e);
                    },

                },
                {
                    label:'Удалить',
                    click:function(){
                        if (e.data.self.options.zakazDirtRemoveURL){
                            $.post(e.data.self.options.zakazDirtRemoveURL,{
                                dirtId:zID
                            }).done(function(answ){
                                console.log(answ);
                                if (answ.status&&answ.status=='ok'){
                                    e.data.self.update();
                                }else{
                                    $.fn.dropInfo('Не удалось удалить запись','warning');
                                }
                            });
                        }else{
                            console.warn('dirtZakazController','Не указан URL запроса (zakazDirtRemoveURL)');
                        }
                    }
                }
            ],
            beforeClose:function(){
            }
        });        
    },
    _generateDateRow:function(val,rawNumber){
        let self=this;
        let tr=$('<div class="resize-row">');
        let fldOpt=this._allFieldsWidthByName();
        let fldOptKey=Object.keys(fldOpt);
        $.each(val,function(k,v){
            let td=$.isFunction(self._generateTD)?self._generateTD(k,v,val.id):$('<div class="resize-row">').text(v);
            if ($.inArray(k,fldOptKey)>-1){
                td.css({
                    'max-width': fldOpt[k]+'px',
                    'min-width': fldOpt[k]+'px',
                    'width': fldOpt[k]+'px'
                });
            }
            if (k==='empt')
                td.addClass('last');
            else{
                td.mouseenter(function(){
                    let chld=$(this).parent().children();
                    for (let i=0;i<chld.length;i++){
                        let tmp=$(chld.get(i)).attr('style')?$(chld.get(i)).css('background-color'):false;
                        $(chld.get(i)).addClass('dirt-hover');
                        if (tmp){
                            $(chld.get(i)).css('color','#0F0F0F');
                        }
                    }
                    self._doMouseLeave=true;
                });
                td.mouseleave(function(){
                    if (self._doMouseLeave){
                        let chld=$(this).parent().children();
                        if (!$(this).parent().attr('not-remove-hover')){
                            for (let i=0;i<chld.length;i++){
                                $(chld.get(i)).removeClass('dirt-hover');
                                if ($(chld.get(i)).attr('style')) $(chld.get(i)).css('color','inherit');
                            }
                        }
                    }
                });
                td.dblclick({self:self},function(e){
                    e.data.self._doubleClick.call($(this).parent().get(0),e);
                });
                td.contextmenu({self:self},self._rightClick);
                td.addClass('hand')
            }
            tr.append(td);
            if (k==='id'){
                tr.attr('data-key',v);
                td.text(self.options.isDisainer?v:rawNumber);
            }
            if (k==='stageText'){
                if (self._fieldParams && self._fieldParams.colors && $.isArray(self._fieldParams.colors.stage) && val.id && self._hidden[val.id]){
                    let color=self._fieldParams.colors.stage[parseInt(self._hidden[val.id].stage)];
                    if (color){
                        td.css('background-color',color);
                    }
                }
            }
        });
        //tr.append($('<td>'));
        return tr;
    }
};

(function( $ ) {
    $.widget( "custom.dirtZakazController", $.custom.resizebleTable,$.extend({},dirt_zakaz_controller));
}( jQuery ) );
