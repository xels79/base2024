/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


let m_filter_base = {
    reset: function () {
        console.log( this );
        if ( this.eventNamespace.indexOf( 'm_filter_like' ) > -1 ) {
            this._input.val( '' );
            this._oldV = '';
        } else {
            this._massAction( '-2' );
        }
    }
};
( function ( $ ) {
    $.widget( "custom.m_filter_base", $.Widget, $.extend( {}, m_filter_base ) );
}( jQuery ) );