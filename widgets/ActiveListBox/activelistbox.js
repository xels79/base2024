/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$.fn.itUpdateListBox=function(options,change,remove){
    //$.fn.baseAjaxUpdate
    if (options['updateThisAction']){
        opt={
            url:options['updateThisAction'],
            type: 'POST',
            data:{
                ajax:true,
                updateId:options.wId
            }
        };
        console.log(opt);
        $.fn.baseAjaxUpdate(options.wId,opt,function(){
            $.fn.activeListBoxInit(options,change,remove);
        });
    }
};
$.fn.activeListBoxChange=function(self,options,change,remove,submText){
    exec=false;
    //console.log(options);
    if (change){
        if ($.isFunction(change)){
            change(self,function(){
                $.fn.itUpdateListBox(options,change,remove);
            },submText);
            exec=true;
        }
    }
    if (!exec){
        var back={
          url:encodeURIComponent(location.href),
          txt:encodeURIComponent(options.pageName)
        };
        var test='';
        $.each(back,function(ind,el){
            test+= '&Back['+ind+']='+el;
        });
        console.log($(self).attr('url')+test); 
        location=$(self).attr('url')+test;
    }
};
$.fn.activeListBoxInit=function(opt,change,remove){
    if ($.isPlainObject(opt)||$.isArray(opt))
        var options=opt;
    else
        var options=jQuery.parseJSON(opt);
    if (options['addKeyId']&&change){
        $('#'+options['addKeyId']).unbind('click');
        $('#'+options['addKeyId']).click(function(e){
            e.preventDefault();
            $.fn.activeListBoxChange(this,options,change,remove,"Добавить");
            return false;
        });
    }
    $.each($('[role=remove]'),function(ind,el){
        $(el).unbind('click');
        $(el).click(function(e){
            console.log('remove');
            e.preventDefault();
            if (!$.isFunction(options['removeFunction'])){
                removeUrl=$(el).attr('url');
                if (removeUrl){
                    if (confirm(options['removeAsk'])){
                        $.post(removeUrl).done(function(data){
                            console.log(data);
                            $.fn.itUpdateListBox(options,change,remove);
                        });
                    }
                }else{
                    if ($.isFunction(remove)){
                        remove(this,function(){
                            $.fn.itUpdateListBox(options,change,remove);
                        });
                        exec=true;
                    }
                }
            }else{
                options['removeFunction'](this);
            }
            return false;
        });
    });
    $.each($('[role=change]'),function(ind,el){
        $(el).unbind('click');
        $(el).click(function(e){
            e.preventDefault();
            $.fn.activeListBoxChange(this,options,change,remove,"Изменить");
            return false;
        });
    });
    $.each($('[data-toggle=tooltip]'),function(ind,el){
        $(el).tooltip({
            placement:'auto',
            container: 'body',
            delay: { show: 500, hide: 100 }
        });
    });
    $.each($('[data-content]'),function(ind,el){
        $(el).popover({
            trigger:'hover',
            html:true,
            placement:'auto',
            container:'body',
            delay: { show: 1000, hide: 100 }
        });
        $(el).click(function(e){
            e.preventDefault();
        });
        $(el).parent().click(function(e){
            $(el).popover('show');
        });
        $(el).parent().mouseleave(function(e){
            $(el).popover('hide');
        });

    });
};
