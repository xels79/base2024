
(function( $ ) {
    $.widget( "custom.blockFormat", $.Widget,{
        _w:0,_h:0,
        _mainEl:null,
        options:{
            wrapClass:'bfTable',
            maxRow:6,
            maxCol:8,
            row:0,
            col:0,
            w:1,
            h:1,
            bW:300,
            bH:200,
            afterUpdate:null,
            CText:'C',
            DText:'D'
        },
        _create: function() {
            this._super();
            this.element.addClass(this.options.wrapClass);
            this._mainEl=$('<div>').addClass('sub');
            this.element.append(this._mainEl);
            if (!this.options.bW) this.options.bW=this.element.width()-10;
            if (!this.options.bH) this.options.bH=this.element.height()-10;
            if (!this.options.row) this.options.row=1;
            if (!this.options.col) this.options.col=1;
            if (this.options.row>this.options.maxRow) this.options.maxRow=this.options.row;
            if (this.options.col>this.options.maxCol) this.options.maxCol=this.options.col;
            this._start();
        },
        _start:function(notCallAfterUD){
            let sw,sh,tw,th;
            sw=this.options.bW/this.options.col;
            sh=this.options.bH/this.options.row;
            tw=sw/this.options.w;
            th=sh/this.options.h;
            this._mainEl.removeAttr('style');
            if (this.options.row*(this.options.h*tw)>this.options.bH){
                this._w=this.options.w*th;
                this._h=this.options.h*th;
            }else{
                this._w=this.options.w*tw;
                this._h=this.options.h*tw;
            }
            for(let r=0;r<this.options.row;r++){
                let row=$("<div>");
                for(let c=0;c<this.options.col;c++){
                    row.append(this._createCol());
                }
                this._mainEl.append(row);
            }
            this._ending();
            this._mainEl.css('width',this._mainEl.width()+(this._w/100)*25);
            let whTmp=$('.wInfo');
            whTmp.css('left',whTmp.parent().width()/2-whTmp.width()/2);
            whTmp=$('.hInfo');
            whTmp.css('top',whTmp.parent().height()/2-whTmp.height()/2);
            if (notCallAfterUD!==false && $.isFunction(this.options.afterUpdate)) this.options.afterUpdate.call(this.element);
        },
        __createRedline:function(el,fCHt,isEnd,fC2W){
            let isVert=fC2W?true:false;
            el.append($('<img>').attr(!isVert?{
                src:'pic/redpoint.png',
                width:1,
                height:fCHt-3,
            }:{
                src:'pic/redpoint.png',
                width:fC2W-3,
                height:1,
            }).css(!isVert?(!isEnd?{
                top:0,
                left:-1
            }:{
                bottom:0,
                left:-1
            }):(!isEnd?{
                bottom:-1,
                left:0
            }:{
                bottom:0,
                left:-1
            })));
            el.append($('<img>').attr(!isVert?{
                src:'pic/redpoint.png',
                width:1,
                height:fCHt-3,
            }:{
                src:'pic/redpoint.png',
                width:fC2W-3,
                height:1,
            }).css(!isVert?(!isEnd?{
                top:-1,
                right:-1
            }:{
                bottom:0,
                right:-1
            }):(!isEnd?{
                top:-1,
                left:-1
            }:{
                bottom:0,
                right:-1
            })));
        },
        __proceedRow:function(self,r,c,fC2W,fCHt,isEnd,colCnt){
            let fC=$(this).children(':first-child');
            let fC2=$(this).children(':nth-child(2)').children(':first-child');
            let fCW=fC.width();
            fCHt=fCHt?fCHt:fC.height();
            fC2W=fC2W?fC2W:fC2.width();
            isEnd=isEnd?isEnd:false;
            colCnt=colCnt?colCnt:$(this).parent().children().length;
            let tmp=$('<span>').css({
                width:fC2W,//(fCHt)*2,
                height:fCHt,
                top:0,
                left:0,
                color:'#00f3b4'
            });
            if (r===0 && c===0){
                $(this).children(':nth-child(2)').children(':last-child').css('font-size',fCHt*0.7);
                $(this).children(':nth-child(2)').children(':last-child').append($('<p>').addClass('wInfo').text(self.options.w));
                $(this).children(':nth-child(2)').children(':last-child').append($('<p>').addClass('hInfo').text(self.options.h));
            }
            fC.css('font-size',fCHt*0.7);
            if ((r===0 || isEnd) && c===0){
                tmp.text(self.options.CText);
                tmp.append($('<img>').attr({
                    src:'pic/greenpoint.png',
                    width:fC2W-3,
                    height:1,
                }).css(!isEnd?{
                    bottom:-1,
                    left:0
                }:{
                    top:-1,
                    left:0
                }));
                tmp.append($('<img>').attr({
                    src:'pic/greenpoint.png',
                    width:1,
                    height:fCHt-3,
                }).css(!isEnd?{
                    top:0,
                    right:-1
                }:{
                    bottom:0,
                    right:-1
                }));
                fC.append(tmp);
            }else if((!r ||isEnd) && c) {
                self.__createRedline(tmp,fCHt,isEnd);
                fC2.css('font-size',fCHt*0.7);
                fC2.append($('<span>').css({
                    width:fC2W,//(fCHt)*2,
                    height:fCHt,
                    top:fC2.height()/2-fCHt/2,
                    left:0,
                    color:'#a20038'
                }).text(self.options.DText));
                fC.append(tmp);
            }else if (r && !c && !isEnd){
                fC.append($('<span>').text(self.options.DText).css({
                    width:fC2W,//(fCHt)*2,
                    height:fCHt,
                    top:0,
                    left:fCW/2-fC2W/2,
                    color:'#a20038'
                }));
                self.__createRedline(tmp,fCHt,isEnd,fC2W);
                fC.append(tmp);
            }
            if (c===colCnt-1){
                let tmp=$('<span>').css({
                    width:fC2W,//(fC.height())*2,
                    height:fCHt,
                    top:0,
                    left:fCW,
                    color:'#00f3b4'
                });
                if (!r || (r===$(this).parent().parent().children().length-1 && isEnd)){
                    tmp.text(self.options.CText);
                    tmp.append($('<img>').attr({
                        src:'pic/greenpoint.png',
                        width:fC2W-3,
                        height:1,
                    }).css(!isEnd?{
                        bottom:-1,
                        right:0
                    }:{
                        top:-1,
                        right:0
                    }));
                    tmp.append($('<img>').attr({
                        src:'pic/greenpoint.png',
                        width:1,
                        height:fCHt-3,
                    }).css(!isEnd?{
                        top:0,
                        left:-1
                    }:{
                        bottom:0,
                        left:-1
                    }));
                    fC.append(tmp);
                }else if (!isEnd){
//                    self.__createRedline(tmp,fCHt,isEnd,fC2W);
                    tmp.append($('<img>').attr({
                        src:'pic/redpoint.png',
                        width:fC2W-3,
                        height:1,
                    }).css({
                        bottom:-1,
                        right:0
                    }));
                    tmp.append($('<img>').attr({
                        src:'pic/redpoint.png',
                        width:fC2W-3,
                        height:1,
                    }).css({
                        top:-1,
                        right:0
                    }));
                    fC.append(tmp);
                }                
            }
            return {
                fC:fC,
                fC2:fC2
            };
        },
        _ending:function(){
            let r=0,self=this,fC,fC2;
            this._mainEl.children().each(function(){
                let c=0;
                $(this).children().each(function(){
                    let tmp=self.__proceedRow.call(this,self,r,c);
                    fC=tmp.fC;
                    fC2=tmp.fC2;
                    c++;
                });
                r++;
            });
            if (!fC || !fC2) return;
            let lastR=$('<div>').css('height',fC.height())
            this._mainEl.append(lastR);
            for (let i=0;i<this.options.col;i++){
                let tmp=$('<div>').css('width',this._w).append('<div>');
                lastR.append(tmp);
                this.__proceedRow.call(tmp,self,r,i,fC2.width(),fC.height(),true,this.options.col);
            }

        },
        _createCol:function(){
            let rv=$("<div>").width(this._w).height(this._h);
            let t=$("<div>");
            let m=$("<div>").append($("<div>")).append($("<div>"));
            rv.append(t).append(m);
            return rv;
        },
        recalculate:function(notCallAfterUD){
            this._mainEl.empty();
            this._start(notCallAfterUD);            
        },
        setCol:function(val){
            let value=parseInt(val);
            if (isNaN(value) || value<1) value=1;
            if (value<this.options.maxCol){
                this.options.col=value;
                this.recalculate();
                return true;
            }else{
                return false;
            }
        },
        setRow:function(val){
            let value=parseInt(val);
            if (isNaN(value) || value<1) value=1;
            if (value<this.options.maxRow){
                this.options.row=value;
                this.recalculate();
                return true;
            }else{
                return false;
            }            
        },
        addCol:function(notRedraw){
            if (this.options.col<this.options.maxCol){
                this.options.col++;
                if (notRedraw!==false) 
                    this.recalculate();
                else
                    if ($.isFunction(this.options.afterUpdate)) this.options.afterUpdate.call(this.element);
            }
        },
        addRow:function(notRedraw){
            if (this.options.row<this.options.maxRow){
                this.options.row++;
                if (notRedraw!==false) 
                    this.recalculate();
                else
                    if ($.isFunction(this.options.afterUpdate)) this.options.afterUpdate.call(this.element);
            }
        },
        removeCol:function(notRedraw){
            if (this.options.col>1){
                this.options.col--;
                if (notRedraw!==false) 
                    this.recalculate();
                else
                    if ($.isFunction(this.options.afterUpdate)) this.options.afterUpdate.call(this.element);
            }
        },
        removeRow:function(notRedraw){
            if (this.options.row>1){
                this.options.row--;
                if (notRedraw!==false) 
                    this.recalculate();
                else
                    if ($.isFunction(this.options.afterUpdate)) this.options.afterUpdate.call(this.element);
            }
        },
        getRowCount:function(){
            return this.options.row;
        },
        getColCount:function(){
            return this.options.col;
        },
        getPCS:function(){
            return this.options.col*this.options.row;
        }

    });
}( jQuery ) );