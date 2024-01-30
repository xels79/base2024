/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


let bugalter_post_zakaz_controller={
    _dialog:null,
    options:{
        savePaiedUrl:'',
    },
    _create:function(){
        
        this._super();
        this._editCollName='';
        this.options.isDisainer=true;
    },
    _generateTD:function(key,val,id,subid){
        let td=$('<td>'),ht=14;
        let _val=parseInt(val);
        if (!this._isOdd)
            td.css('background-color',this._oddColor);
        td.text(val);
        if (!isNaN(_val)){
            if ((key==='material_residue_list' || key==='podryad_residue_list') && _val){
//                td.empty().append($('<button>').text(val));
                td.css('color','red');
            }
        }
        if (key==='material_paied_list'){
            let tcl=this._hidden[id]['material_total_coast_list'][subid];
            let p=this._hidden[id]['material_paied_list'][subid];
            tcl=($.type(tcl)==='string')?parseInt(tcl.replace(/\s/g, '')):parseInt(tcl);
            p=($.type(p)==='string')?parseInt(p.replace(/\s/g, '')):parseInt(p);
            if (subid && tcl-p){
                td.empty()
                  .append($('<button>')
                  .text(val))
                  .attr('title','Оплатить')
                  .click({
                    self:this,
                    total:tcl,
                    paied:p,
                    id:subid
                  },this._paiClicked);
            }
        }
        if (key==='podryad_paied_list'){
            let tcl=this._hidden[id]['podryad_total_coast_list'][subid];
            let p=this._hidden[id]['podryad_paied_list'][subid];
            tcl=($.type(tcl)==='string')?parseInt(tcl.replace(/\s/g, '')):parseInt(tcl);
            p=($.type(p)==='string')?parseInt(p.replace(/\s/g, '')):parseInt(p);

            if (subid && tcl-p){
                td.empty()
                  .append($('<button>')
                  .text(val))
                  .attr('title','Оплатить')
                  .click({
                    self:this,
                    total:tcl,
                    paied:p,
                    id:subid
                  },this._paiClicked);
            }
        }
        return td;
    },
    _paiRequest:function(self,data){
        let val=$('#user-input').val();
        let dialog=self._dialog;
        if ($('#user-input').val())
            val=parseFloat($('#user-input').val());
        else
            val=data.total-data.paied;
        if (isNaN(val)){
            dialog.bodyContent.parent().children('small').remove();
            dialog.bodyContent.parent().append($('<small>').text('Неверные символы!').css('color','red'));
            $('#user-input').trigger('focus');
        }else{
            if (val>data.total-data.paied){
                dialog.bodyContent.parent().children('small').remove();
                dialog.bodyContent.parent().append($('<small>').text('Слишком много!').css('color','red'));
                $('#user-input').trigger('focus');
            }else{
                if (self.options.savePaiedUrl){
                    console.log();
                    $.post(self.options.savePaiedUrl,{
                        id:data.id,
                        paid:val
                    }).done(function(data){
                        if (data.status=='ok'){
                            console.log('ok');
                            dialog.close();
//                            delete self._dialog;
//                            self._dialog=null;
                            self.update();
                        }else{
                            dialog.bodyContent.parent().children('small').remove();
                            dialog.bodyContent.parent().append($('<small>').text('Ошибка сервера: "'+data.errorText+'" !').css('color','red'));
                            $('#user-input').trigger('focus');
                        }
                    });
                    console.log('saving');
                }else{
                    console.warn('Не задан "savePaiedUrl"');
                }
            }
        }
    },
    _paiClicked:function(e){
        console.log(e.data);
        let self=e.data.self;
        self._dialog=m_alert('Оплатить материал',
            $('<div>').addClass('input-group')
                .append($('<span>').addClass('input-group-addon').text('Оплатить:'))
                .append($('<input>').addClass('form-control').attr({
                    id:'user-input',
                    type:'text',
                    placeholder:e.data.total-e.data.paied
                }).keyup(function(ke){
                    if (ke.keyCode===13){
                        self._paiRequest.call(this,self,e.data)
                    }
                })),{
                    label:'Сохранить',
                    click:function(sub_e){self._paiRequest.call(this,self,e.data)}
                },'Отмена',function(){
                    delete self._dialog;
                    self._dialog=null;
                },function(){
//                    alert('a_init');
                    $('#user-input').trigger('focus');
                });
    },
    _generateTableFooter:function(){
        let rVal=this.element.children('tfoot');
        let tr;
        if (!rVal.length){
            rVal=$('<tfoot>').appendTo(this.element);
            tr=$('<tr>').appendTo(rVal);
        }else{
            tr=rVal.children('tr:first-child');
            tr.empty();
        }
        let self=this;
        if ($.type(this._hidden2)==='object'){
            $.each(this.element.children('tbody').children('tr:first-child').children(),function(ind,el){
                let td=$('<td>').appendTo(tr);
                console.log($(el).attr('data-colkey'),$.inArray($(el).attr('data-colkey'),Object.keys(self._hidden2)));
                if ($.inArray($(el).attr('data-colkey'),Object.keys(self._hidden2))>-1){
                    td.html(self._hidden2[$(el).attr('data-colkey')]);
                }
            });
        }
        return rVal;
    },

};

(function( $ ) {
    $.widget( "custom.bugalterPostZakazController", $.custom.materialToOrder,$.extend({},bugalter_post_zakaz_controller));
}( jQuery ) );
