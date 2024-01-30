/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function( $ ) {
    $.widget( "custom.tecnicalsOptionsDialog", $.custom.maindialog,{
        _availabel:{},
        _values:{},
        _availabelMaterOther:{},
        _availabelZakaz:{},
        _buttCnt:0,
        _firstStart:true,
        options:{
            title:'Настройка технички',
            getUrl:null,
            width:700,
        },
        _create:function(){
            let self=this;
            this.options.buttons= [
                {
                  text: "Сохранить",
                  click: function(){self._saveClick.call(self,this);}
                },
                {
                    text:"Отмена",
                    click: function(){self.close();}
                },
                {
                    text:"Обновить",
                    click: function(){self._requestDate();}
                }
            ];
            this._super();
            this.element.addClass('tecnicals-options');
            this.uiDialog.addClass('zakaz-popup-base');
            this._requestDate();
        },
        _saveClick:function(e){
            let self=this;
            if (this.options.getUrl){
                this._showBunner('Сохранить');
                let options={
                    values:this._values,
                    availabel:this._availabel,
                    availabelMaterOther:this._availabelMaterOther,
                    availabelZakaz:this._availabelZakaz
                };
                console.log('send',options);
                $.post(this.options.getUrl,{options:options}).done(function(answ){
                    console.log(answ)
                    self._hideBunner();
                    if (answ.status&&answ.status==='ok'){
                        self.close();
                    }else{
                        m_alert('Ошибка','При сохранение произошда ошибка!',true,false);
                    }
                }).fail(function(answ){new m_alert('Ошибка сервера',answ.responseText,true,false);});
            }
        },
        _createDD:function(arr,click,subKey,defVal,isSub){
            subKey=subKey?subKey:false
            let self=this;
            let rVal=$('<div>').addClass('dropdown');
            let butt=$('<button>').attr({
                    id:'tecnikalSelButton'+(this._buttCnt++),
                    type:'button',
                    'data-toggle':'dropdown',
                    'aria-haspopup':"true",
                    'aria-expanded':"false"
                })
                .text('Выберите')
                .append($('<span>').addClass('caret'))
                .appendTo(rVal);
            let ul=$('<ul>')
                    .addClass('dropdown-menu')
                    .attr('aria-labelledby','tecnikalSelButton')
                    .appendTo(rVal);
            if (isSub){
                ul.append($('<li class="dropdown-header">Из структуры материала</li>'));
            }
            $.each(arr,function(key,val){
                let li=$('<li>')
                        .appendTo(ul)
                        .append($('<a>').attr({
                            href:'#',
                            'data-key':key
                        }).text(subKey?val[subKey]:val));
                if (key==defVal){
                    butt.text(subKey?val[subKey]:val).append($('<span>').addClass('caret'));
                    butt.attr('data-key-value',key);
                }
                if ($.isFunction(click)) li.children('a').click({self:self,butt:butt},click)
            });
            if (isSub){
                ul.append($('<li role="separator" class="divider"></li>'));
                ul.append($('<li class="dropdown-header">Материал доп. информация</li>'));
                $.each(self._availabelMaterOther,function(key,val){
                    let li=$('<li>')
                            .appendTo(ul)
                            .append($('<a>').attr({
                                href:'#',
                                'data-key':key
                            }).text(subKey?val[subKey]:val));
                    if (key==defVal){
                        butt.text(subKey?val[subKey]:val).append($('<span>').addClass('caret'));
                        butt.attr('data-key-value',key);
                    }
                    if ($.isFunction(click)) li.children('a').click({self:self,butt:butt},click)                
                });
                ul.append($('<li role="separator" class="divider"></li>'));
                ul.append($('<li class="dropdown-header">Из заказа</li>'));
                $.each(self._availabelZakaz,function(key,val){
                    let li=$('<li>')
                            .appendTo(ul)
                            .append($('<a>').attr({
                                href:'#',
                                'data-key':key
                            }).text(subKey?val[subKey]:val));
                    if (key==defVal){
                        butt.text(subKey?val[subKey]:val).append($('<span>').addClass('caret'));
                        butt.attr('data-key-value',key);
                    }
                    if ($.isFunction(click)) li.children('a').click({self:self,butt:butt},click)                
                });
            }
            rVal.mouseover(function(){
                let objInd=$(this).parent().index()+1;
                let parInd=Math.floor($(this).parent().parent().index()/2)*3;
                
                $('#technikal-help').addClass('help-img'+(objInd+parInd));
            });
            rVal.mouseleave(function(){$('#technikal-help')
                        .removeClass('help-img1')
                        .removeClass('help-img2')
                        .removeClass('help-img3')
                        .removeClass('help-img4')
                        .removeClass('help-img5')
                        .removeClass('help-img6')});
            return rVal;
        },
        _subMenuClick:function(e){
            let kolIndex=$(this).parent().parent().parent().parent().index()
                +Math.floor($(this).parent().parent().parent().parent().parent().index()/2)*3;
            let self=e.data.self;
            let butt=e.data.butt;
            let mainIndex=$('#tecnikalSelButton0').attr('data-key-value');
            //let kolIndex=$(this).parent().parent().parent().parent().index();
            butt.text($(this).text()).append($('<span>').addClass('caret'));
            butt.attr('data-key-value',$(this).attr('data-key'));
            if (!self._values[mainIndex]) self._values[mainIndex]=[];
            self._values[mainIndex][kolIndex]=$(this).attr('data-key');
            console.log(self._values);
        },
        _menuSelect:function(e){
            let self=e.data.self;
            let body=self.element.find('.panel-body');
            let butt=e.data.butt;
            butt.text(self._availabel[$(this).attr('data-key')].name).append($('<span>').addClass('caret'));
            butt.attr('data-key-value',$(this).attr('data-key'));
            body.empty();
            let stuct=JSON.parse(self._availabel[$(this).attr('data-key')].struct);
            let tbl=$('<table>').appendTo(body);
            let defVal=self._values[$(this).attr('data-key')]?self._values[$(this).attr('data-key')]:false;
            $('<tr>')
                    .appendTo(tbl)
                    .append($('<th>').text('Колонка №1'))
                    .append($('<th>').text('Колонка №2'))
                    .append($('<th>').text('Колонка №3'));
            $('<tr>')
                    .appendTo(tbl)
                    .append($('<td>').append(self._createDD(stuct,self._subMenuClick,false,$.type(defVal[0])!=='undefined'?defVal[0]:null,true)))
                    .append($('<td>').append(self._createDD(stuct,self._subMenuClick,false,$.type(defVal[1])!=='undefined'?defVal[1]:null,true)))
                    .append($('<td>').append(self._createDD(stuct,self._subMenuClick,false,$.type(defVal[2])!=='undefined'?defVal[2]:null,true)));
            $('<tr>')
                    .appendTo(tbl)
                    .append($('<th>').text('Колонка №4'))
                    .append($('<th>').text('Колонка №5'))
                    .append($('<th>').text('Колонка №6'));
            $('<tr>')
                    .appendTo(tbl)
                    .append($('<td>').append(self._createDD(stuct,self._subMenuClick,false,$.type(defVal[3])!=='undefined'?defVal[3]:null,true)))
                    .append($('<td>').append(self._createDD(stuct,self._subMenuClick,false,$.type(defVal[4])!=='undefined'?defVal[4]:null,true)))
                    .append($('<td>').append(self._createDD(stuct,self._subMenuClick,false,$.type(defVal[5])!=='undefined'?defVal[5]:null,true)));
            body.append($('<p>').addClass('help-text').text('Рис:'));
            body.append($('<div>').attr({
                id:"technikal-help",
                class:'help-img'
                //src:"css/pic/t_help0.gif"
            }));
        },
        _requestDate:function(){
            let self=this;
            if (this.options.getUrl){
                this._showBunner();
                $.post(this.options.getUrl).done(function(answ){
                    self.element.empty();
                    self._availabel=answ.availabel;
                    self._values=answ.values;
                    self._availabelMaterOther=answ.availabelMaterOther;
                    self._availabelZakaz=answ.availabelZakaz;
                    if ($.type(self._values)!=='object') self._values={};
                    let panel=$('<div>').addClass('panel panel-default').appendTo(self.element);
                    let head=$('<div>').addClass('panel-heading').appendTo(panel);
                    $('<div>').addClass('panel-body').appendTo(panel);
                    head.append(self._createDD(self._availabel,self._menuSelect,'name'));
                    if (self._firstStart){
                        self._afterFirstStartComlite();
                        self._firstStart=false;
                    }
                    self._hideBunner();
                    console.log(answ); 
                }).fail(function(answ){
                    new m_alert('Ошибка сервера',answ.responseText,true,false);
                });
            }else{
                console.warn('tecnicalsOptionsDialog->_requestDate:','Не задан "getUrl"');
            }
        }
    });
}( jQuery ) );

(function( $ ) {$.widget( "custom.tecnicalsOptions", $.custom.openButton,{_className:'tecnicalsOptionsDialog'});}( jQuery ) );