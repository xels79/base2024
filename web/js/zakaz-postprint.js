/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var zakaz_postprint={
    _postprint_rows:function(tab){
        var prod_info=$('<input>').addClass('dialog-form-control').width(220).attr({disabled:true, id:'zakaz_pprint_prod_info',readonly:'readonly'}).val('');
        var copies_info=$('<input>').addClass('dialog-form-control').width(100).attr({disabled:true, id:'zakaz_pprint_copies_info',readonly:'readonly'}).val($('#Zakaz-number_of_copies').val());
        var copies_info2=$('<input>').addClass('dialog-form-control').width(100).attr({disabled:true, id:'zakaz_pprint_copies_info1',readonly:'readonly'}).val($('#Zakaz-number_of_copies1').val());
        tab.append($('<div>')
                .append($('<div>'))
                .append($('<div>')
                    .css('width','80%')
                    .append($('<div>')
                        .addClass('dialog-control-group-inline')
                        .append($('<label>').text('Продукция:'))
                        .append(prod_info))
                    .append($('<div>')
                        .addClass('dialog-control-group-inline')
                        .append($('<label>').text('Тираж:'))
                        .append(copies_info)
                        .append($('<span>').addClass('add-on').text('шт.'))
                        .append(copies_info2)
                        .append($('<span>').addClass('add-on').text('шт.')))
                        
                )
                .append($('<div>'))
                //.append($('<div>').css('width','50'))
                );

        console.log('zakaz-postprint');
        tab.append($('<div>').addClass('dialog-row params')
            .append(this._zakaz_params_post_row(tab))
        );
    },
    _zakaz_params_post_create_row:function(txt,k,ul){
        var self=this;
        var val=this.options.form.fields.post_print&&this.options.form.fields.post_print[k]&&this.options.form.fields.post_print[k]['info']?this.options.form.fields.post_print[k]['info']:'';
        var val2=this.options.form.fields.post_print&&this.options.form.fields.post_print[k]&&this.options.form.fields.post_print[k]['date']?this.options.form.fields.post_print[k]['date']:'';
        console.log('_zakaz_params_post_create_row',val);
        return $('<tr>')
            .append($('<td>').text(this.post_p_tbl.children('tbody').children().length-1))
            .append($('<td>').text(txt))
            .append($('<td>').append($('<input>').attr({
                    type:'text',
                    tabindex:self.options.tabIndex++,
                    name:self._createFieldName('post_print')+'['+k+'][info]',
                }).val(val).enterAsTab()))
            .append($('<td>').text('Дата:'))
            .append($('<td>').append($('<input>').attr({
                    type:'text',
                    tabindex:self.options.tabIndex++,
                    name:self._createFieldName('post_print')+'['+k+'][date]',
            }).val(val2).enterAsTab().datepicker()))
            .append($('<td>').append($('<button>').attr({
                class:'btn btn-danger',
                tabindex:-1
            }).append($('<span>').addClass('glyphicon glyphicon-remove')).click(function(e){
                e.preventDefault();
                var tr=$(this).parent().parent();
                m_alert('Внимание','Удалить строку: "'+tr.children(':first-child').text()+'"',function(){
                    var pt=tr.parent();
                    self._zakaz_params_find_li_in_ul(ul,k).removeAttr('style');
                    tr.remove();
                    for (var i=2;i<pt.children().length;i++){
                        pt.children(':nth-child('+i+')').children(':first-child').text(i-1);
                    }
                });
            })));
    },
    _zakaz_params_post_row:function(tab){
        this.options.postPrintMenu=this.options.postPrintMenu?this.options.postPrintMenu:[];
        if (this.options.form.fields.post_print&&$.type(this.options.form.fields.post_print)==='string'){
            this.options.form.fields.post_print=JSON.parse(this.options.form.fields.post_print);
        }else{
            this.options.form.fields.post_print={};
        }
        if (!this.post_p_tbl){
            this.post_p_tbl=$('<table>').addClass('table table-post').append($('<tbody>'));
            let b=$('<button>').attr({
                'data-toggle':'dropdown',
                'aria-haspopup':'true',
                'aria-expanded':'false',
                class:'dropdown-toggle b-button b-plus',
                id:'zakaz-dd'+(window.ddId++), 
                tabindex:-1
            }).append($('<span>').addClass('').text('+'));
			console.log('postprint',window.ddId);
            var ul=$('<ul>').addClass('dropdown-menu'),self=this;
            $.fn.renderUlConten(ul,this.options.postPrintMenu,function(e){
                var txt=$(this).text();
                var k=parseInt($(this).attr('data-key'));
                console.log(this,k,txt);
                k=!isNaN(k)?k:$(this).parent().index();
                self._zakaz_params_post_create_row(txt,k,ul).insertBefore(self.post_p_tbl.children('tbody').children(':last-child'));
                $(this).parent().css('display','none');        
            });
            
            this.post_p_tbl
                    .append($('<tr>').append($('<th>').text('Постпечатка:').attr('colspan',6)))
                    .append($('<tr>')
                    .append($('<td>').attr('colspan',6).addClass('dropdown')
                        .append(b)
                        .append(ul).on('show.bs.dropdown',function(e){
                            if ($(this).offset().top>=tab.parent().offset().top+200){
                                $(this).addClass('dropup');
                                $(this).children('.dropdown-menu').removeAttr('style');
                            }else{
                                $(this).removeClass('dropup');
                                console.log(tab.parent().parent().innerHeight(),$(this).position().top);
                                console.log(tab.parent().parent().innerHeight()-$(this).position().top);
                                $(this).children('.dropdown-menu').css({
                                    'max-height':tab.parent().parent().innerHeight()-$(this).position().top+80
                                });
                            }
                        }))
                    );
        }
        if (Object.keys(this.options.form.fields.post_print).length) this._zakaz_params_draw_loaded_post(ul);
        return $('<div>').addClass('dialog-control-group-inline').append(this.post_p_tbl);
    },
    _zakaz_params_find_in_postprint:function(search){
        var rVal={label:'Не найден'};
        $.each(this.options.postPrintMenu,function(){
            if (search==this.id){
                rVal=this;
                return false;
            }
        });
        return rVal;
    },
    _zakaz_params_draw_loaded_post:function(ul){
        var self=this;
        console.log('_zakaz_params_draw_loaded_post',this.options.form.fields.post_print);
        $.each(this.options.form.fields.post_print,function(k,v){
            console.log(k,v);
            self._zakaz_params_post_create_row(self._zakaz_params_find_in_postprint(k).label,k,ul).insertBefore(self.post_p_tbl.children('tbody').children(':last-child'));
            self._zakaz_params_find_li_in_ul(ul,k).css('display','none');
        });
    },

};