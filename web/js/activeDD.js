/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var _ADDDEBUG = false;
window.ddId=1;
(function( $ ) {
    $.widget( "custom.activeDD", $.ui.autocomplete,$.extend({
        loadPicEl:null,
        firstStart:true,
        hideMessage:false,
        _new_sourse:null,
		ddID:0,
        options:{
            cachePerfixName:'DD',
            cacheable:true,
            cacheKey:'',
            oldVal:false,
            requestUrl:null,
            loadPicUrl:null,
            otherParam:{},
            bunnerClass:'activeDD-load',
            minLength :1,
            strictly:false,
            arrowClasses:'glyphicon glyphicon-triangle-bottom',
            onClickOrChange:null,
            onReady:null,
            afterUpdate:null,
            //position: { my : "right top", at: "right bottom"},
            zindex:false,
            autoLoad:true,
            inputId:false,
            contClassAdd:'',
            dontShowFirstError:false,
            select:function(e,ui){
                let el=$(this);
//                if ( _ADDDEBUG ) console.log(e,this);
                if ($.type(ui.item)==='object'){
                    el.val(ui.item.label);
                    if (ui.item.value && ui.item.value!='-1'){
                        el.attr('data-key',ui.item.value);
                    }else{
                        el.removeAttr('data-key');
                    }
                }else if ($.type(ui.item)==='string'){
                    el.val(ui.item);
                }
                if ($(this).activeDD('option','oldVal')!==$(this).attr('data-key')){
                    $(this).activeDD('waschange',ui);
                }
                e.preventDefault();
            },
        },
        _renderMenu: function( ul, items ) {
            var that = this;
            $.each( items, function( index, item ) {
                that._renderItemData( ul, item );
            });
//            ul.css({
//                "max-height":"none",
//            });
//            let ulH=ul.height();
//            if ( _ADDDEBUG ) console.log("activeDD",ulH);
//            if ( _ADDDEBUG ) console.log("activeDD",ul.parent().offset());
//            if ( _ADDDEBUG ) console.log("activeDD",$(window).height());
//            let lotH=$(window).height()-ul.parent().offset().top;
//            if ( _ADDDEBUG ) console.log("activeDD",lotH);
//            if (lotH<ulH){
                ul.css({
                    "max-height":"400px",
                    "overflow-y":"auto"
                });
//            }
            $( ul ).find( "li:odd" ).addClass( "odd" );//.css("background-color","#F00");
        },
        waschange:function(ui){
//            if ( _ADDDEBUG ) console.log('BASE Change');
            this.options.oldVal=this.element.attr('data-key');
            if (this.options.inputId){
                let tmpOld=$('#'+this.options.inputId).val();
                if ($.type(this.options.oldVal)!=='undefined'){
                    $('#'+this.options.inputId).val(this.options.oldVal);
                }else{
                    $('#'+this.options.inputId).val(0);
                }
                if (tmpOld!==$('#'+this.options.inputId).val()) $('#'+this.options.inputId).trigger('change');
            }
            if (!ui && this.element.attr('data-key')){
                ui={item:this.findByValue(this.element.attr('data-key'))};
            }
            if ($.isFunction(this.options.onClickOrChange))
                this.options.onClickOrChange.call(this.element,ui,this);
            else if ($.type(this.options.onClickOrChange)==='array'){
                if ($.isFunction(this.options.onClickOrChange[0]))
                    this.options.onClickOrChange[0].call(this.element,this.options.onClickOrChange.length>1?this.options.onClickOrChange[1]:null,ui,this);
                else if (this.options.onClickOrChange.length>1 && $.isFunction(this.options.onClickOrChange[1]))
                    this.options.onClickOrChange[1].call(this.element,this.options.onClickOrChange[0],ui,this);
            }
        },
        flashcache:function(){
            if (this.options.cacheKey){
                if ( _ADDDEBUG ) console.log('drop_D - чистим кэш');
                stones.cacheRuntime.unSet(this.options.cacheKey);
            }
        },
        _initDefault:function(){
            let self=this;
            let a=$('<a>'),wasOpen=false;
            let oldminLength=this.options.minLength;
            a.append($('<span>').attr({class:this.options.arrowClasses}));
            a.insertAfter(this.element);
            a.mousedown(function(){
                wasOpen=self.menu.activeMenu.is(':visible');//self.Widget()?self.Widget().is(':visible'):false;
            }).click(function(){
//                if ( _ADDDEBUG ) console.log('clk');
                self.element.trigger('focus');
                //self.options.minLength=oldminLength;
                if (wasOpen) return;
                self.options.minLength=0;
                if (self.options.source&&self.options.source.length) self.search('');
                self.options.minLength=oldminLength;
            });
            this.element.focusout({self:this},this._fOut);
            this.element.focusin({self:this},this._fIn);
            this.element.keydown({self:this},function(e){
                if (!self.menu.activeMenu.is(':visible')){
                    self.options.minLength=0;
                    if (self.options.source&&self.options.source.length) self.search('');
                    self.options.minLength=oldminLength;                    
                }
            });
            if (this.options.requestUrl&&this.options.autoLoad){
                this._doRequest();
            }else{
                if (this.firstStart){
                    this.firstStart=false;
//                    if (this._new_sourse){
//                        this.option('source',this._new_sourse);
//                    }
                    if ($.isFunction(this.options.onReady)) this.options.onReady.call(this.element,this);
                }
            }
            if (this.options.zindex){
                this.menu.element.css('z-index',this.options.zindex);
                this.menu.element.removeClass('ui-front');
                
            }
//            if ( _ADDDEBUG ) console.log(this);
            
        },
        _initSelect:function(){
            let self=this;
            this.options.source=[];
            this.element.children().each(function(){ 
                this.options.source[this.options.source.length]={ 
                    value:$(this).attr('value'),
                    label:$(this).text()
                };
            });
            
            let div=$('<div>');
            let inp=$('<input>').attr('name',this.element.attr('name')).addClass('ui-autocomplete-input');
            this.element.parent().empty().append(div);
            div.append(inp);
            this.element=inp;
            
            this._initDefault();
        },
        _create: function() {
            if (!this.options.requestUrl&&!this.options.source){
                console.warn('activeDD: не задан список возможных и адрес запроса тоже.');
            }
            this._super();
            //this._initDefault();
            this.ddID=window.ddId++;
            if (this.element.get(0).tagName==='SELECT'){ 
                this._initSelect();
            }else{
                this._initDefault();
            }
        },
        _fIn:function(e){
            e.data.self.element.parent().addClass('hasFocus');
        },
        setOldValue:function(val){
            this.options.oldVal=val;
        },
        _fOut:function(e){
            let self=e.data.self, isFind=false,val=self.element.val();
            self.element.parent().removeClass('hasFocus');
            if (!self.options.strictly || !self.options.source || !self.options.source.length) return;
            if (!val.length){
                self.element.removeAttr('data-key');
                self.element.parent().removeClass('hasError');
                if (self.options.oldVal!=self.element.attr('data-key'))
                    $(self.element).activeDD('waschange');
                return;
            }
            $.each(self.options.source,function(ind,el){
                if ($.type(el)==='string'){
                    isFind=el.toUpperCase()===self.element.val().toUpperCase();
                }else{
                    isFind=el.label.toUpperCase()===self.element.val().toUpperCase();
                }
                if (isFind && $.type(el)==='object'){
                    if (el.value!=-1){
                        self.element.attr('data-key',el.value);
//                        $(self.element).activeDD('waschange');
                    }
                }else{
                    self.element.removeAttr('data-key');
                }
                return !isFind;
            });
            if (!isFind){
                self.element.removeAttr('data-key');
                self.element.parent().removeClass('hasError');
                self.element.val('');
                $(self.element).activeDD('waschange');
                
                //$(this).trigger('focus');
            }else{
                self.element.parent().removeClass('hasError');
                if (self.options.oldVal!=self.element.attr('data-key'))
                    $(self.element).activeDD('waschange');
            }
        },
        _hideBunner:function(){
            if (this.options.loadPicUrl) this.element.next().remove();
            this.element.next().removeAttr('style');
            this.element.removeAttr('disabled');
        },
        _showBunner:function(){
            if (this.options.loadPicUrl){
                this.element.next().css('display','none');
                $().insertAfter()
                $('<img>').attr({
                    src:this.options.loadPicUrl,
                    class:this.options.bunnerClass,
                }).insertAfter(this.element);
            }
            this.element.attr('disabled',true);
        },
        findByValue:function(val,clk){
            let self=this;
            let rVal=null;
//            if ( _ADDDEBUG ) console.log(this);
            $.each(this.options.source,function(id,el){
                let f=false
                if ($.type(el)==='string'){
                    f=el==val;
                }else if ($.type(el)==='object'){
                    f=el.value==val;
                }
                if (f){
                    rVal=el;
                }
                if (clk && rVal!==null){
                    let item=self.menu.activeMenu.children(':nth-child('+id+')');
                    item.trigger('click');
//                    if ( _ADDDEBUG ) console.log(item);
                }
                return rVal===null;
            });
            return rVal;
        },
        findByText:function(val,clk){
            let self=this;
            let rVal=null;
//            if ( _ADDDEBUG ) console.log(this);
            $.each(this.options.source,function(id,el){
                let f=false
                if ($.type(el)==='string'){
                    f=el==val;
                }else if ($.type(el)==='object'){
                    f=el.label==val;
                }
                if (f){
                    rVal=el;
                }
                if (clk && rVal!==null){
                    let item=self.menu.activeMenu.children(':nth-child('+id+')');
                    item.trigger('click');
//                    if ( _ADDDEBUG ) console.log(item);
                }
                return rVal===null;
            });
            return rVal;
        },
        update:function(callB,hideMessage){
            if (hideMessage) this.hideMessage=true;
            if (this.options.requestUrl)
                this._doRequest(callB);
            else
                console.error('activeDD: не задан requestUrl!');
        },
        _proceedRequest:function(data, callB, key){
            let self=this;
            if (data.status==='error'){
                if ( _ADDDEBUG ) console.log('activeDD',this,'hasError');
                if (!this.hideMessage)
                    m_alert(data.headerText,data.errorText,true,false);
                else
                    this.hideMessage=false;
            }
            if (data.source){
                this.option('source',data.source);
                stones.cacheRuntime.setData(key, {data:data, callB:callB});
            }else{
                console.warn('activeDD: в ответ на запрос данные не получены!');
                stones.cacheRuntime.setData(key, null);
                return;
            }
            this._hideBunner();
            if (this.firstStart){
                this.firstStart=false;
                if ($.isFunction(this.options.onReady))
                    this.options.onReady.call(this.element, this, data);
                else if ($.isFunction(this.options.afterUpdate)) this.options.afterUpdate.call(this.element,this, data);
            }else{
                if ($.isFunction(this.options.afterUpdate)) this.options.afterUpdate.call(this.element,this, data);
            }
            if ($.isFunction(callB)) callB.call(this.element, this, data);
            
        },
        _createKey:function(oParam){
            let ret=this.options.cachePerfixName+this.ddID;
            if (typeof (oParam.tableName)==='string'){
                ret+=oParam.tableName;
            }
			if (typeof(oParam.category)==='array'){
				ret+=oParam.category.toString();
			}else if (typeof(oParam.category)==='object'){
				let oKey=Object.keys(oParam.category);
				for (let i in oKey){
					ret+=oParam.category[oKey[i]];
				}
			}else if (typeof(oParam.category)==='string'){
				ret+=oParam.category;
			}

            if (typeof(oParam.id)!=='undefined'){
                ret+='ddWithId'+oParam.id;
            }
            return ret;
        },
        _doRequest:function(callB){
            let oParam=$.isFunction(this.options.otherParam)?this.options.otherParam.call():this.options.otherParam;
            let self=this;
            let tmpD=null,key;
            if (!oParam) oParam={};
            this._showBunner();
            if (!this.options.cacheKey){
                    key=this._createKey(oParam);
                    this.options.cacheKey=key;
            }else{
                    key=this.options.cacheKey;
            }
            if (typeof(key)==='string'){
                tmpD=stones.cacheRuntime.getData(key);
            }
            if (this.options.cacheable && tmpD!==null){
                if ( _ADDDEBUG ) console.log('drop_D - from cache',this.options.requestUrl,oParam,"key:"+key);
                this._proceedRequest(tmpD.data, callB, key);
            }else{
                if ( _ADDDEBUG ) console.log('drop_D - request',this.options.requestUrl,oParam,"key:"+key);
                $.post(this.options.requestUrl,oParam).done(function(data){
                    self._proceedRequest(data, callB, key);
                });
            }
        }
    }));
}( jQuery ) );
