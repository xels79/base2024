/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom.oplatadialog", $.custom.maindialog,{
        _oplataIsChange:false,
        _tb:null,
        _tbl:null,
        options:{
            minimizable:false,
            modal:true,
            width:380,
            height:400,
            parent:null,            //Контроллер родитель
            urlGetOplataList:'',    //URL Списка
            urlUpdateOplataList:'', //URL Для обновления
            bigLoaderPicUrl:'',     //URL Картинки загрузка
            zakazId:0,              //Номер заказа
            bigLoaderPicUrl:'',     //URL картинки загрузка
            zakazCoast:0,           //Стоимость заказа
        },
        _create:function(){
            var self=this;
            this.options.beforeClose=function(event, ui){return self._beforeClose.call(self,event, ui, this);};
            this.options.buttons=[
                {
                    text: "Ok",
                    icon: "ui-icon-heart",
                    click: function() {self._okClick.call(self);}
                },
                {
                    text: "Отмена",
                    icon: "ui-icon-heart",
                    click: function() {
                      $( this ).oplatadialog( "close" );
                    }
                }
            ];
            this._super();
            this._tb=$('<tbody>');
            this._tbl=$('<table>').addClass('table oplata').append($('<caption>').text('Заголовок')).append(this._tb);
        },
        _prepareElToSave:function(item,opt,errors){
            var dk=$(item).attr('data-key');
            if ($.type(dk)!=='undefined'){
                var dt=$(item).children(':first-child').children('input').val();
                var summ=$(item).children(':nth-child(2)').children('input').val();
                if (!dt){
                    errors.hasError=$(item).children(':first-child').children('input');
                    errors.errorMessage='Укажите дату';
                    return false;
                }
                if ((!summ || summ=='0')&& dk!=='remove'){
                    errors.hasError=$(item).children(':nth-child(2)').children('input');
                    errors.errorMessage='Сумма не может быть 0';
                    return false;
                }
                switch(dk){
                    case 'new':
                        opt.add[opt.add.length]={date:dt,summ:summ};
                        break;
                    case 'remove':
                        opt.remove[opt.remove.length]=$(item).attr('data-key-remove');
                        break;
                    default:
                        opt.update[parseInt(dk)]={date:dt,summ:summ};
                        break;
                }
            }
            return true;
        },
        _okClick:function(){
            this._showBunner(this.options.bigLoaderPicUrl?$('<img>').attr({src:this.options.bigLoaderPicUrl,height:180}):'');
            var opt={id:this.options.zakazId,update:{},add:[],remove:[]},self=this;
            var errors={hasError:null,errorMessage:''};
            this._tb.children('[data-key]').each(function(){
                return self._prepareElToSave(this,opt,errors);
            });
            if (errors.hasError){
                m_alert('Ошибка заполнения',errors.errorMessage,function(){
                    self._hideBunner();
                    errors.hasError.trigger('focus');
                },false);
            }else{
                if (this._oplataCheckChange()){
                    $.post(this.options.urlUpdateOplataList,opt).done(function(dt){
                        if (dt.status==='ok'){
                            self._oplataIsChange=null;
                            self.close ();
                        }else{
                            self._hideBunner();
                            m_alert('Ошибка срвера',dt.errorText,true,false);
                        }
                    });
                }else{
                    this._oplataIsChange=null;
                    this.close();                            
                }
            }

        },
        _oplataCheckChange:function(){
            if (this._tb===null) return false;
            return this._oplataIsChange || this._tb.children('[data-key=new]').length!==0 || this._tb.children('[data-key=remove]').length!==0;
        },
        _beforeClose:function(event, ui){
            var self=this;
            if (self._oplataIsChange===null || !self._oplataCheckChange()){
                if ($.isFunction(this.options.callBack)) this.options.callBack.call(this.options.parent?this.options.parent:this);
                //d.remove();
            }else{
                m_alert('Внимание','Все не сохранённые данные будут потерены!<br>Закрыть окно?',function(){
                    self._oplataIsChange=null;
                    self.close();
                },true);
                return false;
            }            
        },
        open:function(){
            this._super();
            console.log(this);
            this.uiDialog.addClass('block-format-popup bugalter-oplata-popup');
            this._request();
        },
        close:function(){
            this._super();
            if (this._oplataIsChange===null || !this._oplataCheckChange()){
                this.element.remove();
                this.destroy();
                delete this;
            }
        },
        _toRemove:function(e){
            var id=$(this).parent().parent().attr('data-key');
            var summ=$(this).parent().parent().children(':nth-child(2)').children('input').val();
            if (id==='new'){
                $(this).parent().parent().remove();
            }else if (id!=='remove'){
                $(this).parent().parent().attr({'data-key':'remove', 'data-key-remove':$(this).parent().parent().attr('data-key')}).css({display:'none'});
            }
            e.data.self._updateHeader();
        },
        _addRaw:function(item, beforeLast){
            var ost=parseFloat(this.options.zakazCoast)-this._vsegoOpl();
            var attr={};
            if (beforeLast) attr.placeholder=ost;
            var Data = new Date(Date.now());//, Year=Data.getFullYear(), Month=Data.getMonth(), Day=Data.getDate();
            Data.toLocaleDateString()
            var inp=$('<input>').attr(attr).val(item.summ?parseFloat(item.summ).toFixed(0):'').onlyNumeric({defaultVal:''}).change({self:this},function(e){
                        e.data.self._oplataIsChange=true;
                        var ost=parseFloat(e.data.self.options.zakazCoast)-e.data.self._vsegoOpl();
                        if (ost<0){
                            $(this).val(parseFloat($(this).val())+ost);
                        }
                        if (!$(this).val() || $(this).val()=='0'){
                            $(this).val('');
                            $(this).attr('placeholder',ost.toFixed(0));
                        }
                        e.data.self._updateHeader();
                    }).focusout({self:this},function(e){
                        if (!$(this).val()){
                            var ost=parseFloat(e.data.self.options.zakazCoast)-e.data.self._vsegoOpl();
                            $(this).val(ost).trigger('change');
//                            e.data.self._tb.find('[placeholder]').attr('placeholder');
                        }
                    }).keyup({self:this},function(e){
                        if (!$(this).val()){
                            var ost=parseFloat(e.data.self.options.zakazCoast)-e.data.self._vsegoOpl();
                            $(this).attr('placeholder',ost);
                            e.data.self._updateHeader();
                        }
                    });
            var tr=$('<tr>').attr('data-key',item.id)
                    .append($('<td>').append($('<input>').val(item.dateText?item.dateText:(Data.toLocaleDateString())).datepicker().change({self:this},function(e){
                        e.data.self._oplataIsChange=true;
                    })))
                    .append($('<td>').append(inp))
                    .append($('<td>').append($('<a>').append($('<span>').addClass('glyphicon glyphicon-remove text-danger')).click({self:this},this._toRemove)));
            if (beforeLast)
                tr.insertBefore(this._tb.children(':last-child'));
            else
                this._tb.append(tr);
            if (item.id==='new') inp.trigger('focus');
        },
        _vsegoOpl:function(){
            var rVal=0.0;
            $.each(this._tb.children('[data-key]'),function(){
                if ($(this).attr('data-key')!=='remove'){
                    var val=$(this).children(':nth-child(2)').children('input').val();
                    rVal+=val?parseFloat(val):0;
                }
            });
            return rVal;
        },
        _updateHeader:function(checkCanAdd){
            var hdr=this._tbl.children('caption').empty(),self=this;
            checkCanAdd=checkCanAdd===false?false:true;
            if (this.options.zakazCoast){
                var ost=parseFloat(this.options.zakazCoast)-this._vsegoOpl();
                if (ost>0){
                    if (checkCanAdd){
                        if (this._tb.find('.glyphicon-plus-sign').length===0){
                            this._tb.append($('<tr>').append($('<td>').attr('colspan',3).append($('<a>').append($('<span>').addClass('glyphicon glyphicon-plus-sign text-success')).click(function(){
                                self._addRaw({id:'new',dateText:'',summ:''},true);
                            }))));                            
                        }
                    }
                    hdr .append($('<span>').append($('<span>').text('Стоимость: ')).append($('<span>').text(parseFloat(this.options.zakazCoast).toFixed(0)+'р.')))
                        .append($('<span>').append($('<span>').text('- осталось: ')).append($('<span>').addClass('bg-danger').text(ost.toFixed(0)+'р.')));

                }else{
                    if (checkCanAdd){
                        this._tb.find('.glyphicon-plus-sign').parent().parent().parent().remove();
                    }
                    hdr .append($('<span>').append($('<span>').text('Стоимость: ')).append($('<span>').text(parseFloat(this.options.zakazCoast).toFixed(0)+'р.')))
                        .append($('<span>').append($('<span>').text('-')).append($('<span>').addClass('bg-primary').text('оплачен полностью.')));
                }
            }else{
                hdr.text('Сумма заказа 0???');
            }
        },
        _request:function(){
            var self=this;
            if (!this.options.urlGetOplataList || !this.options.urlUpdateOplataList){
                this.element.append($('<h2>').text('Не заданны URL'));
            }else if (!this.options.zakazId){
                this.element.append($('<h2>').text('Не заданны номер заказа'));
            }else{
                this._showBunner(this.options.bigLoaderPicUrl?$('<img>').attr({src:this.options.bigLoaderPicUrl,height:180}):'');
                $.post(this.options.urlGetOplataList,{id:this.options.zakazId}).done(function(dt){
                    self._hideBunner();
                    self.element.empty();
                    self._tb.empty();
                    if (dt.status==='ok'){
                        self.element.append(self._tbl);
                        self._tb.append($('<tr>').append($('<th>').text('Дата')).append($('<th>').text('Сумма')).append($('<td>')));
                        $.each(dt.values,function(){
                            self._addRaw(this);
                        });
                        self._tb.append($('<tr>').append($('<td>').attr('colspan',3).append($('<a>').append($('<span>').addClass('glyphicon glyphicon-plus-sign text-success')).click(function(){
                            self._addRaw({id:'new',dateText:'',summ:''},true);
                        }))));
                        self._updateHeader();
                    }else{
                        m_alert('Ошибка сервера',dt.errorText?dt.errorText:'Неизвестная ошибка',true,false,function(){
                            self._oplataIsChange=null;
                            self.close();
                        });
                    }
                });

            }
        }
    });
}( jQuery ) );