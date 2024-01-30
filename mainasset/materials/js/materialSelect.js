/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var stones=stones||{};
stones.materials=stones.materials||{};
stones.materials.select=stones.materials.select||{};

stones.materials.select.eng=function(options){
    
    stones.materials.select.container.call(this,options);
    this.bench=new Date();
    this.drawPointer=0;
    this.keys=[];
    //this.input.change({self:this},this.inputChange);
    this.inputOldVal='';
    this.input.keyup({self:this},this.inputKeyUp);
    this.input.keydown({self:this},this.inputKeyDown);
    this.selectedItem=-1;
    this.inputData={
        string:[],
        number:[],
        size:[],
        count:0,
        proceed:0
    };
    this.table.keydown({self:this},this.tableKeyControl);
    this.data={
        list:{},
        updateTime:0
    }
    this.drawTimer=setInterval(()=>{
        this.checkForeDraw();
    },0.5);
    this.checkForUpdate();
    console.log('stones.materials.select',this);
};
stones.materials.select.eng.prototype=Object.create(stones.materials.select.container.prototype);
Object.defineProperty(stones.materials.select.eng.prototype, 'constructor', { 
    value: stones.materials.select.eng, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

stones.materials.select.eng.prototype.resetSearchPointer=function(){
    this.bench=new Date();
    this.drawPointer=0;
};

stones.materials.select.eng.prototype.tableKeyControl=function(e){
    let self=e.data.self;
    if (e.key==="ArrowDown"){
        let el=$(this).children('.mat-select-active');
        e.preventDefault();
        if (!el.length){
            el=$(this).children(':first-child').trigger('click');
        }else{
            let pEl=el.next();
            if (pEl.length){
                let height=pEl.outerHeight();
                let eTop=$(this).scrollTop()+pEl.position().top;
                let pH=$(this).scrollTop()+$(this).height();
                pEl.trigger('click');
                if (eTop>pH){
                    $(this).scrollTop(eTop-height*2);
                }
                console.log(eTop,pH);
            }
        }
    }else if (e.key==="ArrowUp"){
        let el=$(this).children('.mat-select-active');
        e.preventDefault();
        if (!el.length){
            el=$(this).children(':last-child').trigger('click');
        }else{
           let pEl=el.prev();
            if (pEl.length){
                let height=pEl.outerHeight();
                let eTop=$(this).scrollTop()+pEl.position().top-height;
                let pH=$(this).scrollTop();
                pEl.trigger('click');
                if (eTop<pH){
                    $(this).scrollTop(eTop-$(this).height()+height);
                }
                console.log(eTop,pH);
            }else{
                self.input.trigger('focus');
            }
        }
    }
};

stones.materials.select.eng.prototype.createHTML=function(val,strings,isString,fromBegin){
    let rVal={html:val,hasMatch:false,pos:-1},mainReg;
    fromBegin=typeof(fromBegin)==='undefined'?true:fromBegin;
    if (/^(а|А)[0-5]{1,1}$/gm.test(val)){
        val=val.replace(/^(а|А)/,'A');
    }
    for (let i in strings){
        mainReg='^'+strings[i]+'';
        if (!fromBegin){
            mainReg+='|.*\s+('+strings[i]+')';
        }
        if (val==strings[i]){
            rVal.hasMatch=true;
            rVal.html='<span class="mat-select-highlight">'+val+'</span>';
        }else{
            let regexp=new RegExp(mainReg,'i');
            rVal.html=val.replace(regexp, function(match,p1){
                rVal.hasMatch=true;
                return '<span class="mat-select-highlight">'+(p1?p1:match)+'</span>';
            });
        }
        if (rVal.hasMatch){
            rVal.pos=i;
            break;
        }else if(isString){
            let _cmpS=NEWFUNCTION.transliterate(strings[i]);
            mainReg='^'+_cmpS+'';
            if (!fromBegin){
                mainReg+='|.*\s+('+_cmpS+')';
            }
            if (val==_cmpS){
                rVal.hasMatch=true;
                rVal.html='<span class="mat-select-highlight">'+val+'</span>';
            }else{
                let regexp=new RegExp(mainReg,'i');
                rVal.html=val.replace(regexp, function(match,p1){
                    rVal.hasMatch=true;
                    return '<span class="mat-select-highlight">'+(p1?p1:match)+'</span>';
                });
            }
        }
    }
    return rVal;
}

stones.materials.select.eng.prototype.checkForeDraw=function(){
    if (this.drawPointer<this.keys.length){
        let d=$('<div>').attr('data-key',this.drawPointer);
        let mCnt=0;
        let inputData=$.extend(true,{},this.inputData);
        with (this.data.list[this.keys[this.drawPointer++]]){
            let tmp;
            tmp=this.createHTML(mainParams.firmName,inputData.string);
            if (tmp.hasMatch){
                mCnt++;
                inputData.string.splice(tmp.pos,1);
            }
            d.append($('<span>').attr('title','Поставщик').html(tmp.html));
                tmp=this.createHTML(mainParams.materialName,inputData.string,true,false);
                if (tmp.hasMatch){
                    mCnt++;
                    inputData.string.splice(tmp.pos,1);
                }
                d.append($('<span>').attr('title','Материал').html(tmp.html));
            for (let n in depandTableNames){
                if (depandTableNames[n].type==='string'){
                        tmp=this.createHTML(dependValues[depandTableNames[n].name],inputData[depandTableNames[n].type],true,false);
                        d.append($('<span>').html(tmp.html));
                        if (tmp.hasMatch){
                            inputData[depandTableNames[n].type].splice(tmp.pos,1);
                            mCnt++;
                        }
                }else{
                    tmp=this.createHTML(dependValues[depandTableNames[n].name],inputData[depandTableNames[n].type]);
                    d.append($('<span>').html(tmp.html));
                    if (tmp.hasMatch){
                        inputData[depandTableNames[n].type].splice(tmp.pos,1);
                        mCnt++;
                    }
                }
            }
            let _money=parseFloat(money.coast);
            if (isNaN(_money)) _money=0;
            d.append($('<span>').addClass('mat-select-right').text(_money.toFixed(2)));
        }
        if (mCnt===inputData.count){// || mCnt>2){
            this.table.append(d);
            d.click({self:this},this.itClick);
        }
        if (this.drawPointer===this.keys.length){
            this.informer.css('width','100%');
            console.log('Все записи обработаны ('+this.drawPointer+') затрачено: '+((new Date() - this.bench)/1000).toFixed(2)+'c.');
        }else{
            this.informer.css('width',Math.floor(this.drawPointer/this.keys.length*100)+'%');
        }
    }
};

stones.materials.select.eng.prototype.redrawAll=function(){
    this.keys=Object.keys(this.data.list);
    this.table.empty();
    this.resetSearchPointer();
}

stones.materials.select.eng.prototype.checkForUpdate=function(callB){
    let self=this;
    if (this.options.materialRequestListUrl){
        let opt={};
        let tmpCacheData=stones.cacheRuntime.getData('request','materialSelect');
        if (tmpCacheData && tmpCacheData.updateTime){
            opt.oldUpdateTime=tmpCacheData.updateTime;
        }
        $.post(self.options.materialRequestListUrl,opt).done(function(data){
            console.log(data.status);
            if (data.status==='update'){
                self.data.list=data.answer;
                self.data.updateTime=data.updateTime;
                stones.cacheRuntime.setData('request',{
                    list:data.answer,
                    updateTime:data.updateTime
                },'materialSelect');
                self.redrawAll();
            }else if (data.status==='ok'){
                if (!self.data.updateTime || !self.data.list){
                    self.data.list=tmpCacheData.list;
                    self.data.updateTime=tmpCacheData.updateTime;
                    self.redrawAll();
                }
                stones.cacheRuntime.reFreshTime('request','materialSelect');
            }
            if (typeof(callB)==='function') callB.call(self,data.status==='update');
        });
    }else{
        console.warn('select.eng.inputChange - не указан URL!')
    }
}

stones.materials.select.eng.prototype.proceedInputValue=function(val){
    let regex1 = /[\S]+/gm;
    let m,self=this;
    this.inputData={
        string:[],
        number:[],
        size:[],
        count:0,
        proceed:0
    };
    val=val.replace(/\"(.*)\"|\'(.*)\'/gm,function(match,p1){
        self.inputData.string.push(p1);
        self.inputData.count++;
        return '';
    });
    while ((m = regex1.exec(val)) !== null) {
        // This is necessary to avoid infinite loops with zero-width matches
        if (m.index === regex1.lastIndex) {
            regex1.lastIndex++;
        }

        // The result can be accessed through the `m`-variable.
        m.forEach((match, groupIndex) => {
            if (/^[0-9]+\*{1,1}[0-9]+$|^[0-9]+(х|Х){1,1}[0-9]+$|^[0-9]+(x|X){1,1}[0-9]+$|^(a|A|а|А)[0-5]{1,1}$/gm.test(match)){
                match=match.replace(/[x|X]|[х|Х]/gm ,"*");
                if (/^(а|А)[0-5]{1,1}$/gm.test(match)){
                    match=match.replace(/^(а|А)/,'A');
                }
                this.inputData.size.push(match);
            }else if(/^[0-9]+$/gm.test(match)){
                this.inputData.number.push(match);
            }else{
                this.inputData.string.push(match);
            }
            this.inputData.count++;
        });
    }    
};

stones.materials.select.eng.prototype.itClick=function(e){
    let self=e.data.self;
    if (self.selectedItem>-1){
        $('[data-key='+self.selectedItem+']').removeClass('mat-select-active');
    }
    self.selectedItem=parseInt($(this).attr('data-key'));
    $(this).addClass('mat-select-active');
};

stones.materials.select.eng.prototype.inputKeyDown=function(e){
    let self=e.data.self;
    if (e.key==='ArrowDown'){
        e.preventDefault();
        self.table.children(':first-child').trigger('click');
        self.table.trigger('focus');
    }
};

stones.materials.select.eng.prototype.inputKeyUp=function(e){
    let self=e.data.self;
    let val=$(this).val().trim();
    if (self.keyUpTimeOut){
        clearTimeout(self.keyUpTimeOut);
        self.keyUpTimeOut=0;
    }
    if (val!=self.inputOldVal){
        self.keyUpTimeOut=setTimeout(()=>{
            self.keyUpTimeOut=0;
            self.proceedInputValue(val);
            self.inputOldVal=val;
            self.checkForUpdate(function(needUpdate){
                if (!needUpdate){
                    self.redrawAll();
                }
            });
        },250);
    }
};
