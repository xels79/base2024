/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom.activeDD2", $.Widget,$.extend({},{
        _inp:null,
        _firstStart:true,
        hideMessage:false,
        _menu:null,
        _inp_cover:null,
        _megabunner:null,
        options:{
            otherParam:{},
            onClickOrChange:null,
            onReady:null,
            afterUpdate:null,
            inputId:false,
            requestUrl:'',
            source:[],
            change:null
        },
        _create: function() {
            this._super();
            let self=this;
            console.log(this.element); 
            if (this.element.get(0).tagName==='SELECT'){ 
                this.element.children().each(function(){
                    self.options.source.push({
                        label:$(this).text(),
                        value:$(this).attr('value')
                    });
                });
                let input=$('<input>').attr({
                    name:this.element.attr(name),
                    type:'hidden'
                }).css('display','none');
                this._inp =$('<input>').addClass('activedd2-v');
                this.element.replaceWith(input);
                this.element=input;
                this._inp.insertAfter(this.element);
                this._postInit();
            }else if (this.element.get(0).tagName!=='INPUT'){
                    console.error('activeDD2 - ошибка инициализации объект должен быть селект или инпут');
                }else{
                    if (!this.options.source && this._firstStart){
                        this.doRequstList(this._postInit);
                    }else{
                        this._postInit();
                    }
                }

        },
        _postInit:function(){
            if (!this._firstStart){
                return;
            }else{
                this._firstStart=false;
                $('<div>').addClass('activedd2-button').click({self:this},this._openClick).append($('<span>').addClass('glyphicon glyphicon-chevron-down')).insertAfter(this._inp);
                this._inp.parent().addClass('activedd2');
                //$().keydown();
                this._inp
                    .keydown({self:this},this._kDown)
                    .keyup({self:this},this._kUp);
                this._menu=$('<ul>');
                this._inp.parent().append(this._menu);
            }
        },
        _prepareMenu:function(){
            this._menu.empty();
            let val=!this._megabunner?this._inp.val():this._megabunner.find('.activedd2-v').val();
            console.log(this.options.source);
            this.options.source.forEach((item)=>{
                let li=$('<li>');
                if (val.length){
                    if (item.label.indexOf(val)>-1){
                        let tmp=item.label.substr(0,item.label.indexOf(val)-1);
                        tmp+='<b>'+val+'</b>'+item.label.substr(item.label.indexOf(val)+val.length);
                        li.html(tmp);
                        this._menu.append(li);
                    }
                }else{
                    li.text(item.label).attr({
                        value:item.value
                    });
                    this._menu.append(li);
                }
            })
        },
        _showMenu:function(setFocus){
            this._prepareMenu();
            if (!this._megabunner){
                
                this._megabunner=$('<div>').css({
                    position:'absolute',
                    top:0,
                    left:0,
                    'min-width':$(document).width(),
                    'min-height':$(document).height(),
                    'z-index':50000
                }).addClass('activedd-bunner').click({self:this},this._hideClick).appendTo('body');
               let inp=this._inp.clone();
               let btn=this._inp.parent().children('.activedd2-button').clone();
               let inpcont=$('<div>').addClass('activedd2').append(inp).append(btn).width(this._inp.parent().width());
               let menuCont=$('<div>').addClass('activedd2-menu-cont').append(this._menu);
               let cont=$('<div>').append(inpcont).append(menuCont).css({
                   position:'absolute',
                   top:this._inp.parent().offset().top-1,
                   left:this._inp.parent().offset().left,
                   'min-width':this._inp.parent().innerWidth()-5
               });
               this._megabunner.append(cont);
               inp.keydown({self:this},this._kDown)
                  .keyup({self:this},this._kUp);
               btn.click({self:this},this._hideClick);
               inp.click(function(e){
                   e.stopPropagation();
                   e.preventDefault();
               });
               if (setFocus){
                   inp.trigger('focus');
                   inp.get(0).setSelectionRange(inp.length,inp.length);
               }
            }   
        },
        _hideMenu:function(){
            if (this._megabunner){
                this._megabunner.remove();
                this._megabunner=null;
            }
        },
        _openClick:function(e){
            e.data.self._showMenu.call(e.data.self);
            e.preventDefault();
        },
        _hideClick:function(e){
            e.data.self._hideMenu.call(e.data.self);
            e.preventDefault();
        },
        _kDown:function(e){
            console.log(e);
        },
        _kUp:function(e){
            console.log($(this).val(),$(this).val().length);
            //this._prepareMenu();
            if ($(this).val().length>2){
                if (e.data.self._megabunner){
                    e.data.self._prepareMenu();
                }else{                    
                    e.data.self._showMenu(true);
                }
            }else if (e.data.self._megabunner){
                e.data.self._prepareMenu();
            }

        },
        _doRequest:function(callB){
            let oParam=$.isFunction(this.options.otherParam)?this.options.otherParam.call():this.options.otherParam;
            let self=this;
            if (!oParam) oParam={};
            this._showBunner();
            console.log('dropD2',this.options.requestUrl,oParam);
            $.post(this.options.requestUrl,oParam).done(function(data){
                if (data.status==='error'){
//                    let hdr=$('<span>').text('Ура'+data.headerText);
                    console.log('activeDD2',self,'hasError');
                    if (!self.hideMessage)
                        m_alert(data.headerText,data.errorText,true,false);
                    else
                        self.hideMessage=false;
                }
                if (data.source){
                    self.option('source',data.source);
                }else{
                    console.warn('activeDD2: в ответ на запрос данные не получены!');
                }
                self._hideBunner();
                if ($.isFunction(callB)) callB.call(self);
            });
        },
        _hideBunner:function(){
            if (this.options.loadPicUrl) {
                this._inp.parent().children('img').remove();
            }
            this._inp.parent().children('span').removeAttr('style');
            this._inp.removeAttr('disabled');
        },
        _showBunner:function(){
            if (this.options.loadPicUrl){
                this._inp.parent().children('span').css('display','none');
                $('<img>').attr({
                    src:this.options.loadPicUrl,
                    class:this.options.bunnerClass,
                }).insertAfter(this._inp);
            }
            this._inp.attr('disabled',true);
        },

    }));
}( jQuery ) );
