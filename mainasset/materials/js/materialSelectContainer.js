/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var stones=stones||{};
stones.materials=stones.materials||{};
stones.materials.select=stones.materials.select||{};

stones.materials.select.container=function(options){
    let self=this,tInerval=0,phdPos=0;
    let placeholder=typeof(options.placeholder)==='string'||typeof(options.placeholder)==='array'?options.placeholder:
        'Кликните и начните набирать название фирмы поставщика, или название материала, размер, или любой другой параметр.';
    this.drawTimer=0;
    this.keyUpTimeOut=0;
    this.events={
        afterClose:typeof(options.afterClose)==='function'?options.afterClose:null,
        beforeClose:typeof(options.beforeClose)==='function'?options.beforeClose:null,
        onSelect:typeof(options.onSelect)==='function'?options.onSelect:null
    }
    this.options={
        loadPicUrl:typeof(options.loadPicUrl)==='string'?options.loadPicUrl:'',
        materialRequestListUrl:typeof(options.materialRequestListUrl)==='string'?options.materialRequestListUrl:'',
    }
    this.bunner=   $('<div>').addClass('mat-select-bunner');
    this.informer=$('<div>').css('width','0%');
    this.container=$('<div>').addClass('mat-select-container');
    this.header=   $('<div>').addClass('mat-select-header').appendTo(this.container);
    this.container.append($('<div>').addClass('mat-select-informer').append(this.informer));
    this.value=null;
    this.input=    $('<input>').attr({
        type:'text'
    }).appendTo(this.header);
    this.table=    $('<div>').addClass('mat-select-table').attr({tabindex:10}).appendTo(this.container);
    this.footer=   $('<div>').addClass('mat-select-footer').appendTo(this.container);
    Object.defineProperty(this, 'cacheCategoryName', { 
        value: 'stoneSelectMterial', 
        enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
        writable: false
    });
    this.container.click(function(e){e.preventDefault();});
    this.bunner.click(function(){
        if (tInerval){
            clearInterval(tInerval);
            tInerval=0;
        }
        self.clearAll();
    });
    $(window).on('keyup',function(e){
        if (e.keyCode===27){
            if (tInerval){
                clearInterval(tInerval);
                tInerval=0;
            }
            e.preventDefault();
            self.clearAll();
        }
    });
    $('body').append(this.bunner).append(this.container);
    this.container.css({
        top:$(window).scrollTop()+Math.ceil($(window).height()/2-this.container.height()/2)+'px',
        left:Math.ceil($(window).width()/2-this.container.width()/2)+'px'
    });
    if (typeof(placeholder)==='string'){
        let smaller=8;
        let W=this.header.width(),H=this.header.height();
        let canvas=$('<canvas>').attr({width:W,height:H}).insertBefore(this.input);
        let ctx=canvas.get(0).getContext("2d");
        let fH=this.header.height()-smaller;
        let xStep=3, strWidth=0,isFocus=false,isVisible=true,moveArr=[];
        this.input.css('background-color','transparent');
        ctx.font = fH+"px Arial";
        strWidth=ctx.measureText(placeholder).width;
        moveArr.push({str:placeholder,textX:W-xStep});
        this.input.focusout(function(){
            if (!$(this).val().length){
                canvas.removeAttr('style');
                isVisible=true;
            }else{
                canvas.css('visibility','hidden');
                isVisible=false;
            }
            isFocus=false;
        }).focusin(function(){
            if ($(this).val().length){
                canvas.css('visibility','hidden');
                isVisible=false;
            }else{
                canvas.removeAttr('style');
                isVisible=true;
            }
            isFocus=true;
        }).keyup(function(){
             if ($(this).val().length){
                 canvas.css('visibility','hidden');
                isVisible=false;
             }else{
                isVisible=true;
                canvas.removeAttr('style');
            }
        });
        tInerval=setInterval(function(){
            if (!isVisible) return;
            if (!isFocus){
                ctx.fillStyle = "#8888cc";
            }else{
                ctx.fillStyle = "#E5E5E5";
            };
            let tmpArr=[];
            // Сохраняем текущую матрицу трансформации
            ctx.save();
            // Используем идентичную матрицу трансформации на время очистки
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.clearRect(0, 0, W, H);
            // Возобновляем матрицу трансформации
            ctx.restore();
            for (let i in moveArr){
                moveArr[i].textX-=xStep;
                if (strWidth+moveArr[i].textX>0){
                    ctx.fillText(moveArr[i].str, moveArr[i].textX, fH-smaller/4);
                    tmpArr[tmpArr.length]={str:moveArr[i].str,textX:moveArr[i].textX};
                    if (moveArr.length<2 && strWidth+moveArr[i].textX<W/2){
                        tmpArr[tmpArr.length]={str:moveArr[i].str,textX:W-xStep};
                    }
                }
            }
            moveArr=tmpArr;
        },50);
    }else{
        this.input.attr('placeholder',placeholder[phdPos++]);
        tInerval=setInterval(function(){
            if (phdPos>=placeholder.length) phdPos=0;
            self.input.attr('placeholder',placeholder[phdPos++]);
        },3000);
    }
};

stones.materials.select.container.prototype.clearAll=function(){
    $(window).off('keyup');
    this.container.remove();this.bunner.remove();
    if (this.drawTimer){
        clearInterval(this.drawTimer);
        this.drawTimer=0;
    }
    if (this.keyUpTimeOut){
        clearTimeout(this.keyUpTimeOut);
    }
};
