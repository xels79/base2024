/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var firm_list={
    options:{
        editForm:{
            firm_id:'',
            requestUrl:null,
            baseActionName:null,
            loadPicUrl:null,
            pointPicUrl:null,
            loadPjaxPicUrl:null,

            id_associated_column:'',
            id_column_with_record_id:'',
            form_Id:'',
//            baseActionName:'',
            width:'300px',
            onTabValidate:'',
            address:'',
            ks:'',
            okpo:'',
            bank:'',
            contentBackgroundColor:'white',
            bodyBackgrounColor:'white',
            ajaxlist_zakone:'',
            controller:{},
            
            pjaxId:{}
        },
        contextMenuKeys:{
            view:'Просмотр',
            change:'Карточка',
            remove:'Удалить'
        },
        actionNames:{
            add:'добавить',
            change:' параметры оплаты',
            view:'просмотр'
        }

    },
    _editCollName:null,
    _create:function(){
        this._super();
        let self=this;
        //this.options.contextMenuKeys.change+=' '+this.options.editForm.headerText+'а';
        $('#select-WOP').change(function(e){
            var val=parseInt($(this).val());
            if (val){
                self.options.otherRequestOptions.woptype=val;
            }else{
                delete self.options.otherRequestOptions.woptype;
            }
            console.log(self.options.otherRequestOptions)
            self.update();
        });
    },
    _afterInitSub:function(el){
        console.log('childReady');
        $(el).multiTabController({
            requestUrl:this.options.editForm.ajaxlist_zakone,
            headerText:'Заказчики',
            pointPicUrl:this.options.editForm.pointPicUrl,
            loadPicUrl:this.options.editForm.loadPicUrl,
            contentBackgroundColor:'#c4c7c7',
            bodyBackgrounColor:'#fff',
            firm_id:'#curfirm_id',
            pjaxId:this.options.editForm.pjaxId
        });
    },
    _rightClick:function(mainEvent){
        mainEvent.preventDefault();
        mainEvent.stopPropagation();
        let self=mainEvent.data.self;
        self._blockColor=this;
        let items=[];
        let id=$(mainEvent.currentTarget).attr('data-key');
        let isMain=$(mainEvent.currentTarget).attr('data-ismain')?true:false;
        let msgTxt=$(mainEvent.currentTarget).attr('data-message');
        if (msgTxt==='') msgTxt=id;
        if (!msgTxt) msgTxt=$(mainEvent.currentTarget).children(':first-child').text();
        //data-colkey="mainName"
        if (!msgTxt){
            let tmpInd=$('[data-colkey="mainName"]').index();
            if (tmpInd){
                msgTxt=$(mainEvent.currentTarget).children(':nth-child('+(tmpInd+1)+')').text();
            }
        }
        if (!msgTxt) msgTxt=$(mainEvent.currentTarget).children(':nth-child(2)').text();
        if ($.type(self.options.contextMenuKeys.view)==='string'){
            items[items.length]={
                label:self.options.contextMenuKeys.view,
                click:function(e){
                    self._addClick(mainEvent,{
                        'req':{
                            name:'view'+self.options.editForm.baseActionName
                        },
                        id:id,
                        
                    });
                }
            }
        };
        if ($.type(self.options.contextMenuKeys.change)==='string'){
            items[items.length]={
                label:self.options.contextMenuKeys.change+' '+self.options.editForm.headerText+'а',//self.options.contextMenuKeys.change,
                click:function(e){
                    mainEvent.data.afterInit=function(){self._afterInitSub.call(self,this.dialog.body)};
                    self._addClick(mainEvent,{
                        'req':{
                            name:'change'+self.options.editForm.baseActionName
                        },
                        id:id,
                        header:'Карточка '+self.options.editForm.headerText+'а'
//                        header:self.options.contextMenuKeys.change+' '+self.options.editForm.headerText+'а'
                    });
                }
            }
        };
        if (isMain){
            console.log('Настройка меню',self.options);
            items[items.length]={
                label:'Параметры оплаты',
                click:function(e){
                    self._addClick(mainEvent,{
                        'req':{
                            name:'change-main'+self.options.editForm.baseActionName
                        },
                        width:400,
                        id:id
                    });
                }
            }
            
        }
        if ($.type(self.options.contextMenuKeys.remove)==='string'){
            items[items.length]={
                label:self.options.contextMenuKeys.remove,
                click:function(e){
                    new m_alert('Внимание','Удалить запись '+msgTxt+' ?',function(e){
                            $.post(self.options.editForm.requestUrl,{
                                'req':{
                                    name:'remove'+(!isMain?self.options.editForm.baseActionName:'')
                                },
                                id:id
                            }).done(function(data){
                                var url=self.options.editForm.requestUrl+self.options.editForm.classN;
                                if ($(self.options.editForm.firm_id).length) url+='&id='+$(self.options.editForm.firm_id).val();
                                self.update();
                                if (data.status && data.status==='error'){
                                    new mDialog.m_alert({
                                        headerText:"Ошибка выполнения",
                                        content:data.errorText,
                                        okClick:'Закрыть'
                                    });
                                }
                            }).always(function(){
                            }).fail(function(answ){
                                console.error(answ);
                            });
                    });
                }
            }
        };
        new dropDown({
            posX:mainEvent.clientX,
            posY:mainEvent.clientY,
            items:items,
            beforeClose:function(){
                if (self._blockColor){
                    let tmp=self._blockColor;
                    self._blockColor=null;
                    $(tmp).trigger('mouseleave');
                }
            }
        });        
        
    },
    _addClick:function(e,action){
        let self=e.data.self;
        if ($.type(action)!=='object') action={'req':{
                    name:'add'+self.options.baseActionName
            }};
        if ($.type(action.req)!=='object') action.req={};
        $.each(self.options.requestParam,function(key,val){
            action.req[key]=val;
        });
//        console.groupCollapsed(self.createMessageText('addClick'));
        let header;
        if(action.header){
            header=action.header;
        }else{
            header=self.options.editForm.headerText;
            if ($.type(self.options.actionNames)==='object'){
                $.each(self.options.actionNames,function(key,val){
                    if (action.req.name.indexOf(key)===0){
                        header+=' '+val;
                        return false;
                    }
                });
            }
        }
        //String.substr()
        header=header.substr(0,1).toUpperCase()+header.substr(1);
        console.log(e,this);
        let options={ 
            headerText:header,
            firm_id:self.options.editForm.firm_id,
            id_associated_column:self.options.editForm.id_associated_column,
            id_column_with_record_id:self.options.editForm.id_column_with_record_id,
            form_Id:self.options.editForm.form_Id,
            baseActionName:self.options.editForm.baseActionName,
            controller:self,
            requestUrl:self.options.editForm.requestUrl,
            loadPicUrl:self.options.editForm.loadPicUrl,
            width:self.options.editForm.width,
            requestOtherParam:action,
            loadPicUrl:self.options.editForm.loadPicUrl,
            pointPicUrl:self.options.editForm.pointPicUrl,
            onTabValidate:self.options.editForm.onTabValidate,
            'address':self.options.editForm.address,
            ks:self.options.editForm.ks,
            okpo:self.options.editForm.okpo,
            bank:self.options.editForm.bank,
            contentBackgroundColor:self.options.editForm.contentBackgroundColor,
            bodyBackgrounColor:self.options.editForm.bodyBackgrounColor,
            beforeClose:function(){
                  self.update();
            },
            requestUpdateParent:function(){
                
            },
            afterInit:function(){
                if ($.isFunction(e.data.afterInit)) e.data.afterInit.call(this);
                if ($('#zak-blacklist').length){
                    if (!$('#zak-blacklist').val()){
                        $('#zak-blacklist').addClass('disabled');
                    }
                    $('#zak-blacklist')
                        .focusin({self:self},self._blackList)
                        .focusout({self:self},self._blackListFOut);
                }
            }
        };
        
        if (self.options.editForm.loadPjaxPicUrl) options.loadPjaxPicUrl=self.options.editForm.loadPjaxPicUrl
//        if ($.isFunction(self.options.editForm.onChildReady)) options.afterInit=self.options.editForm.onChildReady;
        if (self.options.editForm.bankSearchUrl) options.bankSearchUrl=self.options.editForm.bankSearchUrl;
        if (action){
            if (action.width) options.width=action.width;
        }
        if (e.data){
            if (e.data.post){
                options.requestOtherParam=e.data.post;
            }
        }
//        console.debug(self.createMessageText('addClick.options'),options);
        //new $.custom.simple_form(options);
        console.log(options);
        $.custom.simpleForm(options);
    },
    _blackListFOut:function(e){
        console.log($(this).attr('type'));
        if (!$(this).val() && $(this).attr('type')==='text'){
            $(this).addClass('disabled').val('');
            $(this).parent().removeClass('has-error').parent().find('.help-block').text('').parent().removeAttr('style');
        }else if ($(this).val()){
            $(this).parent().removeClass('has-error').parent().find('.help-block').text('').parent().removeAttr('style');
        }
    },
    _blackList:function(e){
        console.log();
        if ($(this).hasClass('disabled')){
            let el=this;
            m_alert('Внимание','Добавить в чёрный список',function(){
                $(el).removeClass('disabled');
                $(el).trigger('focus');
                $(el).parent().addClass('has-error').parent().find('.help-block').text('Укажите причину').parent().css('display','block');
            },true);
            $(this).trigger('blur');
        }
    },
    _bindRightClick:function(tr){
        tr.contextmenu({self:this},this._rightClick);
        tr.attr('data-ismain','true');
    },
    _blockColor:null,
    _mEnter:function(e){
        $(this).children(':not(:last-child)').each(function(){
            if ($(this).hasClass('last'))return;
            let bkC=$(this).css('background-color');
            if (bkC==='rgba(0, 0, 0, 0)' || !bkC){
                bkC='rgba(255, 255, 255, 0)';
            }
            $(this).attr('data-color-back',bkC);
            $(this).css('background-color','rgb(182,218,255)');
        });
    },
    _mLeave:function(e){
        if (!e.data.self._blockColor){
            $(this).children(':not(:last-child)').each(function(){
                if ($(this).attr('data-color-back')){
                    $(this).css('background-color',$(this).attr('data-color-back'));
                }
            });
        }
    },
    _generateDateRow:function(dt,hidden){ //Новый ряд dt=Содержимое
        console.log('fL=>_generateDateRow: page',this._fieldParams);
        console.log('fL=>_generateDateRow: page',this.element.children('tr:first'));
        console.log('fL=>_generateDateRow: page',this._hidden[dt.firm_id]);
        //var cnt=this.element.children('tbody').children(':first-child').children().length;
        let cnt=this._tHeader.children( ':first-child' ).children().length;
        let self=this;
        let newCnt=1,rVal=$( '<div class="resize-row">' )
                .attr('data-key',dt.firm_id);
        let fldOpt=this._allFieldsWidthByName();
        let fldOptKey=Object.keys(fldOpt);

        if (this._showAttentionColumn) rVal.append(this._attentionColl(dt.firm_id))
        rVal.append(this._technikalColl(dt.firm_id));
        $.each(dt,function(key,val){
            if (newCnt < cnt){
                var td=$( '<div class="resize-cell">' );
                if ($.inArray(key,fldOptKey)>-1){
                    td.css({
                        'max-width': fldOpt[key]+'px',
                        'min-width': fldOpt[key]+'px',
                        'width': fldOpt[key]+'px'
                    });
                }
                if (key==='firm_id')
                    td.text(self._firstElNum++);
                else
                    td.text(val);
                if (newCnt === cnt-(self._showAttentionColumn?3:2)) td.addClass('last'); //Устоновить последнюю колонку
                if (self._hidden[dt.firm_id].blackList && key!=='empt'){
                    td.css('background-color','#ddd');
                    td.attr('title','В чёрном списке: '+self._hidden[dt.firm_id].blackList);
                }
                rVal.append(td);
            }
            newCnt++;
        });
        //rVal.append($('<td>'));
        this._bindRightClick(rVal);
        //rVal.mouseenter({self:this},this._mEnter).mouseleave({self:this},this._mLeave);
        return rVal;
    },

};

(function( $ ) {
    $.widget( "custom.firmList", $.custom.zakazListController,$.extend({},firm_list));
}( jQuery ) );
