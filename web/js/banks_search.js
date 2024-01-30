/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom.banksSearch", $.custom.baseW,{
        list:{},
//        listContent:{},
        dialog:null,
        _isOpen:false,
        _create: function() {
            this.fields={
                requestUrl:'Не заданн URL запроса',
                afterInit:{message:'Функция не задана!','default':null},
                width:{message:'Размер не задан используем стандартный!','default':'12cm'},
                address:{message:'Идентификатор адреса не задан ','default':false},
                ks:{message:'Идентификатор кор.счет не задан ','default':false},
                okpo:{message:'Идентификатор ОКПО не задан ','default':false},
                bank:{message:'Идентификатор Банк не задан ','default':false},
                findField:{message:'Поле для поиска','default':'bik'}
            };
            if (this._super()===true){
                this._initall();
            }
        },
        _initall:function(){
            this.element.keyup({self:this},function(e){
                let self=e.data.self;
                let val=self.element.val();
                if (val.length>3){
                    console.debug(self.createMessageText('change'),val);
                    $.post(self.options.requestUrl,{
                        q:val,
                        f:self.options.findField
                    }).done(function(data){
                        console.debug(self.createMessageText('change.answer'),data);
                        if (data.status=='ok'){
                            if ($.type(data.list)==='object'){
                                self._createList(data.list);
                                self._draw();
                            }else{
                                self._clearList();
                                self._draw();
                            }
                        }
                    }).fail(function(data){
                        
                    });
                }else{
                    if (self._isOpen&&self.dialog){
                        console.debug(self.createMessageText('change'),'close.d');
                        self.dialog.css('display','none');
                        self._isOpen=false;
                    }
                }
            });
            this.element.focusout({self:this},function(e){
                let self=e.data.self;
                if ($(e.relatedTarget).hasClass('list-group-item')){
                    console.debug(self.createMessageText('change.answer'),e.relatedTarget);
                }else{
                    if (self.dialog) self.dialog.css('display','none');
                    self._isOpen=false;                    
                }
            });
            this.element.attr('AUTOCOMPLETE','off');
        },
        _clearList:function(){
            let self=this;
            $.each(this.list,function(key,val){
                $(val).unbind('click');
                $(val).remove();
                delete self.list[key];
            });
            this.list={};
//            delete this.listContent;
//            this.listContent={};
        },
        _createList:function(list){
            let self=this;
            this._clearList();
            console.groupCollapsed(self.createMessageText('_createList'));
            $.each(list,function(key,val){
                self.list[key]=self._createItemByBik(key,val);
                self.list[key].click({self:self,dt:val},self._itClick);
                //self.listContent[key]=val;
            });
            console.groupEnd();
        },
        _itClick:function(e){
            let self=e.data.self;
            console.debug(self.createMessageText('_itClick'),e.data.dt);
            if (self.options.ks) $(self.options.ks).val(e.data.dt.ks);
            if (self.options.okpo) $(self.options.okpo).val(e.data.dt.okpo);
            if (self.options.address){
                let strTmp='';
                if (e.data.dt.zip) strTmp+=e.data.dt.zip;
                if (e.data.dt.city){
                    if (strTmp.length) strTmp+=' ';
                    strTmp+=e.data.dt.city;
                }
                if (e.data.dt.address){
                    if (strTmp.length) strTmp+=' ';
                    strTmp+=e.data.dt.address;
                }
                $(self.options.address).val(strTmp);
            }
            if (self.options.zip) $(self.options.address).val(e.data.dt.address);
            if (self.options.bank) $(self.options.bank).val(e.data.dt.full_name);
            $(self.element).attr('value',(e.data.dt.bik));
            $(self.element).val(e.data.dt.bik);
            $(self.element).focusout();
            self.dialog.css('display','none');
            self._isOpen=false;
        },
        _draw:function(){
            if (Object.keys(this.list).length){
                console.debug(this.createMessageText('_show'),this.list);
                if (!this.dialog){
                    this.dialog=$.fn.creatTag('div',{
                        id:'banksDialog',
                        style:{
                            display:'block',
                            top:this.element.offset().top+this.element.height()+'px',
                            left:this.element.offset().left+'px'
                        },
                        'class':'pick-up'
                    });
                    $('body').append(this.dialog);
                    this.isOpen=true;
                }else{
                    if (!this._isOpen){
                        this.dialog.css('display','block');
                        this._isOpen=true;
                    }
                }
                let lst=$.fn.creatTag('div',{
                    'class':'list-group'
                });
                $.each(this.list,function(key,val){
                    lst.append(val);
                });
                this.dialog.empty().append(lst);
            }else{
                if (this.dialog) this.dialog.css('display','none');
                this._isOpen=false;
            }
        },
        _createItemByBik:function(bik,item){
            let a=$.fn.creatTag('a',{href:'#','class':'list-group-item'});
            let h=$.fn.creatTag('h4',{'class':'list-group-item-heading'});
            let p=$.fn.creatTag('p',{'class':'list-group-item-text'});
            p.text(item.short_name);
            h.text(bik);
            a.append(h);
            a.append(p);
            return a;
        },
        getWidgetName:function(){
            return 'banksSearch';
        },

    });
}( jQuery ) );
