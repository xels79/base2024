/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom.inputRORemove", $.custom._inputROCBunner,$.extend({
        options:{
            action:'',
            afterSave:null,
        },
        _create:function(){
            this._super();
            this.element.click({self:this},this._askDialog);
            console.log('inputRORemove init.');
        },
        _askDialog:function(e){
            let self=e.data.self;
            e.preventDefault();
            if (self.options.action){
                let pRow=self.element.parent().parent();
                let id=pRow.attr('data-key');
                pRow.addClass('st-to-remove');
                new mDialog.m_alert({
                    headerText:'Внимание',
                    content: 'Удалить запись ?',
                    okClick:{
                        label:"Удалить",
                        click:function(){
                            $.post(self.options.action,{id:id}).done(function(answ){
                                if (answ.status==='ok'){
                                    if (typeof(answ.updateElementsText)==='object'){
                                        let k=Object.keys(answ.updateElementsText);
                                        for (let i in k){
                                            $(answ.updateElementsText[k[i]].selector).html(answ.updateElementsText[k[i]].value);
                                        }
                                    }
                                    if (typeof(self.options.afterSave)==='function'){
                                        self.options.afterSave.call(self);
                                    }
                                }
                            });
                        }
                    },
                    canselClick:{
                        label:"Отменить",
                        click:function(){
                            pRow.removeClass('st-to-remove');
                        }
                    },
                });                
            }else{
                new mDialog.m_alert({
                    headerText:'inputRORemove - Ошибка',
                    content: 'Не указан экшен "action"!',
                    okClick:'Закрыть'
                });
            }
        },
    }));
}( jQuery ) );
