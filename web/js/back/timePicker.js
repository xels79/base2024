/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var idCode=0;
(function( $ ) {
    $.widget( "custom.timePicker", $.Widget,{
        inputId:'',
        selectId:'',
        panel:null,
        H_panel:null,
        M_panel:null,
        oldH:0,
        oldM:0,
        timerH:0,
        timerM:0,
        stepH:0,
        stepM:0,
        options:{
            id:'',
            hour:false,
            minutes:false,
            defaultClass:'time-picker',
            step:1
        },
        _create:function(){
            this._super();
            if (this.element.attr('id')){
                this.inputId=this.element.attr('id');
            }else{
                this.element.attr('id','timePickerInput'+idCode);
            }
            if (this.options.id)
                this.selectId=this.options.id;
            else{
                this.selectId='timePickerSelect'+idCode;
                this.options.id='timePickerSelect'+idCode++;
            }
            this.element.val(this._creatValue(this._parseString(this.element.val())));
            this.element.focusout({self:this},this._fOut);
            this.element.keydown({self:this},this._kDown);
            this.element.mouseup({self:this},this._kMousUp);
            this.element.focus({self:this},this._kFocus);
        },
        _creatValue:function(v){
            var rVal='';
            if (v.hour<10){
                rVal+='0'+v.hour;
            }else{
                rVal+=v.hour;
            }
            rVal+=':';
            if (v.minutes<10){
                rVal+='0'+v.minutes;
            }else{
                rVal+=v.minutes;
            }
            return rVal;
        },
        _parseString:function(str){
            str=str?str:'00:00';
            var vH=0, vM=0, sepPos=str.indexOf(':');
            if (sepPos>-1){
                vH=parseInt(str.substr(0,sepPos));
                vH=!isNaN(vH)?(vH<24?vH:23):0;
                vM=parseInt(str.substr(sepPos+1));
                vM=!isNaN(vM)?(vM<60?vM:59):0;
                
            }else{
                vH=parseInt(str);
                vH=!isNaN(vH)?(vH<24?vH:23):0;
            }
            return {hour:vH,minutes:vM};
        },
        _fOut:function(e){
            var self=e.data.self;
            console.log(e);
            if ($(e.relatedTarget).attr('id')!==self.selectId){
                self._removePanel();
            }
        },
        _kDown:function(e){
            var self=e.data.self;
            var k=e.key,cod=e.keyCode,val=$(this).val(),el=$(this).get(0);
            var cursorPos=val.slice(0, el.selectionEnd).length;
            if (k!='Backspace'&&k!='Tab'&&k!='Enter'&&k!='ArrowLeft'&&k!='ArrowRight'&&k!='Home'&&k!='End'&&k!='ArrowUp'&&k!='ArrowDown'){
                if (cod>47 && cod<58){
                    if (cursorPos==0){
                        el.setSelectionRange(1,1);
                        cursorPos=1;
                    }
                    if (cursorPos>5){
                        el.setSelectionRange(5,5);
                        cursorPos=5;
                    }
                    if (cursorPos==3){
                        el.setSelectionRange(4,4);
                        cursorPos=4;
                    }
                   if (cursorPos<=5){
                        console.log(val.substr(0,cursorPos-1),k,val.substr(cursorPos));
                        var valO=self._parseString(val.substr(0,cursorPos-1)+k+val.substr(cursorPos));
                        $(this).val(self._creatValue(valO));
                        if (cursorPos<5) cursorPos++;
                        if (cursorPos==3) cursorPos++;
                        el.setSelectionRange(cursorPos-1,cursorPos);
                        self._panelHTo(valO.hour);
                        self._panelMTo(valO.minutes);
                    }
                }
                e.preventDefault();
            }else if (k=='ArrowRight'){
                if (cursorPos<5){
                    if (++cursorPos==3) cursorPos++;
                    el.setSelectionRange(cursorPos-1,cursorPos);
                }
                e.preventDefault();
            }else if (k=='Backspace' || k=='ArrowLeft'){
                if (cursorPos>1){
                    if (--cursorPos==3) cursorPos--;
                    el.setSelectionRange(cursorPos-1,cursorPos);
                }
                e.preventDefault();
            }else if (k=='ArrowUp' || k=='ArrowDown'){
                var incr=k=='ArrowUp'?1:-1;
                var tmp=self._parseString(val);
                if (cursorPos>0&&cursorPos<3){
                    tmp.hour+=incr;
                    if (tmp.hour>23) tmp.hour=0;
                    if (tmp.hour<0) tmp.hour=23;
                    self._panelHTo(tmp.hour);
                }else if (cursorPos>3&&cursorPos<6){
                    tmp.minutes+=incr;
                    if (tmp.minutes>59) tmp.minutes=0;
                    if (tmp.minutes<0) tmp.minutes=59;
                    self._panelMTo(tmp.minutes);
                }
                if (cursorPos!=3){
                    $(this).val(self._creatValue(tmp));
                    el.setSelectionRange(cursorPos-1,cursorPos);
                }
                e.preventDefault();
            }
        },
        _kMousUp:function(e){
            var val=$(this).val();
            var el=$(this).get(0);
            var cursorPos=val.slice(0, el.selectionEnd).length;
            if (cursorPos==0 || cursorPos==3) cursorPos++;
            console.log(val.slice(0, el.selectionStart).length);
            el.setSelectionRange(cursorPos-1,cursorPos);
            
        },
        _kFocus:function(e){
            var self=e.data.self;
            self._showSelectPanel();
            self._fitPanel();
        },
        _removePanel:function(){
//            return;
            if (this.timerH) clearInterval(this.timerH);
            if (this.timerM) clearInterval(this.timerM);
            this.panel.remove();
            delete (this.panel);
            this.panel=null;
            this.timerM=0;
            this.timerH=0;
            this.oldH=0;
            this.oldM=0;
        },
        _fitPanel:function(){
            var tmp=this._parseString(this.element.val());
            this._panelHTo(tmp.hour);
            this._panelMTo(tmp.minutes);
        },
        _panelTo:function(val,key){
            var self=this;
            
            if (val>this['old'+key]){
                this['step'+key]=-4;
            }else if (val<this['old'+key]){
                this['step'+key]=4;
            }else{
                this['step'+key]=0;
            }
            console.log(val,this['old'+key],this['step'+key]);
            if (this['step'+key]){
                if (this['timer'+key]){
                    clearInterval(this['timer'+key]);
                    this['timer'+key]=0;
                }
                this['timer'+key]=setInterval(function(){
                    var p=self[key+'_panel'].children(':first-child');
                    if (!p.length){
                        clearInterval(self['timer'+key]);
                        self['timer'+key]=0;
                        return;
                    }
                    var top=p.position().top;
                    
                    if (Math.abs(Math.abs(top)-val*20) <120 && Math.abs(self['step'+key])===4) 
                        self['step'+key]=self['step'+key]/2;
                    if (Math.abs(Math.abs(top)-val*20) <50 && Math.abs(self['step'+key])===2) 
                        self['step'+key]=self['step'+key]/2;
                    console.log('step',top,-1*val*20);
                    if ((top>-1*val*20 && self['step'+key]<0)||(top<-1*val*20 && self['step'+key]>0)){
                        p.css({
                            top:top+self['step'+key]
                        });
                    }else{
                        clearInterval(self['timer'+key]);
                        self['timer'+key]=0;
                        self['old'+key]=val;
                        var tmpV=self._parseString(self.element.val());
                        if (key==='H'){
                            tmpV.hour=val;
                        }else{
                            tmpV.minutes=val;
                        }
                        self.element.val(self._creatValue(tmpV));
                    }
                    var topP=p.parent().offset().top+p.parent().innerHeight()/2-20;
                    p.children('[data-key]').each(function(){
                        var el=$(this),topE=el.offset().top;
//                        console.log(topP,topE);
                        if (topE>topP-10&&topE<topP+10){
//                            el.css('background-color','#A0A0A0;');
                            el.addClass('step3');
                            el.removeClass('step1 step2')
                        }else if (topE>topP-30&&topE<topP+30){
//                            el.css('background-color','#D0D0D0;');
                            el.addClass('step2');
                            el.removeClass('step1 step3')
                        }else if (topE>topP-60&&topE<topP+60){
//                            el.css('background-color','#D0D0D0;');
                            el.addClass('step1');
                            el.removeClass('step2 step3');
                        }else{
                            el.removeAttr('class');
                        }
                    });
                },10);
            }
        },
        _panelHTo:function(val){
            this._panelTo(val,'H');
        },
        _panelMTo:function(val){
            this._panelTo(val,'M');
        },
        _drawPanelContent:function(cnt,key){
            var tmp0=$('<div>'),self=this;
            for (var i=0;i<cnt+5;i++){
                var tmp=$('<div>');
                if (i>3){
                    var v=i-4;
                    tmp.attr('data-key',v)
                    if (v==0){
                        tmp.append($('<div>')).append($('<div>').text(v<10?('0'+v):v)).append($('<div>'));
                    }else{
                        tmp.append($('<div>').text(v<10?('0'+v):v)).append($('<div>'));
                    }
                    tmp.click({val:v, key:key},function(e){
                        self._panelTo(e.data.val, e.data.key);
                    });
                }
                tmp0.append(tmp);
            }
            this[key+'_panel'].append(tmp0);            
        },
        _mWell:function(e){
            var delta = e.originalEvent.wheelDelta/120;
            console.log(delta);
            e.preventDefault();
            if (delta>0){
                e.data.self._panelTo(e.data.self['old'+e.data.key]+1,e.data.key);
            }else if (delta<0){
                e.data.self._panelTo(e.data.self['old'+e.data.key]-1,e.data.key);
            }
        },
        _showSelectPanel:function(){
            var elPos=this.element.offset(),self=this;
            var wellHT=-1,wellMT=-1;
            this.panel=$('#'+this.selectId);
            if (!this.panel.length){
                this.H_panel=$('<div>').bind('mousewheel',function(e){
                    var delta = e.originalEvent.wheelDelta/120;
                    if (wellHT===-1) wellHT=self.oldH;
                    if (delta>0){
                        if (wellHT+1<24) self._panelHTo(++wellHT);
                    }else if (delta<0){
                        if (wellHT-1>=0) self._panelHTo(--wellHT);
                    }
                    e.preventDefault();
                });
                this.M_panel=$('<div>').bind('mousewheel',function(e){
                    var delta = e.originalEvent.wheelDelta/120;
                    if (wellMT===-1) wellMT=self.oldM;
                    if (delta>0){
                        if (wellMT+1<60) self._panelMTo(++wellMT);
                    }else if (delta<0){
                        if (wellMT-1>=0) self._panelMTo(--wellMT);
                    }
                    e.preventDefault();
                });
                this.panel=$('<div>').addClass('time-picker').offset({
                    top:elPos.top+this.element.outerHeight(),
                    left:elPos.left
                }).attr({
                    id:this.selectId,
                    tabindex:"0"
                }).append(this.H_panel).append($('<div>').append($('<div>')).append($('<div>')).append($('<div>'))).append(this.M_panel).appendTo('body');
                this.panel.focusout(function(e){
                    if ($(e.relatedTarget).attr('id')!==self.inputId){
                        self._removePanel();
                    }
                });
                self._drawPanelContent(23,'H');
                self._drawPanelContent(59,'M');
//                this.H_panel.children(':first-child').children('[data-key]').click({self:this},function(e){
//                    e.data.self._panelHTo(parseInt($(this).attr('data-key')))
//                });
//                this.M_panel.children(':first-child').children('[data-key]').click({self:this},function(e){
//                    e.data.self._panelMTo(parseInt($(this).attr('data-key')))
//                });
                
            }
            console.log(elPos);
        },
    });
}( jQuery ) );