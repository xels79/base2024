/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var stones=stones||{};
stones.widgets=stones.widgets||{};

stones.widgets.SpendingTableTabs=function(options){
    let self=this;
    this.widgetId=null;
    this.pjaxId=null;
    stones.baseComponent.call(this,options,["widgetId","pjaxId"]);
    this.pageVarName='sttTabs-'+this.widgetId;
    if (this.pjaxId[0]!=='#') this.pjaxId='#'+this.pjaxId;
    if (this.widgetId[0]!=='#') 
        this._widgetId='#'+this.widgetId;
    else
        this._widgetId=this.widgetId;
    $(this.listId+'>li>a').click(function(){
        const regex = /^.*tab(\d*)/;
        let str = $(this).attr('href');
        let m;
        if ((m = regex.exec(str)) !== null) {
            console.log(m[1]);
            let tmpURL = URI( window.location.href );
            tmpURL.setSearch( 'spendsMonth', parseInt(m[1])+1 );
            window.history.pushState( null, "Title", tmpURL.toString( ) );
        }
    });
    console.log('SpendingTableTabs init.');
};

stones.widgets.SpendingTableTabs.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.widgets.SpendingTableTabs.prototype, 'constructor', { 
    value: stones.widgets.SpendingTableTabs, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});
Object.defineProperty(stones.widgets.SpendingTableTabs.prototype, 'listId', { 
    get:function(){
        if (this.widgetId[0]!=='#'){
            return '#ssTabs'+ this.widgetId;
        }else{
            return '#ssTabs'+ this.widgetId.substr(1);
        }
    },
});
