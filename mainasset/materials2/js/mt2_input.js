/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var stones=stones||{};
stones.materials2=stones.materials2||{};
stones.materials2.select=stones.materials2.select||{};

/*
 * Класс Text
 * 
 * @param {object} options
 * @returns {stones.materials2.select.text}
 */
stones.materials2.select.text=function(options){
    let _left=$('<div>').addClass('mat2-input-content mat2-input-left-part');
    let _right=$('<div>').addClass('mat2-input-content mat2-input-right-part');
    stones.baseComponent.call(this,options);
    Object.defineProperty(this, 'left', { 
        get:function(){
            return _left;
        }
    });
    Object.defineProperty(this, 'right', { 
        get:function(){
            return _right;
        }
    });

}
stones.materials2.select.text.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.materials2.select.text.prototype, 'constructor', { 
    value: stones.materials2.select.text, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

Object.defineProperty(stones.materials2.select.text.prototype, 'value', { 
    get:function(){
        return this.left.text()+this.right.text();
    },
    set:function(value){
        this.right.text('');
        this.left.text(value);
    }
});


stones.materials2.select.text.prototype.addTextToLeft=function(txt){
    this.left.text(this.left.text()+txt);
};
stones.materials2.select.text.prototype.addTextToRight=function(txt){
    this.right.text(txt+this.right.text());
};
stones.materials2.select.text.prototype.removeLastSign=function(){
    let val=this.left.text();
    if (val.length)
        this.left.text(val.substr(0,val.length-1));
};
stones.materials2.select.text.prototype.scrollRight=function(){
    let val=this.left.text();
    if (val.length){
        let sign=val.substr(val.length-1);
        this.addTextToRight(sign);
        this.left.text(val.substr(0,val.length-1));
    }
};
stones.materials2.select.text.prototype.scrollLeft=function(){
    let val=this.right.text();
    if (val.length){
        let sign=val.substr(0,1);
        this.addTextToLeft(sign);
        this.right.text(val.substr(1));
    }
};

/*
 * 
 * Класс Input
 * 
 */
stones.materials2.select.input=function(options){
    this.input=null;
    this.cursor=null;
    stones.baseComponent.call(this,options);
    if (!this.input){
        console.error('select.input - не передан обязательный параметр input');
    }
    if (!this.cursor){
        console.error('select.input - не передан обязательный параметр cursor');
    }
    this.initCursor();
    this.input.keydown({self:this},this.keyDown);
    this.input.focusin({self:this},this.getFocus);
}
stones.materials2.select.input.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.materials2.select.input.prototype, 'constructor', { 
    value: stones.materials2.select.input, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

stones.materials2.select.input.prototype.getFocus=function(e){
    let self=e.data.self;
    self.cursor.isOn=true;
}

stones.materials2.select.input.prototype.keyDown=function(e){
    let self=e.data.self;
    if (e.key==='ArrowLeft'){
        self.text.scrollRight();
    }else if (e.key==='ArrowRight'){
        self.text.scrollLeft();
    }else if(e.key==='Backspace'){
        self.text.removeLastSign();
    }else if (/^.$/g.test(e.key)){
        self.text.addTextToLeft(e.key);
    }else{
        console.log(e.key);
    }
}

stones.materials2.select.input.prototype.cursorMove=function(){
    
}


stones.materials2.select.input.prototype.initCursor=function(){
    this.text=new stones.materials2.select.text();
    this.input.append(this.text.left);
    this.input.append(this.cursor.element);
    this.input.append(this.text.right);
    this.cursor.blinkTimer=setInterval(()=>{
        if (this.cursor.isShown){
            this.cursor.isShown=false;
            this.cursor.element.hide();            
        }else if (this.cursor.isOn){
            this.cursor.isShown=true;
            this.cursor.element.show();
        }
        
    },500);
}


stones.materials2.select.input.prototype.val=function(value){
    if (typeof(value)==='string'){
        this.text.value=value;
        return value;
    }else{
        return this.text.value;
    }
}