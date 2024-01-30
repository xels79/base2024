/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function( $ ) {
  $.widget( "mProg.mProgress", {
 
    // Здесь задается список настроек и их значений по умолчанию
    options: { 
      barWidth: null
    },
 
    // Функция, вызываемая при активации виджета на элементе
    _create: function() {
        console.debug('mProgress:_create:');
    },
 
    // Этот метод вызывается для изменения настроек плагина
    _setOption: function( key, value ) { 
      // В jQuery UI 1.8, можно делегировать задачи методу
      // _setOption родительскому плагину:
      //$.Widget.prototype._setOption.apply( this, arguments );
 
      // А начиная с UI 1.9 для этого достаточно вызвать метод _super
      this._super( "_setOption", key, value );
    },
 
    // деструктор - метод, который будет вызван при удалении плагина с элемента.
    // Он нужен чтобы очистить элемент от всех модификаций, сделанных плагином
    // (удалить вспомогательные классы, атрибуты и элементы)
    destroy: function() {
        console.debug('mProgress:destroy:');
        $.Widget.prototype.destroy.call( this );
      // In jQuery UI 1.9 and above, you would define _destroy instead of destroy and not call the base method
    },
    width:function(w){
//        console.debug('mProgress:width:',w);
//        console.debug('mProgress:width:',this.element);
        $(this.element).css('background-size',w);
    }
  });
}( jQuery ) );

