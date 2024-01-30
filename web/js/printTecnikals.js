/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$( document ).ready( function () {
    //let mywindow = window;

    $( 'body' ).css( {
//        width: '20cm',
//        height: 1128 + 'px',
//        'min-height': 1128 + 'px',
//        'max-height': 1128 + 'px',
//        overflow: 'hidden'
    } );
    //let r = A4 - ( $( 'body' ).children( ':first-child' ).children( ':first-child' ).height() );
    //alert( 'h:' + $( 'body' ).height() + ' div:' + $( 'body' ).children( ':first-child' ).children( ':first-child' ).height() );
    let th = 0;
    setTimeout( function () {
        let A4 = 1250;
        let s1 = $( '<style type="text/css">' );
        $( 'body' ).children( ':first-child' ).children( ':first-child' ).children( 'tbody' ).children( ':not(:last-child)' ).each( function () {
            A4 -= $( this ).outerHeight();
            $( this ).attr( 'id', 'tr_' + A4 );
            s1.html( s1.html() + '@media print{#tr_' + A4 + '>td,#tr_' + A4 + '>th{height:' + $( this ).height() + 'px!important;}}' );
            s1.html( s1.html() + '#tr_' + A4 + '>td,#tr_' + A4 + '>th{height:' + $( this ).height() + 'px!important;}' );
            console.log( this, $( this ).height( ), $( this ).outerHeight(), $( this ).innerHeight() );
        } );
        $( 'head' ).append( s1 );
        console.log( A4 );
        //alert( 'th:' + th + ' img:' + ( 1122 - th ) );
        if ( $( 'body' ).find( 'img' ).length ) {
            $( 'body' ).find( 'img' ).each( function () {
                let h = $( this ).height();
                let s = $( '<style type="text/css">' );
                if ( $( this ).width() < $( this ).height() ) {
                    s.html( '@media print{img{width:auto!important;height:auto!important;max-height:' + ( A4 ) + 'px!important;max-width:' + ( $( this ).parent().parent().width() - 10 ) + 'px!important;}}img{width:auto!important;height:auto!important;max-height:' + ( A4 ) + 'px!important;max-width:' + ( $( this ).parent().parent().width() - 10 ) + 'px!important;}' );
                } else {
                    s.html( '@media print{img{height:auto!important;max-height:' + ( A4 ) + 'px!important;width:auto;max-width:' + ( $( this ).parent().parent().width() - 10 ) + 'px!important;}}' );
                }
                $( 'head' ).append( s );
            } );
        }
        if ( $( 'body' ).find( 'iframe' ).length ) {
            $( 'body' ).find( 'iframe' ).each( function () {
                let h = $( this ).height();
                let s = $( '<style type="text/css">' );
                s.html( '@media print{iframe{width:' + ( $( this ).parent().parent().width( ) - 10 ) + 'px!important;min-width:' + ( $( this ).parent().parent().width( ) - 10 ) + 'px!important;height:' + ( A4 ) + 'px!important;min-height:' + ( A4 ) + 'px!important;}}' );
                $( 'head' ).append( s );
            } );
        }

        setTimeout( function () {
            window.document.close();
            setTimeout( function () {
                window.close();
            }, 400 );

        }, 400 );
        //mywindow.document.close(); // necessary for IE >= 10
        // window.focus(); // necessary for IE >= 10
        window.print();
    }, 100 );
} );