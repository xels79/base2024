/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var _BFDEBUGE = false;
var mainDebug = 0;
var debugMode = true;
window.statusCodes = {
    200: '(200) Ok',
    400: '(400) Bad Request',
    401: '(401) Unauthorized',
    403: '(403) Forbidden',
    404: '(404) Not Found',
    405: '(405) Method Not Allowed',
    408: '(408) Request Timeout',
    500: '(500) Internal Server Error'
};
function lg ( val ) {
    if ( mainDebug )
        if ( _BFDEBUGE ) console.log( val );
}
$.fn.checkIsInteger = function ( val ) {
    for ( i = 0; i < val.length; i++ ) {
        var c = val.charAt( i );
        if ( ( c < '0' || c > '9' ) && c != '-' )
            return true;
    }
    ;
    return false;
};
$.fn.jsonToStringSub = function ( obj, bracked, replace ) {
    rVal = bracked;
    first = true;
    $.each( obj, function ( key, value ) {
        lg( key );
        if ( first ) {
            first = false;
        } else
            rVal += ',';
        if ( bracked === '{' )
            rVal += '"' + key + '":';
        if ( $.isPlainObject( value ) || $.isArray( value ) )
            rVal += $.fn.jsonToString( value, replace );
        else
        if ( value in replace )
            rVal += replace[value];
        else
        if ( $.isNumeric( value ) )
            rVal += value;
        else
            rVal += '"' + value + '"';
    } );
    if ( bracked === '{' )
        rVal += '}';
    else
        rVal += ']';
    return rVal;
};
$.fn.jsonToString = function ( obj, replace ) {
    if ( !$.isPlainObject( replace ) )
        replace = {};
    if ( $.isPlainObject( obj ) )
        return $.fn.jsonToStringSub( obj, '{', replace );
    else
    if ( $.isArray( obj ) )
        return $.fn.jsonToStringSub( obj, '[', replace );
    else
        lg( obj );
};
$.fn.jsonMerge = function ( json1, json2 ) {
    var out = {};
    for ( var k1 in json1 ) {
        if ( json1.hasOwnProperty( k1 ) )
            out[k1] = json1[k1];
    }
    for ( var k2 in json2 ) {
        if ( json2.hasOwnProperty( k2 ) ) {
            if ( !out.hasOwnProperty( k2 ) )
                out[k2] = json2[k2];
            else if (
                    ( typeof out[k2] === 'object' ) && ( out[k2].constructor === Object ) &&
                    ( typeof json2[k2] === 'object' ) && ( json2[k2].constructor === Object )
                    )
                out[k2] = jsonMerge( out[k2], json2[k2] );
        }
    }
    return out;
};
$.fn.getValue = function ( obj, key, def ) {
    if ( def )
        rVal = def;
    else
        rVal = false;
    if ( obj && key ) {
        if ( obj[key] ) {
            rVal = obj[key];
        }
    }
    return rVal;
};
$.fn.baseAjaxUpdate = function ( target, ajaxOpt, onDone ) {
    $.ajax( ajaxOpt ).done( function ( data ) {
        if ( _BFDEBUGE ) console.log( data );
        if ( $.fn.getValue( data, 'status' ) === 'ok' ) {
            content = $.fn.getValue( data, 'html', 'error' );
            if ( $.isPlainObject( target ) ) {
                target.replaceWith( content );
            } else {
                $( '#' + target ).replaceWith( content );
            }
            if ( $.isFunction( onDone ) )
                onDone();
        }
    } );
};
$.fn.createObj = function ( opt ) {
    var rVal;
    //if ( _BFDEBUGE ) console.log(opt);
    if ( $.isPlainObject( opt ) ) {
        if ( opt['tag'] ) {
            rVal = $( '<' + opt.tag + '>' );
            if ( opt['html'] )
                rVal.html( opt.html );
            else
            if ( opt['text'] )
                rVal.text( opt.text );
            if ( opt['options'] ) {
                $.each( opt.options, function ( id, val ) {
                    rVal.attr( id, val );
                } );
            }
        }
    }
    return rVal;
};
$.fn.enableButtons = function () {
    $.each( $.find( 'form' ), function ( id, el ) {
        $.each( $( el ).find( 'button' ), function ( idS, elS ) {
            $( elS ).removeAttr( 'disabled' );
        } );
    } );
};
$.fn.addField = function ( formId, option, defVal ) {
    if ( $.type( defVal ) !== 'object' )
        defVal = {};
    function addOne ( opt ) {
        var div = $( '<div>' );
        if ( !opt['separator'] ) {
            var lbl = $( '<label>' );
            var inp = $( '<input>' );
            lbl.addClass( 'control-label col-sm-4' );
            lbl.attr( 'for', opt.inputId );
            lbl.text( opt.label );
            if ( opt['placeholder'] )
                inp.attr( 'placeholder', opt.placeholder );
            var div2 = $( '<div>' );
            div2.addClass( 'col-sm-5' );
            if ( opt.inputId ) {
                inp.attr( 'type', 'text' );
                inp.attr( 'id', opt.inputId );
                inp.addClass( 'form-control' );//input-sm
                if ( $.type( defVal[opt.inputId] ) !== "undefined" ) {
                    int.val( defVal[opt.inputId] );
                }
                //inp.addClass('input-sm');
                if ( opt['name'] )
                    inp.attr( 'name', opt.name );
                div2.append( inp );

                if ( opt['help'] ) {
                    div2.append( $( '<span  class="help-block">' + opt.help + '</span>' ) );
                }
            } else {
                div2 = false;
            }
            div.addClass( 'form-group' );
            div.append( lbl );
            if ( div2 ) {
                div.append( div2 );
                div.append( $( '<p class="help-block help-block-error"></p>' ) );
            }
        } else {
            div.addClass( 'row' );
            div.addClass( 'separator' );
            if ( opt['text'] )
                div.append( $( '<p>' + opt.text + '</p>' ) );
        }
        if ( $.isPlainObject( formId ) )
            formId.append( div );
        else
            $( formId ).append( div );
    }
    ;
    if ( $.isArray( option ) ) {
        $.each( option, function ( id, el ) {
            if ( el )
                addOne( el );
        } );
    } else {
        if ( option )
            addOne( option );
    }
};

$.fn.AlertBefore = function ( opt ) {
    if ( $.isPlainObject( opt ) ) {
        var div = $( '<div class="alert">' );
        if ( !opt['type'] )
            opt['type'] = 'alert-default';
        else
            opt.type = 'alert-' + opt.type;
        div.addClass( opt.type );
        div.addClass( 'fade in' );
        div.append( $( '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' ) );
        if ( opt['text'] ) {
            if ( $.type( opt['text'] ) === 'object' )
                div.append( opt['text'] );
            else
                div.append( $( '<p>' + opt.text + '</p>' ) );
        }
        if ( opt['timeOut'] ) {
            window.setTimeout( function () {
                div.alert( 'close' );
            }, opt.timeOut * 1000 );
        }
        if ( opt['parentId'] )
            if ( $.type( opt['parentId'] ) === 'string' )
                $( '#' + opt.parentId ).before( div );
            else
                opt['parentId'].before( div );
        else
            $( '#maincontainer' ).before( div );
    }
};
$.fn.AlertAfter = function ( opt ) {
    if ( $.isPlainObject( opt ) ) {
        var div = $( '<div class="alert">' );
        if ( !opt['type'] )
            opt['type'] = 'alert-default';
        else
            opt.type = 'alert-' + opt.type;
        div.addClass( opt.type );
        div.addClass( 'fade in' );
        div.append( $( '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' ) );
        if ( opt['text'] ) {
            if ( $.type( opt['text'] ) === 'object' )
                div.append( opt['text'] );
            else
                div.append( $( '<p>' + opt.text + '</p>' ) );
        }
        if ( opt['timeOut'] ) {
            window.setTimeout( function () {
                div.alert( 'close' );
            }, opt.timeOut * 1000 );
        }
        if ( opt['parentId'] )
            if ( $.type( opt['parentId'] ) === 'string' )
                $( '#' + opt.parentId ).after( div );
            else
                opt['parentId'].after( div );
        else
            $( '#maincontainer' ).after( div );
    }
};
$.fn.creatTag = function ( name, opt ) {
    var rVal = $( '<' + name + '>' );
    //if ( _BFDEBUGE ) console.log('Создан тэг: '+name);
    if ( $.type( opt ) !== 'object' )
        opt = {};
    if ( $.type( opt.style ) === 'object' ) {
        var style = '';
        $.each( opt.style, function ( id, val ) {
            style += id + ':' + val + ';';
        } );
        opt.style = style;
    }
    if ( $.type( opt.text ) !== 'undefined' ) {
        var text = opt.text;
        delete opt.text;
    } else
        var text = false;
    if ( $.type( opt.html ) !== 'undefined' ) {
        var html = opt.html;
        delete opt.html;
    } else
        var html = false;
    if ( $.type( opt ) === 'object' )
        rVal.attr( opt );
    if ( text !== false )
        rVal.text( text );
    if ( html !== false )
        rVal.html( html );
    return rVal;
};
var ddId = 1;
$.fn.renderDropDown = function ( _parent, items, _options ) {
    var parent = false, _opt = $.type( _options ) === 'object' ? _options : {}, inp = null, hid = null;
    var cnt = $( '<div>' ).addClass( 'input-group' );
    var opt = $.extend( {}, {
        input_name: false,
        input_id: false,
        input_val: '',
        dropup: false,
        right: false,
        left: false,
        afterChange: false,
        parentWin: null,
        css: null
    }, _opt );
    var dd = $( '<div>' ).addClass( 'input-group-btn' + ( opt.dropup ? ' dropup' : ' dropdown' ) );
    if ( $.type( opt.css ) === 'object' )
        cnt.css( opt.css );
    if ( _BFDEBUGE ) console.log( 'renderDropDown' );
    if ( $.type( _parent ) === 'object' )
        parent = _parent;
    else if ( $.type( _parent ) === 'string' )
        parent = $( '#' + _parent );
    var ul = $( '<ul>' );//$('<ul class="dropdown-menu">');
    ul.addClass( 'dropdown-menu' + ( opt.right ? ' dropdown-menu-right' : ( opt.left ? ' pull-left' : '' ) ) );
    if ( opt.left )
        ul.css( {
            right: 0,
            left: 'auto'
        } );
    cnt.on( 'show.bs.dropdown', function ( e ) {
        if ( !opt.parentWin )
            return;
        if ( $( this ).offset().top + $( this ).outerHeight() + ul.outerHeight() >= opt.parentWin.offset.top + opt.parentWin.h )
            $( this ).addClass( 'dropup' );
        else
            $( this ).removeClass( 'dropup' );

    } );
    dd.append( $( '<button>' ).attr( {
        'data-toggle': 'dropdown',
        'aria-haspopup': 'true',
        'aria-expanded': 'false',
        class: 'dropdown-toggle btn btn-default',
        id: 'zakaz-dd' + window.ddId++,
        tabindex: -1
    } ).append( $( '<span>' ).addClass( 'caret' ) ) );
    if ( !$.isFunction( opt.afterChange ) )
        opt.afterChange = false;
    if ( opt.input_name ) {
        hid = $( '<input>' )
                .attr( {
                    type: 'hidden',
                    name: opt.input_name,
                    id: opt.input_id
                } )
                .val( opt.input_val );
        cnt.append( hid );
    }
    inp = $( '<span>' ).addClass( 'form-control' );
    cnt.append( inp );
    $.fn.renderUlConten( ul, items, function ( e ) {
        if ( _BFDEBUGE ) console.log( this );
        inp.text( $( this ).text() );
        if ( hid ) {
            hid.val( $( this ).attr( 'data-key' ) );
        } else {
            inp.attr( 'data-key', $( this ).attr( 'data-key' ) );
        }
        if ( opt.afterChange )
            opt.afterChange.call( this );
    }, function ( it ) {
        if ( $.type( opt.input_val ) !== 'undefined' && opt.input_val == it.value ) {
            inp.text( it.label );
            hid.val( it.value );
            if ( hid ) {
                hid.val( it.value );
            } else {
                inp.attr( 'data-key', it.value );
            }

        }
    } );
    dd.append( ul );
    cnt.append( dd );
    if ( parent )
        parent.append( cnt );
    else
        return cnt;
};
$.fn.renderUlConten = function ( parentId, dt, callb, onEachCreate ) {
    function renderIt ( it ) {
        var a = $.fn.creatTag( 'a', it['linkOptions'] );
        if ( it['label'] )
            a.text( it.label );
        if ( $.isFunction( callb ) )
            a.click( callb );
        var li = $.fn.creatTag( 'li' );
        li.append( a );
        return li;
    }
    var parent;
    if ( $.type( parentId ) === 'object' )
        parent = parentId;
    else
        parent = $( '#' + parentId );
    parent.children().remove();
    $.each( dt, function ( id, val ) {
//        if ( _BFDEBUGE ) console.log('renderUlConten',val);
        if ( !val['linkOptions'] )
            val['linkOptions'] = {};
        if ( !val['linkOptions']['data-key'] ) {
            if ( $.type( val.id ) === 'undefined' )
                val['linkOptions']['data-key'] = parent.children().length;
            else
                val['linkOptions']['data-key'] = val.id;
        }
        if ( $.isFunction( onEachCreate ) ) {
            onEachCreate( {label: val.label, value: $.type( val.id ) === 'undefined' ? parent.children().length : val.id} );
        }
        parent.append( renderIt( val ) );
    } );
};
$.fn.addBusy = function ( id ) {
    if ( id ) {
        if ( $.type( id ) === 'object' ) {
            var targ = id;
        } else {
            var targ = $( '#' + id );
        }
        var tmpInfo = $( '#loadingBig' ).clone();
        tmpInfo.attr( 'id', 'tmpInfo' );
        tmpInfo.removeAttr( 'style' );
        targ.append( tmpInfo );
    }
};
$.fn.removeBusy = function () {
    $( '#tmpInfo' ).remove();
};
$.fn.getUrlForAjaxFromHref = function ( url ) {
    //return url.slice(url.indexOf('?')).split(/[&?]{1}[\w\d]+=/);
    var rVal = {};
    var tmp = url.slice( url.indexOf( '?' ) + 1 ).split( '&' );
    $.each( tmp, function ( id, el ) {
        var tmp2 = el.split( '=' );
        rVal[tmp2[0]] = tmp2[1];
    } );
    return rVal;
};
$.fn.findParentByClass = function ( obj, classN ) {
    var tmp = $( obj );
    while ( tmp[0].tagName !== 'BODY' && !tmp.hasClass( classN ) ) {
        tmp = tmp.parent();
        if ( _BFDEBUGE ) console.log( tmp[0].tagName );
    }
    if ( tmp[0].tagName === 'BODY' )
        return false;
    else
        return tmp;
}
$.fn.findParentForm = function ( obj ) {
    var tmp = $( obj );
    if ( _BFDEBUGE ) console.log( tmp[0].tagName );
    while ( tmp[0].tagName !== 'FORM' ) {
        tmp = tmp.parent();
        if ( _BFDEBUGE ) console.log( tmp[0].tagName );
    }
    return tmp;
}
$.fn.mSerialize = function ( obj, namesAttr ) {
    var rVal = {};
    var tmp = $( obj ).serializeArray();
//    if ( _BFDEBUGE ) console.log(tmp);
    var tmp2 = {};
    $.each( tmp, function ( id, val ) {
        if ( val.name == '_csrf' ) {
            rVal._csrf = val.value;
        } else if ( !$.isArray( namesAttr ) || !$.inArray( val.name, namesAttr ) ) {
            var name = val.name.substr( 0, val.name.indexOf( '[' ) );
//            if ( _BFDEBUGE ) console.log(val.name,namesAttr);
            var subName = val.name.match( /[^[}]+(?=])/g );
//            if ( _BFDEBUGE ) console.log(subName);
            if ( $.type( rVal[name] ) == 'undefined' )
                rVal[name] = {};
            switch ( subName.length ) {
                case 1:
                    rVal[name][subName[0]] = val.value;
                    break;
                case 2:
                    if ( $.type( rVal[name][subName[0]] ) == 'undefined' )
                        rVal[name][subName[0]] = {};
                    rVal[name][subName[0]][subName[1]] = val.value;
                    break;
                case 3:
                    if ( $.type( rVal[name][subName[0]] ) == 'undefined' )
                        rVal[name][subName[0]] = {};
                    if ( $.type( rVal[name][subName[0]][subName[1]] ) == 'undefined' )
                        rVal[name][subName[0]][subName[1]] = {};
                    rVal[name][subName[0]][subName[1]][subName[2]] = val.value;
                    break;
            }
        }
    } );
    return rVal;
};
$.fn.twoEditableLists = function ( defVal, list, onChange, options, keyCompare ) {
    function inArray ( n, arr, key ) {
        key = key ? key : '';
        arr = arr ? arr : [ ];
        var rVal = -1;
        var needle = $.type( n ) === 'string' ? n : n.label;
        $.each( arr, function ( k, v ) {
            if ( $.type( v ) === 'string' ) {
                if ( v === needle ) {
                    rVal = k;
                }
            } else if ( $.type( v ) === 'object' && key ) {
                if ( v[key] === needle ) {
                    rVal = k;
                }
            }
            return rVal === -1;
        } );
        return rVal;
    }
    ;
    keyCompare = keyCompare ? keyCompare : '';
    options = $.type( options ) === 'object' ? options : {};
    defVal = $.type( defVal ) ? defVal : [ ];
    list = $.type( list ) === 'array' ? list : [ ];
    options = $.extend( {
        containerClass: 'c-list',
        firstListClass: 'list-group',
        secondListClass: 'list-group',
        firstListItemClass: 'list-group-item',
        secondListItemClass: 'list-group-item',
        itemTag: 'a',
        listTag: 'div',
        containerTag: 'div'
    }, options );
    var ul1 = $.fn.creatTag( options.listTag, {'class': options.firstListClass} );
    var ul2 = $.fn.creatTag( options.listTag, {'class': options.secondListClass} );
    ul1.addClass( 'connectedSortable' )
            .sortable( {
                items: "a:not(.ui-state-disabled)",
                connectWith: '.connectedSortable',
                revert: true,
                update: onChange
            } ).disableSelection();
    ul2.addClass( 'connectedSortable' )
            .sortable( {
                connectWith: '.connectedSortable',
                revert: true,
                update: onChange
            } ).disableSelection();
    var cont = $.fn.creatTag( options.containerTag, {'class': options.containerClass} );
    if ( _BFDEBUGE ) console.log( list, defVal );
    for ( var i = 0; i < list.length; i++ ) {
        if ( _BFDEBUGE ) console.log( list[i], $.inArray( list[i], defVal ) );
        var opt = {'class': options.firstListItemClass};
        if ( $.type( list[i] ) === 'object' ) {
            if ( list[i].label ) {
                opt.text = list[i].label;
            }
            $.each( list[i], function ( k, v ) {
                if ( k !== 'label' )
                    opt[k] = v;
            } );
        } else if ( $.type( list[i] ) === 'string' ) {
            opt.text = list[i];
        }
        if ( inArray( list[i], defVal, keyCompare ) > -1 ) {
            var it1 = $.fn.creatTag( options.itemTag, opt );
            ul1.append( it1 );
        } else {
            var it2 = $.fn.creatTag( options.itemTag, opt );
            ul2.append( it2 );
        }
    }
    cont.append( ul1 );
    cont.append( ul2 );
    return cont;
};
$( '[data-toggle=mCollapse]' ).click( function () {
    console.debug( 'mDtT' );
    $( $( this ).attr( 'data-target' ) ).toggleClass( 'mIn' );
    $( this ).toggleClass( 'glyphicon-chevron-right' );
    $( this ).toggleClass( 'glyphicon-chevron-left' );
} );
function printerSetup () {
    if ( _BFDEBUGE ) console.log( 'printerSetup' );
    $( '#printerClick' ).click( function ( e ) {
        var maxZ = 0;
        var el = null;
        e.preventDefault();
        $( '.view-dialog' ).each( function () {
            if ( _BFDEBUGE ) console.log( $( this ).children( '.ui-dialog-content' ) );
            if ( !( $( this ).children( '.ui-dialog-content' ).viewDialog( 'isMinimized' ) ) ) {
                var zi = parseInt( $( this ).css( 'z-index' ) );
                if ( !isNaN( zi ) && zi > maxZ ) {
                    maxZ = zi;
                    el = $( this ).children( '.ui-dialog-content' );
                }
            }
        } );
        if ( _BFDEBUGE ) console.log( 'printerClick - maxZ', maxZ );
        if ( _BFDEBUGE ) console.log( 'printerClick - onEl', el );
        if ( el )
            el.viewDialog( 'print' );
        else {
            //if ( _BFDEBUGE ) console.log( $( '#zakaz-list' ).length, $( '#zakaz-list' ).disainerZakazController( 'print' ) );
            if ( $( '#zakaz-list' ).length && $( '#zakaz-list' ).disainerZakazController( 'print' ) === true ) {
                return;
            } else {
                if ($('#zakaz-listM').length){
                    let el=$('#zakaz-listM').parent().parent();
                    el.removeClass('hidden-print');
                    setTimeout(function(){
                        el.addClass('hidden-print');
                    },1000);
                }
                window.print();
            }
        }
    } );
}


/* Russian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Andrew Stromnov (stromnov@gmail.com). */
function setLocaleDatePicker () {
    ( function ( factory ) {
        if ( typeof define === "function" && define.amd ) {

            // AMD. Register as an anonymous module.
            define( [ "../widgets/datepicker" ], factory );
        } else {

            // Browser globals
            factory( jQuery.datepicker );
        }
    }( function ( datepicker ) {

        datepicker.regional.ru = {
            closeText: "Закрыть",
            prevText: "&#x3C;Пред",
            nextText: "След&#x3E;",
            currentText: "Сегодня",
            monthNames: [ "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
                "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь" ],
            monthNamesShort: [ "Янв", "Фев", "Мар", "Апр", "Май", "Июн",
                "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек" ],
            dayNames: [ "воскресенье", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота" ],
            dayNamesShort: [ "вск", "пнд", "втр", "срд", "чтв", "птн", "сбт" ],
            dayNamesMin: [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ],
            weekHeader: "Нед",
            dateFormat: "dd.mm.yy",
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ""};
        datepicker.setDefaults( datepicker.regional.ru );

        return datepicker.regional.ru;

    } ) );
    $.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );
}
/*          Боковое меню для Астерион       */
$( document ).ready( function () {
    var width = 40;
    var isAction = false;
    var isOpen = false;
    setLocaleDatePicker();
    printerSetup();
    var timer = null;
//    if ( _BFDEBUGE ) console.log( $( '.side-nav-conteiner ul:first >li' ) );
    //Это для диалогов 
    //Без этого не работают input на втором уровек диалогов
    jQuery(function ($) {      
      $.widget("ui.dialog", $.ui.dialog, {
        _allowInteraction: function(event) {
            return !$(event.target).closest(".mce-container").length || this._super( event );
            }
        });
    });
    $( '.side-nav-conteiner ul:first >li' ).click( function ( e ) {
        if ( !isOpen ) {
            e.preventDefault();
            e.stopPropagation();
        }
    } );
    $( '.side-nav-conteiner ul:first >li' ).mouseenter( function ( e ) {
        var li = $( this );
        if ( !isAction ) {
            timer = setTimeout( function () {
                timer = null;
                var a = li.children( 'a:first-child' );

                isAction = true;
                li.addClass( 'side-nav-informer' );
                a.animate( {
                    width: 160
                }, function () {
                    isOpen = true;
                    if ( !li.children( '.dropdown-menu' ).length ) {
                        isAction = false;
                        li.on( 'mousemove', function ( e ) {
                            e.preventDefault();
                            e.stopPropagation();
                        } );
                        $( document ).on( 'mousemove', function ( e ) {
                            isOpen = false;
                            $( document ).off( 'mousemove' );
                            a.animate( {
                                width: width
                            }, function () {
                                li.removeClass( 'side-nav-informer' );
                                li.off( 'mousemove' );
                            } );
                        } );

                    } else
                        li.children( '.dropdown-menu' ).slideDown( function () {
                            isAction = false;
                            isOpen = true;
                            li.on( 'mousemove', function ( e ) {
                                e.preventDefault();
                                e.stopPropagation();
                            } );
                            $( document ).on( 'mousemove', function ( e ) {
                                $( document ).off( 'mousemove' );
                                isOpen = false;
                                li.children( '.dropdown-menu' ).slideUp( function () {
                                    a.animate( {
                                        width: width
                                    }, function () {
                                        li.removeClass( 'side-nav-informer' );
                                        li.off( 'mousemove' );
                                    } );
                                } );
                            } );
                        } );
                } );
            }, 500 );
        }
//        if ( _BFDEBUGE ) console.log( 'ent', timer );
    } );
    $( '.side-nav-conteiner ul:first >li' ).mouseleave( function ( e ) {
//        if ( _BFDEBUGE ) console.log( 'lv', timer );
        if ( timer !== null ) {
            clearTimeout( timer );
            timer = null;
//            if ( _BFDEBUGE ) console.log( 'tclr', timer );
        }
    } );
//    $('.side-nav-conteiner ul:first >li >a,.side-nav-conteiner ul:first >li').click(function(e){
//        e.preventDefault();
//        if ( _BFDEBUGE ) console.log('clc');
//        //alert('clk');
//    });
    $( '#savebase' ).mouseup( function ( e ) {
        console.debug( 'click', e );
        if ( e.button === 0 ) {
            location.href = $( this ).attr( 'href' );
            var par = $( this ).parent();
            $( this ).remove();
            if ( !$( '.nav-mess' ).length )
                par.parent().parent().remove();
        }
    } );
} );

$.fn.enablePopover = function () {
    $( '.nav-mess' ).each( function () {
        $( this ).popover( {
            trigger: 'hover',
            delay: '500'
        } );
    } );
};
$( '.keyboard button' ).click( function ( e ) {
    var inp = $( this ).parent().parent().parent().parent().parent().next().children( 'input' );
    inp.val( inp.val() + $( this ).text().toLowerCase() );
} );

$.fn.createUlForDropDownFromActiveRecDate = function ( arr, struct ) {
    if ( !struct || !struct.valueName || !struct.labelName )
        return $( '<p>createUlForDropDownFromActiveRecDate: Ошибка в параметрах' );
    var rVal = $( '<ul>' ).addClass( 'dropdown-menu' );
    $.each( arr, function ( ind, el ) {
        rVal.append( $( '<li>' ).append( $( '<a>' )
                .text( this[struct.labelName] )
                .attr( {
                    value: this[struct.valueName],
                    href: '#',
                    role: 'menuItem',
                    tabIndex: -1,
                    'data-key': ind
                } )
                ) );
    } );
    return rVal;
};

$( document ).ready( function () {
    $( '.informer-main' ).click( function () {
        $( this ).children().remove();
    } );
} );

var dropInfo_removeIsStarted = false;
var dropInfo_timeout = 50;
$.fn.dropInfo = function ( txt, type, timeout ) {
    return ;//Отключено
    timeout = timeout ? timeout : dropInfo_timeout;
    function removeIt () {
        $( '.informer-main' ).children( ':first-child' ).slideUp( {
            duration: 200,
            tasing: 'linear',
            queue: false,
            complete: function () {
                $( this ).remove();
                if ( $( '.informer-main' ).children().length ) {
                    dropInfo_removeIsStarted = false;
                    startRemove();
                } else
                    dropInfo_removeIsStarted = false;
            }
        } );
    }
    ;
    function startRemove () {
        if ( !dropInfo_removeIsStarted ) {
            dropInfo_removeIsStarted = true;
            setTimeout( function () {
                removeIt();
            }, parseInt( $( '.informer-main' ).children( ':first-child' ).attr( 'data-timeout' ) ) );
        }
    }
    ;
    if ( window.debugMode === false )
        return;
    var tClass = 'alert-' + ( type ? type : 'primary' );
    var inf = $( '<div>' )
            .addClass( 'alert' )
            .addClass( tClass )
            .addClass( ' fade in' )
            .attr( 'role', 'alert' )
            .attr( 'data-timeout', timeout )
            .height( 30 )
            .html( txt );
    $( '.informer-main' ).append( inf );
    if ( $( '.informer-main' ).children().length > 10 ) {
        $( '.informer-main' ).children( ':first-child' ).remove();
    }
    if ( !dropInfo_removeIsStarted )
        startRemove();
};
$.fn.endingNums = function ( number, titles ) {
    let cases = [ 2, 0, 1, 1, 1, 2 ];
    return titles[ ( number % 100 > 4 && number % 100 < 20 ) ? 2 : cases[( number % 10 < 5 ) ? number % 10 : 5]];
};

//Подключение скриптов
let javascripts = [ ];

$.fn.includeJS = function ( path_data, callb, callBTick ) {
    //alert('пробуем подключить'+path);
    var loadingCnt = 0;
    function inclOne ( path ) {
        for ( var i = 0; i < javascripts.length && !$.isFunction( callBTick ); i++ ) {
            if ( path == javascripts[i] ) {
                console.warn( 'includeJS', 'JavaScript: [' + path + '] уже был подключен ранее!' );
                return false;
            }
        }
        javascripts.push( path );
        if ( !$.isFunction( callBTick ) ) {
            $.ajax( {
                url: path,
                dataType: "script", // при типе script, JS сам инклюдится и воспроизводится
                async: true,
                complete: function ( jqXHR, textStatu ) {
                    loadingCnt--;
                    if ( !loadingCnt && $.isFunction( callb ) )
                        callb.call();
                    if ( _BFDEBUGE ) console.log( 'includeJS', 'JavaScript: [' + path + '] успешно подключен.' );
                },
                error: function ( jqXHR, textStatus, errorThrown ) {
                    console.error( 'includeJS', textStatus, errorThrown );
                }
            } );
        } else {
            $.ajax( {
                url: path,
                dataType: "text", // при типе script, JS сам инклюдится и воспроизводится
                async: true,
                complete: function ( jqXHR, textStatu ) {
                    loadingCnt--;
                    callBTick.call( this, jqXHR.responseText );
                    if ( !loadingCnt && $.isFunction( callb ) )
                        callb.call();
                    if ( _BFDEBUGE ) console.log( 'includeJS', 'JavaScript: [' + path + '] успешно подключен.' );
                },
                error: function ( jqXHR, textStatus, errorThrown ) {
                    console.error( 'includeJS', textStatus, errorThrown );
                }
            } );
        }
        return true;
    }
    if ( $.type( path_data ) === 'string' ) {
        loadingCnt++;
        return inclOne( path_data );
    } else if ( $.type( path_data ) === 'array' ) {
        var rVal = true;
        loadingCnt += path_data.length;
        $.each( path_data, function ( id, el ) {
            rVal = rVal && inclOne( el );
        } );
    } else {
        console.error( 'includeJS', 'Не верное значение пути' );
        return false;
    }


}
$.fn.formatProceed = function ( fString ) {
    if ( !fString )
        return '';
    let tmp = fString.toUpperCase();
    switch ( tmp ) {
        case 'A0':
            return '841*1189';
            break;
        case 'A1':
            return '594*841';
            break;
        case 'A2':
            return '420*594';
            break;
        case 'A3':
            return '297*420';
            break;
        case 'A4':
            return '210*297';
            break;
        case 'A5':
            return '148*210';
            break;

        case 'B0':
            return '1000*1414';
            break;
        case 'B1':
            return '707*1000';
            break;
        case 'B2':
            return '500*707';
            break;
        case 'B3':
            return '353*500';
            break;
        case 'B4':
            return '250*353';
            break;
        case 'B5':
            return '176*250';
            break;

        case 'C0':
            return '917*1297';
            break;
        case 'C1':
            return '648*917';
            break;
        case 'C2':
            return '458*648';
            break;
        case 'C3':
            return '324*458';
            break;
        case 'C4':
            return '229*324';
            break;
        case 'C5':
            return '162*229';
            break;
        case 'C65':
            return '114*229';
            break;


        case 'SR3':
            return '320*450';
            break;
        case 'E65':
            return '110*220';
            break;

        case 'А0':
            return '841*1189';
            break;
        case 'А1':
            return '594*841';
            break;
        case 'А2':
            return '420*594';
            break;
        case 'А3':
            return '297*420';
            break;
        case 'А4':
            return '210*297';
            break;
        case 'А5':
            return '148*210';
            break;

        case 'В0':
            return '1000*1414';
            break;
        case 'В1':
            return '707*1000';
            break;
        case 'В2':
            return '500*707';
            break;
        case 'В3':
            return '353*500';
            break;
        case 'В4':
            return '250*353';
            break;
        case 'В5':
            return '176*250';
            break;

        case 'С0':
            return '917*1297';
            break;
        case 'С1':
            return '648*917';
            break;
        case 'С2':
            return '458*648';
            break;
        case 'С3':
            return '324*458';
            break;
        case 'С4':
            return '229*324';
            break;
        case 'С5':
            return '162*229';
            break;
        case 'С65':
            return '114*229';
            break;


        case 'СР3':
            return '320*450';
            break;
        case 'Е65':
            return '110*220';
            break;

        default:
            return fString;
            break;
    }
}

//
//Сортер объект key=>value по значению
//
var NEWFUNCTION = NEWFUNCTION || {};
NEWFUNCTION.browser = function () {
    var ua = window.navigator.userAgent;
    if ( /edge/i.test( ua ) || /edge\/(\d+(\.\d+)?)/i.test( ua ) )
        return 'Microsoft edge';
    if ( ua.search( /MSIE/ ) > 0 )
        return 'Internet Explorer';
    if ( ua.search( /Firefox/ ) > 0 )
        return 'Firefox';
    if ( ua.search( /Opera/ ) > 0 )
        return 'Opera';
    if ( ua.search( /Chrome/ ) > 0 )
        return 'Google Chrome';
    if ( ua.search( /Safari/ ) > 0 )
        return 'Safari';
    if ( ua.search( /Konqueror/ ) > 0 )
        return 'Konqueror';
    if ( ua.search( /Iceweasel/ ) > 0 )
        return 'Debian Iceweasel';
    if ( ua.search( /SeaMonkey/ ) > 0 )
        return 'SeaMonkey';

    // Браузеров очень много, все вписывать смысле нет, Gecko почти везде встречается
    if ( ua.search( /Gecko/ ) > 0 )
        return 'Gecko';

    // а может это вообще поисковый робот
    return 'Search Bot';
}

let __orderObj = function ( val, prev, next ) {
    val = val || null;
    prev = prev || null;
    next = next || null;
    this.next = next;
    this.prev = prev;
    this.val = val;
};
let _sorted = function () {}

_sorted.prototype.isBig = function ( v1, v2, pos, l ) {
    if ( v1.length && v2.length ) {
        //return v1.charCodeAt( 0 ) < v2.charCodeAt( 0 );
        pos = pos || 0;
        l = l || v1.length > v2.length ? v1.length : v2.length;
        if ( v1.charCodeAt( pos ) < v2.charCodeAt( pos ) ) {
            return true;
        } else if ( v1.charCodeAt( pos ) === v2.charCodeAt( pos ) ) {
            if ( pos + 1 < l ) {
                return this.isBig( v1, v2, pos + 1 );
            } else {
                return v1.length >= v2.length;
            }
        } else {
            return false;
        }
    } else {
        return v1.length > v2.length;
    }
};
NEWFUNCTION.sorted = function ( vals ) {
    let tmp = [ ];
    this.setTmp = function ( val ) {
        tmp = val;
    };
    this.getTmp = function ( key ) {
        if ( typeof ( key ) === 'undefined' ) {
            return tmp;
        } else {
            return tmp[key];
        }
    };
    this.pushTmp = function ( val ) {
        tmp.push( val );
    };
    _sorted.call( this );
    Object.keys( vals ).forEach( ( item ) => {
        this.pushNext( item, vals[item] );
    } );
}
NEWFUNCTION.sorted.prototype = Object.create( _sorted.prototype );
NEWFUNCTION.sorted.prototype.keys = function () {
    let rVal = [ ];
    this.getTmp().forEach( ( item ) => {
        rVal.push( item.key );
    } );
    return rVal;
};
NEWFUNCTION.sorted.prototype.get = function ( key ) {
    let rVal = null;
    let isFound = false;
    for ( let i = 0; i < this.getTmp().length && !isFound; i++ ) {
        if ( key === this.getTmp( i ).key ) {
            rVal = this.getTmp( i ).val;
            isFound = true;
        }
    }
    if ( !isFound ) {
        console.error( 'Ключ:' + key + ' не найден' );
    }
    return rVal;
};
NEWFUNCTION.sorted.prototype.pushNext = function ( key, val ) {
//    if ( _BFDEBUGE ) console.log( key, val );
    if ( this.getTmp().length ) {
        let tmpV = [ ], doCompare = true;
        this.getTmp().forEach( ( item ) => {
            if ( doCompare && this.isBig( val, item.val ) ) {
                doCompare = false;
                tmpV.push( {key: key, val: val} );
                tmpV.push( item );
            } else {
                tmpV.push( item );
            }
        } );
        if ( doCompare ) {
            tmpV.push( {key: key, val: val} );
        }
        this.setTmp( tmpV );
    } else {
        this.pushTmp( {key: key, val: val} );
    }
};
/*
 * @syntax NEWFUNCTION.sorted2(vals)
 * @param {Object} vals -типа key:values
 * @returns {Object}
 */
NEWFUNCTION.sorted2 = function ( vals ) {
    let last = null, first = null, count = 0;
    this.setFirst = function ( f ) {
        first = f;
    };
    this.getTmp = function ( key ) {
        if ( typeof ( key ) === 'undefined' ) {
            return first;
        } else {
            let rVal = null;
            let tmp = first;
            while ( rVal === null && tmp !== null ) {
                if ( tmp.val.key == key ) {
                    rVal = tmp;
                } else {
                    tmp = tmp.next;
                }
            }
            if ( rVal === null ) {
                console.error( 'Ключ "' + key + '" не найден!' );
            }
            return rVal;
        }
    };
    this.pushTmp = function ( val ) {
        if ( !last ) {
            last = new __orderObj( val );
            first = last;
        } else {
            last.next = new __orderObj( val, last );
            last = last.next;
        }
        count++;
    };
    _sorted.call( this );
    this.pushNext = function ( key, val ) {
        let tmp = this.getTmp();
        let fEl = null;

        while ( fEl === null && tmp !== null ) {
            if ( this.isBig( val, tmp.val.val ) ) {
                fEl = tmp;
            } else {
                tmp = tmp.next;
            }
        }
        if ( fEl !== null ) {
            if ( fEl.prev === null ) {
                let dt = new __orderObj( {key: key, val: val}, null, fEl );
                fEl.prev = dt;
                this.setFirst( dt );
            } else {
                fEl.prev.next = new __orderObj( {key: key, val: val}, fEl.prev, fEl );
                fEl.prev = fEl.prev.next;

            }
            count++;
        } else {
            this.pushTmp( {key: key, val: val} );
        }
    };
    Object.defineProperty( this, 'lenght', {
        enumerable: false,
        configurable: false,
        get: function () {
            return count;
        }
    } );
    Object.keys( vals ).forEach( ( item ) => {
        this.pushNext( item, vals[item] );
    } );
}
NEWFUNCTION.sorted2.prototype = Object.create( _sorted.prototype );
NEWFUNCTION.sorted2.prototype.get = function ( key ) {
    return this.getTmp( key ).val.val;
};
NEWFUNCTION.sorted2.prototype.keys = function () {
    let rVal = [ ];
    let tmp = this.getTmp();
    while ( tmp ) {
        rVal[rVal.length] = tmp.val.key;
        tmp = tmp.next;
    }
    return rVal;
};

NEWFUNCTION.FixTable = function ( table ) {
    var inst = this;
    this.table = table;
    if (!$( inst.table ).lenght) return;
    $( 'tr > th', $( this.table ) ).each( function ( index ) {
        var div_fixed = $( '<div/>' ).addClass( 'fixtable-fixed' );
        var div_relat = $( '<div/>' ).addClass( 'fixtable-relative' );
        div_fixed.html( $( this ).html() );
        div_relat.html( $( this ).html() );
        $( this ).html( '' ).append( div_fixed ).append( div_relat );
        $( div_fixed ).hide();
    } );

    this.StyleColumns();
    this.FixColumns();

    $( table ).scroll( function () {
        inst.FixColumns();
        inst.StyleColumns();
    } ).resize( function () {
        inst.StyleColumns()
    } );
}

NEWFUNCTION.FixTable.prototype.StyleColumns = function () {
    var inst = this;
    var s_top = $( this.table ).scrollTop();
    $( 'tr > th', $( this.table ) ).each( function () {
        var div_relat = $( 'div.fixtable-relative', $( this ) );
        var th = $( div_relat ).parent( 'th' );
        if ( _BFDEBUGE ) console.log( $( th ).offset().left, $( th ).offset().top );
        if ( !$( div_relat ).attr( 'tunned' ) ) {
            $( 'div.fixtable-fixed', $( this ) ).css( {
                'width': $( th ).outerWidth( true ) - parseInt( $( th ).css( 'border-left-width' ) ) + 'px',
                'height': $( th ).outerHeight( true ) + 'px',
                'left': $( th ).offset().left, //- parseInt($(th).css('padding-left')) + 'px',
                'top': $( th ).offset().top + s_top,
                'padding-top': ( $( div_relat ).offset().top + s_top ) - $( inst.table ).offset().top + 'px',
                'padding-left': $( th ).css( 'padding-left' ),
                'padding-right': $( th ).css( 'padding-right' )
            } );
            if ( $( th ).offset().left && $( th ).offset().top ) {
                $( div_relat ).attr( 'tunned', 'tunned' );
            }
        }

    } );
}

NEWFUNCTION.FixTable.prototype.FixColumns = function () {
    var inst = this;
    var show = false;
    var s_top = $( this.table ).scrollTop();
    var h_top = $( inst.table ).offset().top;

    if ( s_top < ( h_top + $( inst.table ).height() - $( inst.table ).find( '.fixtable-fixed' ).outerHeight() ) && s_top > h_top ) {
        show = true;
    }
    if ( s_top > 0 ) {
        show = true;
    }

    $( 'tr > th > div.fixtable-fixed', $( this.table ) ).each( function () {
        show ? $( this ).show() : $( this ).hide()
    } );
}

NEWFUNCTION.transliterate = (
    function() {
        var
            rus = "щ   ш  ч  ц  ю  я  ё  ж  ъ  ы  э  а б в г д е з и й к л м н о п р с т у ф х ь".split(/ +/g),
            eng = "shh sh ch cz yu ya yo zh `` y' e` a b v g d e z i j k l m n o p r s t u f x `".split(/ +/g)
        ;
        return function(text, engToRus) {
            var x;
            for(x = 0; x < rus.length; x++) {
                text = text.split(engToRus ? eng[x] : rus[x]).join(engToRus ? rus[x] : eng[x]);
                text = text.split(engToRus ? eng[x].toUpperCase() : rus[x].toUpperCase()).join(engToRus ? rus[x].toUpperCase() : eng[x].toUpperCase());
            }
            return text;
        }
    }
)();
