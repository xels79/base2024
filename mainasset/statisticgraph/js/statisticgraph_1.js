/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var stones=stones||{};
stones.statistic=stones.statistic||{};

stones.statistic.graphics=function(options){
    this.chartContainer=null;
    this.colors = [
        '#A0FFA0',
        '#0FF0F0',
        '#A0A0FF',
        '#FFC0C0',
        '#FFC0F0',
        '#FFF000',
        '#0808F0',
        '#F0A080',
        '#0C30F0',
        '#0FF0F0',
        '#F0F0F0',
        '#FFF0F0',
        '#FFF0F0',
        '#FFF0F0',
        '#00F0F0',
    ];
    this.secondData = [ ];this.secondData2 = [ ];this.firstDP0 = [ ];this.firstDP1 = [ ];this.dateOptions = {month: 'long'};this.year = '';
    this.secondDataDirt = [ ];this.secondDataDirt2 = [ ];this.secondDataPtr = [ ];
    this.test=null;
    this.managers_exists=null;
    this.total=null;
    stones.baseComponent.call(this,options);
    if (!this.test){
        console.error('stones.statistic.graphics - не передан обязательный параметр: "test"');
        return this;
    }
    if (!this.managers_exists){
        console.error('stones.statistic.graphics - не передан обязательный параметр: "managers_exists"');
        return this;
    }
    if (!this.total){
        console.error('stones.statistic.graphics - не передан обязательный параметр: "total"');
        return this;
    }
    if (!this.chartContainer){
        console.error('stones.statistic.graphics - не верный параметр: "chartContainer"');
        return this;
    }
    this.initAll();
    let tmpGRD=this._optionsGR2();
    this.optionsGR=[this._optionsGR(),tmpGRD,$.extend(true,{},tmpGRD)];
    this.optionsGR[2].data=this.getSecondData( this.secondData2 );
    this.optionsGR[2].title.text="Сумма прибыли за " + this.year + 'г.';
    this.chartGR=[];
    console.log(this,'Init ok');
}

stones.statistic.graphics.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.statistic.graphics.prototype, 'constructor', { 
    value: stones.statistic.graphics, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

stones.statistic.graphics.prototype.toggleDataSeries = function ( e ) {
    if ( typeof ( e.dataSeries.visible ) === "undefined" || e.dataSeries.visible ) {
        e.dataSeries.visible = false;
    } else {
        e.dataSeries.visible = true;
    }
    e.chart.render( );
};

stones.statistic.graphics.prototype.initAll=function(){
    let self=this;
    $.each(this.test, function ( ) {
        let tmpD = $.type( this.query ) === 'array' ? ( this.query[0] ? this.query[0] : [ ] ) : this.query;
        self.firstDP0[self.firstDP0.length] = {
            label: new Date( this.from ).toLocaleDateString( 'ru-RU', self.dateOptions ),
            y: tmpD.total1 ? parseInt( tmpD.total1 ) : 0
        };
        self.firstDP1[self.firstDP1.length] = {
            label: new Date( this.from ).toLocaleDateString( 'ru-RU', self.dateOptions ),
            y: tmpD.total1_profit ? parseInt( tmpD.total1_profit ) : 0
        };
        if ( !self.year )
            self.year = new Date( this.from ).toLocaleDateString( 'ru-RU', {year: 'numeric'} );
    } );
    $.each(this.managers_exists, function ( key, val ) {
        self.secondDataPtr[val.id] = key;
        self.secondDataDirt[key] = {
            type: "column",
            yValueFormatString: "#,##0", // - " + val.realname + "'",
            indexLabel: "{y}",
            color: self.colors[key],
            indexLabelFontSize: 14,
            axisXType: "secondary",
            toolTipContent: "<span style='\"'color: {color};'\"'>{name}</span>: {y}р.",
            indexLabelFormatter: function ( e ) {
                return   CanvasJS.formatNumber( e.dataPoint.y, "#,#00'р.'" ) + " - " + e.dataPoint.name;
            },
            dataPoints:(function(){
                let rv=[];
                for (let i=1;i<=12;i++){
                    rv.push({
                        y:null,
                        label:new Date( "1988-"+(i<10?("0"+i):i)+"-01" ).toLocaleDateString( 'ru-RU', self.dateOptions )
                    });
                }
                return rv;
            }()),
            showInLegend: true,
            legendText: val.realname,
        };
        self.secondDataDirt2[key]=$.extend(true,{},self.secondDataDirt[key]);
    } );
    $.each(this.total, function ( keyM, val ) {
        let from = this.from;
        if ( $.type( this.query ) === 'array' ) {
            for ( let i = 0; i < self.secondDataDirt.length; i++ ) {
                if ( $.type( this.query[i] ) != 'undefined' ) {
                    self.secondDataDirt[self.secondDataPtr[this.query[i].ourmanager_id]].dataPoints[keyM] = {
                        label: new Date( from ).toLocaleDateString( 'ru-RU', self.dateOptions ), //tmp[i].name, //new Date( this.from ).toLocaleDateString( 'ru-RU', dateOptions ),
                        y: this.query[i].total1 ? parseInt( this.query[i].total1 ) : 0,
                        name: this.query[i].manger_name,
                        indexLabelOrientation: "vertical", indexLabelPlacement: "inside"
                                //x: keyM
                    };
                     self.secondDataDirt2[self.secondDataPtr[this.query[i].ourmanager_id]].dataPoints[keyM] = {
                        label: new Date( from ).toLocaleDateString( 'ru-RU', self.dateOptions ), //tmp[i].name, //new Date( this.from ).toLocaleDateString( 'ru-RU', dateOptions ),
                        y: this.query[i].total1_profit ? parseInt( this.query[i].total1_profit ) : 0,
                        name: this.query[i].manger_name,
                        indexLabelOrientation: "vertical", indexLabelPlacement: "inside"
                                //x: keyM
                    };
                }
            }
        }
    } );
   $.each( self.secondDataDirt, function ( ) {
        let y = 0;
        $.each( this.dataPoints, function ( ) {
            y += this.y;
        } );
        if ( y ){
            self.secondData[self.secondData.length] = this;
		}
    } );
    $.each( self.secondDataDirt2, function ( ) {
        let y = 0;
        $.each( this.dataPoints, function ( ) {
            y += this.y;
        } );
        if ( y ){
            self.secondData2[self.secondData2.length] = this;
		}
    } );
    this.initPjax();
    console.log( 'stones.statistic.graphics:init_data', this.firstDP0, this.firstDP1, this.secondData, this.secondData2 );
};

stones.statistic.graphics.prototype.proceedPjaxCanvas=function(idTxt){
    let test=JSON.parse($('#chartContainer4').attr('data-value'));
    /*
    let eng2;

    eng2=new stones.statistic.graphics({
        chartContainer:["chartContainer","chartContainer4","chartContainer5"],
        test:<?= yii\helpers\Json::encode($test) ?>,
        managers_exists:<?= yii\helpers\Json::encode($managers_exists) ?>,
        total:<?= yii\helpers\Json::encode($total) ?>
    });
    eng2.run();

     */
    console.log(dt);
};

stones.statistic.graphics.prototype.initForm=function(){
    $('#graphic-year').change(function(){
        $('#graphic-form').submit();
    });        
};

stones.statistic.graphics.prototype.initPjax=function(){
    let self=this;
    $(document).on('pjax:success', function() {
        self.initForm();
        self.proceedPjaxCanvas();
        //self.proceedPjaxCanvas('chartContainer5');
        //chartContainer4
    });
    self.initForm();
};

stones.statistic.graphics.prototype.getSecondData = function ( sData ) {
    let total = 0;
    let second = {
        type: "splineArea",
        color: "rgba(54,158,173,.15)",
        markerBorderColor: "gray",
        markerSize: 10,
        markerBorderThickness: 3,
        indexLabel: "",
        yValueFormatString: "#,##0",
        axisXType: "primary",
        showInLegend: true,
        legendText: "Общая",
        axisXIndex: 1,
        dataPoints: [ ],
        toolTipContent: "За {name}: {label}",

    }
    for ( let i = 0; i < 12; i++ ) {
        total = 0;
        for ( let n in sData ) {
            if ( typeof sData[n].dataPoints[i] !== 'undefined' ) {
                total += sData[n].dataPoints[i].y;
            }
        }
        second.dataPoints[ second.dataPoints.length ] = {
            label: CanvasJS.formatNumber( total, "#,#00'р.'" ),
            y: total != 0 ? total : null,
            name: new Date( "1988-"+(( i + 1 )<10?("0"+( i + 1 )):( i + 1 )) + "-01" ).toLocaleDateString( 'ru-RU', this.dateOptions )
        };
    }
    sData[sData.length] = second;
    return sData;
    //}
};


stones.statistic.graphics.prototype._optionsGR=function(){
    return {
        animationEnabled: true,
        title: {
            text: "Статистика по менеджеру за  " + this.year + 'г.'
        },
        axisX: {
            title: 'Месяца',
        },
        axisY: {
            title: 'Руб',
            viewportMinimum: 250,
        },
        legend: {
            horizontalAlign: 'right',
            verticalAlign: 'top'
        },
        data: [
            {
                type: "column",
                yValueFormatString: "#,##0",
                indexLabel: "{y}",
                color: "#546BC1",
                dataPoints: this.firstDP0,
                showInLegend: true,
                legendText: 'Сумма',
            },
            {
                type: "column",
                yValueFormatString: "#,##0",
                indexLabel: "{y}",
                color: "#FF6BC1",
                dataPoints: this.firstDP1,
                showInLegend: true,
                legendText: 'Прибыль'
            },
        ]
    };
};

stones.statistic.graphics.prototype._optionsGR2 = function(){ 
    return {
        theme: "light2",
        title: {
            text: "Сумма заказов за " + this.year + 'г.'
        },
        toolTip: {
            shared: true,
        },
        axisX2: [
            {
                title: 'Месяца',
                tickLength: 15,
                valueFormatString: "000###,#0",
            },
            {
                titleFontColor: "#C24642",
                lineColor: "#C24642",
                tickColor: "#C24642",
                labelFontColor: "#C24642",

            },
        ],
        axisY: {
            title: 'Руб',
            viewportMinimum: 250,
        },
        legend: {
            horizontalAlign: 'right',
            verticalAlign: 'top',
            cursor: "pointer",
            itemclick: this.toggleDataSeries
        },
        data: this.getSecondData( this.secondData )
    };
};

stones.statistic.graphics.prototype.run=function(){
    if (this.chartContainer.length<this.optionsGR.length){
        console.error('stones.statistic.graphics.run chartContainer id меньше 3');
        return;
    }
    for (let i=0;i<3;i++){
        let tmp=new CanvasJS.Chart( this.chartContainer[i], this.optionsGR[i]);
        this.chartGR.push(tmp);
        tmp.render();
    }
    this.selFReStart( );
};

stones.statistic.graphics.prototype.sendData = function ( self ) {
    let fd = new FormData( /*$('#select-form').get(0)*/ );
    let tmp = URI.parse( window.location.href );
    let query = URI.parseQuery( tmp.query );
    let newUrl = URI( {
        hostname: tmp.hostname,
        path: tmp.path,
        protocol: tmp.protocol,
    } );
    newUrl.setSearch( 'r', query.r );
    console.log( newUrl.toString( ) );
    if ( $.type( $( this ).attr( 'name' ) ) !== 'undefined' ) {
        if ( $.type( $( this ).attr( 'checked' ) ) !== 'undefined' ) {
            $( this ).removeAttr( 'checked' );
        } else {
            $( this ).attr( 'checked', true );
        }
    }
    $( '#select-form' ).find( '[type="checkbox"],[type="button"]' ).each( function ( id, val ) {
        if ( val.checked ) {
            fd.append( $( this ).attr( 'name' ), 'on' );
        }
    } );
    $.each( $( '[type=checkbox]' ), function ( ) {
        if ( $.type( $( this ).attr( 'checked' ) ) !== 'undefined' ) {
            newUrl.setSearch( $( this ).attr( 'name' ), 'on' );
        }
    } );
    window.history.pushState( null, "Title", newUrl.toString( ) );
    $.ajax( {
        type: 'post',
        url: $( this ).attr( 'action' ),
        data: fd,
        cache: false,
        contentType: false,
        processData: false,
        forceSync: false,
        success: function ( data ) {
            if ( data.test ) {
                let i = 0;
                $.each( data.test, function ( ) {
                    let tmpD = $.type( this.query ) === 'array' ? ( this.query[0] ? this.query[0] : [ ] ) : this.query;
                    self.chartGR[0].options.data[0].dataPoints[i].y = tmpD.total1 ? parseInt( tmpD.total1 ) : 0;
                    self.chartGR[0].options.data[1].dataPoints[i++].y = tmpD.total1_profit ? parseInt( tmpD.total1_profit ) : 0;
                } );
                console.log( data, self.chartGR[0] );
                self.chartGR[0].options.animationEnabled = true;
                self.chartGR[0].render( );
            }
        }
    } );
};

stones.statistic.graphics.prototype.selFReStart = function ( ) {
    let selfM=this;
    $( '#select-form' ).find( 'ul' ).find( 'a' ).click( function ( e ) {
        selfM.sendData.call( this, selfM );
    } );
    $( '#select-form' ).find( '[type="checkbox"],[type="button"]' ).click( function ( e ) {
        if ( $( this ).attr( 'data-key' ) === '0' )
            return;
        let self = this;
        $( '#select-form' ).find( '[type="checkbox"],[type="button"]' ).each( function ( ) {
            if ( this !== self && ( $( this ).attr( 'checked' ) === 'checked' || this.checked ) ) {
                $( this ).prop( 'checked', false ).val( '' );
                $( this ).removeAttr( 'checked' );
                console.log( 'doUncheck', this );
            }
        } );
        console.log( this );
        console.log( $( this ).get( 0 ).checked, $( this ).attr( 'checked' ) );
        if ( $( this ).get( 0 ).checked && !$( this ).attr( 'checked' ) ) {
            $( this ).attr( 'checked', true );
        } else if ( $( this ).attr( 'checked' ) ) {
            e.preventDefault( );
        }
        selfM.sendData.call( this, selfM );
    } );
}

