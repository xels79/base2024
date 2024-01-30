/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var stones=stones||{};
stones.widgets=stones.widgets||{};
stones.widgets.SpendingTable=function(options){
    let self=this;
    this.widgetId=null;
    this.action=null;
    this.actionAdd=null;
    this.actionRemove=null;
    this.keyColumnName=null;
    this.dataFormName=null;
    this.keyColumnValue=0;
    this.date=null;
    this.toUpdateControllerVarName='';
    
    stones.baseComponent.call(this,options,["widgetId","action","keyColumnName","dataFormName","date"]);
    this.needUpdate=false;
    if (this.hasError) return this;
    this.pjId='#'+$('#'+this.widgetId).children(':first-child').attr('id');
//    console.log($(pjId+'>div>ul>li>a'));
//    console.log($('#'+$(pjId).children(':first-child').attr('id')));
    $(this.pjId).pjax(this.pjId+'>div>ul>li>a',{
        container:this.pjId,
        timeout:17000
    });
    $(this.pjId).on('pjax:success',function(){
        self.clickSetup.call(self);
    });
    this.clickSetup();
    console.log('SpendingTable', this);
};

stones.widgets.SpendingTable.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.widgets.SpendingTable.prototype, 'constructor', { 
    value: stones.widgets.SpendingTable, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

stones.widgets.SpendingTable.prototype.clickSetup=function(){
    let self=this;
    $('#'+this.widgetId).find('[role="edit-cell"]').each(function(){
        let opt={
            action:self.action,
            dataColumnName:$(this).parent().attr('data-column-name'),
            keyColumnName:self.keyColumnName,
            keyColumnValue:parseInt($(this).parent().parent().attr('data-key')),
            dataFormName:self.dataFormName
        };
//        if (self.toUpdateControllerVarName){
//            opt.afterSave=function(dt){
//                //stones.widgets[self.toUpdateControllerVarName].update.call(stones.widgets[self.toUpdateControllerVarName]);
//            };
//        }
        $(this).navigateKey().inputROC(opt);
    });
    $('#'+this.widgetId).find('[role="st-add"]').inputROAdd({
        action:this.actionAdd,
        formName:this.dataFormName,
        date:this.date,
        afterSave:function(){
            $.pjax.reload(self.pjId,{
                container:self.pjId,
                timeout:17000
            });
        }
    });
    //inputRORemove
    $('#'+this.widgetId).find('[role="st-remove"]').inputRORemove({
        action:this.actionRemove,
        afterSave:function(){
//            console.log($('#'+self.widgetId).children(':first-child').attr('id'));
            $.pjax.reload(self.pjId,{
                container:self.pjId,
                timeout:17000
            });
        }
    });
    
}

stones.widgets.SpendingTable.prototype.toString=function(){
    return "object SpendingTable";
};

/**
 * 
 * Статичная таблица по зарплате
 * 
 */

stones.widgets.StaticSpendingTable=function(options){
    this.pjaxId=null;
    this.greedViewId=null;

    stones.baseComponent.call(this,options,["pjaxId","greedViewId"]);
    if (this.hasError) return this;
    if (this.pjaxId[0]!=='#') this.pjaxId='#'+this.pjaxId;
    if (this.greedViewId[0]!=='#') this.greedViewId='#'+this.greedViewId;
    this.initPjax();
    console.log('StaticSpendingTable init', this);
};

stones.widgets.StaticSpendingTable.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.widgets.StaticSpendingTable.prototype, 'constructor', { 
    value: stones.widgets.StaticSpendingTable, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});
stones.widgets.StaticSpendingTable.prototype.initPjax=function(){
    let self=this;
//    console.log($(this.greedViewId+'>ul>li>a'));
    $(document).pjax(this.greedViewId+'>ul>li>a',{
        container:this.pjaxId,
        fragment:this.pjaxId,//this.greedViewId,
        timeout:7000
    });
}

stones.widgets.MainSpendingTable=function(options){
    let self=this;
    this.widgetId=null;
    this.action=null;
    this.keyColumnValue=null;
    this.dataFormName=null;
    this.keyColumnName=null;
    stones.baseComponent.call(this,options,["widgetId", "action", "keyColumnValue", "dataFormName", "keyColumnName"]);
    if (this.hasError) return this;
    $('[role="st-'+this.widgetId+'-save"]').each(function(){
            $(this).navigateKey();
            $(this).inputROC({
            action:self.action,
            dataColumnName:$(this).attr('data-column-name'),
            keyColumnName:self.keyColumnName,
            keyColumnValue:self.keyColumnValue,
            dataFormName:self.dataFormName
        });
    });
    console.log('MainSpendingTable init', this);
};

stones.widgets.MainSpendingTable.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.widgets.MainSpendingTable.prototype, 'constructor', { 
    value: stones.widgets.MainSpendingTable, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});

stones.widgets.MainSpendingTable.prototype.update=function(){
    console.log('MainSpendingTable update first input');
    $($('[role="st-'+this.widgetId+'-save"]').get(3)).inputROC('save');
};