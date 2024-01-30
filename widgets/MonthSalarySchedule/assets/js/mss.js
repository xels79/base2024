/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var stones=stones||{};
stones.widgets=stones.widgets||{};
stones.widgets.MSS=function(options){
    this.widgetId=null;
    this.chartId=null;
    this.selectId=null;
    this.data=null;
    this.year='';
    this.chrat=null;
    this.month=[
        'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
    ];
    stones.baseComponent.call(this,options,["widgetId","chartId","data","selectId"]);
    jQuery( '#'+ this.selectId ).change({self:this},this.yaerChange);
    this.start();
    console.log('MSS',this);
};
stones.widgets.MSS.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.widgets.MSS.prototype, 'constructor', { 
    value: stones.widgets.MSS, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

stones.widgets.MSS.prototype.start=function(){
    let self=this;
    this.chart = new CanvasJS.Chart(this.chartId, {
	animationEnabled: true,
	title:{
		text: "Зарплата за "+this.year
	},	
	axisY: {
		title: "Рубли",
		titleFontColor: "#4F81BC",
		lineColor: "#4F81BC",
		labelFontColor: "#4F81BC",
		tickColor: "#4F81BC",
                interval: 10000
	},
	toolTip: {
            shared: false,
            contentFormatter: function(e){
              return ( self.month[e.entries[0].dataPoint.x] + ' - ' + e.entries[0].dataSeries.name + " " + e.entries[0].dataPoint.y + "" ) ;
            },
	},
        axisX:{
            labelFormatter: function ( e ) {
                   return self.month[e.value] ;  
            }
        },
        data: this.data,
	legend: {
		cursor:"pointer",
		itemclick: this.toggleDataSeries
	}
    });
    this.chart.render();

};

stones.widgets.MSS.prototype.toggleDataSeries=function(e){
    if (e.dataSeries.name==="Все1" || e.dataSeries.name==="Все2" || e.dataSeries.name==="Все3"){
        for(let el in e.chart.data){
            if (e.chart.data[el].name!=="Все1" && e.chart.data[el].name!=="Все2" && e.chart.data[el].name!=="Все3"){
                if (e.dataSeries.name==="Все1"){
                    e.chart.data[el].set('visible',true);
                }else if (e.dataSeries.name==="Все2"){
                    e.chart.data[el].set('visible',false);
                }else{
                    e.chart.data[el].set('visible',!e.chart.data[el].get('visible'));
                }
                //e.chart.legend.dataSeries[el].visible=false;//!e.chart.data[el].visible;
            }
        }
    }else{
        if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
        }
        else {
                e.dataSeries.visible = true;
        }
    }
    e.chart.render();
};

stones.widgets.MSS.prototype.yaerChange=function(e){
    console.log(jQuery(this).val());
    let self=e.data.self;
    let tmpURL = URI( window.location.href );
    let varName=jQuery(this).attr('name');
    let postOption={ mss:self.widgetId };
    postOption[varName]=jQuery(this).val();
    tmpURL.setSearch( 'mss', self.widgetId );
    tmpURL.setSearch( varName, postOption[varName] );
    window.history.pushState( null, "Title", tmpURL.toString( ) );
    
    jQuery.post(tmpURL.toString( ), postOption ).done(function(dt){
        console.log(dt);
        if (dt.status==='ok' && dt.widgetId===self.widgetId){
            while(self.data.length){
                self.data.shift();
            }
            for (let i in dt.data){
                self.data.push(dt.data[i]);
            }
            self.chart.render();
        }else{
            console.error("Неверный ответ сервера!");
        }
    });
};