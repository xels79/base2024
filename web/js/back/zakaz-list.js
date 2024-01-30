/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var zakaz_list={
    _fieldParams:null,
    _list:null,
    _hidden:null,
    _attention:null,
    _stageLevels:['Согласование', 'У дизайнера', 'Печать', 'Готов' ,'Сдан','С ошибкой'],
    _currentPage:0,
    _count:0,
    _showAttentionColumn:false,
    options:{
        bigLoaderPicUrl:null,
        requestUrl:null,
        setsizesUrl:null,
        getAvailableColumnsUrl:null,
        setColumnsUrl:null,
        getOneRawUrl:null,
        changeRowUrl:null,
        removeRowUrl:null,
        copyRowUrl:null,
        viewRowUrl:null,
        canEditStage:false,
        canEditZakaz:false,
        canEditOtherOrder:true,
        stageLevels:null,
        userId:0
    },
    _create:function(){
        var uri=new URI(window.location.href);
        var params=URI.parseQuery(uri.query());
        //var pCnt=Math.ceil(this._count/this._fieldParams.pageSize);
        var optBut=$('<a>')
            .append($('<span>').addClass('glyphicon glyphicon-cog'))
            .attr('title','Настройки таблицы')
            .click({self:this},function(e){
                e.data.self._optionsClick();
            });
        this._super();
        if (this.options.bigLoaderPicUrl){
            this.element.append($('<tr class="bunn">').append($('<td>').append($('<img>').attr({
                src:this.options.bigLoaderPicUrl,
            }).css({
                'max-width':20,
                'max-height':20
            }))));
        }else{
            this.element.append($('<tr class="bunn">').append($('<td>').text('Загрузка...')));
        }
        this.element.children('caption').append(optBut);
        if (params.page)
            this._requstDate(this._firstStart,{page:params.page});
        else
            this._requstDate(this._firstStart);
        $(window).on('popstate',{self:this},this._popstate);
    },
/*
 * Кнопка назад браузера
 * @param {type} e event
 * @returns {undefined}
 */
    _popstate:function(e){
        console.log(e,e.state);
        if ($.type(e.state)==='object'&&$.type(e.state.page)!=='undefined'){
            var self=e.data.self;
            self.element.children('tbody').children(':not(:first-child)').remove();
            self.element.children('tfoot').children(':first-child').children(':first-child').empty();
            self.__showBunnerAtRowAndGetIt();
            self._requstDate(self._secondStart,{page:e.state.page});
        }
    },
/*
 * Диалог настроек таблицы: содержимое диалога правая часть
 * @returns {$('div')}
 */
    _optionsContent2:function(){
        var rVal=$('<div>');
        rVal.append($('<h4>').text('Цвета:'));
        var colorsStage=$('<div>').addClass('colors-setup');
        colorsStage.append($('<p>').text('Этап работы'));
        for (var i=0;i<this._stageLevels.length;i++){
            var row=$('<div>').addClass('stage-colors');
            var inp=$('<input>').attr({type:'text'}).cPicker();
            row.append($('<span>').text(this._stageLevels[i]));
            if (this._fieldParams.colors&&this._fieldParams.colors.stage&&this._fieldParams.colors.stage[i]){
                inp.val(this._fieldParams.colors.stage[i]);
            }
            row.append(inp);
            colorsStage.append(row);
        }
        rVal.append(colorsStage);
        return rVal;
    },
/*
 * Диалог настроек таблицы: содержимое диалога левая часть
 * @param {type} d
 * @returns {$('<div>')}
 */
    _optionsContent:function(d){ //
        var rVal=$('<div>'),self=this;
        $.post(this.options.getAvailableColumnsUrl).done(function(answ){
            console.log(answ);
            var colsToAdd=[],existsCol=[];
            if (answ.availableColumns&&answ.options){
                rVal.append($('<h4>').text('Колонки:'));
                rVal.append($('<div>')
                        .addClass('content-header')
                        .append($('<p>').text('Добавлены'))
                        .append($('<p>').text('Доступны')));
                for (var i=0;i<answ.options.constant.length;i++){
                    existsCol[existsCol.length]={
                        label:answ.options.constant[i].label,
                    };
                    colsToAdd[colsToAdd.length]={
                        label:answ.options.constant[i].label,
                        class:'list-group-item ui-state-disabled'
                    };                                            
                }
                for (var i=0;i<answ.options.add.length;i++){
                    existsCol[existsCol.length]={
                        label:answ.options.add[i].label,
                        'data-key':answ.options.add[i].name
                    };
                    if (answ.options.add[i].name!=='empt'){
                        colsToAdd[colsToAdd.length]={
                            label:answ.options.add[i].label,
                            'data-key':answ.options.add[i].name
                        };
                        if ($.inArray(answ.options.add[i].name,Object.keys(answ.availableColumns))>-1){
                            delete answ.availableColumns[answ.options.add[i].name];
                        }
                    }
                }
                $.each(answ.availableColumns,function(key,val){
                    colsToAdd[colsToAdd.length]={
                        label:val,
                        'data-key':key
                    };                        
                });
                console.log(existsCol,colsToAdd);
                rVal.append($.fn.twoEditableLists(existsCol,colsToAdd,null,{
                    firstListClass:'list-group to-add'
                },'label'));
                rVal.append($('<h4>').text('Другие настройки:'));
                rVal.append($('<div>')
                        .addClass('dialog-control-group-inline')
                        .append($('<label>').text('Заказов на странице:'))
                        .append($('<input>').val($.type(answ.options.pageSize)!=='undefined'?answ.options.pageSize:10).attr('id','table-options-pagesize'))
                        );
                rVal.append($('<div>')
                        .addClass('dialog-control-group-inline')
                        .append($('<label>').text('Показывать предупреждения:'))
                        .append($('<input>').attr({
                            id:'table-options-showAttentionColumn',
                            type:'checkbox',
                            checked:self._showAttentionColumn===true
                        }).click(function(){
                            if ($(this).attr('checked'))
                                $(this).removeAttr('checked');
                            else
                                $(this).attr('checked',true);
                        }))
                        );
                d.maindialog('hideBunner').maindialog('moveToCenter');
            }
        }).fail(function(answ){
            new m_alert('Ошибка сервера',answ.responseText,true,false);
            console.error(answ);
        });
        return rVal;
    },
/*
 * Диалог настроек таблицы сохранение настроек
 * @param {type} d - объект $ контент диалога
 * @returns {undefined}
 */
    _optionsSave:function(d){
        console.log(d);
        if (this.options.setColumnsUrl){
            var toSend=[],colors={stage:[]};
            console.log(d.find('.to-add').children('[data-key]'));
            d.find('.to-add').children('[data-key]').each(function(){
                toSend[toSend.length]=$(this).attr('data-key');
            });
            d.find('.stage-colors').each(function(){
                var val=$(this).children('input').val();
                colors.stage[colors.stage.length]=val?val:"";
            });
            console.log($('#table-options-showAttentionColumn').attr('checked'));
            $.post(this.options.setColumnsUrl,{
                coltoadd:toSend.length?toSend:'remove-all',
                colors:colors,
                pageSize:$.isNumeric($('#table-options-pagesize').val())?parseInt($('#table-options-pagesize').val()):10,
                showAttentionColumn:$('#table-options-showAttentionColumn').attr('checked')==='checked'
            }).done(function(answ){
                console.log(answ);
                if (answ.status==='ok'){
                    d.maindialog('close');
                    window.location.reload(true);
                }
            }).fail(function(answ){
                new m_alert('Ошибка сервера',answ.responseText,true,false);
                console.error(answ);
        });
        }else{
            console.warn('_optionsSave','Не задан setColumnsUrl');
        }
    },
/*
 * Диалог настроек таблицы - поля и тд...
 * @returns {undefined}
 */
    _optionsClick:function(){
        if (this.options.getAvailableColumnsUrl){
            var self=this,d=$('<div>')
                .appendTo('body')
                .attr({
                    id:'zakaz_list_options_dialog',
                    title:'Настройки таблицы'
                });
            d.append(this._optionsContent(d))
                .append(this._optionsContent2())
                .maindialog({
                    modal:true,
                    resizable:false,
                    close:function(){
                        d.remove();
                    },
                    open:function(e,ui){
                        $(this).parent().addClass('zakaz-list-popup');
                    },
                    buttons:[
                        {
                            text:'Сохранить',
                            click:function(){
                                self._optionsSave(d);
                            }
                        },
                        {
                            text:'Отменить',
                            click:function(){
                                $(this).maindialog('close');
                            }
                        }
                    ]
                }).maindialog('showBunner');
        }else{
            console.warn('_optionsClick: Не передан getAvailableColumnsUrl');
        }
    },
/*
 * Запрос данных с сервера и вызов указанной функции
 * в случае успеха
 * @param {function} callB - коллбэк функция
 * @param {object} options - параметры на сервер
 * @returns {undefined}
 */
    _requstDate:function(callB,options){
        options=$.type(options)==='object'?options:{};
        var self=this;
        if (this.options.requestUrl){
            $.post(this.options.requestUrl,options).done(function(answ){
                console.log(answ);
                if ($.isFunction(callB)) callB.call(self,answ);
            }).fail(function(answ){
                new m_alert('Ошибка сервера',answ.responseText,true,false);
                console.error(answ);
            });
        }else{
            console.warn('zakazListController._requstDate()','Не задана requestUrl');
        }
    },
/*
 * Находит нужный ряд,
 * Заменяет его банром
 * И возвращает чтобы не искать
 * @param {type} id - Идентификатор элемента
 * @returns {$('<tr>')}
 */
    __showBunnerAtRowAndGetIt:function(id){
        var tr=id?this.element.children('tbody').children('[data-key='+id+']'):$('<tr>').addClass('bunn').appendTo(this.element.children('tbody'));
        //var colCnt=this.element.children('tbody').children().first().children().length;
        if (this.options.bigLoaderPicUrl){
            tr.empty().append($('<td>')
            .append($('<img>').attr({
                src:this.options.bigLoaderPicUrl,
            }).css({
                'max-width':20,
                'max-height':20
            })).attr({
                colspan:Object.keys(this._allFields()).length
            }).append($('<td>')).append($('<td>')));
        }else{
            tr.empty().append($('<td>')
                .text('Загрузка...')
                .attr({
                    colspan:Object.keys(this._allFields()).length
                }).append($('<td>')).append($('<td>')));
        }
        return tr;
    },
/*
 * Обновление ряда
 * @param {int} id - идентификатор
 * @param {object} newVals - новые значения
 * @returns {undefined}
 */
    _updateRaw:function(id,newVals){
        newVals=newVals?newVals:{};
        if (this.options.getOneRawUrl){
            var tr=this.__showBunnerAtRowAndGetIt(id),self=this;
            $.post(this.options.getOneRawUrl,{id:id,update:newVals}).done(function(answ){
                console.log(answ);
                if (answ.status=='ok'){
                    if (answ.attention){
                        self._attention[answ.row.id]=answ.attention;
                    }else{
                        if (self._attention[answ.row.id]) delete self._attention[answ.row.id];
                    }
                    tr.replaceWith(self._generateDateRow(answ.row,answ.hidden));
                }
            }).fail(function(answ){
                new m_alert('Ошибка сервера',answ.responseText,true,false);
                console.error(answ);
            });
        }else{
            window.location.reload(true);
        }
    },
    _zakazChangeClick:function(e){ //Кнопки изменить заказ
        var id=parseInt($(this).parent().parent().attr('data-key'));
        var self=e.data.self;
        id=!isNaN(id)?id:0;
        console.log('_zakazChangeClick',id);
        console.log(document.def_opt);
        var d=$('#zakaz_dialog');
        if (!d.length){
            d=$.fn.creatTag('div',{
                id:'zakaz_dialog'
            });
            $('body').append(d);
            d.zakazAddEditController($.extend({},window.def_opt,{z_id:id,close:function(){
                $(window.dialogSelector).zakazAddEditController('destroy');
                $(window.dialogSelector).remove();
                self._updateRaw(id);
            }}));
        }
        if (!d.zakazAddEditController('isOpen'))
            d.zakazAddEditController('open');
    },
    _zakazRemoveClick:function(e){
        var id=parseInt($(this).parent().parent().attr('data-key'));
        var self=e.data.self;
        id=!isNaN(id)?id:0;
        if (id){
            m_alert('Внимание','<h2 style="color:red;">Действие отменить невозможно!</h2><p>Удалить заказ №'+id+' ?</p>',function(){
                $.post(self.options.removeRowUrl,{id:id}).done(function(answ){
                    console.log(answ);
                    self.update();
                    if (answ.menu&&answ.label){
                        $('.nav-mess-main').children('.dropdown-menu').empty().html($(answ.menu).html());
                        $('.nav-mess-main').children('.dropdown-toggle').empty().html(answ.label+'<b class="caret"></b>');
                        $('.stored-z')
                            .click(window.storedZClick)
                            .contextmenu(window.storedZContextMenu);
                        $.fn.enablePopover();
                    }
                }).fail(function(answ){
                    new m_alert('Ошибка сервера',answ.responseText,true,false);
                    console.error(answ);
                });
            },true);
        }else{
            console.warn('_zakazRemoveClick:Номер заказа не определён');
        }
    },
    _technicalsViewAfterGetCompliteAndPaste:function(dialog,dialogObject){
        var self=this;
        console.log(dialog.find('.dropdown-menu').find('a'));
        dialog.find('.dropdown-menu').find('a').click(function(e){
            e.preventDefault();
            console.log(this);
            var url=$(this).attr('data-url');
            var imgs=$('.tImg-view'),img;
            if (imgs.children().length>1){
                imgs.children(':first-child').remove();
            }
            if ($.inArray($(this).attr('data-ext'),['doc','pdf','docx','ai','ppt','pptx','txt','xls','xlsx','psd','zip','rar','fb2'])>-1){
                console.log('rc',url);
                console.log(location.origin);
                img=($('<iframe>').attr({
                    src:'http://docs.google.com/viewer?url='+encodeURIComponent(location.origin+url)+'&a=bi&embedded=true',
                }));
            }else if ($.inArray($(this).attr('data-ext'),['img','bmp','png','gif','jpg','ico'])>-1){
                img=$('<img>').attr({
                    src:url,
                });
            }else{
                img=$('<h3>').html('Просмотр<br>Не поддерживается!');
            }
            if (imgs.children().length){
                imgs.children(':first-child').addClass('img1').removeClass('img2');
                img.addClass('img2');
            }
            img.appendTo(imgs);
            dialogObject.showBunner();
            img.on('load',function(){
                dialogObject.hideBunner();
                dialogObject._afterFirstStartComlite();
            });
            img.ready(function(){
                dialogObject._afterFirstStartComlite();
            });
        });
    },
    _technicalsViewClick:function(e){
        var id=parseInt($(this).parent().parent().attr('data-key'));
        var self=e.data.self;
        var uri=new URI(self.options.viewRowUrl);
        
        uri.addSearch('technicals','true');
        console.log(e);
        id=!isNaN(id)?id:0;
        if (id&&!e.ctrlKey){
            var tmp=$('[zakaz-technicals-num='+id+']');
            if (tmp.length){
                tmp.viewDialog('moveToTop').viewDialog('restore');
            }else{
                $.custom.viewDialog({
                    id:id,
                    url:uri.toString(),
                    optionName:'zakaz-technicals-num',
                    minWidth:1000,
                    getOptionToPrint:{technicalsPrin:"true"},
                    afterGetCompliteAndPaste:function(dialogObject){
                        self._technicalsViewAfterGetCompliteAndPaste(this,dialogObject);
                    }
                });
            }
        }else if (id&&e.ctrlKey){
            e.preventDefault();
            uri.addSearch('id',id);
            console.log(uri.toString());
            window.open(uri.toString(), '_blank');
        }else{
            console.warn('_technicalsViewClick:Номер заказа не определён');
        }
        
    },
    _zakazViewClick:function(e){
        var id=parseInt($(this).parent().parent().attr('data-key'));
        var self=e.data.self;
        var uri=new URI(window.location.href);
        var tmp=URI.parseQuery(uri.query());
        var page=$.type(tmp.page)!=='undefined'?tmp.page:0;
        console.log(e);
        id=!isNaN(id)?id:0;
        if (id&&!e.ctrlKey){
            var tmp=$('[zakaz-num='+id+']');
            if (tmp.length){
                tmp.viewDialog('moveToTop').viewDialog('restore');
            }else{
                $.custom.viewDialog({
                    id:id,
                    url:self.options.viewRowUrl,
                    afterGetCompliteAndPaste:function(){
                        this.find('.list-group-item').click(function(e){
                            if ($(this).attr('href'))
                                window.open($(this).attr('href'), '_blank');
                            e.preventDefault();
                        }).each(function(){
                            if ($(this).children(':first-child').children(':first-child').width()>$(this).width()){
                                $(this).addClass('cut');
                                $(this).attr('title',$(this).children(':first-child').children(':first-child').text());
                            }
                        });
                    }
                });
            }
        }else if (id&&e.ctrlKey){
            e.preventDefault();
            uri=new URI(self.options.viewRowUrl);
            uri.addSearch('id',id);
            console.log(uri.toString());
            window.open(uri.toString(), '_blank');
        }else{
            console.warn('_zakazRemoveClick:Номер заказа не определён');
        }
    },
    _attentionColl:function(id){
    /*
     * Вывод предупредительной колонки
     */
        var rVal=$('<td>').addClass('technikal');
        if (this._attention&&this._attention[id]){
            rVal.append($('<a>')
                    .addClass('text-danger')
                    .append($('<span>').addClass('glyphicon glyphicon-alert'))
                    .attr({
                        href:'#',
                        title:this._attention[id]
                    })
                    .click(function(e){e.preventDefault();}));
        }
        return rVal;
    },
    _technikalColl:function(id){ //Калонка управления
        var rVal=$('<td>').addClass('technikal');
        var remove=$('<a>')
                .append($('<spna>').addClass('glyphicon glyphicon-trash'))
                .attr({
                    href:'#',
                    title:'Удалить'
                }).click({self:this},this._zakazRemoveClick);
        var edit=$('<a>')
                .append($('<spna>').addClass('glyphicon glyphicon-pencil'))
                .attr({
                    href:'#',
                    title:'Изменить'
                }).click({self:this},this._zakazChangeClick);
        var view=$('<a>')
                .append($('<spna>').addClass('glyphicon glyphicon-eye-open'))
                .attr({
                    href:'#',
                    title:'Просмотр'
                }).click({self:this},this._zakazViewClick);
        var technicals=$('<a>')
                .append($('<spna>').addClass('glyphicon glyphicon-info-sign'))
                .attr({
                    href:'#',
                    title:'Техничка'
                }).click({self:this},this._technicalsViewClick);
        var copy=$('<a>')
                .append($('<spna>').addClass('glyphicon glyphicon-duplicate'))
                .attr({
                    href:'#',
                    title:'Скопировать'
                }).click({self:this},this._zakazViewClick);
        //removeRowUrl:null,
        if (this.options.viewRowUrl){
            rVal.append(technicals).append(view);
        }
        if (this.options.canEditZakaz){
            if (this.options.canEditOtherOrder||this.options.userId==this._hidden[id]['ourmanager_id']) rVal.append(edit);
        }
        if (this.options.copyRowUrl&&this.options.canEditZakaz){
            rVal.append(copy);
        }
        if (this.options.removeRowUrl&&this.options.canEditZakaz){
            rVal.append(remove);
        }
        return rVal;
    },
    _allFields:function(){ //Объединяет все колонки
        var allFields=[];
        $.each(this._fieldParams.constant,function(){allFields[allFields.length]=this});
        $.each(this._fieldParams.add,function(){allFields[allFields.length]=this});
        return allFields;
    },
    _drawCols:function(){   //Заголовки с елементами управления
        var allFields=this._allFields();
        var cnt=allFields.length,tr=$('<tr>');
        if (this._showAttentionColumn){
            tr.append($('<th>').css({
                'max-width':'20px',
                'min-width':'20px',
                'width':'20px'            
            }).addClass('technikal'));
        }
        tr.append($('<th>').css({
            'max-width':'70px',
            'min-width':'70px',
            'width':'70px'            
        }).addClass('technikal'));
        //
        $.each(allFields,function(){
            tr.append($('<th>').text(this.label)
                    .css({
                        'max-width':this.width+'px',
                        'width':this.width+'px',
                    })
                    .attr({'data-colkey':this.name})
            );
        });
        if (allFields.length){
            var last=$('<th>').css({
                'max-width':'5px',
                width:'5px'
            }),options={};
            tr.append(last);
            last.prev().addClass('last');
            this.element.append(tr);
            if (this.options.setsizesUrl){
                this.element.colResizable({
                    liveDrag:true,
                    disabledColumns:[0,allFields.length,allFields.length+1],
                    onResize:function(){
                        self._resizeDone.call(tr,{self:self});
                    }
                  });    
            }else
                console.warn('zakaz-list->_drawCols','Не задан setsizesUrl');
//            this.element.append(tr.resizebletr(options));
        }
    },
    _resizeDone:function(data){ //Сохранение размеров по окончанию их изменения
        var self=data.self,allFields=$.extend({},self._fieldParams.constant,self._fieldParams.add);
        var key=Object.keys(allFields),tr=$('<tr>'),cnt=key.length-1;
        var child=$(this).children('[data-colkey]');
        var send={};
        $.each(child,function(){
            send[$(this).attr('data-colkey')]={name:$(this).attr('data-colkey'),width:Math.floor($(this).width()),label:$(this).text()};
        });
        $.post(self.options.setsizesUrl,{options:send}).done(function(answ){
            console.log(answ);
        }).fail(function(answ){
            new m_alert('Ошибка сервера',answ.responseText,true,false);
            console.error(answ);
        });
    },
    _generateStageSelect:function(val){ //Состояние заказа
//        console.log('_generateStageSelect',val);
        var select=$('<select>').attr({
//            class:'form-control',
            name:'stage',
            back:val
        });
        var stageText="";
        for (var i=0;i<this._stageLevels.length;i++){
            var opt=$('<option>').attr('value',i).text(this._stageLevels[i]);
            if (i==parseInt(val)){
                opt.attr({'selected':true});
            }
            select.append(opt);
        }
        select.change({self:this},this._stageChange);
        return select;
    },
    _stageChange:function(e){//Изменемие состояния заказа
        var el=this,idZak=$(this).parent().parent().attr('data-key');
        m_alert('Внимание','<p>Изменить состояние заказа №'+idZak+'</p><p>C <em>"'+e.data.self._stageLevels[parseInt($(el).attr('back'))]+'"</em> на <em>"'+e.data.self._stageLevels[parseInt($(el).val())]+'"</em></p>',function(){
            $(el).attr('back',$(el).val());
            if (e.data.self.options.changeRowUrl){
                var tr=e.data.self.__showBunnerAtRowAndGetIt(idZak);
                $.post(e.data.self.options.changeRowUrl,{id:idZak,row:{stage:parseInt($(el).val())}}).done(function(answ){
//                    console.log(answ);
                    if (answ.status=='ok'){
                        tr.replaceWith(e.data.self._generateDateRow(answ.row,answ.hidden));
                    }
                }).fail(function(answ){
                    new m_alert('Ошибка сервера',answ.responseText,true,false);
                    console.error(answ);
                });
            }else{
                m_alert('Информация','<p>Невозможно сохранить.</p><p>Не задан <em>changeRowUrl</em></p>',true,false);
            }
        },function(){
            $(el).val($(el).attr('back'));
        });
    },
    _generateDateRow:function(dt,hidden){ //Новый ряд dt=Содержимое
        var cnt=this.element.children('tbody').children(':first-child').children().length;
        var self=this;
        var newCnt=1,rVal=$('<tr>')
                .attr('data-key',dt.id);
        if (this._showAttentionColumn) rVal.append(this._attentionColl(dt.id))
        rVal.append(this._technikalColl(dt.id));
        $.each(dt,function(key,val){
            if (newCnt < cnt){
                var td=$('<td>');
                if (key==='stageText'&&self.options.canEditStage&&self._hidden){
                    td.append(self._generateStageSelect(hidden?hidden.stage:self._hidden[dt.id].stage))
                    if (self._fieldParams&&self._fieldParams.colors&&self._fieldParams&&self._fieldParams.colors.stage){
                        td.css('background-color',self._fieldParams.colors.stage[hidden?hidden.stage:self._hidden[dt.id].stage]);
                    }
                }else
                    td.html(val);
                if (newCnt === cnt-(self._showAttentionColumn?3:2)) td.addClass('last'); //Устоновить последнюю колонку
                rVal.append(td);
            }
            newCnt++;
        });
        rVal.append($('<td>'));
        return rVal;
    },
    _drawContent:function(){ //Выводит все полученные ряды
        var self=this;
        if (this._list){
            $.each(this._list,function(key,val){
                self.element.append(self._generateDateRow(this));
            });
        }
    },
    _paginatioClick:function(e){ //Нажата кнопка пагинации
        e.preventDefault();
        var self=e.data.self,page=$(this).attr('data-key');
        var uri=new URI(window.location.href);
        uri.setSearch('page',page);
        //console.log(window.location.hash);
        console.log(URI.parseQuery(uri.query()));
        console.log(uri.href());
        self.element.children('tbody').children(':not(:first-child)').remove();
        self.element.children('tfoot').children(':first-child').children(':first-child').empty();
        self.__showBunnerAtRowAndGetIt();
        window.history.pushState({page:page},null,uri.href());
        self._requstDate(self._secondStart,{page:page});
    },
    update:function(){
        var uri=new URI(window.location.href);
        var tmp=URI.parseQuery(uri.query());
        var page=$.type(tmp.page)!=='undefined'?tmp.page:0;
        this.element.children('tbody').children(':not(:first-child)').remove();
        this.element.children('tfoot').children(':first-child').children(':first-child').empty();
        this.__showBunnerAtRowAndGetIt();
        this._requstDate(this._secondStart,{page:page});
    },
    _drawFooter:function(){//Выводит footer таблицы
        var pCnt=Math.ceil(this._count/this._fieldParams.pageSize);
        var ft=this.element.children('tfoot');
        if (!ft.length){
            ft=$('<tfoot>')
                    .append($('<tr>').append($('<td>').attr({colspan:Object.keys(this._allFields()).length}))
                    .append($('<td>'))
                    .append($('<td>')))
                    .appendTo(this.element);
        }
        if (pCnt<2) return;
        var td=ft.children(':first-child').children(':first-child');
        var list=$('<ul>').addClass('pagination')
            ,prev=$('<li>')
                    .append($('<a>')
                    .attr({
                        href:'#',
//                        'aria-label':'Previous',
                        'data-key':this._currentPage-1,
                    }).html('<span aria-hidden="true">&laquo;</span>'))
                    .appendTo(list)
            ,next=$('<li>')
                    .append($('<a>')
                    .attr({
                        href:'#',
//                        'aria-label':'Next',
                        'data-key':this._currentPage+1,
                    }).html('<span aria-hidden="true">&raquo;</span>'));
        if (!this._currentPage)
            prev.addClass('disabled');
        else
            prev.children('a').click({self:this},this._paginatioClick);
        if (this._currentPage===pCnt-1)
            next.addClass('disabled');
        else
            next.children('a').click({self:this},this._paginatioClick);
        for (var i=0;i<pCnt;i++){
            var li=$('<li>').appendTo(list);
            if (this._currentPage===i) li.addClass('active');
            li.append($('<a>')
                    .attr({
                        href:'#',
                        'data-key':i,
                    })
                    .click({self:this},this._paginatioClick)
                    .text(i+1));
        }
        list.append(next);
        td.append($('<nav>').attr({'aria-label':'Page navigation'}).append(list));
    },
    _firstStart:function(answ,onEnd){ //Вывод первых колонок в пустую таблицу
        this.element.find('.bunn').remove();
        if (answ.colOptions){
            this._fieldParams=answ.colOptions;
            this._list=answ.list;
            this._hidden=answ.hidden;
            this._currentPage=answ.page?answ.page:0;
            this._count=answ.count;
            if (answ.attention) this._attention=answ.attention;
            this._showAttentionColumn=answ.colOptions.showAttentionColumn?true:false;
            this._drawCols();
            this._drawContent();
            this._drawFooter();
            if ($.type(onEnd)==='array' && onEnd.length>1 && $.isFunction(onEnd[1])){
                onEnd[1].call(onEnd[0]);
            }

        }else
            console.warn('zakazListController._firstStart()','Сервер не передал параметры колонок');
    },
    _secondStart:function(answ, onEnd){ //Вывод следующей стр
        this.element.find('.bunn').remove();
        if (answ.colOptions){
//            this._fieldParams=answ.colOptions;
            this._list=answ.list;
            this._hidden=answ.hidden;
            this._currentPage=answ.page?answ.page:0;
            this._count=answ.count;
            //this._drawCols();
            this._drawContent();
            this._drawFooter();
            if ($.type(onEnd)==='array' && onEnd.length>1 && $.isFunction(onEnd[1])){
                onEnd[1].call(onEnd[0]);
            }
        }else
            console.warn('zakazListController._secondStart','Сервер не передал параметры колонок');
    }
};

(function( $ ) {
    $.widget( "custom.zakazListController", $.Widget,$.extend({},zakaz_list));
}( jQuery ) );