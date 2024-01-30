/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

let idCode = 0;
( function ( $ ) {
    $.widget( "custom.timePicker", $.Widget, {
        inputId: '',
        selectId: '',
        panel: null,
        H_panel: null,
        M_panel: null,
        oldH: 0,
        oldM: 0,
        timerH: 0,
        timerM: 0,
        stepH: 0,
        stepM: 0,
        lastOfMin: 0,
        options: {
            id: '',
            hour: false,
            minutes: false,
            defaultClass: 'time-picker',
            step: 1,
            hourFrom: 0,
            hourTo: 23,
            startValue: '',
        },
        _create: function () {
            this._super();
            if ( this.element.attr( 'id' ) ) {
                this.inputId = this.element.attr( 'id' );
            } else {
                this.element.attr( 'id', 'timePickerInput' + idCode );
            }
            if ( this.options.id )
                this.selectId = this.options.id;
            else {
                this.selectId = 'timePickerSelect' + idCode;
                this.options.id = 'timePickerSelect' + idCode++;
            }
            for ( let i = 0; i < 59; i += this.options.step ) {
                if ( i < 59 )
                    this.lastOfMin = i;
            }
            if ( this.element.val().length )
                this.element.val( this._creatValue( this._parseString( this.element.val() ) ) );
            this.element.focusout( {self: this}, this._fOut );
            this.element.keydown( {self: this}, this._kDown );
            this.element.mouseup( {self: this}, this._kMousUp );
            this.element.focus( {self: this}, this._kFocus );
        },
        _creatValue: function ( v ) {
            let rVal = '';
            if ( v.hour > this.options.hourTo ) {
                v.hour = this.options.hourTo
            } else if ( v.hour < this.options.hourFrom ) {
                v.hour = this.options.hourFrom;
            }
            if ( v.hour < 10 ) {
                rVal += '0' + v.hour;
            } else {
                rVal += v.hour;
            }
            rVal += ':';
            if ( v.minutes < 10 ) {
                rVal += '0' + v.minutes;
            } else {
                rVal += v.minutes;
            }
            return rVal;
        },
        _parseString: function ( str ) {
            str = str ? str : '00:00';
            let vH = 0, vM = 0, sepPos = str.indexOf( ':' );
            if ( sepPos > -1 ) {
                vH = parseInt( str.substr( 0, sepPos ) );
                vH = !isNaN( vH ) ? ( vH < 24 ? vH : 23 ) : 0;
                vM = parseInt( str.substr( sepPos + 1 ) );
                vM = !isNaN( vM ) ? ( vM < 60 ? vM : 59 ) : 0;

            } else {
                vH = parseInt( str );
                vH = !isNaN( vH ) ? ( vH < 24 ? vH : 23 ) : 0;
            }
            return {hour: vH, minutes: vM};
        },
        _fOut: function ( e ) {
            let self = e.data.self;
            console.log( e );
            if ( $( e.relatedTarget ).attr( 'id' ) !== self.selectId ) {
                self._removePanel();
            }
        },
        _kDown: function ( e ) {
            let self = e.data.self;
            let k = e.key, cod = e.keyCode, val = $( this ).val(), el = $( this ).get( 0 );
            let cursorPos = val.slice( 0, el.selectionEnd ).length;
            if ( k != 'Backspace' && k != 'Tab' && k != 'Enter' && k != 'ArrowLeft' && k != 'ArrowRight' && k != 'Home' && k != 'End' && k != 'ArrowUp' && k != 'ArrowDown' ) {
                if ( cod > 47 && cod < 58 ) {
                    if ( cursorPos == 0 ) {
                        el.setSelectionRange( 1, 1 );
                        cursorPos = 1;
                    }
                    if ( cursorPos > 5 ) {
                        el.setSelectionRange( 5, 5 );
                        cursorPos = 5;
                    }
                    if ( cursorPos == 3 ) {
                        el.setSelectionRange( 4, 4 );
                        cursorPos = 4;
                    }
                    if ( cursorPos <= 5 ) {
                        console.log( val.substr( 0, cursorPos - 1 ), k, val.substr( cursorPos ) );
                        let valO = self._parseString( val.substr( 0, cursorPos - 1 ) + k + val.substr( cursorPos ) );
                        valO.minutes = self._checkMinutesVal( valO.minutes );
                        $( this ).val( self._creatValue( valO ) );
                        if ( cursorPos < 5 )
                            cursorPos++;
                        if ( cursorPos == 3 )
                            cursorPos++;
                        el.setSelectionRange( cursorPos - 1, cursorPos );
                        self._panelHTo( valO.hour );
                        self._panelMTo( valO.minutes );
                    }
                }
                e.preventDefault();
            } else if ( k == 'ArrowRight' ) {
                if ( cursorPos < 5 ) {
                    if ( ++cursorPos == 3 )
                        cursorPos++;
                    el.setSelectionRange( cursorPos - 1, cursorPos );
                }
                e.preventDefault();
            } else if ( k == 'Backspace' || k == 'ArrowLeft' ) {
                if ( cursorPos > 1 ) {
                    if ( --cursorPos == 3 )
                        cursorPos--;
                    el.setSelectionRange( cursorPos - 1, cursorPos );
                }
                e.preventDefault();
            } else if ( k == 'ArrowUp' || k == 'ArrowDown' ) {
                let incr = k == 'ArrowUp' ? 1 : -1;
                let tmp = self._parseString( val );
                let step = self._getIncriseStep();
                if ( cursorPos > 0 && cursorPos < 3 ) {
                    tmp.hour += incr;
                    if ( tmp.hour > self.options.hourTo )
                        tmp.hour = self.options.hourFrom;
                    if ( tmp.hour < self.options.hourFrom )
                        tmp.hour = self.options.hourTo;
                    self._panelHTo( tmp.hour );
                } else if ( cursorPos > 3 && cursorPos < 6 ) {
                    tmp.minutes += incr * step;
                    if ( tmp.minutes > 59 )
                        tmp.minutes = 0;
                    if ( tmp.minutes < 0 )
                        tmp.minutes = self.lastOfMin;
                    self._panelMTo( tmp.minutes );
                }
                if ( cursorPos != 3 ) {
                    $( this ).val( self._creatValue( tmp ) );
                    el.setSelectionRange( cursorPos - 1, cursorPos );
                }
                e.preventDefault();
            }
        },
        _kMousUp: function ( e ) {
            let val = $( this ).val();
            let el = $( this ).get( 0 );
            let cursorPos = val.slice( 0, el.selectionEnd ).length;
            if ( cursorPos == 0 || cursorPos == 3 )
                cursorPos++;
            console.log( val.slice( 0, el.selectionStart ).length );
            el.setSelectionRange( cursorPos - 1, cursorPos );

        },
        _kFocus: function ( e ) {
            let self = e.data.self;
            if ( !self.element.val().length ) {
                self.element.val( self._creatValue( self._parseString( self.options.startValue ) ) );
            }
            self._showSelectPanel();
            self._fitPanel();
        },
        _range: function ( start, end, step ) {
            step = $.type( step ) === 'number' ? step : 1;
            let size = Math.floor( ( end - start ) / step );
            return [ ...Array( size ).keys() ].map( i => i * step );
        },
        _checkMinutesVal: function ( min ) {
            let actuals = this._range( 0, 60, this._getIncriseStep() )
            console.log( actuals, min );
            while ( actuals.indexOf( min ) === -1 ) {
                console.log( min );
                min++;
                if ( min === 60 )
                    min = 0;
            }
            return min;
        },
        _getIncriseStep: function () {
            switch ( this.options.step ) {
                case 5:
                    return 5;
                    break;
                case 10:
                    return 10;
                    break;
                case 15:
                    return 15;
                    break;
                case 30:
                    return 30;
                    break;
                default:
                    return 1;
                    break;
            }
        },
        _removePanel: function () {
//            return;
            if ( this.timerH )
                clearInterval( this.timerH );
            if ( this.timerM )
                clearInterval( this.timerM );
            this.panel.remove();
            delete ( this.panel );
            this.panel = null;
            this.timerM = 0;
            this.timerH = 0;
            this.oldH = 0;
            this.oldM = 0;
        },
        _fitPanel: function () {
            let tmp = this._parseString( this.element.val() );
            this._panelHTo( tmp.hour );
            this._panelMTo( tmp.minutes );
        },
        _colorazeItems: function ( p ) {
            let topP = p.parent().offset().top + p.parent().innerHeight() / 2 - 10;
            console.log( '_colorazeItems' );
            p.children( '[data-key]' ).each( function () {
                let el = $( this ), topE = el.offset().top;
                let opc = ( topE > topP ? topP / topE : topE / topP );
                opc = opc > 0.95 ? 1 : ( opc > 0.7 ) ? ( opc / 2 ).toPrecision( 2 ) : ( opc / 3 ).toPrecision( 2 );
                if ( topE > topP - 10 && topE < topP + 10 ) {
                    el.addClass( 'step3' );
                    el.removeClass( 'step1 step2' )
                } else if ( topE > topP - 30 && topE < topP + 40 ) {
                    el.addClass( 'step2' );
                    el.removeClass( 'step1 step3' )
                } else if ( topE > topP - 50 && topE < topP + 70 ) {
                    el.addClass( 'step1' );
                    el.removeClass( 'step2 step3' );
                } else {
                    el.removeAttr( 'class' );
                }
                el.css( 'opacity', opc );
                console.log( );
            } );
        },
        _panelTo: function ( val, key ) {
            let self = this;

            if ( val > this['old' + key] ) {
                this['step' + key] = -4;
            } else if ( val < this['old' + key] ) {
                this['step' + key] = 4;
            } else {
                this['step' + key] = 0;
            }
            console.log( val, this['old' + key], this['step' + key] );
            let p = self[key + '_panel'].children( ':first-child' );
            if ( this['step' + key] ) {
                if ( this['timer' + key] ) {
                    clearInterval( this['timer' + key] );
                    this['timer' + key] = 0;
                }
                this['timer' + key] = setInterval( function () {

                    if ( !p.length ) {
                        clearInterval( self['timer' + key] );
                        self['timer' + key] = 0;
                        return;
                    }
                    let top = p.position().top;
                    let toPos = key === 'M' ? ( ( val / self._getIncriseStep() ) * 20 ) : ( val * 20 );
                    if ( Math.abs( Math.abs( top ) - val * 20 ) < 120 && Math.abs( self['step' + key] ) === 4 )
                        self['step' + key] = self['step' + key] / 2;
                    if ( Math.abs( Math.abs( top ) - val * 20 ) < 50 && Math.abs( self['step' + key] ) === 2 )
                        self['step' + key] = self['step' + key] / 2;
//                    console.log('step',top,-1*val*20);
                    if ( ( top > -1 * toPos && self['step' + key] < 0 ) || ( top < -1 * toPos && self['step' + key] > 0 ) ) {
                        p.css( {
                            top: top + self['step' + key]
                        } );
                    } else {
                        clearInterval( self['timer' + key] );
                        self['timer' + key] = 0;
                        self['old' + key] = val;
                        let tmpV = self._parseString( self.element.val() );
                        if ( key === 'H' ) {
                            tmpV.hour = val;
                        } else {
                            tmpV.minutes = val;
                        }
                        self.element.val( self._creatValue( tmpV ) );
                    }
                    self._colorazeItems( p );
                }, 10 );
            } else {
                self._colorazeItems( p );
            }
        },
        _panelHTo: function ( val ) {
            this._panelTo( val, 'H' );
        },
        _panelMTo: function ( val ) {
            this._panelTo( val, 'M' );
        },
        _drawPanelContent: function ( cnt, key, step, from ) {
            let tmp0 = $( '<div>' ), self = this;
            from = from ? from : 0;
            step = step ? step : 1;
            for ( let i = 0; i < cnt + 5; ) {
                let tmp = $( '<div>' );
                if ( i > 3 ) {
                    let v = i - 4;
                    if ( v >= from ) {
                        tmp.attr( 'data-key', v )
                        if ( v == cnt - step || v == 0 ) {
                            tmp.append( $( '<div>' ) ).append( $( '<span>' ) ).append( $( '<div>' ).text( v < 10 ? ( '0' + v ) : v ) );
                        } else {
                            tmp.append( $( '<div>' ) ).append( $( '<div>' ) ).append( $( '<div>' ).text( v < 10 ? ( '0' + v ) : v ) );
                        }
                        tmp.click( {val: v, key: key}, function ( e ) {
                            self._panelTo( e.data.val, e.data.key );
                        } );
                        i += step;
                        tmp0.append( tmp );
                    } else {
                        i += step;
                        tmp0.append( tmp );
                    }
                } else {
                    i++;
                    tmp0.append( tmp );
                }
            }
            this[key + '_panel'].append( tmp0 );
        },
        _showSelectPanel: function () {
            let elPos = this.element.offset(), self = this;
            let wellHT = -1, wellMT = -1;
            this.panel = $( '#' + this.selectId );
            if ( !this.panel.length ) {
                this.H_panel = $( '<div>' ).bind( 'mousewheel', function ( e ) {
                    let delta = e.originalEvent.wheelDelta / 120;
                    if ( wellHT === -1 )
                        wellHT = self.oldH;
                    if ( delta > 0 ) {
                        if ( wellHT + 1 <= self.options.hourTo )
                            self._panelHTo( ++wellHT );
                    } else if ( delta < 0 ) {
                        if ( wellHT - 1 >= self.options.hourFrom )
                            self._panelHTo( --wellHT );
                    }
                    e.preventDefault();
                } );
                this.M_panel = $( '<div>' ).bind( 'mousewheel', function ( e ) {
                    let delta = e.originalEvent.wheelDelta / 120;
                    if ( wellMT === -1 )
                        wellMT = self.oldM;
                    if ( delta > 0 ) {
                        if ( wellMT + self._getIncriseStep() < 60 ) {
                            wellMT += self._getIncriseStep();
                            self._panelMTo( wellMT );
                        }
                    } else if ( delta < 0 ) {
                        if ( wellMT - self._getIncriseStep() >= 0 ) {
                            wellMT -= self._getIncriseStep();
                            self._panelMTo( wellMT );
                        }
                    }
                    e.preventDefault();
                } );
                this.panel = $( '<div>' ).addClass( 'time-picker' ).offset( {
                    top: elPos.top + this.element.outerHeight(),
                    left: elPos.left
                } ).attr( {
                    id: this.selectId,
                    tabindex: "0"
                } ).append( this.H_panel ).append( $( '<div>' ).append( $( '<div>' ) ).append( $( '<div>' ) ).append( $( '<div>' ) ) ).append( this.M_panel ).appendTo( 'body' );
                this.panel.focusout( function ( e ) {
                    if ( $( e.relatedTarget ).attr( 'id' ) !== self.inputId ) {
                        self._removePanel();
                    }
                } );
                self._drawPanelContent( self.options.hourTo, 'H', 1, self.options.hourFrom );
                self._drawPanelContent( 59, 'M', this._getIncriseStep() );
            }
            console.log( elPos );
        },
    } );
}( jQuery ) );