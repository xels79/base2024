/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var stones=stones||{};
stones.widgets=stones.widgets||{};

stones.widgets.SpendingToYourself=function(options){
    let self=this;
    this.action=null;
    this.keyColumnName=null;
    this.keyColumnValue=null;
    this.dataColumnName=null;
    this.dataFormName=null;
    this.targetId=null;
    //totalUpdateSelectorsText:'',
    stones.baseComponent.call(this,options,["action","keyColumnName","dataColumnName","dataFormName","targetId","keyColumnValue"]);
    $('#'+this.targetId).onlyNumeric({allowPoint:true}).enterAsTab().inputROC({
        action:this.action,
        keyColumnName:this.keyColumnName,
        dataColumnName:this.dataColumnName,
        dataFormName:this.dataFormName,
        keyColumnValue:this.keyColumnValue
    });
    console.log('SpendingToYourself init.',this);
};

stones.widgets.SpendingToYourself.prototype=Object.create(stones.baseComponent.prototype);
