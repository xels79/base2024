/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var stones=stones||{};
stones.materials2=stones.materials2||{};
stones.materials2.select=stones.materials2.select||{};

stones.materials2.select.container=function(options){
    let self=this,tInerval=0,phdPos=0;
    let placeholder=typeof(options.placeholder)==='string'||typeof(options.placeholder)==='array'?options.placeholder:
        'Кликните и начните набирать название фирмы поставщика, или название материала, размер, или любой другой параметр.';
    this.events={
        afterClose:typeof(options.afterClose)==='function'?options.afterClose:null,
        beforeClose:typeof(options.beforeClose)==='function'?options.beforeClose:null,
        onSelect:typeof(options.onSelect)==='function'?options.onSelect:null
    }
    this.options={
        materialRequestListUrl:typeof(options.materialRequestListUrl)==='string'?options.materialRequestListUrl:'',
    }
    this.bunner=   $('<div>').addClass('mat2-select-bunner');
    this.informer=$('<div>').css('width','0%');
    this.container=$('<div>').addClass('mat2-select-container');
    this.header=   $('<div>').addClass('mat2-select-header').appendTo(this.container);
    this.container.append($('<div>').addClass('mat2-select-informer').append(this.informer));
    this.value=null;
    this.runningLine=null;
    this.cursor={
        blinkTimer:0,
        element:$('<div>').addClass('mat2-input-content mat2-cursor').hide(),
        isOn:false,
        isShown:false,
        selectStart:0,
        selectEnd:0
    };
    this.input=new stones.materials2.select.input({
        input:$('<div>')
            .attr('tabindex',10)
            .addClass('mat2-select-input').appendTo(this.header),
        cursor:this.cursor
    });
//    this.input=    $('<div>')
//            .attr('tabindex',10)
//            .addClass('mat2-select-input').appendTo(this.header);
    this.container.click(function(e){e.preventDefault();});
    this.bunner.click(function(){
        if (tInerval){
            clearInterval(tInerval);
            tInerval=0;
        }
        self.clearAll();
    });
    $('body').append(this.bunner).append(this.container);
    this.container.css({
        top:$(window).scrollTop()+Math.ceil($(window).height()/2-this.container.height()/2)+'px',
        left:Math.ceil($(window).width()/2-this.container.width()/2)+'px'
    });
    if (typeof(placeholder)==='string'){
        this.runningLine=new stones.materials2.select.runningLine({
            text:placeholder,
            input:this.input
        });
    }else{
        this.input.input.attr('placeholder',placeholder[phdPos++]);
        tInerval=setInterval(function(){
            if (phdPos>=placeholder.length) phdPos=0;
            self.input.input.attr('placeholder',placeholder[phdPos++]);
        },3000);
    }

};
stones.materials2.select.container.prototype.clearAll=function(){
//    $(window).off('keyup');
    if (this.runningLine)this.runningLine.stop();
    this.container.remove();this.bunner.remove();
    if (this.cursorBlinkTimer){
        clearInterval(this.cursorBlinkTimer);
    }
//    if (this.drawTimer){
//        clearInterval(this.drawTimer);
//        this.drawTimer=0;
//    }
//    if (this.keyUpTimeOut){
//        clearTimeout(this.keyUpTimeOut);
//    }
};
