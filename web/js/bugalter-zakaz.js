/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


let bugalter_zakaz_controller={
    options:{
        viewRowUrl:null,
        changeRowUrl:null,
        canChange:[1,2,8],
        canChangeTo:[3,4,6,8],
        urlGetOplataList:'',
        urlUpdateOplataList:'',
        urlGetOneRow:'',
        urlSetMaterialStatus:'',
    },
    _doMouseLeave:false,
    _create:function(){

        this._super();
        this._editCollName='';
        this.options.isDisainer=true;
        console.log('bugalterZakazController - init',this);
    },
    _doubleClick:function(e){
        console.log('_doubleClick');
    },
    __sndChangeMaterialStatus:function(el,id,status, dopText, queryParam, callB){
        let zId=$(el).parent().parent().parent().attr('data-key'),self=this;
        let txt='<p>В заказе №'+zId+(dopText?(' '+dopText):'')+'</p><p>Изменить статус оплаты с <b>"'+(status?'не оплачен':'оплачен')+'"</b> на <b>"'+(status?'оплачен':'не оплачен')+'"</b>?';
        m_alert('Внимание',txt,function(){
            let row=self.element.find('tr[data-key='+zId+']'), cCnt=row.children().length
            row.empty().append($('<td>').attr('colspan',cCnt).text('Загрузка'));
            $.post(self.options.urlSetMaterialStatus,$.extend({},{id:id, status:status},queryParam)).done(function(dt){
                if (dt.status==='error'){
                    m_alert('Ошибка сервера',dt.errorText,true,false);
                }else{
                    self._updateOneRaw(zId);
                }
            });
        },true,function(){
            if ($.isFunction(callB)) callB();
        });
    },
    _changeMaterialStatus:function(e){
        console.log('_changeMaterialStatus');
        let self=e.data.self,isAction=false;
        let id=$(this).attr('data-material-key');//(e.data.queryParam && e.data.queryParam.isOtherSpend)? $(this).parent().parent().parent().attr('data-key'): 
        let el=this;
        let queryParam=e.data.queryParam?e.data.queryParam:{};
        let parent=$(this).parent().parent().parent().attr('not-remove-hover',true);
        e.preventDefault();
        if (!self.options.urlSetMaterialStatus){
            console.error('bugalterZakazController не передан urlSetMaterialStatus');
            return;
        }
//        if (isNaN(id)){
//            console.error('bugalterZakazController не dthysq data-material-key',id);
//            return;
//        }
        isAction=true;
        if (e.data.status){
            self.__sndChangeMaterialStatus(el, id, e.data.status, e.data.dopText, queryParam, function(){
                self._rowRemoveHLT(parent); 
            });
        }else{
            e.stopPropagation();
            dropDown({
                posX:e.clientX,
                posY:e.clientY,
                items:[
                    {header:'Заказ №'+$(el).parent().parent().parent().attr('data-key')+(e.data.dopHeaderText?(' '+e.data.dopHeaderText):'')},
                    'separator',
                    {
                        label:'Отменить оплату...',
                        click:function(){
                            self.__sndChangeMaterialStatus(el,id,false,e.data.dopText,queryParam,function(){
                                self._rowRemoveHLT(parent);
                            });
                        }
                    }
                ],
                beforeClose:function(){
                    if (!isAction){
                        self._rowRemoveHLT(parent);
                    }
                }

            });
        }
    },
    _generateTD:function(k,v,id){
        let fldOpt=this._allFieldsWidthByName();
        let rVal=$('<div class="resize-cell">').css({
            'max-width': fldOpt[k]+'px',
            'min-width': fldOpt[k]+'px',
            'width': fldOpt[k]+'px'
        });
        if (k=='XXX'){
//            console.log(k,this._hidden[id].stage);
//            let stage=parseInt(this._hidden[id].stage);
//            stage=isNaN(stage)?0:stage;
//            if ($.inArray(stage,this.options.canChange)>-1 && stage!==8){
//                rVal.append($('<button>')
//                        .text(v)
//                        .click({self:this},function(e){
//                            $(this).parent().parent().attr('not-remove-hover',true)
//                            e.data.self.__showChangeStateDialogAndDoIt($(this).parent().parent(),e.data.self.options.canChangeTo);
//                        })
//                    );
//            }else{
//                rVal.text(v);
//            }
        }else if(k==='material_total_coast_list'){
            for (let i in v){
                let chkB=$('<span>').attr('data-material-key',i).addClass(v[i].paid?'glyphicon glyphicon-check':'glyphicon glyphicon-unchecked');
                let dTx='<b>, материал</b> от поставщика <b>"'+v[i].firm_name+'"</b>';
                if (!v[i].paid){
                    chkB.click({self:this,status:true, dopText:dTx},this._changeMaterialStatus);
                }else{
                    chkB.contextmenu({self:this,status:false, dopText:dTx, dopHeaderText:'постав.: "'+v[i].firm_name+'"'},this._changeMaterialStatus);
                }
                rVal.append($('<div>').addClass('info-line has-flag')
                                .append($('<div>').text(v[i].firm_name))
                                .append($('<span>').text(v[i].value))
                                .append(chkB));
            }
        }else if(k==='podryad_total_coast_list'){
            for (let i in v){
                let chkB=$('<span>').attr('data-material-key',i).addClass(v[i].paid!=0?'glyphicon glyphicon-check':'glyphicon glyphicon-unchecked');
                let dTx='<b>, работа</b> от исполнителя <b>"'+v[i].firm_name+'"</b>';
                if (v[i].paid==0){
                    chkB.click({self:this,status:true, dopText:dTx, queryParam:{isPodryad:true}},this._changeMaterialStatus);
                }else{
                    chkB.contextmenu({self:this,status:false, dopText:dTx, dopHeaderText:'исполнит.: "'+v[i].firm_name+'"', queryParam:{isPodryad:true}},this._changeMaterialStatus);
                }
                rVal.append($('<div>').addClass('info-line has-flag')
                                .append($('<div>').text(v[i].firm_name))
                                .append($('<span>').text(v[i].value))
                                .append(chkB));
            }            
        }else if(k==='other_spends_list'){
            for (let i in v){
                let chkB=$('<span>').attr('data-material-key',i).addClass(v[i].paid!=0?'glyphicon glyphicon-check':'glyphicon glyphicon-unchecked');
                let dTx='<b>, затраты на "'+v[i].firm_name+'"</b>';
                if (v[i].paid==0){
                    chkB.click({self:this,status:true, dopText:dTx, queryParam:{isOtherSpend:true, zakaz_id:id}},this._changeMaterialStatus);
                }else{
                    chkB.contextmenu({self:this,status:false, dopText:dTx, dopHeaderText:'затраты на "'+v[i].firm_name+'"', queryParam:{isOtherSpend:true, zakaz_id:id}},this._changeMaterialStatus);
                }
                rVal.append($('<div>').addClass('info-line has-flag')
                                .append($('<div>').text(v[i].firm_name))
                                .append($('<span>').text(v[i].value))
                                .append(chkB));
            }            
        }else if (k==='material_firms' || k==='podryad_name_list' || k==='other_spends_list_col'){
            for (let i in v){
                rVal.append($('<div>').addClass('info-line')
                                .append($('<span>').text(v[i])));
            }
        }else
            rVal.html(v);
        return rVal;
    },
    _rightClick:function(e){
//        let zID=parseInt($(this).parent().attr('data-key')),el=$(this).parent().get(0);
//        zID=!isNaN(zID)?zID:-1;
//        e.preventDefault();
//        e.stopPropagation();
//        let opt=[{
//                    label:'Открыть',
//                    click:function(){
//                        e.data.self._doubleClick.call(el,e);
//                    },
//                }];
//        if (zID>0)
//            let stage=parseInt(e.data.self._hidden[zID].stage);
//            if ($.inArray(stage,e.data.self.options.canChange)>-1){
//                opt[opt.length]={
//                    label:'Изменить этап работы',
//                    click:function(){
//                        e.data.self.__showChangeStateDialogAndDoIt.call(e.data.self,el,e.data.self.options.canChangeTo);
//                    },
//                };
//                opt[opt.length]={
//                    label:'Пометить как заказ с ошибкой',
//                    click:function(){
//                        e.data.self._changeStateToErrorClick.call(e.data.self,el);
//                    },
//                };
//            }
//        new dropDown({
//            posX:e.pageX,
//            posY:e.pageY,
//            items:opt,
//            beforeClose:function(e2){
//                $(el).children('.hand').removeClass('dirt-hover');
//                $(el).children('[style]').css('color','inherit');
//                e.data.self._doMouseLeave=true;
//            },
//            afterInit:function(e2){
//                e.data.self._doMouseLeave=false;
//            }
//        });
    },
    _changeStateToErrorClick:function(row){
        console.log('toError');
        let id=parseInt($(row).attr('data-key')),self=this;
        id=!isNaN(id)?id:0;
        if (!this.options.changeRowUrl){
            console.warn('changeStateToErrorClick','Не задан URL запроса');
            return;
        }
        $(row).children('.hand').addClass('dirt-hover');
        m_alert('Внимание','Изменить статус заказа на "Ошибка"',function(){
            $.post(self.options.changeRowUrl,{id:id,stage:9}).done(function(answ){ 
                if (answ.status!='ok'){
                    m_alert('Ошибка сервера',answ.errorText,true,false);
                }
                self.update();
            });
        },true,function(){
            $(row).children('.hand').removeClass('dirt-hover').removeClass('dirt-hover-odd');
            $(row).children('[style]').css('color','inherit');
            $(row).removeAttr('not-remove-hover');
        });

    },
    _changeStateClick:function(row){
        this.__showChangeStateDialogAndDoIt(row);
    },
    tVAGCAP:function(object){
        let self=this;
        console.log(object.find('.dropdown-menu').find('a'));
        object.find('.dropdown-menu').find('a').click(function(e){
            e.preventDefault();
            console.log(this);
            let url=$(this).attr('data-url');
            let imgs=$('.tImg-view'),img;
            if (imgs.children().length>1){
                imgs.children(':first-child').remove();
            }
            if ($.inArray($(this).attr('data-ext'),['doc','pdf','docx','ai','ppt','pptx','txt','xls','xlsx','psd','zip','rar','fb2'])>-1){
                console.log('rc',url);
                console.log(location.origin);
                img=($('<iframe>').attr({
                    src:'https://docs.google.com/viewer?url='+encodeURIComponent(location.origin+url)+'&a=bi&embedded=true',
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
        });

    },
    _generateTableFooter:function(){
        let rVal=this._tBody;
        let fldOpt=this._allFieldsWidthByName();
        let tr=rVal.children('[role="footer"]'); 
        if (!tr.length){
            tr=$('<div class="resize-row resize-no-hover" role="footer">').appendTo(rVal);
        }
        tr.empty();
        let self=this;
        if ($.type(this._hidden2)==='object'){
            console.log(this._tHeader.children(':first-child'));
            $.each(this._tHeader.children(':first-child').children(),function(ind,el){
                let key=self._tHeader.children(':first-child').children(':nth-child('+(ind+1)+')').attr('data-colkey');
                let td=$('<div class="resize-cell">').appendTo(tr).css({
                    'max-width': fldOpt[key]+'px',
                    'min-width': fldOpt[key]+'px',
                    'width': fldOpt[key]+'px'
                });
                console.log($(el).attr('data-colkey'),$.inArray($(el).attr('data-colkey'),Object.keys(self._hidden2)));
                if ($.inArray($(el).attr('data-colkey'),Object.keys(self._hidden2))>-1){
                    td.html(self._hidden2[$(el).attr('data-colkey')]);
                    td.addClass('has-info');
                }
            });
        }
        return rVal;
    },
    _showOplata:function(id,callB){
        console.log('Oplata click');
        let d=$('<div>').attr({title:'Оплата заказа№'+id});
        let dOpt={
            parent:this,
            callBack:callB,
            urlGetOplataList:this.options.urlGetOplataList,
            urlUpdateOplataList:this.options.urlUpdateOplataList,
            bigLoaderPicUrl:this.options.bigLoaderPicUrl,
            zakazId:id,
            zakazCoast:this._hidden[id].total_coast
        };
        if (!$.custom.oplatadialog){
            $('body').css('cursor','wait');
            $.fn.includeJS(['/js/oplatadialog.js'],function(){
                $('body').css('cursor','inherit');
                d.oplatadialog(dOpt);
            });
        }else{
            d.oplatadialog(dOpt);
        }
    },
    _rowRemoveHLT:function(el){
        let chld=el.children();
        el.removeAttr('not-remove-hover');
        for (let i=0;i<chld.length;i++){
            $(chld.get(i)).removeClass('dirt-hover').removeClass('dirt-hover-odd');
            if ($(chld.get(i)).attr('style')) $(chld.get(i)).css('color','inherit');
        }
    },
    _updateOneRaw:function(id){
        let self=this;
        let row=self.element.find('tr[data-key='+id+']'), cCnt=row.children().length;
        if (!this.options.urlGetOneRow){
            console.warn('bugalterZakazController - невозможно обновить ряд, не передан urlGetOneRow');
            return;
        }
        row.empty().append($('<td>').attr('colspan',cCnt).text('Загрузка'));
        this._checkFilters();
        let rQ=$.extend({},{id:id},this.options.otherRequestOptions,{sort:self._sort},this._prepareFilterParamsForRequest());
        $.post(this.options.urlGetOneRow, rQ).done(function(dt){
            if (dt.status==='error'){
                m_alert('Ошибка сервера','<p>Не удалось обновить ряд.</p><p>'+dt.errorText+'</p>');
                return;
            }
            //_generateDateRow
            self._hidden2=dt.hidden2;
            self._hidden[id]=dt.hidden;
            row.replaceWith(self._generateDateRow(dt.row));
            self._generateTableFooter();
            $('.like').m_filter_like('update');
        });
    },
    _rowContext:function(e){
        let self=e.data.self,parent=$(this).parent(),id=parseInt(parent.attr('data-key')),isAction=false;
        e.preventDefault();

        parent.attr('not-remove-hover',true);
        console.log(parseFloat(self._hidden[id].total_coast)>0);
        dropDown({
            posX:e.clientX,
            posY:e.clientY,
            items:[
                {
                    header:'Заказ №'+id
                },
                'separator',
                {
                    label:'Оплата заказа...',
                    disabled:!(parseFloat(self._hidden[id].total_coast)>0),
                    click:function(){
                        isAction=true;
                        self._showOplata(id,function(){
                            self._rowRemoveHLT(parent);
                            self._updateOneRaw(id);
                        });
                    }
                }
            ],
            beforeClose:function(){
                if (!isAction){
                    self._rowRemoveHLT(parent);
                }
            }
        });
    },
    _generateDateRow:function(dt,hidden){ //Новый ряд dt=Содержимое
        let cnt=this._tHeader.children(':first-child').children().length;
        let self=this,nxtR=null;
        let newCnt=1,rVal=$('<div class="resize-row">')
                .attr('data-key',dt.id);
        self._isOdd=!self._isOdd;
        $.each(dt,function(key,val){
            let td=self._generateTD(key,val,dt.id);
            if ($.isArray(val)){
                let l=val.length;
                rVal.addClass('resize-has-sub-cell'+(l<2?'':l<3?'1':'2'));
            }else if($.type(val)==='object'){
                let l=Object.keys(val).length;
                rVal.addClass('resize-has-sub-cell'+(l<2?'':l<3?'1':'2'));
            }
            rVal.append(td);
            if (key==='oplataText'){
                let ost=parseFloat(self._hidden[dt.id].total_coast)-parseFloat(self._hidden[dt.id].oplata);
                if (ost>0)
                    td.attr('title','Долг: '+ost.toFixed(2)).tooltip({container:'body'});
            }
//            console.log(key,val,dt.id);
            if (key&&key!=='empt'){
                td.mouseenter(function(){
                    let chld=$(this).parent().children();
                    for (let i=0;i<chld.length;i++){
                        let tmp=$(chld.get(i)).attr('style')?$(chld.get(i)).css('background-color'):false;
                        if ($(this).parent().index() % 2){
                            $(chld.get(i)).addClass('dirt-hover');
                        }else{
                            $(chld.get(i)).addClass('dirt-hover-odd');
                        }
//                        if (tmp){
//                            $(chld.get(i)).css('color',tmp);
//                        }
                    }
                    self._doMouseLeave=true;
                });
                td.mouseleave(function(){
                    if (self._doMouseLeave){
                        if (!$(this).parent().attr('not-remove-hover')){
                            self._rowRemoveHLT($(this).parent());
                        }
                    }
                });
                td.contextmenu({self:self},self._rowContext);
            }
        });
        return rVal;
    },
};

(function( $ ) {
    $.widget( "custom.bugalterZakazController", $.custom.materialToOrder,$.extend({},bugalter_zakaz_controller));
}( jQuery ) );
