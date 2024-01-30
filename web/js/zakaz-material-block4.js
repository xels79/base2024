/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var block_paket = {
    __block4Block: {},
    __block4GetSizes: function ( str ) {
        let i = 0;
        let names = [ 'wP', 'hP', 'zP' ];
        let rVal = {};
        let tmp = '';
        for ( let n = 0; n < str.length && i < names.length; n++ ) {
            if ( str.charCodeAt( n ) < 48 || str.charCodeAt( n ) > 57 ) {
                rVal[names[i++]] = parseInt( tmp );
                tmp = '';
            } else {
                tmp += str.charAt( n );
            }
        }
        if ( tmp && i < names.length )
            rVal[names[i]] = parseInt( tmp );
        return rVal;
    },
    __block4PaketCutter: function ( dopId ) {
        let self = this;
        dopId = dopId ? dopId : '';
        let key = dopId === '' ? '0' : ( '' + dopId );
//        let block=$('#Zakaz-material_block_format').val()?(JSON.parse($('#Zakaz-material_block_format').val())):{};
        if ( !this.options.paketCutterUrl ) {
            console.error( 'Не указан адресс запроса paketcutter' );
        }
        console.log( this.__block4GetSizes( $( '#Zakaz-product_size' ).val() ) );
        if ( !$( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][type_id]"]' ).length || !$( '[name="Zakaz[materials][' + ( dopId === '' ? '0' : dopId ) + '][mat_id]"]' ) ) {
            m_alert( 'Ошибка', 'Не выбран материал4', true, false );
            return;
        }
        if ( Object.keys( this.__block4GetSizes( $( '#Zakaz-product_size' ).val() ) ).length !== 3 ) {
            m_alert( 'Ошибка', 'Не верный размер<br>Должно быть 3 параметра <i>"<b>Д*Ш*Г</b>"</i>', true, false, function () {
                $( '[href="#tab-pane-zakaz-page0"' ).trigger( 'click' );
                $( '#Zakaz-product_size' ).focus();
            } );
            return;
        }
        console.log( '__block4PaketCutter', {
            type_id: $( '[name="Zakaz[materials][' + key + '][type_id]"]' ),
            mat_id: $( '[name="Zakaz[materials][' + key + '][mat_id]"]' )
        } );
        $.post( this.options.paketCutterUrl, $.extend( {}, {
            type_id: $( '[name="Zakaz[materials][' + key + '][type_id]"]' ).val(),
            mat_id: $( '[name="Zakaz[materials][' + key + '][mat_id]"]' ).val(),
            lam: self.__block4Block[key].isPaketLaminat ? true : false
        }, this.__block4GetSizes( $( '#Zakaz-product_size' ).val() ) )
                ).done( function ( dt ) {
            $( 'div' ).addClass( 'hidden-print' );
            console.log( 'Резка листа ответ сервера', dt );
            if ( ( dt.liffCuter && dt.liffCuter.error ) || ( dt.status && dt.status != 200 ) ) {
                if ( dt.liffCuter && dt.liffCuter.error ) {
                    m_alert( 'Ошибка', dt.liffCuter.error, true, false );
                } else {
                    m_alert( dt.errorHead ? dt.errorHead : 'Ошибка', dt.errorText ? dt.errorText : ( dt.error ? dt.error : ( dt.message ? dt.message : '' ) ), true, false );
                }
                return;
            }
            if ( dt.paket.error ) {
                m_alert( dt.paket.errorHead ? dt.paket.errorHead : 'Ошибка', dt.paket.error );
                return;
            }
            if ( dt.liffCuter.error ) {
                m_alert( dt.liffCuter.errorHead ? dt.liffCuter.errorHead : 'Ошибка', dt.liffCuter.errorText ? dt.liffCuter.errorText : dt.liffCuter.error );
                return;
            }
            $( '#Zakaz-format_printing_block' + dopId ).val( dt.liffCuter.tmp[0] + '*' + dt.liffCuter.tmp[1] );
            let dialog = $( '<div>' ).addClass( 'paket-cutter' ).attr( {title: 'Резка пакета'} );
            let cutter = $( '<div>' ).appendTo( dialog );
            let paket = $( '<div>' ).addClass( 'paket-content' ).appendTo( dialog );
            dialog.appendTo( 'body' ).maindialog( {
                minimizable: false,
                modal: true,
                width: 1000,
                height: 550,
                close: function () {
                    dialog.remove();
                },
                open: function () {
//                    let tmpBlock=self._blockProceed(dopId);
                    let variant = self.__block4Block[key].block.var;
                    $( this ).parent().addClass( 'block-format-popup' );
                    if ( !variant )
                        if ( dt.liffCuter.data2.pcs == $( '#Zakaz-blocks_per_sheet' + dopId ).val() && parseInt( $( '#Zakaz-blocks_per_sheet' + dopId ).val() ) !== 0 && !isNaN( parseInt( $( '#Zakaz-blocks_per_sheet' + dopId ).val() ) ) )
                            variant = 2;
                        else
                            variant = 1;
                    self.__block4Block[key].block.var = variant;
                    self.__cutterShouBody( variant, dt.liffCuter, cutter, true, function ( val ) {
                        self.__block4Block[key].block.var = val;
                    } );
                    self.__block4DrawPaket( paket, dt.paket, dopId );
                    dialog.parent().offset( {top: $( document ).height() / 2 - dialog.parent().height() / 2} );
                },
                buttons: [
                    {
                        text: 'Сохранить рисунок',
                        click: function () {
//                            let fileExist=false;
                            let fileExist = $( '[data-file-name="is_new_paket_pic.png"]' ).length != 0 || $( '[data-file-name="zakaz' + self.options.z_id + '_paket_pic.png"]' ).length != 0;
//                            $.each(self._loadedFileList.des,function(key,v){
//                                if (v=="zakazis_new_paket_pic.png"||v=="zakaz"+self.options.z_id+"_paket_pic.png"){
//                                    fileExist=true;
//                                }
//                                return !fileExist;
//                            });
                            if ( !fileExist ) {
                                let canv = $( '#paket-pic' ).get( 0 );
                                let dataURL = canv.toDataURL( 'image/png', 1 );
                                let arr = dataURL.split( ',' ), mime = arr[0].match( /:(.*?);/ )[1],
                                        bstr = atob( arr[1] ), n = bstr.length, u8arr = new Uint8Array( n );
                                while ( n-- ) {
                                    u8arr[n] = bstr.charCodeAt( n );
                                }
                                let blob = new Blob( [ u8arr ], {type: mime} );
                                self._dropAddFileToLoad( [ {
                                        name: ( self.options.z_id ? ( "zakaz" + self.options.z_id ) : ( "is_new" ) ) + "_paket_pic.png",
                                        size: blob.size,
                                        blob: blob
                                    } ], false );
                            } else {
                                m_alert( 'Предупреждение', 'Файл уже существует!', true, false );
                            }
                        }
                    },

                    {
                        text: 'Печать',
                        click: function () {
                            window.print();
                        }
                    },
                    {
                        text: "Ok",
                        click: function () {
                            let name = 'data' + $( '#cutter-radio-inp' ).val();
                            let tir=parseInt($('#Zakaz-number_of_copies').val());
                            tir=isNaN(tir)?0:tir;
                            self._blockProceed( dopId, self.__block4Block[key] );
                            let in_block=self.__block4Block[key].isToPcsPaket ? 0.5 : 1;
                            let b_num=Math.floor(tir/(dt.liffCuter[name].pcs*in_block));
                            $( '#Zakaz-num_of_products_in_block' + dopId ).val( in_block );
                            $( '#Zakaz-blocks_per_sheet' + dopId ).val( dt.liffCuter[name].pcs ).trigger( 'change' );
                            $('#Zakaz-num_of_printing_block' + dopId ).val(b_num);
                            dialog.maindialog( 'close' );
                            if (self.__block4Recal()){
                                self.__blockRecalMainParam(dopId);
                            }
                        }
                    },
                    {
                        text: "Отмена",
                        click: function () {
                            dialog.maindialog( 'close' );
                        }
                    }
                ]
            } );
        } ).fail( function ( er1 ) {
            if ( $.type( er1.responseJSON ) === 'object' )
                m_alert( er1.responseJSON.name, er1.responseJSON.message, true, false );
            else
                m_alert( "Ошибка сервера", er1.responseText, true, false );
        } );
    },
    __block4DrTxt: function ( ctx, txt, x, y, ln ) {
        ln = ln ? ln : 4;
        let metrics = ctx.measureText( txt );
        ctx.clearRect( x - ln, y - 9, Math.ceil( metrics.width ) + ln * 2, 10 );
        ctx.fillText( txt, x, y );//Math.ceil(top/2));
    },
    __block4DrawPaket ( cont, dt, dopId ) {
//        let block=$('#Zakaz-material_block_format').val()?(JSON.parse($('#Zakaz-material_block_format').val())):{};
        let key = dopId === '' ? '0' : ( '' + dopId );
        let nameEnd = this.__block4Block[key].isPaketLaminat ? 'lam.png' : '.png';
        let padding = 10;
        let top = 73.6, step = 45.7
        let left1 = 17.3, left2 = 41.7, lefSide = 10.3, dStep = 11;
        let self = this;
        let canvas = $( '<canvas>' ).css( {
            top: padding,
            left: padding,
            width: 575,
            height: 390,
        } ).attr( 'id', 'paket-pic' );
        let img = new Image();
        let name = '/pic/paket/paket_';
        if ( dt.HorSzText.length < 5 ) {
            this.__block4Block[key].isToPcsPaket = true;
            if ( dt.params.h > dt.params.w ) {
                name += '3';
                top = 80.3;
            } else {
                name += '4';
                left1 = 32.3, left2 = 70.4;
                top = 80.3;
            }
        } else {
            this.__block4Block[key].isToPcsPaket = false;
            if ( dt.params.h > dt.params.w ) {
                name += '1';
                top = 80.3;
            } else {
                name += '2';
                top = 45.4;
            }
        }
        img.src = name + nameEnd;
        img.onload = function () {
            let canv = canvas.get( 0 );
            canv.width = 1150;
            canv.height = 780;
            canv.backgroundcolor = '#fff';
            left1 = Math.round( canv.width / 100 * left1 );
            left2 = Math.round( canv.width / 100 * left2 );
            lefSide = Math.round( canv.width / 100 * lefSide );
            step = Math.round( canv.width / 100 * step );
            top = Math.round( canv.height / 100 * top );
            dStep = Math.round( canv.height / 100 * dStep );
            let ctx = canv.getContext( "2d" );
            ctx.clearRect( 0, 0, canv.width, canv.height );
            ;
            ctx.drawImage( img, 0, 0, canv.width, canv.height );
            ctx.fillStyle = "#00F";
            ctx.strokeStyle = "#F00";
            ctx.font = "italic bold 14pt Arial";
            ctx.textAlign = "left";
            ctx.save();
            ctx.translate( lefSide, 2 * top / 3 );
            ctx.rotate( 3 * Math.PI / 2 );
            self.__block4DrTxt( ctx, dt.VertSz, 0, 0 );
            ctx.restore();
            ctx.save();
            ctx.translate( lefSide, top + dStep );
            ctx.rotate( 3 * Math.PI / 2 );
            let metrics = ctx.measureText( dt.bottom + 'мм' );
            self.__block4DrTxt( ctx, dt.bottom + 'мм', -1 * Math.ceil( metrics.width / 2 - 1 ), 0, 0 );
            ctx.restore();
            for ( let i = 1, n = 0; i < dt.HorSzText.length - 1; i++ ) {
                self.__block4DrTxt( ctx, dt.HorSzText[i++] + 'мм', left1 + ( step * n ), top );
                self.__block4DrTxt( ctx, dt.HorSzText[i] + 'мм', left2 + ( step * n ), top );
                n++;
            }
        };
        cont.append( canvas );
    },
    __block4Recal: function () {
        let m_count = $( '.mat-middle-zone' ).children( 'div:first-child' ).children().length - 1;
        if ( !$( '#Zakaz-product_size' ).val() ) {
            m_alert( 'Ошибка', 'Не указан размер изделия', true, false, function () {
                $( '[href="#tab-pane-zakaz-page0"' ).trigger( 'click' );
                $( '#Zakaz-product_size' ).focus();
            } );
            return false;
        }
        if ( !this._totalCount() ) {
            m_alert( 'Ошибка', 'Не указан тираж', true, false, function () {
                $( '[href="#tab-pane-zakaz-page0"' ).trigger( 'click' );
                $( '#Zakaz-number_of_copies' ).focus();
            } );
            return false;
        } else if ( m_count > 1 ) {
            if ( !this._totalCount( '1' ) ) {
                m_alert( 'Ошибка', 'Не указан тираж2', true, false, function () {
                    $( '[href="#tab-pane-zakaz-page0"' ).trigger( 'click' );
                    $( '#Zakaz-number_of_copies1' ).focus();
                } );
                return false;
            }
        }
        return true;
    },
    __drawTypeBlock4: function ( tbd, num ) {
        let self = this;
        let dopId = num ? num : '';
        let key = ( dopId === '' ? '0' : ( '' + dopId ) );
        console.log( '__drawTypeBlock4' );
        tbd.parent().removeAttr( 'class' );
        tbd.parent().addClass( 'block-body typeBlock4' );
        this.__block4Block[key] = self._blockProceed( dopId );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Блоков с листа:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_products_in_block' + dopId, {type: 'hidden'} ) ).append( this._createInput( 'blocks_per_sheet' + dopId, {}, {
            change: [ {self: this, dopId: dopId}, self._blockRecalMainParam ]
        } ).val( this.options.form.fields['blocks_per_sheet' + dopId] ) ) ).append( $( '<span>' ).append( $( '<img>' ).attr( {src: 'pic/k1.png', title: 'Расчитать', id: 'blockRecalButton' + dopId} ).click( {self: this, dopId: dopId}, function ( e ) {
            console.log( '__drawTypeBlock4', 'calck click' );
            if ( e.data.self.__block4Recal( false ) )
                e.data.self.__block4PaketCutter.call( self, dopId );
        } ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Формат блока:' ) ).append( $( '<span>' ).append( this._createInput( 'format_printing_block' + dopId, {readonly: 'readonly'} ).val( this.options.form.fields['format_printing_block' + dopId] ).change( {self: this, dopId: dopId}, function ( e ) {
            $( this ).val( self.__proceedStringToSizeArray( $( this ).val() ) );
            self._blockRecalMainParam.call( this, e );
        } ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Ламинация:' ) ).append( $( '<span>' ).append( $( '<input>' ).attr( {
            type: 'checkbox',
            checked: self.__block4Block[key].isPaketLaminat ? true : false
        } ).change( {self: this, dopId: dopId}, function ( e ) {
            if ( $( this ).attr( 'checked' ) ) {
                $( this ).removeAttr( 'checked' );
                self.__block4Block[key].isPaketLaminat = false;
            } else {
                $( this ).attr( 'checked', true );
                self.__block4Block[key].isPaketLaminat = true;
            }
            self._blockProceed( dopId, self.__block4Block[key] );
            self._blockRecalMainParam.call( this, e );
        } ).val( self.__block4Block[key].isPaketLaminat ? 1 : 0 ) ) ) );
        tbd.append( $( '<div>' ).append( $( '<div>' ).text( 'Кол-во блоков:' ) ).append( $( '<span>' ).append( this._createInput( 'num_of_printing_block' + dopId, {readonly: 'readonly'} ).val( this.options.form.fields['num_of_printing_block' + dopId] ) ) ) );
        this.__block4Recal();
    },
};

