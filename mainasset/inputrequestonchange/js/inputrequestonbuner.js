/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom._inputROCBunner", $.Widget,$.extend({
        timer:0,
        buner:null,
        isBunerVisible:false,
        showTime:-1,
        _clearInterval:function(){
            if (this.timer){
                clearTimeout(this.timer);
                this.timer=0;
            }
            if (this.isBunerVisible) this.buner.hide();
        },
        _sendBuner:function(){
            this._clearInterval();
            if ($.custom.inputROCOptions.yellow){
                this.buner.attr('src',$.custom.inputROCOptions.yellow).show();
                this.isBunerVisible=true;
                this.timer=setTimeout(()=>{this._bunersTimer();},200);
            }
        },
        _okBuner:function(){
            this._clearInterval();
            if ($.custom.inputROCOptions.green){
                this.buner.attr('src',$.custom.inputROCOptions.green).show();
                this.isBunerVisible=true;
                this.showTime=2;
                this.timer=setTimeout(()=>{this._bunersTimer();},200);
            }            
        },
        _errorBuner:function(){
            this._clearInterval();
            if ($.custom.inputROCOptions.red){
                this.buner.attr('src',$.custom.inputROCOptions.red).show();
                this.isBunerVisible=true;
            }            
        },
        _removeBuner:function(){
            this._clearInterval();
            this.buner.hide();
            this.isBunerVisible=false;
        },
        _bunersTimer:function(){
            if (this.showTime===-1){
                if (this.isBunerVisible){
                    this.buner.hide();
                    this.isBunerVisible=false;
                    this.timer=setTimeout(()=>{this._bunersTimer();},100);
                }else{
                    this.isBunerVisible=true;
                    this.buner.show();
                    this.timer=setTimeout(()=>{this._bunersTimer();},200);
                }
            }else{
                this.showTime--;
                if (this.showTime>0){
                    this.timer=setTimeout(()=>{this._bunersTimer();},200);
                }else{
                    this.showTime=-1;
                    this.buner.hide();
                    this.isBunerVisible=false;
                }
            }
        }
    }));
}( jQuery ) );

