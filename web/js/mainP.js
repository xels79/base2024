/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom.mainPController", $.custom.baseW,{
        getWidgetName:function(){
            return 'mainPController'
        },
        _create: function() {
            this.fields={
                requestCustomer:'Не задан адрес запроса для заказчиков',
                bankSearchUrl:{message:'Url для поиска банка не задан!','default':false},
                loadPicUrl:{message:'Картинка загрузки не задана!','default':''},
            };
            let controller=this;
            if (this._super()===true){
                this.element.click({self:this},function(e){
                    let self=e.data.self;
                    self.dialog=new ajaxdialog({
                        requestList:self.options.requestCustomer,
                        //requestListParam:self.options.requestOtherParam,
                        contentBackgroundColor:'#66c2d1',
                        bodyBackgrounColor:'white',
                        headerText:'Заказчики',
                        style:{
                            width:'15cm',
                        },
//                        beforeClose:function(){
//                            console.debug(self.createMessageText('beforeClose'),'run delete');
//                            if ($.isFunction(self.options.beforeClose)) self.options.beforeClose.call(this);
//                            delete self;
//                        }
                    });
                    self.dialog.afterInit=function(dialog){
                        self.reInitCustomer(dialog,self);
                        if ($.isFunction(self.options.afterInit)) self.options.afterInit.call(self);
                    };
                    self.dialog.show();
                });
            }
        },
        reInitPJaxCustomer:function(reload){
            if (reload){
                $.pjax.reload('#customerListPjax',{
                    push:false,
                    timeout:2000,
                    type:'POST'                    
                });
            }else{
                $('#customerListPjax').pjax({
                    push:false,
                    timeout:2000,
                    type:'POST'
                });
            }
        },
        reInitCustomer:function(dalog,self){
            console.debug(self.createMessageText('reInitCustomer'),'reInit');
//            $('#addFirm').click({self:this},function(e){
//                e.preventDefault();
//                let self=e.data.self;
//                $.custom.simpleForm({
//                    requestUrl:self.options.requestCustomer,
//                    headerText:'Добавиь фирму заказчика',
//                    pointPicUrl:self.options.pointPicUrl,
//                    requestUpdateParent:function(){
//                         self.requestUpdateParent(true);
//                    },
//                    width:'10cm',
//                    controller:self,
//                    form_Id:'#addFirmForm',
//                    baseActionName:'zak'
//                });
//            });
            
        },
    });
}( jQuery ) );
