/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom.inputROAdd", $.custom._inputROCBunner,$.extend({
        options:{
            action:'',
            formName:'',
            date:'',
            afterSave:null,
        },
        _create:function(){
            this._super();
            this.element.click({self:this},this._askDialog);
            console.log('inputROAdd - init.');
        },
        _askDialog:function(e){
            let self=e.data.self;
            e.preventDefault();
            if (self.options.action && self.options.formName && self.options.date){
                let d=$('<div>').attr('title','Добавить запись.');
                let f=$('<form>').attr('role','form').appendTo(d);
                let tmpD=$('<div>').addClass('form-group').appendTo(f);
                let b=$('<button>').addClass('btn btn-default').text('Добавить');
                tmpD.append($('<label>').text('Название').attr('for','stf-name'));
                let name=$('<input>').addClass('form-control').attr({
                    type:'text',
                    id:'stf-name',
                    autocomplete:'off'
                }).appendTo(tmpD);
                tmpD.append($('<span>').addClass('help-block'));
                tmpD=$('<div>').addClass('form-group').appendTo(f);
                tmpD.append($('<label>').text('Стоимость').attr('for','stf-coast'));
                let coast=$('<input>').addClass('form-control').attr({
                    type:'text',
                    id:'stf-coast',
                    autocomplete:'off'
                }).appendTo(tmpD);
                tmpD.append($('<span>').addClass('help-block'));
                f.append(b);
                d.dialog({
                    modal:true,
                    width:500
                });
                b.click({
                    self:self,
                    dialog:d,
                    name:name,
                    coast:coast
                },self.saveClick)
                name.focusout(function(){
                    self._checkErrors(name,null);
                });
                coast.focusout(function(){
                    self._checkErrors(null,coast);
                });
            }else{
                new mDialog.m_alert({
                    headerText:'inputROAdd - Ошибка',
                    content: !self.options.date?'Не указана дата "date"':(self.options.action?'Не указан класс формы "formName"!':'Не указан экшен "action"!'),
                    okClick:'Закрыть'
                });
            }
        },
        _checkErrors:function(name,coast){
            let hasError=false;
            if (name){
                name.parent().removeClass('has-error');
                name.next().text('');
            }
            if (coast){
                coast.parent().removeClass('has-error');
                coast.next().text('');
            }
            if (name && !name.val().length){
                name.next().text('Поле "название" неможет быть пустым');
                name.parent().addClass('has-error');
                hasError=true;
            }
            if (coast && coast.val().length){
                let val=coast.val().trim();
                let regex = /^\d+$|^\d+\.\d+$/gm;
                if (!regex.test(val)){
                    coast.next().text('Поле "Стоимость" должно быть числом');
                    coast.parent().addClass('has-error');
                    hasError=true;                    
                }
            }
            return hasError;
        },
        saveClick:function(e){
            let {self,dialog,name,coast}=e.data;
            let data={};
            data[self.options.formName]={};
            e.preventDefault();
            if (self._checkErrors()) return;
            data[self.options.formName].name=name.val();
            data[self.options.formName].coast=coast.val().trim();
            data[self.options.formName].date=self.options.date;
            $.post(self.options.action,data).done(function(answ){
                if (answ.status==='ok'){
                    dialog.dialog('close');
                    dialog.dialog('destroy');
                    dialog.remove();
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
        },
    }));
}( jQuery ) );
