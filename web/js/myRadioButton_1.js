/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var mRButtDefId=1, mRButtDefInputId=1;

var mRButt={
        _inp:null,
        options:{
            wrapClass:'m-radio',    //Имя CSS класса по умолчанию
            itemTagName:'span',     //Имя тэга для каждого элемента
            values:{},              //Набор ключ:значение
            defaultValue:0,         //Значение по умолчанию
            id:false,               //id - обёртки
            inputId:false,          //id - невидимого input
            name:'',                //Параметр name для input
            onChange:null,          //Функция которая выполница при изменение значения
            onClick:null,           //Функция которая выполница при нажатие кнопки мыши
            useInput:false,         //Если = true буднт использован INPUT
        },
        _create:function(){
            var self=this;
            this._super();
            this._inp=$('<input>').attr('type','hidden');
            if (this.options.id===false){
                this.options.id='radioButton'+mRButtDefId++;
            }
            if (this.options.inputId===false){
                this.options.inputId='radioButtonInput'+mRButtDefInputId++;
            }
            this.element.addClass(this.options.wrapClass).attr('id',this.options.id);
            this._inp.attr({
                id:this.options.inputId,
                name:this.options.name
            });
           $.each(this.options.values,function(id,el){
               if (self.options.useInput){
                    var opt={type:'radio',name:self.options.name};
                    if (id==self.options.defaultValue){
                        opt.checked='checked';
                    }
                    var tmp=$('<'+self.options.itemTagName+'>').attr('data-key',id).appendTo(self.element)
                        .append($('<input>').val(id).attr(opt).click({self:self},self._itClick))
                        .append($('<span>').text(el));
                    if (id==self.options.defaultValue){
                        tmp.addClass('on');
                    }
                }else{
                    var tmp=$('<'+self.options.itemTagName+'>').attr('data-key',id).click({self:self},self._itClick).appendTo(self.element).append($('<span>').text(el));
                    if (id==self.options.defaultValue){
                        tmp.addClass('on');
                        self._inp.val(id);
                        if ($.isFunction(self.options.onChange)) self.options.onChange.call(self._inp,id);
                    }
                }
            });
            if (!self.options.useInput){
                this._inp.change(function(){
                    if ($.isFunction(self.options.onChange)) self.options.onChange.call(this,$(this).val());
                });
                this.element.append(this._inp);
            }
        },
        _itClick:function(e){
            if (this.tagName==='INPUT'){
                $(this).parent().parent().children().removeClass('on');
                $(this).parent().addClass('on');
                if ($.isFunction(e.data.self.options.onClick)) e.data.self.options.onClick.call(this,$(this).val());
                if ($.isFunction(e.data.self.options.onChange)) e.data.self.options.onChange.call(this,$(this).val());
            }else{
                if (!$(this).hasClass('on')){
                    $(this).parent().children(e.data.self.options.itemTagName).removeClass('on');
                    e.data.self._inp.val($(this).attr('data-key'));
                    $(this).addClass('on');
                    if ($.isFunction(e.data.self.options.onClick)) e.data.self.options.onClick.call(this,$(this).attr('data-key'));
                    if ($.isFunction(e.data.self.options.onChange)) e.data.self.options.onChange.call(this,$(this).attr('data-key'));
                }
            }
        },
        value:function(val){
            if (val){
                var i=1,self=this;
                $.each(this.options.values,function(id,el){
                    if (id==val){
                        self._inp.val(val);
                        self.element.children(self.options.itemTagName).removeClass('on');
                        self.element.children(self.options.itemTagName+':nth-child('+i+')').addClass('on');
                        if ($.isFunction(self.options.onChange)) self.options.onChange.call(self._inp,val);
                        return false;
                    }
                    i++;
                });
                return val;
            }else{
                return this._inp.val();
            }
        }
    };

(function( $ ) {
$.widget( "custom.radioButton", $.Widget,mRButt);
}( jQuery ) );