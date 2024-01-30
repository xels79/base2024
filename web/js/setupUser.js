/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
let setup_User_Dialog={
        options:{
            requestList:null,
            userChangeUrl:null,
            userRemoveUrl:null,
            accssecChangeUrl:null,
            minWidth:800,
            minHeight:450,
            editId:0
        },
        pageUrl:'',// Для работы pajx и pager
        preventClose:true,
        _helpPauseTimer:0,
        _helpTimer:0,
        _helpImg:null,
        _create:function(){
            let self=this;
            this._super();
            this.options.beforeClose=function(e,ui){
                if (self.preventClose&&!$('#user-accsses-save').hasClass('disabled')){
//                    e.preventDefault();
                    let a=new m_alert('Внимание','<p>Настройки доступа не сохранены.</p><p>Игнорировать и выйти?</p>',function(e){
                        a.close();
                        delete a;
                        self.preventClose=false;
                        self.close();
                    },{
                        'label':'Отмена',
                        click:function(){a.close();delete a;}
                    });
                    return false;
                }
            }
            console.log('setupUserDialog',this);
        },
/*
 * Получение данных с сервера
 * и предворительная настройка
 * @returns {undefined}
 */
        _requestDate:function(){
            let self=this;
            self.pageUrl=this.options.requestList;
            $.post(this.options.requestList).done(function(answ){
                self.element.empty().html(answ);
                $('#userlistPjax').pjax('a',{
                    push:false,
                    timeout:2000,
                    type:'POST',
                    container:'#userlistPjax'
                });
                $('#userlistPjax').on('pjax:success',function(e){
                    self.hideBunner();
                    self._initAfterUpdate($(this));
                }).on('pjax:click',function(e){
                    self.pageUrl=$(e.target).attr('href');
                });
                $('#user-add').click(function(){
                    self._showChangeDialog(null,0);
                });
                $('#roles-list').children().click({self:self},self._rolesListClick);
                $('[aria-labelledby=select-allow-actions]').find('a').click({self:self,elId:'user-accsses-info-allow',acrossId:'user-accsses-info-deny'},self._allowDenyActionClick)
                    .mouseenter({self:self},self._elMouseEnter)
                    .mouseleave({self:self},self._elMouseLeave);
                $('[aria-labelledby=select-deny-actions]').find('a').click({self:self,elId:'user-accsses-info-deny',acrossId:'user-accsses-info-allow'},self._allowDenyActionClick)
                    .mouseenter({self:self},self._elMouseEnter)
                    .mouseleave({self:self},self._elMouseLeave);
                self._initAfterUpdate($('#userlistPjax'));
            }).fail(function(answ){
                new m_alert('Ошибка сервера',answ.responseText,true,false);
                console.error(answ);
            });
        },
/*
 * Правая вкладка "Настройка уровней доступа"
 * Добовляет элемент к списку
 * @param {type} a Тэг а на который кликнули
 * @param {string} elId id - списка для добавления
 * @param {string} accrosElId  id - списка для удаления (соседний список)
 * @returns {boolean} true - если произведены изменения
 */
        _addElToList:function(a,elId,accrosElId){
            let el=$('#'+elId);
            let acrossEl=$('#'+accrosElId);
            if (!el.children('[value = "'+$(a).attr('data-value')+'"]').length){
                if ($(a).attr('data-action')==='all'){
                    el.children('[data-controller = "'+$(a).attr('data-controller')+'"]').remove();
                    acrossEl.children('[data-controller = "'+$(a).attr('data-controller')+'"]').remove();
                }else{
                    el.children('[value = "'+$(a).attr('data-controller')+'/all"]').remove();
                }
                let cld=el.children('[data-controller = "'+$(a).attr('data-controller')+'"]');
                if (!cld.length){
                    $('<option>')
                        .attr({
                            'data-controller':$(a).attr('data-controller'),
//                            disabled:true
                        })
                        .contextmenu(function(e){e.preventDefault();})
                        .text($(a).parent().parent().children('[data-controller = "'+$(a).attr('data-controller')+'"].dropdown-header').text())
                        .mouseenter({self:this},this._elMouseEnter)
                        .mouseleave({self:this},this._elMouseLeave)
                        .appendTo(el);
                }
                let accroOption=acrossEl.children('[value = "'+$(a).attr('data-value')+'"]');
                if (accroOption.length){
                    if (acrossEl.children('[data-controller = "'+$(a).attr('data-controller')+'"]').length<3){
                        acrossEl.children('[data-controller = "'+$(a).attr('data-controller')+'"]').remove();
                    }else{
                        accroOption.remove();
                    }
                }
                if (acrossEl.children('[value = "'+$(a).attr('data-controller')+'/all"]').length){
                    acrossEl.children('[data-controller = "'+$(a).attr('data-controller')+'"]').remove();
                }
                $('<option>')
                        .attr({
                            'data-controller':$(a).attr('data-controller'),
                            value:$(a).attr('data-value')
                        })
                        .contextmenu({self:this},this._elRightClick)
                        .mouseenter({self:this},this._elMouseEnter)
                        .mouseleave({self:this},this._elMouseLeave)
                        .text($(a).text())
                        .insertAfter(el.children('[data-controller = "'+$(a).attr('data-controller')+'"]:last'));
//                $(a).css('display','none');
                return true;
            }else
                return false
        },
        _elMouseEnter:function(e){
            let self=e.data.self,op=0,opst=0.2;
            let fName='/pic/help/accsses/'+($(this).attr('value')||$(this).attr('data-value')||$(this).attr('data-controller')).replace(new RegExp("/",'g'),'_')+'.jpg';
            let zInd=parseInt(self.element.css('z-index'));
            zInd=isNaN(zInd)?50000:(zInd+1);
            if (self._helpPauseTimer){
                clearTimeout(self._helpPauseTimer);
                self._helpPauseTimer=0;
            }
            if (self._helpTimer){
                clearInterval(self._helpTimer);
                self._helpTimer=0;
            }
            if (self._helpImg){
                self._helpImg.remove();
                delete self._helpImg;
                self._helpImg=null;
            }
            self._helpPauseTimer=setTimeout(function(){
                self._helpPauseTimer=0;
                self._helpImg=$('<img>')
                                .addClass('help-img-popup')
                                .css({
                                    'z-index':zInd,
                                    top:e.pageY+5,
                                    left:e.pageX+15,
                                })
                                .attr({
                                    src:fName
                                })
                                .appendTo('body');
                self._helpTimer=setInterval(function(){
                    op+=opst;
                    if (op>=1){
                        clearInterval(self._helpTimer);
                        self._helpTimer=0;
                        op=1;
                    }
                    self._helpImg.css('opacity',op);
                },100);
            },500);
            console.log(fName);
        },
        _elMouseLeave:function(e){
            let self=e.data.self;
            if (self._helpTimer){
                clearInterval(self._helpTimer);
                self._helpTimer=0;
            }
            if (self._helpImg){
                self._helpImg.remove();
                delete self._helpImg;
                self._helpImg=null;
            }
            if (self._helpPauseTimer){
                clearTimeout(self._helpPauseTimer);
                self._helpPauseTimer=0;
            }
        },
/*
 * Правая вкладка "Настройка уровней доступа"
 * Правая кнопка
 * @param {type} e - click event
 * @returns {undefined}
 */
        _elRightClick:function(e){
            let self=e.data.self,el=$(this);
            $(this).parent().val($(this).val());
            let ddOpt={
                posX:e.clientX,
                posY:e.clientY,
                items:[
                    {
                        'label':'Удалить экшен',
                        click:function(){
                            let controllerName=el.attr('data-controller');
                            //el.remove();
                            if (el.parent().children('[data-controller = "'+controllerName+'"]').length<3){
                                el.parent().children('[data-controller = "'+controllerName+'"]').remove();
                            }else{
                                el.remove();
                            }
                            self._storeAccssesParamToCurrentRole();
                        }
                    }
                ]
            };
            e.preventDefault();
            new dropDown(ddOpt);
        },

/*
 * Правая вкладка "Настройка уровней доступа"
 * Событие клик выпадающего меню
 * @param {type} e - параметр события
 * @returns {undefined}
 */
        _allowDenyActionClick:function(e){
            if (e.data.self._addElToList(this,e.data.elId,e.data.acrossId))
                e.data.self._storeAccssesParamToCurrentRole();
        },
/*
 * Правая вкладка "Настройка уровней доступа"
 * Выводит параметры массива в id1
 * @param {array} arr
 * @param {string} idMenu - меню
 * @param {string} id1 - элемент селект1
 * @param {string} id2 - элемент селект2
 * @returns {undefined}
 */
        _showValues:function(arr,idMenu,id1,id2){
            let self=this;
            $('#'+id1).empty();
            $.each(arr,function(k,v){
                let el=$('#'+idMenu).next().find('[data-value="'+v+'"]');
                self._addElToList(el, id1, id2);
                self._hideMenuItemByVal(idMenu,el.attr('data-controller'),v);
            });            
        },
        
/*
 * Правая вкладка "Настройка уровней доступа"
 * Список ролей выбран, событие клик
 * @param {type} e - параметры события
 * @returns {undefined}
 */
        _rolesListClick:function(e){
            let self=e.data.self;
            let dt=JSON.parse($(this).attr('data-value'));
            $('#select-allow-actions').removeClass('disabled');
            $('#select-deny-actions').removeClass('disabled');
            $('#select-allow-actions').next().children().removeAttr('style');
            $('#select-deny-actions').next().children().removeAttr('style');

            self._showValues(dt.allow,'select-allow-actions', 'user-accsses-info-allow', 'user-accsses-info-deny');
            self._showValues(dt.denied,'select-deny-actions', 'user-accsses-info-deny', 'user-accsses-info-allow');
        },
/*
 * Правая вкладка "Настройка уровней доступа"
 * Кнопка сохранения данных - сохраняет на сервер
 * @param {type} e click event
 * @returns {undefined}
 */
        _saveToServerccssesParamToCurrentRole:function(e){
            let self=e.data.self;
            let el=$(this);
            let rulls={};
            $('#roles-list').children().each(function(){
                let id=parseInt($(this).attr('value'));
                rulls[id]=$(this).attr('data-value');
            });
            console.log(rulls);
            $.post(self.options.accssecChangeUrl,{rulls:rulls}).done(function(answ){
                console.log(answ);
                if (answ.status==='ok'){
                    $.fn.dropInfo('Сохранено.');
                    $('#user-accsses-save')
                            .unbind('click')
                            .addClass('disabled');
                }else{
                    console.error('_saveToServerccssesParamToCurrentRole',answ.errors);
                }
            }).fail(function(answ){
                new m_alert('Ошибка сервера',answ.responseText,true,false);
                console.error(answ);
            });
        },
/*
 * Правая вкладка "Настройка уровней доступа"
 * Убирает пункт меню по значению
 * @param {string} mId - id дроп доун меню
 * @param {string} cotrollerId - контроллер
 * @param {string} val -значение
 * @returns {undefined}
 */
        _hideMenuItemByVal:function(mId,cotrollerId,val){
            let menu=$('#'+mId).next();
            let tmp=val.split('/');
            let action=tmp[tmp.length-1];
            if (action!=='all'){
                menu.find('[data-value="'+cotrollerId+'/'+action+'"]').parent().css('display','none');
            }else{
                menu.find('[data-controller="'+cotrollerId+'"]').each(function(){
                    if ($(this).tagName=='A'||$(this).get(0).tagName=='A')
                        $(this).parent().css('display','none');
                    else
                        $(this).css('display','none');
                });
            }
        },
/*
 * Правая вкладка "Настройка уровней доступа"
 * Сохраняет выбранные поз. в элементе роли
 * @returns {undefined}
 */        
        _storeAccssesParamToCurrentRole:function(){
            let rVal={
                allow:[],
                denied:[],
            },roleNum=parseInt($('#roles-list').val()),self=this;
            roleNum=isNaN(roleNum)?0:roleNum;
            $('#select-allow-actions').next().children().removeAttr('style');
            $('#select-deny-actions').next().children().removeAttr('style');
            $('#user-accsses-info-allow').children('[value]').each(function(){
                rVal.allow[rVal.allow.length]=$(this).attr('value');
                console.log(this);
                self._hideMenuItemByVal('select-allow-actions',$(this).attr('data-controller'),$(this).attr('value'));
            });
            //select-deny-actions
            $('#user-accsses-info-deny').children('[value]').each(function(){
                rVal.denied[rVal.denied.length]=$(this).attr('value');
                self._hideMenuItemByVal('select-deny-actions',$(this).attr('data-controller'),$(this).attr('value'));
            });
            if (roleNum){
                console.log(rVal,$('#roles-list').children().get(roleNum-1));
                $($('#roles-list').children().get(roleNum-1)).attr('data-value',JSON.stringify(rVal));
                if (this.options.accssecChangeUrl&&$('#user-accsses-save').hasClass('disabled')){
                    $('#user-accsses-save')
                            .click({self:this},this._saveToServerccssesParamToCurrentRole)
                            .removeClass('disabled');
                }
            }
        },
        
/*
 * Левая вкладка "Пользователи"
 * После получения pjax данных
 * @param {type} el - pjax контейнер
 * @returns {undefined}
 */        
        _initAfterUpdate:function(el){
            el.find('[data-key]').contextmenu({self:this},this._userListContextMenu);
        },
/*
 * Левая вкладка "Пользователи"
 * Вывод ошибок во всплывающей форме для модели TblUser
 * @param {type} err - список ошибок от Yii ActiveRecord
 * @returns {undefined}
 */
        _userListUserFormShowError:function(err){
            console.log(err);
            $.each(err,function(k,v){
                $('#tbluser-'+k).parent().addClass('has-error');
                $('#tbluser-'+k).next().text(v);
            });
        },


/*
 * Левая вкладка "Пользователи"
 * Диалог изменеия пользователя
 * @param {type} el: $('<tr>') - на катором произошол клик
 * @param {type} id: id -заказа
 * @returns {undefined}
 */        
        _showChangeDialog:function(el,id){
            let self=this;
            let dialog=new $.custom.viewDialog({
                 url:self.options.userChangeUrl,
                 post:true,
                 modal:true,
                 minWidth :600,
                 minHeight:400,
                 id:id?id:0,
                 checkId:false,
                 addClass:'modal2',
                 title:el?('Редактируем профиль пользователя №'+el.children(':first-child').text()):(id?'Профиль пользователя':'Добавить нового порлзователя'),
                 afterGetCompliteAndPaste:function(){
                     this.find('[type=text]').focusout(function(){
                         $(this).parent().removeClass('has-error');
                         $(this).next().text('');
                     });
                     $('#user-change-pass').click(function(){
                         let hInp=$(this).parent().parent().prev();
                         hInp.children('label').text('Новый пароль');
                         hInp.find('input').attr('type','password');
                         console.log(hInp);
                     });
                },
                buttons:[
                    {
                        text:'Сохранить',
                        click:function(){
                             let fd=new FormData($('#user-form')[0]);
                             dialog.showBunner('Сохранение....');
                             fd.append('id',id);
                             $.ajax({
                                 type:'post',
                                 url:self.options.userChangeUrl,
                                 data:fd,
                                 cache: false,
                                 contentType: false,
                                 processData: false,
                                 forceSync: false,
                                 complete:function(){
                                     dialog.hideBunner();
                                 },
                                 success:function(answ){
                                    if (answ.status==='ok'){
                                         dialog.close();
                                         if ($('#userlistPjax').length){
                                            if (el) el.removeClass('selected');
                                            self.showBunner();
                                            $.pjax.reload('#userlistPjax', {
                                                url:self.pageUrl,
                                                push:false,
                                                timeout:2000,
                                                replace:false,
                                                type:'POST'
                                           });
                                        }
                                    }else{
                                        self._userListUserFormShowError(answ.errors,dialog);
                                    }
                                 }
                             });
                        },
                        'class':'btn btn-main'
                    },
                    {
                        text:'Отменить',
                        click:function(){dialog.close();if (el) el.removeClass('selected');},
                        'class':'btn btn-main-font-black'
                    }
                ]
            }) ;
            
        },
/*
 * Левая вкладка "Пользователи"
 * Меню по правой кнопки
 * @param {type} e - click event
 * @returns {undefined}
 */
        _userListContextMenu:function(e){
            let self=e.data.self,id=parseInt($(this).attr('data-key')),el=$(this);
            e.preventDefault();
            console.log(this);
            id=!isNaN(id)?id:0;
            let ddOpt={
                posX:e.pageX,
                posY:e.pageY,
                items:[]
            };
            if (id){
                if (self.options.userChangeUrl){
                    el.addClass('selected');
                    ddOpt.items[ddOpt.items.length]={
                        'label':'Изменить',
                        'click':function(){
                            self._showChangeDialog(el,id);
                        }
                    }
                }
                if (self.options.userRemoveUrl){
                    el.addClass('selected');
                    ddOpt.items[ddOpt.items.length]={
                        label:'Удалить',
                        click:function(){
                            let a=new m_alert('Внимание','Удалить пользователя №'+el.children(':first-child').text(),function(){
                                $('.selected').removeClass('selected');
                                    $.post(self.options.userRemoveUrl,{id:id}).done(function(answ){
                                        if (answ.status==='ok'){
                                            $.pjax.reload('#userlistPjax', {
                                                url:self.pageUrl,
                                                push:false,
                                                timeout:2000,
                                                replace:false,
                                                type:'POST'
                                            });
                                        }else{
                                            console.error('userListContextMenu->userRemove:',answ.errorText);
                                        }
                                    });
                                delete a;
                            },function(){$('.selected').removeClass('selected');delete a;});
                        }
                    };
                }
                if (ddOpt.items.length){
                    new dropDown(ddOpt);
                }
            }
        },
        open:function(){
            if (!this.options.editId&&this.options.requestList){
                this._super();
                this._requestDate();
            }else if(this.options.editId&&this.options.userChangeUrl){
                this._showChangeDialog(null,this.options.editId);
            }else{
                new m_alert('Ошибка','setupUserDialog - неверные настройки',true,false);
            }
        },
};

(function( $ ) {$.widget( "custom.setupUserDialog", $.custom.maindialog,setup_User_Dialog);}( jQuery ) );

(function( $ ) {$.widget( "custom.setupUser", $.custom.openButton,{_className:'setupUserDialog'});}( jQuery ) );