//
//
//

var Options=[];

$.fn.activefoneValidate=function(current,event){
    
        var i=0;
        var err=false;
        $.each(Options[current].valueList,function(ind,val){
            err=$.fn.activefoneCheckAndShowError(current,$('#'+Options[current].widgetID+'_'+i).children('input:first'),$('#'+Options[current].widgetID+'_'+i));
            if (err){
                $('#'+Options[current].widgetID+'_'+i).children('input:first').focus();
                return false;
            }
            i++;
        });
        if (!err){
            console.log(Options[current].valueList);
            console.log(Options[current]);
            console.log( $('#'+Options[current].fieldID).val());
            $('#'+Options[current].fieldID).val($.fn.jsonToString(Options[current].valueList));
            //if (event) event.preventDefault();
            return true;
        }else{
            if (event) event.preventDefault();
            return false;
        }
};
$.fn.activefoneSelNewCheck=function(current,el,toTogle){
    if (!$.fn.activefoneCheckAndShowError(current,el,$(el).parent().parent(),true)){
        if (toTogle){
            $(toTogle).attr('disabled',false);
            $(el).parent().parent().children('span:last').children().attr('disabled',false);
        }
        Options[current].valueList[$(el).val()]='';
        
        $(el).parent().parent().children('input:last').css('display','block');
        $(el).parent().parent().children('input:last').focus();
        if ($(el).val().length>11){
            $(el).parent().attr('title',$(el).val());
            $(el).parent().text($(el).val().substr(0,8)+'...');
        }else{
            $(el).parent().text($(el).val());
        }                            
    };
};
$.fn.activefoneSelNew=function(current,toTogle){
    var rVal=$('<div style="width:100%;">').addClass('dropdown');

    rVal.append($('<a href="#" data-toggle="dropdown" class="dropdown-toggle">Выбор<b class="caret"></b></a>'));

    var ul=$('<ul>').addClass('dropdown-menu');
    $.each(Options[current]['foneNames'],function(ind,val){
        if (val) ul.append($('<li>').html('<a href="'+ind+'">'+val+'</a>'));
    });
    ul.append($('<li>').html('<a href="-1">Добавить</a>'));
    rVal.append(ul);
    ul.children('li').click(function(e){
        e.preventDefault();
        var ind=$(this).children('a:first').attr("href");
        if (ind>=0){
            $(this).parent().parent().parent().parent().children('input:last').css('display','block');
            $(this).parent().parent().parent().parent().children('input:last').focus();
            //console.log($(this).parent().parent().parent());
            if (Options[current]['foneNames'][ind].length>11){
                $(this).parent().parent().parent().attr('title',Options[current]['foneNames'][ind]);
                $(this).parent().parent().parent().text(Options[current]['foneNames'][ind].substr(0,8)+'...');
            }else{
                $(this).parent().parent().parent().text(Options[current]['foneNames'][ind]);
            }


            Options[current].valueList[Options[current]['foneNames'][ind]]='';
            console.log(Options[current].valueList);
            delete(Options[current]['foneNames'][ind]);
            Options[current]['foneNames']=$.fn.activefonrReCalculationArray(Options[current].foneNames);
        }else{
            var inp=$('<input type="text">');
            inp.attr('id',Options[current]['widgetID']+'_addNewFone');
            inp.focusout(function(){
                        $.fn.activefoneSelNewCheck(current,this,toTogle);
                    });
            $(this).parent().parent().parent().append(inp);
            $(this).parent().parent().parent().children().remove('div');
            inp.parent().parent().children('span:last').children().attr('disabled',true);
            if (toTogle){
                $(toTogle).attr('disabled',true);
            }
            inp.focus();
        }
    });
    return rVal;
};
$.fn.activefoneCheckAndShowError=function(current,obj,parObj,isText){
    if ($(obj).val()===''){
        parObj.addClass('has-error');
        $('#'+parObj.attr('id')+'_error').text(Options[current]['errorMess']);
        $('#'+parObj.attr('id')+'_error').css('display','block');
        $(obj).focus();
        return true;
    }else{
        var tmp=$(obj).val();
        if ($.fn.checkIsInteger(tmp)&&!isText){
            parObj.addClass('has-error');
            $('#'+parObj.attr('id')+'_error').text(Options[current]['errorMess']);
            $('#'+parObj.attr('id')+'_error').css('display','block');                    
            $(obj).focus();
            return true;
        }else{
            parObj.removeClass('has-error');
            parObj.addClass('has-success')
            $('#'+parObj.attr('id')+'_error').css('display','none');
            return false;
        }
    }
};
$.fn.activefonrReCalculationArray=function(val){
    var tmp=[];
    $.each(val,function (ind,el){
        if (el) tmp.push(el);
    });
    //console.log(tmp);
    return tmp;
};
$.fn.activefoneReInit=function(current){
    console.log(Options[current].valueList);
    $('['+Options[current].widgetID+'_role="line"]').each(function(index,el){
        //console.log(index);
        $(el).children('input').keypress(function(event){
            if (event.which===13){
                if (!$.fn.activefoneCheckAndShowError(current,this,$(this).parent())){
                    var ind=$(this).parent().children('span:first').attr('title');
                    if (!ind) ind=$(this).parent().children('span:first').text();
                    Options[current].valueList[ind]=$(this).val();
                }else{
                    event.preventDefault();
                    return false;
                }
            }
        });
        $(el).children('input').unbind('focusout');
        $(el).children('input').focusout(function(){
            if (!$.fn.activefoneCheckAndShowError(current,this,$(this).parent())){
                var ind=$(this).parent().children('span:first').attr('title');
                if (!ind) ind=$(this).parent().children('span:first').text();
                Options[current].valueList[ind]=$(this).val();
                //Options[current].valueList=$.fn.activefonrReCalculationArray(Options[current].valueList);
                console.log(Options[current].valueList);
            };
        });
    });
     $('['+Options[current].widgetID+'_toremove]').each(function(index,el){
         $(el).unbind('click');
         $(el).click(function(){
            Options[current].foneNames=$.fn.activefonrReCalculationArray(Options[current].foneNames);
            var ind=$('#'+Options[current].widgetID+'_'+$(el).attr(Options[current].widgetID+'_toremove')).children('span:first').attr('title');
            if (!ind) ind=$('#'+Options[current].widgetID+'_'+$(el).attr(Options[current].widgetID+'_toremove')).children('span:first').text();
            //console.log(ind);
            Options[current].foneNames.push(ind);
            delete (Options[current].valueList[ind]);
            $('#'+Options[current].widgetID+'_'+$(el).attr(Options[current].widgetID+'_toremove')).remove();
            $('#'+Options[current].widgetID+'_'+$(el).attr(Options[current].widgetID+'_toremove')+'_error').remove();
         });
     });
    $('#'+Options[current]['widgetID']+'_add').unbind('click');
    $('#'+Options[current]['widgetID']+'_add').click(function(e){
        e.preventDefault();
        var tmp=$('#'+Options[current]['widgetID']+'_add_with_label').clone();

        tmp.attr('id',Options[current]['widgetID']+'_'+Options[current]['elCount']);
        tmp.attr(Options[current]['widgetID']+'_role','line');
        tmp.css('display','');
        tmp.children('span:first').append($.fn.activefoneSelNew(current,'#'+Options[current]['widgetID']+'_add'));
        tmp.children('input').css('display','none');
        tmp.children('span:last').children().attr(Options[current].widgetID+'_toremove',Options[current]['elCount']);
        $(this).before($(tmp));
        var tmp=$('#'+Options[current]['widgetID']+'_error').clone();
        tmp.attr('id',Options[current]['widgetID']+'_'+Options[current]['elCount']+'_error');
        $(this).before($(tmp));
        Options[current]['elCount']++;
        $.fn.activefoneReInit(current);
    });
    
    $('form').submit(function(event){
        $.fn.activefoneValidate(current,event);
    });
};

$.fn.activefoneInit=function(opt){
    var current=Options.length;
    if ($.isPlainObject(opt))
        Options[current]=opt;
    else
        Options[current]=jQuery.parseJSON(opt);
    if (!$.isPlainObject(Options[current].valueList)) Options[current].valueList={};
    //console.log(Options[current]);
    $.fn.activefoneReInit(current);
    return current;
};
