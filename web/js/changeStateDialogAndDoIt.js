/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var showChangeStateDialogAndDoIt={
    __showChangeStateDialogAndDoIt:function(row,valsArray){
        let self=this,id=parseInt($(row).attr('data-key'));
        let select=$('<select>').addClass('form-control');
        let ml=null;
        let curentStage=parseInt(this._hidden[id].stage);
        let next=-1;
        let arrayPos=$.inArray(curentStage,valsArray);
        if (arrayPos>-1 && arrayPos<valsArray.length-1){
            next=valsArray[arrayPos+1];
        }
        if (!this.options.changeRowUrl){
            console.warn('changeStateToErrorClick','Не задан URL запроса');
            return;
        }
        if (!$.isArray(this._stageLevels)){
            console.warn('changeStateToErrorClick','Не передан список состояний (stageLevels)!');
            return;
        }
        
        for (let i=0;i<self._stageLevels.length;i++){
            if ($.inArray(i,valsArray)>-1){
                let option=$('<option>')
                        .text(self._stageLevels[[i]])
                        .attr('value',i);
                if (i===next){
                    option.attr('selected',true);
//                    select.val(i);
                }
                select.append(option);
            }
        }
        m_alert('Внимание',$('<div>')
                .addClass('input-group select-stage')
                .append($('<span>').addClass('input-group-addon').text('Изменить статус заказа на '))
                .append(select),function(){
                    console.log(parseInt(select.val()));
                    $.post(self.options.changeRowUrl,{id:''+id,stage:parseInt(select.val())}).done(function(answ){
                        if (answ.status!='ok'){
                            m_alert('Ошибка сервера',answ.errorText,true,false);
                        }
                        if (answ.pechatnikTable && self.options.pechatnikTable && $.isFunction(self.__drawPTable)){
                            self.options.pechatnikTable=answ.pechatnikTable;
                            self.update();
                            self.__drawPTable.call(self);
                        }else{
                            self.update();
                        }
                });
        },true,function(){
            $(row).children('.hand').removeClass('dirt-hover');
            $(row).children('[style]').css('color','inherit');
            $(row).removeAttr('not-remove-hover');
        });
        $(row).children('.hand').addClass('dirt-hover');
    },

};