/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var stones=stones||{};
stones.materials2=stones.materials2||{};
stones.materials2.select=stones.materials2.select||{};

stones.materials2.select.runningLine=function(options){
    this.text='Бегущая строка';
    this.input=null;
    this.xStep=2;
    this.autoRun=true;
    stones.baseComponent.call(this,options);
    if (!this.input){
        console.error('runningLine - не указано обязательное свойство input - (jQuery object)');
    }
    this.container=this.input.input.parent();
    this.width=this.container.width();
    this.height=this.container.height();
    this.element=$('<canvas>').attr({width:this.width,height:this.height}).insertBefore(this.input.input);
    this.ctx=this.element.get(0).getContext("2d");
    this.fontHeight=this.height-8;
    this.isFocus=false,this.isVisible=true,this.moveArr=[];
    this.input.input.css('background-color','transparent');
    this.ctx.font = this.fontHeight+"px Arial";
    this.strWidth=this.ctx.measureText(this.text).width;
    this.moveArr.push({str:this.text,textX:this.width-this.xStep});
    this.tInerval=0;
    if (this.autoRun) this.run();
    console.log(this,'runningLine');
};

stones.materials2.select.runningLine.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.materials2.select.runningLine.prototype, 'constructor', { 
    value: stones.materials2.select.runningLine, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

stones.materials2.select.runningLine.prototype.stop=function(){
    clearInterval(this.tInerval);
    this.tInerval=0;
    this.input.input.off('keyup');
    this.input.input.off('focusin');
    this.input.input.off('focusout');
};
stones.materials2.select.runningLine.prototype.startEvents=function(){
    let self=this;
    this.input.input.on('focusout',function(){
        if (!self.input.val().length){
            self.element.removeAttr('style');
            self.isVisible=true;
        }else{
            self.element.css('visibility','hidden');
            self.isVisible=false;
        }
        self.isFocus=false;
    }).on('focusin',function(){
        if (self.input.val().length){
            self.element.css('visibility','hidden');
            self.isVisible=false;
        }else{
            self.element.removeAttr('style');
            self.isVisible=true;
        }
        self.isFocus=true;
    }).on('keyup',function(){
         if (self.input.val().length){
             self.element.css('visibility','hidden');
            self.isVisible=false;
         }else{
            self.isVisible=true;
            self.element.removeAttr('style');
        }
    });

};

stones.materials2.select.runningLine.prototype.run=function(){
    let self=this;
    this.startEvents();
    this.tInerval=setInterval(function(){
        if (!self.isVisible) return;
        if (!self.isFocus){
            self.ctx.fillStyle = "#8888cc";
        }else{
            self.ctx.fillStyle = "#E5E5E5";
        };
        let tmpArr=[];
        // Сохраняем текущую матрицу трансформации
        self.ctx.save();
        // Используем идентичную матрицу трансформации на время очистки
        self.ctx.setTransform(1, 0, 0, 1, 0, 0);
        self.ctx.clearRect(0, 0, self.width, self.height);
        // Возобновляем матрицу трансформации
        self.ctx.restore();
        for (let i in self.moveArr){
            self.moveArr[i].textX-=self.xStep;
            if (self.strWidth+self.moveArr[i].textX>0){
                self.ctx.fillText(self.moveArr[i].str, self.moveArr[i].textX, self.fontHeight-2);
                tmpArr[tmpArr.length]={str:self.moveArr[i].str,textX:self.moveArr[i].textX};
                if (self.moveArr.length<2 && self.strWidth+self.moveArr[i].textX<self.width/2){
                    tmpArr[tmpArr.length]={str:self.moveArr[i].str,textX:self.width-self.xStep};
                }
            }
        }
        self.moveArr=tmpArr;
    },25);
};
