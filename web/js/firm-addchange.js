/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


let firm_addchange={
    options:{
        simpleForm:{
            firm_id:0,
            id_associated_column:'',
            id_column_with_record_id:'',
            form_Id:'',
//            baseActionName:'',
            requestUrl:'',
            loadPicUrl:'',
            width:300,
            pointPicUrl:'',
            onTabValidate:'',
            address:'',
            ks:'',
            okpo:'',
            bank:'',
            contentBackgroundColor:'white',
            bodyBackgrounColor:'white',
            controller:{},
            beforeClose:function(){
//                console.groupEnd();
            },
            requestUpdateParent :function(){},
        },
        beforeOpen:null,
        actionParams:null,
        defaultActionName:'add',
        headerText:'Не задан',
        baseActionName:'',
        requestParam:{},
        isOnRightClick:false,
        classN:'',
        actionNames:{
            add:'добавить',
            change:'изменить',
            view:'просмотр'
        }
    },
    _create:function(){
        this._super();
        if (this.options.isOnRightClick){
            
        }else{
            this.element.click({self:this},this._click);
        }
    },
    _click:function(maineEvent,action){
        let self=maineEvent.data.self;
        maineEvent.preventDefault();
        if ($.isFunction(self.options.beforeOpen)){
            if (self.options.beforeOpen.call(self.element.get(0),self)===false) return;
        }
        let options=$.extend({},{},self.options.simpleForm);
        if ($.type(action)!=='object'){
            if ($.isFunction(self.options.actionParams)){
                action=$.extend({},{'req':{
                    name:self.options.defaultActionName+self.options.baseActionName
                }},self.options.actionParams.call(self.element.get(0)));
            }else if($.type(self.options.actionParams)==='object'){
                action=$.extend({},{'req':{
                    name:self.options.defaultActionName+self.options.baseActionName
                }},self.options.actionParams);
            }else{
                action={'req':{
                    name:self.options.defaultActionName+self.options.baseActionName
                }};
            }
        }
        if ($.type(action.req)!=='object') action.req={};
        $.each(self.options.requestParam,function(key,val){
            action.req[key]=val;
        });
        let header=self.options.headerText;
        if ($.type(self.options.actionNames)==='object'){
            $.each(self.options.actionNames,function(key,val){
                if (action.req.name.indexOf(key)===0){
                    header+=' '+val;
                    return false;
                }
            });
        }
        console.log('firm_addchange->_click',self.options,action);
        header=header.substr(0,1).toUpperCase()+header.substr(1,header.length-1);
        options.headerText=header;
        options.requestOtherParam=action;
        options.baseActionName=self.options.baseActionName;
//        options.afterInit=function(){
//            this.dialog.body.find('[type=text]').focus(function(e){
//                console.log('focus');
//                e.stopImmediatePropagation();
//                e.preventDefault();
//                e.stopPropagation();
//                
//            });
//            this.dialog.body.find('[type=text]').focusout(function(e){
//                console.log('focusout');
//                e.stopImmediatePropagation();
//                e.preventDefault();
//                e.stopPropagation();
//            });
//            this.dialog.body.find('[type=text]').click(function(e){
//                console.log('click');
//                e.stopImmediatePropagation();
//                e.preventDefault();
//                e.stopPropagation();
//            });
//            
//        }
        $.custom.simpleForm(options);
    }
};

(function( $ ) {
    $.widget( "custom.firm_addchange", $.Widget,$.extend({},firm_addchange));
}( jQuery ) );