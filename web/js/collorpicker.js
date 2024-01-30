/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

let color_picker={
    _dialog:null,
    _bunner:null,
    _input:null,
    _keyProcess:false,
    options:{
        
    },
    _create:function(){
        this._super;
        if (this.element[0].tagName==='INPUT'){
            this.element.click({self:this},function(e){
                e.preventDefault();
                e.data.self._open.call(e.data.self);
            });
            this.element.keydown({self:this},function(e){
                console.log('keydown',e.keyCode);
                if (e.keyCode>40){
                    e.data.self._open.call(e.data.self);
                }
            });
        }else
            this.element.click({self:this},_open);
    },
    __generateOffset:function(dH,dW){
        let elOf=this.element.offset();
        let elW=this.element.outerWidth();
        let elH=this.element.outerHeight();
        let offs={};
        //console.log('offset',this.element.offset());
        if (elOf.top+dH<$(window).height()){
            offs.top=elOf.top;
        }else{
            if (elOf.top+elH-dH>0)
                offs.top=elOf.top+elH-dH;
            else
                offs.top=0;
        }
        if (elOf.left+elW+dW<$(window).width()){
            offs.left=elOf.left+elW;
        }else{
            if (elOf.left-dW>0)
                offs.left=elOf.left-dW;
            else
                offs.left=0;
        }
        return offs;
    },
    _byte2Hex:function(n){
      let nybHexString = "0123456789ABCDEF";
      return String(nybHexString.substr((n >> 4) & 0x0F,1)) + nybHexString.substr(n & 0x0F,1);
    },
    _RGB2Color:function(r,g,b){
      return '#' + this._byte2Hex(r) + this._byte2Hex(g) + this._byte2Hex(b);
    },
    _hexToRgb:function (h){
        let r = parseInt((this._cutHex(h)).substring(0,2),16),
                g = parseInt((this._cutHex(h)).substring(2,4),16),
                b = parseInt((this._cutHex(h)).substring(4,6),16)
        return {r:r,g:g,b:b};
    },
    _cutHex:function(h)  {return (h.charAt(0)=="#") ? h.substring(1,7):h},
    __changeColor:function(sl){
        if (this._keyProcess) return;
        let chld=sl.children();
//        console.log(sl.prev());
        let h=this._RGB2Color($(chld[0]).slider('value'),$(chld[1]).slider('value'),$(chld[2]).slider('value'));
        this._input.val(h);
        sl.prev().attr('style','background-color:'+h+';');
    },
    __generateBar2:function(){
        let rVal=$('<div>').addClass('sliders'),self=this;
        let val=this._input.val();
        let opt={
            orientation: "horizontal",
            range: "min",
            max: 255,
            slide:function(e,u){self.__changeColor.call(self,rVal);},
            change:function(e,u){self.__changeColor.call(self,rVal);}
        };
        if (val.length===7&&val[0]==='#')
            val=this._hexToRgb(val);
        else
            val={r:0,g:0,b:0};
        rVal.append($('<div>').addClass('red').slider($.extend({},opt,{value:val.r})));
        rVal.append($('<div>').addClass('green').slider($.extend({},opt,{value:val.g})));
        rVal.append($('<div>').addClass('blue').slider($.extend({},opt,{value:val.b})));
        return rVal;
    },
    _generateWin:function(){
        let dH=140;
        let dW=400;
        let rVal=$('<div>').addClass('CP-dialog').css({
            'max-width':dW+'px',
            'min-width':dW+'px',
            'max-height':dH+'px',
            'min-height':dH+'px'
        }).offset(this.__generateOffset(dH,dW));
        rVal.append($('<div>').addClass('cube'));
        rVal.append(this.__generateBar2());
        if (this._input.val().length===7&&this._input.val()[0]==='#'){
            rVal.children('.cube').attr('style','background-color:'+this._input.val()+';');
        }
        return rVal;
    },
    _setNewVals:function(val){
        let chld=this._dialog.children('.sliders').children();
        if (chld.length!==3) return;
        let tmp=this._cutHex(val.toUpperCase());
        console.log(tmp);
        let rgb={
            r:$(chld[0]).slider('value'),
            g:$(chld[1]).slider('value'),
            b:$(chld[3]).slider('value')
        };
        //let h=this._RGB2Color($(chld[0]).slider('value'),$(chld[1]).slider('value'),$(chld[2]).slider('value'));
        if (tmp.length<2) return;
        rgb.r=parseInt(tmp.substring(0,2),16);
        if (tmp.length>3) rgb.g=parseInt(tmp.substring(2,4),16);
        if (tmp.length>5) rgb.b=parseInt(tmp.substring(4,6),16);
        this._keyProcess=true;
        $(chld[0]).slider('value',rgb.r);
        $(chld[1]).slider('value',rgb.g);
        $(chld[2]).slider('value',rgb.b);
        this._keyProcess=false;
        this._dialog.children('.cube').attr('style','background-color:'+this._RGB2Color(rgb.r,rgb.g,rgb.b)+';');        
    },
    _open:function(){
        if (!this._dialog&&!this._input&&!this._dialog){
            let elPos=this.element.offset();
            this._bunner=$('<div>').appendTo('body').addClass('CP-bunner');
            this._input=$('<input>').attr({type:'text'});
            this._input.css({top:elPos.top,left:elPos.left});
            this._input.width(this.element.outerWidth());
            this._input.height(this.element.outerHeight());
            console.log(this.element[0].tagName);
            this._input.val(this.element[0].tagName==='INPUT'?this.element.val():this.element.text());
            this._bunner.append(this._input);
            this.element.css('visibility','hidden');
            this._input.focus();
            this._bunner.click({self:this},this._bunnerClose);
            this._dialog=this._generateWin();
            this._bunner.append(this._dialog);
            this._input.click(function(e){
                //e.preventDefault();
                e.stopPropagation();
            });
            this._dialog.click(function(e){e.stopPropagation();});
            this._input.keydown({self:this},function(e){
                console.log(e.keyCode);
                if (e.keyCode===9||e.keyCode===13){
                    e.data.self._bunnerClose.call(this,e);
                    
                }
            });
            this._input.keypress({self:this},function(e){
                let val=$(this).val();
                if (((e.keyCode>47&&e.keyCode<58)||(e.keyCode>64&&e.keyCode<71)||(e.keyCode>96&&e.keyCode<104))&&val.length&&val.length<7)
                    e.data.self._setNewVals.call(e.data.self,val+e.key);
                else if(e.key!=='#'||(e.key==='#'&&val.length)){
                    if (!val.length){
                        if ((e.keyCode>47&&e.keyCode<58)||(e.keyCode>64&&e.keyCode<71)||(e.keyCode>96&&e.keyCode<104)){
                            if (val.length<7) $(this).val('#'+e.key);
                            e.data.self._setNewVals.call(e.data.self,$(this).val());
                        }else
                            $(this).val('#');
                    }
                    e.preventDefault();
                    e.stopPropagation();
                }
                    
            });
        }
    },
    _bunnerClose:function(e){
        //return;
        let self=e.data.self,val=self._input.val();
        console.log('close',self._input.val());
        if (val.length&&val[0]!='#') val='#'+val;
        if (self.element[0].tagName==='INPUT')
            if (val.length===7||!val.length) self.element.val(val.toUpperCase());
        else
            if (val.length===7||!val.length) self.element.text(val.toUpperCase());
        self._input.remove();
        self._dialog.remove();
        self._bunner.remove();
        delete self._input;
        delete self._bunner;
        delete self._dialog;
        self._input=null;
        self._bunner=null;
        self._dialog=null;
        self.element.css('visibility','visible');
    }
};
(function( $ ) {
    $.widget( "custom.cPicker", $.Widget,color_picker);
}( jQuery ) );