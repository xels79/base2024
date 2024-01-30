/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aHide=true;
var ActiveDropDown=jQuery.Class.create({
    loadLikeFromLabel:function(lbl){
        return rVal={
            likefilterajaxurl:lbl.attr('likefilterajaxurl'),
            likerequstvarname:lbl.attr('likerequstvarname'),
            likerequestname:lbl.attr('likerequestname'),
            likefiltervarname:lbl.attr('likefiltervarname'),
        };
//        if (lbl.attr('likeOnNewValue')){
//            rVal['likeOnNewValue']
//        }
    },
    init: function(opt,
                   addFnc,
                   clickFnc,
                   afterClickFnc,
                   likeOnNewValue,
                   likeOnAfterListUpdate,
                   likeOnBeforListUpdate,
                   likeOnBeforListUpdateGetParam){
        var tmp;
        if ($.isPlainObject(opt))
            tmp=opt;
        else
            tmp=jQuery.parseJSON(opt);
        if (tmp['prevDef'])
            this.prevDef=true;
        else
            this.prevDef=false;
        if (tmp['exactValue'])
            this.exactValue=true;
        else
            this.exactValue=false;
        
        this.menu=$('#'+tmp.menuId);
        this.label=$('#'+tmp.labelId);
        this.errTag=this.label.parent().parent().children('p:last-child');
        if (!this.errTag.length) this.errTag=false;
        if (tmp['likeParam']){
            if (tmp['likeParam']===true){
                this.likeParam=this.loadLikeFromLabel(this.label);
            }else{
                this.likeParam=tmp['likeParam'];
            }
            if ($.isFunction(likeOnNewValue))
                this.likeParam.likeOnNewValue=likeOnNewValue;
            else
                this.likeParam.likeOnNewValue=false;
            if ($.isFunction(likeOnAfterListUpdate))
                this.likeParam.likeOnAfterListUpdate=likeOnAfterListUpdate;
            else
                this.likeParam.likeOnAfterListUpdate=false;
            if ($.isFunction(likeOnBeforListUpdate))
                this.likeParam.likeOnBeforListUpdate=likeOnBeforListUpdate;
            else
                this.likeParam.likeOnBeforListUpdate=false;
            if ($.isFunction(likeOnBeforListUpdateGetParam))
                this.likeParam.likeOnBeforListUpdateGetParam=likeOnBeforListUpdateGetParam;
            else
                this.likeParam.likeOnBeforListUpdateGetParam=false;
        }else
            this.likeParam=false;
        if (tmp['likeOtherParam'])
            this.likeOtherParam=tmp['likeOtherParam'];
        else
            this.likeOtherParam=false;
        this.back='';
        this.label.focusin({thisObj:this},function(e){
            e.data.thisObj.back=$(this).val();
            if (aHide){
                $('.dropdown-toggle').parent().removeClass('open');
            }else{
                aHide=true;
            }
        });
        this.showDD=function(mm){
            if (!mm) mm=this.menu;
            if (!mm.parent().hasClass('open')){
                mm.parent().addClass('open');
                mm.prev().attr('aria-expanded',true);
            }
        };
        this.hideDD=function(mm){
            if (!mm) mm=this.menu;
            console.debug('hideDD',mm.parent());
        };
        if (this.likeParam){
            /*
             * 
             *              Поиск похожих
             * 
             * 
             * */
            this.label.focusout({thisObj:this},function(e){
                var t=e.data.thisObj;
                var found=false;
                var idLvl=t.label.attr('id').substring(3,t.label.attr('id').lenght);
                if (idLvl!=$(e.relatedTarget).parent().parent().attr('id')){
                    if ($(this).val().length){
                        var val=$(this).val().toLowerCase();
                        $.each(e.data.thisObj.menu.children(),function(id,el){
                            var a=$(el).children('a:first-child');
                            var aVal=a.text();
                            if (val===aVal.toLowerCase()){
                                console.log(aVal.toLowerCase());
                                a.click();
                                found=true;
                                return false;
                            }
                        });
                        if (!found){
                            var continueCheck=true;
                            if ($.isFunction(t.likeParam.likeOnNewValue)){
                                continueCheck=t.likeParam.likeOnNewValue(e,$(this).val());
                            }
                            if (continueCheck){
                                if (e.data.thisObj.menu.children().length&&t.exactValue){
                                    e.data.thisObj.menu.children(':first-child').children('a:first-child').click();
                                }else{
                                    if (t.exactValue){
                                        $(this).val('');
                                        t.formControlID.val(null);
                                    }
                                }
                                t.likeDefault(this);
                            }
                        }
                    }
                    t.likeDefault(this);
                    t.hideDD(t.menu);
                    console.debug('f:out',$(e.relatedTarget).parent().children('ul'));
                }
            }),
            this.createDopParam=function(t,el){
                var dopParam={};
                if (t.likeOtherParam)
                    dopParam=t.likeOtherParam;
                if ($.isFunction(t.likeParam.likeOnBeforListUpdateGetParam))
                    dopParam=$.extend(dopParam,t.likeParam.likeOnBeforListUpdateGetParam.call(el,t));
                console.debug('createDopParam',dopParam,t.likeParam);
                return dopParam;
            },
            this.likeDefault=function(el){
                var t=this;
                var menu=t.menu;
                var data={ajax:true};
                if (!this.likeParam) return;
                data[t.likeParam.likerequstvarname]=t.likeParam.likerequestname;
                data[t.likeParam.likefiltervarname]='';
                data=$.extend(data,t.createDopParam(t,el));
                if ($.isFunction(t.likeParam.likeOnBeforListUpdate)){
                    t.likeParam.likeOnBeforListUpdate.call(el,t);
                }
                $.ajax({url:t.likeParam.likefilterajaxurl,type:'post',data:data}).done(function(data){
                    if (data['status']==='ok'){
                        menu.html($(data.html).find('#'+menu.attr('id')).html());
                        menu.children('li').click({thisObj:t} ,t.itemClick);
                        if (t.likeParam.likeOnAfterListUpdate){
                            t.likeParam.likeOnAfterListUpdate.call(el,t);
                        }
                    };
               });
            };
            this.label.keyup({thisObj:this} ,function(e){
                var t=e.data.thisObj;
                var lbl=t.label;
                var menu=t.menu;
                var data={ajax:true};
                console.debug('init:label.keUp',e);
                if (e.key!=='Tab'&&e.key!=='Enter'){
                    data[t.likeParam.likerequstvarname]=t.likeParam.likerequestname;
                    data[t.likeParam.likefiltervarname]=lbl.val();
                    if (t.likeOtherParam)
                        data=$.extend(data,t.createDopParam(t,this));
                    if ($.isFunction(t.likeParam.likeOnBeforListUpdate)){
                        t.likeParam.likeOnBeforListUpdate.call(this,t);
                    }
                    $.ajax({url:t.likeParam.likefilterajaxurl,type:'post',data:data}).done(function(data){
                        if (data['status']==='ok'){
                            menu.html($(data.html).find('#'+menu.attr('id')).html());
                            menu.children('li').click({thisObj:t} ,t.itemClick);
                            console.debug('sh',menu.prev());
                            if ($.isFunction(t.likeParam.likeOnAfterListUpdate)) t.likeParam.likeOnAfterListUpdate(this,t);
                            //if (menu.children('li').length)
                            if (!menu.children('li').length&&lbl.val().length){
                                t.hideDD(menu);
                            }else{
                                t.showDD();
                            }
                        };
                   });
                }
            });
        }
        
        
        if (tmp.formControlID){
            this.formControlID=$('#'+tmp.formControlID);
        }else{
            this.formControlID=null;
        }
        if ($.isFunction(clickFnc)){
            this.clickFunction=clickFnc;
        }else{
            this.clickFunction=false;
        }
        if ($.isFunction(afterClickFnc)){
            this.afterClickFunction=afterClickFnc;
        }else{
            this.afterClickFunction=false;
        }
        if ($.isFunction(addFnc)){
            this.addFunction=addFnc;
        }else{
            this.addFunction=false;
        }
        if (tmp.formId){
            this.formId=tmp.formId;
        }else{
            this.formId=false;
        }
        this.cFieldValueExist=function(obj){
            if (obj){
                var chk=false;
                $.each(obj.menu.children(),function(id,el){
                    var a=$(el).children('a:first');
                    console.debug('cFieldValueExist',a);
                    if (obj.label.val()==a.text()){
                        obj.formControlID.val(a.attr('value'));
                        chk=true;
                        return false;
                    }
                });
                return chk;
            }
        }
        var iSTIfNotFound=tmp.iSTIfNotFound;
        if (iSTIfNotFound!=="undefined"){
            this.iSTIfNotFound=tmp.iSTIfNotFound;
            if (this.iSTIfNotFound){
                if (this.formId){
                    $('#'+this.formId).submit({thisObj:this},function(e){
//                        e.preventDefault();
                        var t=e.data.thisObj;
                        console.debug('ActiveDropdown:iSTIfNotFound',$(this));
                        if (t.label[0].tagName==='INPUT'){
                            console.debug('ActiveDropdown:iSTIfNotFound',t.label.val());
                            if (!t.cFieldValueExist(t)) t.formControlID.val('@'+t.label.val());
                        }
                    });
                }
            }
        }
        if (this.label[0]){
            if (this.exactValue&&this.label[0].tagName==='INPUT'){
                this.label.focusout({thisObj:this},function(e){
                    var t=e.data.thisObj;
                    if (t.cFieldValueExist(t)){
                        t.hideError();
                    }
                });
            }
        }
        $(document).submit({thisObj:this},function(e){
            var t=e.data.thisObj;
            console.debug('ActiveDropDown:onSbmit');
            if (t.exactValue){
                if (!t.cFieldValueExist(t)){
                    e.preventDefault();
                    t.showError('Неверное значение');
                    t.label.focus();
                }else{
                    t.hideError();
                }
            }
        });
        this.menu.children('li').click({thisObj:this} ,this.itemClick);
        console.debug('ActiveDropdownInit',this);
    },
    showError:function(txt){
        console.debug('ActiveDropDown:showError',this);
        if (this.exactValue&&this.label[0].tagName==='INPUT'){
            console.debug('ActiveDropDown:showError',this.label.parent());
            this.label.parent().parent().addClass('has-error');
            if (this.errTag) this.errTag.text(txt);
        }
    },
    hideError:function(){
        if (this.label.parent().parent().hasClass('has-error')){
            this.label.parent().parent().removeClass('has-error');
            this.label.parent().parent().addClass('has-success');                        
        }
    },
    itemClick: function(e){
        var t=e.data.thisObj;
        if (t.prevDef) e.preventDefault();
        var tmp=$(this).children(':first').attr('value');
        //console.log('click');
        console.debug('itemClick',tmp);
        if (tmp==-1){
            if (t.addFunction) t.addFunction(this); 
        }else{
            if (t.clickFunction){
                console.debug('itemClick:user',t.formControlID);
                t.clickFunction(this,e);
            }else{
                var txt=$(this).text();
                var title=$(this).attr('title');
                if (!title)title="";
                if (t.label[0].tagName=='INPUT')
                    t.label.val(txt);
                else
                    t.label.text(txt);
                if (txt.length>12) title=txt+' '+title;
                if (title.length>4) t.label.attr('title',title);
                console.debug('itemClick:defult',t.formControlID);
                if (t.formControlID) t.formControlID.val(tmp);
                if (t.afterClickFunction) t.afterClickFunction(this,e) ;
            }
        }
    },
    loadList:function(list){
        console.debug('loadList',list);
        var menu=$(this.menu);
        //$().replaceAll();
        menu.children().remove();
        console.debug('loadList',menu);
        $.each(list,function(id,el){
            var url='';
            var href='#';
            if(el['url']) url=el.url;
            if (el['href']) href=el.href;
            var aOpt={
                value:el.value,
                href:href,
            };
            if (url) aOpt['url']=url;
            menu.append($.fn.createObj({
                tag:'li',
                html:$.fn.createObj({
                    tag:'a',
                    text:el.label,
                    options:aOpt
                }),
                options:el.options
            }));

        });
        this.menu.children('li').unbind('click');
        this.menu.children('li').click({thisObj:this} ,this.itemClick);
    }
});
$.fn.activeDDSetVal=function(idorobj,val){
    var par;
    if ($.type(idorobj)==='OBJECT')
        par=idorobj;
    else
        par=$('#'+idorobj);
    var hiden=par.children(':eq(0)');
    var lbl=par.children(':eq(1)');
    hiden.val(val);
    $.each(par.children('div:last-child').children('ul:last-child').children(),function(id,el){
        var a=$(el).children('a:first-child');
        if (a.attr('value')==val){
            if (lbl[0].tagName==='INPUT'){
                lbl.val(a.text());
            }else{
                lbl.text(a.text());
            }
        }
    });
};