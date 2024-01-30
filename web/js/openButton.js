/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function( $ ) {
    $.widget( "custom.openButton", $.Widget,{
        _dialog:null,
        _className:null,
        _create:function(){
            let self=this;
            this._super();
            if (!this.options.close) this.options.close=function(){
                self.close();
            };
            this.element.click({self:this},function(e){
                e.preventDefault();
                if (!this._dialog){
                    e.data.self.open();
                }
            });
        },
        open:function(){
            if (this._className){
                if (!this._dialog){
                    this._dialog=new $.custom[this._className](this.options);
                        if (!this._dialog.isOpen()){
                            this._dialog.open();
                        }
//                    this._dialog.open();
                }else{
                    if (!this._dialog.isOpen()){
                        this._dialog.open();
                    }
                    if (this._dialog.isMinimized()) this._dialog.restore();
                }
            }else{
                console.warn('openButton->open:','Потомок не задал имя класса "_className"');
            }
        },
        close:function(){
            if (this._className){
                if (this._dialog){
                    this._dialog.close();
                    this._dialog.destroy();
                    delete this._dialog;
                    this._dialog=null;
                }
            }else{
                console.warn('openButton->close:','Потомок не задал имя класса "_className"')
            }
        }
    });
}( jQuery ) );