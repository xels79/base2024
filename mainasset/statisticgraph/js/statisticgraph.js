/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var stones=stones||{};
stones.statistic=stones.statistic||{};


$(window).on('beforeunload',function(){
    $('body').append($('<div>').attr({
        id:'gr-bunner'
    }).css({
        position:'absolute',
        top:0,left:0,
        width:$(document).width(),
        height:$(document).height(),
        cursor:'wait',
        'background-color':'gray',
        opacity:0.05,
    }));
    
});


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
    this.firstDP0 = [ ];this.firstDP1 = [ ];this.dateOptions = {month: 'long'};this.year = '';
    this.test=null;
    this.managers_exists=null;
    this.total=null;
    this.columnType=null;
    stones.baseComponent.call(this,options,["test","managers_exists","total","chartContainer","year","columnType"]);
    if (this.chartContainer.length<3){
        console.error('stones.statistic.graphics.run chartContainer id меньше 3');
        return;
    }
    this.initAll();
    this.chartGR=[];
    console.log(this.toString()+' init.');
    $('#gr-bunner').remove();
}

stones.statistic.graphics.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.statistic.graphics.prototype, 'constructor', { 
    value: stones.statistic.graphics, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

stones.statistic.graphics.prototype.toString=function(){
    return '[stones.statistic.graphics Object]';
};


stones.statistic.graphics.prototype.computeManager=function(managers_exists){
    let self=this;
    let rVal={
        secondDataPtr:[],
        secondDataDirt:[]
    };
    $.each(managers_exists, function ( key, val ) {
        rVal.secondDataPtr[val.id] = key;
        rVal.secondDataDirt[key] = {
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
    } );
    return rVal;
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
    let cOptions={
            summColumnName:'total1',
            secondDataPrepare:$.extend(true,{},this.computeManager(this.managers_exists)),
            dateOptions:this.dateOptions,
            year:this.year,
            chartContainerId:this.chartContainer[1],
            data:this.total,
    };
    this.optionsGR=[
        this._optionsGR(),
        new stones.statistic.graphicsCanvas($.extend(true,{},cOptions))
    ];
    cOptions.summColumnName='total1_profit';
    cOptions.chartContainerId=this.chartContainer[2];
    cOptions.title="Сумма прибыли за "+this.year+"г.";
    this.optionsGR.push(new stones.statistic.graphicsCanvas($.extend(true,{},cOptions)));
    $('#graphicsMainTabs').children('li').children('a').click(function(){
        let regex=/\d+$/gm;
        let str=$(this).attr('href');
        let tmp=regex.exec(str);
        if (tmp && typeof(tmp)==='object'){
            let tmpURL = URI( window.location.href );
            tmpURL.setSearch( 'graphicsMainTabsTab', tmp[0] );
            window.history.pushState( null, "Title", tmpURL.toString( ) );
//            console.log(tmp[0]);
        }else{
            console.log('Номер не определен');
        }
    });
    this.initPjax();
};

stones.statistic.graphics.prototype.proceedPjaxCanvas=function(idTxt){
    let managers_exists=JSON.parse($('#chartContainer4').attr('data-value'));
    let year=$('#chartContainer4').attr('data-year');
    let data=JSON.parse($('#chartContainer5').attr('data-value'));
    let cOptions={
            summColumnName:'total1',
            secondDataPrepare:$.extend(true,{},this.computeManager(managers_exists)),
            dateOptions:this.dateOptions,
            year:year,
            chartContainerId:'chartContainer4',
            data:data,
    };
    (new stones.statistic.graphicsCanvas($.extend(true,{},cOptions))).run();
    cOptions.summColumnName='total1_profit';
    cOptions.chartContainerId='chartContainer5';
    cOptions.title="Сумма прибыли за "+year+"г.";
    (new stones.statistic.graphicsCanvas($.extend(true,{},cOptions))).run();
};

stones.statistic.graphics.prototype.initForm=function(){
    $('#graphic-year').change(function(){
        if ($(this).val().length){
            $('#graphic-form').submit();
        }
    });
};

stones.statistic.graphics.prototype.formMonthFilterSubmmitClick=function(e){
    let val=$(this).prev().val();
//    e.preventDefault();
//    e.stopPropagation();
//    console.log(val);
    if (val==='on'){
        val='off';
    }else{
        val='on';
    }
    $(this).prev().val(val);
    let tmpURL = URI( window.location.href );
    tmpURL.setSearch( $(this).prev().attr('name'), val );
    window.history.pushState( null, "filter button", tmpURL.toString( ) );
    $(this).parent().parent().parent().attr('action', tmpURL.toString( ));
//    console.log(val);
};
stones.statistic.graphics.prototype.initTabs1=function(){
    $('#Table1Tabs').children('li').children('a').click(function(){
        let regex=/\d+$/gm;
        let str=$(this).attr('href');
        let tmp=regex.exec(str);
        if (tmp && typeof(tmp)==='object'){
            let tmpURL = URI( window.location.href );
            tmpURL.setSearch( 'Table1TabsTab', tmp[0] );
            window.history.pushState( null, "Title", tmpURL.toString( ) );
//            console.log(tmp[0]);
        }else{
            console.log('Номер не определен');
        }
    });
};

stones.statistic.graphics.prototype.Tabs1ChangeYaer=function(){
    let tmpURL = URI( window.location.href );
    //console.log();
    tmpURL.setSearch( $(this).attr('name'), $(this).val() );
    window.history.pushState( null, "Title", tmpURL.toString( ) );
    $('#formTablesSelectYear').attr( 'action',tmpURL.toString( ) );
    $('#formTablesSelectYear').submit();
}

stones.statistic.graphics.prototype.onFormSubmit=function(){
    $(this).attr('action',window.location.href);
};

stones.statistic.graphics.prototype._initSvodnGraph=function(val,colName,year){
    let rVal=[];
    for (let i in val){
        let t=parseInt(i)+1;
        rVal.push({
            x:new Date(year+'-'+(t<10?('0'+t):t)+'-01'),
            y:val[i][colName]
        });
    }
    return rVal;
};

stones.statistic.graphics.prototype.initSvodnGraph=function(){
    let val=JSON.parse($('#svodn-graph').attr('data-value'));
    let chart,self=this;
    let year=$('#svodn-graph').attr('data-year');
    console.log('svod', "Колонки "+this.columnType);
    chart = new CanvasJS.Chart("svodn-graph", {
        width:$('#statistic-cont').width()-80,
	animationEnabled: true,
        zoomEnabled:true,
	title:{
		text: "Сводный график за год.",
                fontSize:20
	},
	axisX: {
            interval: 1,
            intervalType: "month",
            labelFormatter: function ( e ) {
                   return new Date(e.value).toLocaleDateString( 'ru-RU', {month: 'short'} );
             }  

	},
	axisY: {
		title: "Руб",
		suffix: " р"
	},
	legend:{
		cursor: "pointer",
		fontSize: 16,
		itemclick: function(e){
                    if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                            e.dataSeries.visible = false;
                    }
                    else{
                            e.dataSeries.visible = true;
                    }
                    chart.render();
                },
	},
	toolTip:{
		shared: true,
//                contentFormatter: function ( e ) {
//                    console.log(e);
//                    return "Custom ToolTip" +  e.entries[0].dataPoint.y;  
//                }  

	},
	data: [{
		name: "Оплата",
		type: this.columnType,
		yValueFormatString: "#0.## р.",
		showInLegend: true,
		dataPoints:self._initSvodnGraph(val,'summOplata',year),
                //toolTipContent: "За {name}: {label}",
                //toolTipContent: "{label}<br/>{name}, <strong>{y}</strong>mn Units",
	},
	{
		name: "Прибыль",
		type: this.columnType,
		yValueFormatString: "#0.## р.",
		showInLegend: true,
		dataPoints: self._initSvodnGraph(val,'profit',year),
                //toolTipContent: "За {name}: {label}",
	},
	{
		name: "Траты",
		type: this.columnType,
		yValueFormatString: "#0.## р.",
		showInLegend: true,
		dataPoints: self._initSvodnGraph(val,'spends',year),
                //toolTipContent: "За {name}: {label}",
	},
	{
		name: "Чистая прибыль",
		type: this.columnType,
		yValueFormatString: "#0.## р.",
		showInLegend: true,
		dataPoints: self._initSvodnGraph(val,'cleanProfit',year),
                //toolTipContent: "За {name}: {label}",
	}
        ]
    });
    chart.render();
    $('[name="S3selyer[columnType]"]').change(function(e){
        self.columnType=$(this).val(); 
    });
};

stones.statistic.graphics.prototype.initPjax=function(){
    let self=this;

    $(document).on('pjax:complete',function(){
        $('#gr-bunner').remove();
    });
    $(document).on('pjax:beforeSend',function(){
        $('body').append($('<div>').attr({
            id:'gr-bunner'
        }).css({
            position:'absolute',
            top:0,left:0,
            width:$(document).width(),
            height:$(document).height(),
            cursor:'wait',
            'background-color':'gray',
            opacity:0.05,
        }));        
    });
    $('#firstGraphicsPjax').on('pjax:success', function() {
        self.initForm();
        self.proceedPjaxCanvas();
    });
//    console.log('pajax',$('[role="month-filter-pjax"]'));
    $('[role="month-filter-pjax"]').on('pjax:success', function() {
//        console.log('month-filter-pjax',$('[name="'+$(this).attr('data-reinit-target')+'"]'));
        $(this).children('form').submit(self.onFormSubmit);
        $('[name="'+$(this).attr('data-reinit-target')+'"]').next().on('click',self.formMonthFilterSubmmitClick);
        self.initTabs1();
    });
    $('#s3ChangeYaer-pjax').on('pjax:success', function() {
        $('#formTablesSelectYear').children('div').children('select').change(self.Tabs1ChangeYaer);
        $('#formTablesSelectYear').submit(self.onFormSubmit);
        $('[role="form-month-filter"]').submit(self.onFormSubmit);
        $('[role="form-month-filter"]').find('[type="submit"]').off('click');
        $('[role="form-month-filter"]').find('[type="submit"]').on('click',self.formMonthFilterSubmmitClick);
        self.initSvodnGraph();
    });
    self.initForm();
    self.initTabs1();
    $('#formTablesSelectYear').children('div').children('select').change(self.Tabs1ChangeYaer);
    $('[role="form-month-filter"]').find('[type="submit"]').on('click',this.formMonthFilterSubmmitClick);
    $('[role="form-month-filter"]').submit(self.onFormSubmit);
    $('#formTablesSelectYear').submit(self.onFormSubmit);
    this.initSvodnGraph();
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


stones.statistic.graphics.prototype.run=function(){
    let w=Math.ceil($('#statistic-cont').width()/100*95);
    this.optionsGR[0].width=w;
    this.chartGR=new CanvasJS.Chart( this.chartContainer[0], this.optionsGR[0]);
    this.chartGR.render();
    for (let i=1;i<3;i++){
        this.optionsGR[i].run();
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
                    self.chartGR.options.data[0].dataPoints[i].y = tmpD.total1 ? parseInt( tmpD.total1 ) : 0;
                    self.chartGR.options.data[1].dataPoints[i++].y = tmpD.total1_profit ? parseInt( tmpD.total1_profit ) : 0;
                } );
                self.chartGR.options.animationEnabled = true;
                self.chartGR.render( );
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
//                console.log( 'doUncheck', this );
            }
        } );
        if ( $( this ).get( 0 ).checked && !$( this ).attr( 'checked' ) ) {
            $( this ).attr( 'checked', true );
        } else if ( $( this ).attr( 'checked' ) ) {
            e.preventDefault( );
        }
        selfM.sendData.call( this, selfM );
    } );
}

