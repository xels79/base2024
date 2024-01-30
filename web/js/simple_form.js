/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function( $ ) {
    $.widget( "custom.simpleForm", $.custom.baseW,{
        getWidgetName:function(){
            return 'simpleForm'
        },
        files:[],
        _create: function() {
            this.fields={
                'firm_id':{message:'Не заданн идентификатор поля с идентификатором записи родителя','default':false},
                id_associated_column:{message:'Не заданн идентификатор связонного поля формы','default':false},
                id_column_with_record_id:{message:'Не заданн идентификатор колонки формы с id записи','default':false},
                requestUpdateParent:'Не заданна функция обновления родителя',
                controller:'Не заданн контроллер родителя',
                requestUrl:'Не заданн URL запроса',
                form_Id:'Не заданн идентификатор формы',
                baseActionName:'Не заданно базовое имя действия',
                bankSearchUrl:{message:'Url для поиска банка не задан!','default':false},
                headerText:{message:'Заголовок не заданн!','default':''},
                width:{message:'Размер не задан используем стандартный!','default':'26.36cm'},
                afterSubmit:{message:'Функция не задана','default':false},
                requestOtherParam:{message:'Параметры по умолчанию не заданы','default':false},
                onTabValidate:{message:'Функция не задана!','default':false},
                'address':{message:'Идентификатор адреса не задан ','default':false},
                ks:{message:'Идентификатор кор.счет не задан ','default':false},
                okpo:{message:'Идентификатор ОКПО не задан ','default':false},
                bank:{message:'Идентификатор Банк не задан ','default':false},
                contentBackgroundColor:{message:'Цвет контента по умолчанию',default:'#c4c7c7'},
                bodyBackgrounColor:{message:'Цвет тела по умолчанию',default:'#c4c7c7'},
                modalId:{message:'Ид диалога',default:null},
                modal:{message:'Ид диалога',default:false}
                

            };
            var self=this;
            if (this._super()===true){
                
                if ($.type(self.options.requestOtherParam)!=='object'){
                    self.options.requestOtherParam={
                        'req':{
                            name:'add'+self.options.baseActionName //Действие по умолчанию добавить
                        }
                    };
                }
                console.groupCollapsed(self.createMessageText('init'));
                console.debug('options.requestOtherParam',self.options.requestOtherParam);
                console.debug('options.requestUrl',self.options.requestUrl);
                console.groupEnd();
                let opt={
                    requestList:self.options.requestUrl,
                    requestListParam:self.options.requestOtherParam,
                    contentBackgroundColor:self.options.contentBackgroundColor,
                    bodyBackgrounColor:self.options.bodyBackgrounColor,
                    title:self.options.headerText,
                    reqImgUrl:self.options.loadPjaxPicUrl,
                    modal:self.options.modal,
                    afterInit:function(dialog){
                        self.reInit(dialog,self);
                        if ($.isFunction(self.options.afterInit)) self.options.afterInit.call(self);
                        let par=self.element.parent();
                        par.css('top',$(window).height()/2-par.height()/2);
                    },
                    width:self.options.width,
                    beforeClose:function(){
                        console.debug(self.createMessageText('beforeClose'),'run delete');
                        if ($.isFunction(self.options.requestUpdateParent)) 
                            self.options.requestUpdateParent.call(self.element.get(0),self);
                        if ($.isFunction(self.options.beforeClose)) self.options.beforeClose.call(this);
                        self.element.remove();
                        delete self;
                    }
                };
                self.element=$('<div>'). maindialog(opt);
                if (self.options.modalId){
                    self.element.attr('id',self.options.modalId);
//                    opt.id=self.options.modalId;
                }

//                self.dialog.show();
                self.dialog=((self.element).maindialog('instance'));
            }
        },
        reInit:function(dialog,controller){
            let self=this;
            if (self.options.id_associated_column&&self.options.firm_id)
                $(self.options.id_associated_column).val($(self.options.firm_id).val());
            console.log($(self.options.id_associated_column));
            //* Только для нашей фирмы  !!!!!!!!
            self.dialog.body.find('[role=openImgRekvizit]').click({controller:controller,dialog:dialog}, function(e){   //* Только для нашей фирмы  !!!!!!!!
                $(this).next().click();                                                             //* Только для нашей фирмы  !!!!!!!!
            });                                                                                     //* Только для нашей фирмы  !!!!!!!!

            dialog.body.find('[role=cansel]').click(function(){
                dialog.close();
            });
            console.debug(self.createMessageText('form'),$(self.options.form_Id));
            $(self.options.form_Id).submit({dialog:dialog},function(e){
                e.preventDefault();
                let fd=new FormData($(this).get(0));
                let rekviztiId={};
                if (self.options.id_column_with_record_id){
                    rekviztiId=$(self.options.id_column_with_record_id);
                    console.log($(self.options.id_column_with_record_id));
                }
                console.debug(self.createMessageText('submit:data'),fd);
                console.debug(self.createMessageText('submit:url'),self.options.requestUrl);
                if (rekviztiId.length){
                    fd.append('req[name]','change'+self.options.baseActionName);
                    fd.append('id',rekviztiId.val())
                }else if (self.options.requestOtherParam&&self.options.requestOtherParam.req&&self.options.requestOtherParam.req.name){
                    fd.append('req[name]',self.options.requestOtherParam.req.name);
                    if (self.options.requestOtherParam.id) fd.append('id',self.options.requestOtherParam.id);
                }else
                    fd.append('req[name]','add'+self.options.baseActionName);
                $('.has-error').each(function(){
                    $(this).removeClass('has-error');
                    $(this).next().text('');
                });
//                self.busy(true);
                self.ajax(fd);
            });
            //* Следующая функция только для нашей фирмы  !!!!!!!!
            dialog.body.find('[type=file]').change({controller:controller,dialog:dialog}, function(e){
                console.log(e.data.controller);
                let reader=new FileReader();
                let img=$(this).parent().find('img');
                $(this).parent().find('>input[type=hidden]').remove();
                reader.onload=function(e){
                    console.debug('simple_form:[type=file]change()','Предпросмотр готов');
                    img.attr('src',e.target.result);
                    img.next().css('visibility','visible');
                    img.css('visibility','visible');
                };
                reader.readAsDataURL(this.files[0]);
                console.debug('imgsignatureceo',e.data.controller.files);
            });
            self.initRemovePicKeys();
            if (this.options.onTabValidate===true){
                this.element.find('[name]').focusout({self:this},self._onTabValidate);
            }
            if (self.options.bankSearchUrl&&$('[role=banksearch]').length){
                $('[role=banksearch]').banksSearch({
                    requestUrl:self.options.bankSearchUrl,
                    address:self.options.address,
                    ks:self.options.ks,
                    okpo:self.options.okpo,
                    bank:self.options.bank,
                });
            }
        },
        _onTabValidate:function(e){
            let thisObj=this;
            let self=e.data.self;
            let data=$.fn.mSerialize($(self.options.form_Id),[$(this).attr('name')]);
            let rId={};
            if (self.options.id_column_with_record_id)
                rId=$(self.options.id_column_with_record_id);
            data.req={
                name:'validate'+self.options.baseActionName
            };
            if (rId.length){
                data.id=rId.val();
            }
            console.log(data);
            $.post(self.options.requestUrl,data).done(function(data){
                if (data.status==='error'){
                    let fName='';
                    if (data.modelName)
                        fName=data.modelName.toLowerCase();
                    $.each(data.errors,function(id,val){
                        let pId='#'+fName+'-'+id.toLowerCase();
                        $(pId).parent().addClass('has-error');
                        let span=$(pId).parent().next('span');
                        if (span.length===0){
                            span=$(pId).parent().find('.dialog-error');
                            if (span.length===0){
                                span=$.fn.creatTag('span',{
                                    'class':'dialog-error'
                                });
                                $(pId).parent().append(span);
                            }
                        }
                        console.log(span);
                        span.css('display','block');
                        span.text(val);
                    });                    
                }else{
                    $(thisObj).parent().removeClass('has-error');//has-success
                    $(thisObj).parent().addClass('has-success');
                    let span=$(thisObj).parent().next('span');
                    if (!span.length)
                        span=$(thisObj).parent().find('span');
                    if (span.length){
                        span.text('');
                        span.css('display','none');
                    }
                }
                console.debug(self.createMessageText('Validate ok'),data);
            }).fail(function(data){
                console.debug(self.createMessageText('Validate error'),data);
            });
            
        }
    });
}( jQuery ) );