/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function( $ ) {
    $.widget( "custom.selectAllOnFocus", $.Widget,{
        options:{
            selectAllOnFocusOn:true
        },
        _create:function(){
            this._super();
            this.element.focusin({self:this},this._selectAllOnFocusFIN);
        },
        _selectAllOnFocusFIN:function(e){
            if (e.data.self.options.selectAllOnFocusOn!==false){
                $(this).select();
            }
        }
    });
}( jQuery ) );
(function( $ ) {
    $.widget( "custom.enterAsTab", $.Widget,{
        _isInit_enterAsTab:false,
        _create: function() {
            if (this._isInit_enterAsTab){
                console.warn('enterAsTab:init(Уже инициирован)',this.element);
                return;
            }
            this._super();
            this.element.keydown({self:this},this.__keyDown);
            this._isInit_enterAsTab=true;
//            console.log('enterAsTab:init');
        },
        __keyDown:function(e){
            let k=e.key;
            if (k=='Enter'||k=='Tab'){
                if ($(this).attr('tabindex')){
                    let pass=2;
                    let ind=parseInt($(this).attr('tabindex'));
                    //let ok=false;
                    ind=!isNaN(ind)?ind:0;
                    /*
                    let minInd=0,maxInd=0,nextInd=0;
                    $('[tabindex]').each(function(){
                        let thisTIndez=parseInt($(this).attr('tabindex'));
                        thisTIndez=!isNaN(thisTIndez)?thisTIndez:0;
                        if (thisTIndez>-1){
                            maxInd+=thisTIndez;
                            if (!nextInd && thisTIndez>ind) nextInd=thisTIndez;
                            if (!minInd){
                                minInd=thisTIndez;
                            }else if(minInd>thisTIndez){
                                minInd=thisTIndez;
                            }
                        }                        
                    });
                    if (nextInd){
                        e.preventDefault();
                        $('[tabindex='+nextInd+']').trigger('focus');
                    }else if (maxInd){
                        e.preventDefault();
                        $('[tabindex='+maxInd+']').trigger('focus');
                    }else if(minInd){
                        e.preventDefault();
                        $('[tabindex='+minInd+']').trigger('focus');
                    }
                    */
                    
                    do{
                        $('[tabindex]').each(function(){
                            if (!$(this).attr('disabled')&&$(this).is(':visible')){
                                let thisTIndez=parseInt($(this).attr('tabindex'));
                                thisTIndez=!isNaN(thisTIndez)?thisTIndez:0;
                                if (thisTIndez>-1){
                                    if (pass>0){
                                        if (thisTIndez>ind){
                                            $(this).trigger('focus');
                                            pass--;
                                            e.preventDefault();
                                            //ok=true;
                                            return false;
                                        }
                                    }else{
                                        if (thisTIndez<ind){
                                            $(this).trigger('focus');
                                            e.preventDefault();
                                            pass--;
                                            //ok=true;
                                            return false;
                                        }
                                    }
                                }
                            }
                        });
                        pass--;
                        ind=0;
                    }while(pass>0);
                    //console.log("enterAsTab");
                }else{
                    $(this).trigger('blur');
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
            }            
        },
        isInit:function(){
            return this._isInit_enterAsTab;
        }
    });
}( jQuery ) );


(function( $ ) {
    $.widget( "custom.onlyNumeric", $.Widget,{
        oldVal:null,
        options:{
            allowPoint:false,
            allowStar:false,
            defaultVal:'0',
        },
        _create: function() {
            this._super();
            this.element.keydown({self:this},this._keyDown);
            this.element.focusout({self:this},this._focusOut);
        },
        _focusOut:function(e){
            if (parseFloat($(this).val())==0||!$(this).val().length)
                $(this).val(e.data.self.options.defaultVal);
            else if ($(this).val().length>1){
                    if ($(this).val()[0]==='0'&&$(this).val()[1]!=='.')
                        $(this).val($(this).val().substr(1));
                    else if ($(this).val()[0]==='.'){
                        $(this).val(e.data.self.options.defaultVal+$(this).val());
                    }
            } else if ($(this).val()[0]==='.')
                $(this).val(e.data.self.options.defaultVal);
        },
        _keyDown:function(e){
            let k=e.key;
//            console.(k);
            if (k!='Shift'&&k!='Backspace'&&k!='Tab'&&k!='Enter'&&k!='ArrowLeft'&&k!='ArrowRight'&&k!='Home'&&k!='End'){
                if ((!$.isNumeric(k) && ((k!=='.'||$(this).val().indexOf('.')!==-1||!e.data.self.options.allowPoint) && (k!=='*'||$(this).val().indexOf('*')!==-1||!e.data.self.options.allowStar||!$(this).val().length))))
                    e.preventDefault();
                else{
                    if ((!$(this).val().length||parseFloat($(this).val())===0)&&k=='.')
                        $(this).val(e.data.self.options.defaultVal);
                    else if ($(this).val().length==1&&parseFloat($(this).val())===0)
                        $(this).val('');
                }
            }
        },
    });
}( jQuery ) );

(function( $ ) {
    $.widget( "custom.popoverN", $.Widget,{
        tHide:null,
        tShow:null,
        isEnabled:true,
        options:{
            content:'',
            delay:1000,
            hoverClass:'is-hovered'
        },
        _create: function() {
            this._super();
            this.element
                    .popover({
                        content:this.options.content,
                        html:true,
                        trigger:'manual',
                        //delay:{ "show": 500, "hide": 500 }
                    })
                    .mouseenter({self:this},this.__show)
                    .mouseleave({self:this},this.__hide)
                    .on('shown.bs.popover', {self:this},this.__shown_bs)
        },
        __shown_bs:function(e){
            let self=e.data.self;
            $(this).next().mouseenter(function(){
                self._resetAction();
            }).mouseleave(function(){
                self._resetAction();
                self.tHide=setTimeout(function(){
                    self.element.removeClass(self.options.hoverClass);
                    self.element.popover('hide');
                    self.tHide=null;
                },self.options.delay);                                                                    
            });

        },
        __show:function(e){
            let self=e.data.self;
            self._resetAction();
            if (!self.isEnabled) return;
            self.tShow=setTimeout(function(){
                $(self.element).popover('show');
                self.element.addClass(self.options.hoverClass);
                self.tShow=null;
            },self.options.delay);
        },
        __hide:function(e){
            let self=e.data.self;
            self._resetAction();
            self.tHide=setTimeout(function(){
                self.element.popover('hide');
                self.element.removeClass(self.options.hoverClass);
                self.tHide=null;
            },self.options.delay);
        },
        hide:function(){
            this._resetAction();
            this.element.popover('hide');
            this.element.removeClass(this.options.hoverClass);
        },
        _resetAction:function(){
            if (this.tHide){
                clearTimeout(this.tHide);
                this.tHide=null;
            }
            if (this.tShow){
                clearTimeout(this.tShow);
                this.tShow=null;
            }
        },
        enable:function(){
            this.isEnabled=true;
        },
        disable:function(){
            this.isEnabled=false;
        },
        destroy:function(){
            this.element.popover('destroy');
        }
    });
}( jQuery ) );
