/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var stones=stones||{};
stones.statistic=stones.statistic||{};

stones.statistic.graphicsCanvas=function(options){
    this.summColumnName=null;
    this.secondDataPrepare=null;
    this.dateOptions=null;
    this.title=null;
    this.year = null;
    this.chartContainerId=null;
    this.data=null;
    stones.baseComponent.call(this,options,['summColumnName','secondDataPrepare','dateOptions','year','chartContainerId','data']);
    
    this.secondData=[];
    this.chartGR=null;
    
    this.initAll();
    this.optionsGR=this._optionsGR2();
    this.optionsGR.title.text=this.title?this.title:"Сумма заказов за " + this.year + 'г.';
    console.log(this.toString()+' init.');
}

stones.statistic.graphicsCanvas.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.statistic.graphicsCanvas.prototype, 'constructor', { 
    value: stones.statistic.graphicsCanvas, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

stones.statistic.graphicsCanvas.prototype.toString=function(){
    return '[stones.statistic.graphicsCanvas Object]';
};

stones.statistic.graphicsCanvas.prototype.toggleDataSeries = function ( e ) {
    if ( typeof ( e.dataSeries.visible ) === "undefined" || e.dataSeries.visible ) {
        e.dataSeries.visible = false;
    } else {
        e.dataSeries.visible = true;
    }
    e.chart.render( );
};

stones.statistic.graphicsCanvas.prototype.initAll=function(){
    let self=this;
    $.each(this.data, function ( keyM, val ) {
        let from = this.from;
        if ( $.type( this.query ) === 'array' ) {
            for ( let i = 0; i < self.secondDataPrepare.secondDataDirt.length; i++ ) {
                if ( $.type( this.query[i] ) != 'undefined' ) {
                    self.secondDataPrepare.secondDataDirt[self.secondDataPrepare.secondDataPtr[this.query[i].ourmanager_id]].dataPoints[keyM] = {
                        label: new Date( from ).toLocaleDateString( 'ru-RU', self.dateOptions ), //tmp[i].name, //new Date( this.from ).toLocaleDateString( 'ru-RU', dateOptions ),
                        y: this.query[i][self.summColumnName] ? parseInt( this.query[i][self.summColumnName] ) : 0,
                        name: this.query[i].manger_name,
                        indexLabelOrientation: "vertical", indexLabelPlacement: "inside"
                                //x: keyM
                    };
                }
            }
        }
    } );
   $.each( self.secondDataPrepare.secondDataDirt, function ( ) {
        let y = 0;
        $.each( this.dataPoints, function ( ) {
            y += this.y;
        } );
        if ( y ){
            self.secondData[self.secondData.length] = this;
		}
    } );
};

stones.statistic.graphicsCanvas.prototype.getSecondData = function ( sData ) {
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


stones.statistic.graphicsCanvas.prototype._optionsGR2 = function(){ 
    return {
        theme: "light2",
        height:700,
        title: {
            text: "Заголовок",
            fontSize:26
        },
        toolTip: {
            shared: true,
        },
        axisX: {
                titleFontColor: "#C24642",
                //lineColor: "#C24642",
                //tickColor: "#C24642",
                labelFontColor: "#C24642",
                tickLength: 65,
                //valueFormatString: "000###,#0",
                labelFontSize:19,
                tickColor:'#C24642',
//                labelTextAlign:'center'
                
            },            
        axisX2: {
                title: 'Месяца',
                tickLength: 8,
                titleFontSize:18,
                labelFontSize:16
            },
        axisY: {
            title: 'Руб',
            viewportMinimum: 250,
            labelFontSize:16,
            titleFontSize:18
        },
        legend: {
            horizontalAlign: 'right',
            verticalAlign: 'top',
            cursor: "pointer",
            fontSize:18,
            itemclick: this.toggleDataSeries
        },
        data: this.getSecondData( this.secondData )
    };
};

stones.statistic.graphicsCanvas.prototype.run=function(){
    let w=Math.ceil($('#statistic-cont').width()/100*95);
    this.optionsGR.width=w;
    this.chartGR=new CanvasJS.Chart( this.chartContainerId, this.optionsGR);
    this.chartGR.render();
};