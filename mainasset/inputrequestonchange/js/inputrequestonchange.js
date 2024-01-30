/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
let IROCOptions=function(){
    let _red='',_yellow='',_green='';
    Object.defineProperty(this, 'red', {
        get:function(){
            return _red;
        }
    });
    Object.defineProperty(this, 'yellow', {
        get:function(){
            return _yellow;
        }
    });
    Object.defineProperty(this, 'green', {
        get:function(){
            return _green;
        }
    });
    this.setOptions=function(red,yellow,green){
        if (red) _red=red;
        if (yellow) _yellow=yellow;
        if (green) _green=green;
        console.log('inputROCOptions',{red:_red,yellow:_yellow,green:_green});
    }
    console.log('inputROCOptions',{red:_red,yellow:_yellow,green:_green});
};
$.custom.inputROCOptions=new IROCOptions();

(function( $ ) {
    $.widget( "custom.inputROC", $.custom._inputROCBunner,$.extend({
        options:{
            action:'',
            keyColumnName:'',
            keyColumnValue:0,
            dataColumnName:'',
            dataFormName:'',
            totalUpdateSelectorsText:'',
            afterSave:null,
        },
        _create: function() {
            this._super();
            if (!this.options.action){
                console.error("inputROC не передан обязательный параметр 'action'");
                return;
            }
            if (!this.options.keyColumnName){
                console.error("inputROC не передан обязательный параметр 'keyColumnName'");
                return;
            }
            if (this.options.keyColumnValue<1){
                console.error("inputROC не передан обязательный параметр 'keyColumnValue' или неверное значение");
                return;
            }
            if (!this.options.dataColumnName){
                console.error("inputROC не передан обязательный параметр 'dataColumnName'");
                return;
            }
            if (!this.options.dataFormName){
                console.error("inputROC не передан обязательный параметр 'dataFormName'");
                return;
            }
            this.element.parent().css('position','relative');
            this.buner=$('<img>').css({
                top:2,left:3,width:'10px',height:'10px',
                position:'absolute',display:'none'
            }).appendTo(this.element.parent());
            this.element.change({self:this},this._dataChange);
            this.element.focusin(function(){
                $(this).attr('data-back',$(this).val());
            });
            console.log('inputROC init.');
        },
        _removeClick:function(e){
            let self=e.data.self;
        },
        _addClick:function(e){
            let self=e.data.self;
        },
        save:function(){
            this.__save();
        },
        __save:function(){
            let self=this;
            let rqData={};
            let el=this.element.get(0);
            rqData[self.options.dataFormName]={};
            rqData[self.options.keyColumnName]=self.options.keyColumnValue;
            rqData[self.options.dataFormName][self.options.dataColumnName]=this.element.val();
            self._sendBuner();
            $.post(self.options.action,rqData).done(function(answer){
                self._removeBuner();
                if (answer.status==='ok'){
                    $(el).removeClass('stHasError');
                    $(el).removeAttr('title');
                    $(el).popover('destroy');
                    self._okBuner();
                    if (typeof (answer.total)==='object'){
                        let cnt=$('[data-total="'+answer.total.columnName+'"]').children('span');
                        if (!cnt.length){
                            $('[data-total="'+answer.total.columnName+'"]').text(answer.total.total);
                        }else{
                            cnt.text(answer.total.total);
                        }
                        if (self.options.totalUpdateSelectorsText){
                            cnt=$(self.options.totalUpdateSelectorsText).children('span');
                            if (!cnt.length){
                                $(self.options.totalUpdateSelectorsText).text(answer.total.total)
                            }else{
                                cnt.text(answer.total.total)
                            }
                        }
                    }
                    if (typeof(answer.updateElementsText)==='object'){
                        let k=Object.keys(answer.updateElementsText);
                        for (let i in k){
//                            console.log(i,k[i],answer.updateElementsText[k[i]]);
                            $(answer.updateElementsText[k[i]].selector).html(answer.updateElementsText[k[i]].value);
                        }
                    }
                    if (typeof(self.options.afterSave)==='function'){
                        self.options.afterSave.call(el);
                    }
                }else{
                    if (answer.status==='errorModal'){
                        $(el).addClass('stHasError');
                        if (typeof(answer.errors)==='object'){
                            $(el).attr('title',answer.errors[self.options.dataColumnName]);
                            $(el).popover({trigger:'manual',placement:'top'}).popover('show');
                        }
                        self._errorBuner();
                    }
                }
                console.log(answer);
            });            
        },
        _dataChange:function(e){
            let self=e.data.self;
            let val=$(this).val();
            if (val!==$(this).attr('data-back')){
                self.__save();
            }
        },
    }));
}( jQuery ) );
