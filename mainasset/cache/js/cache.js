/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cache.js
 *
 * @author Александр
 * @version 1.0.0b
 */

var stones=stones||{};
stones.cacheElement=function(options){
    let addTime=(new Date()).getTime();
    let {
        value=null,
        liveTime=this.defaultExpirationDate
        
    }=options?options:{};
    this.category=options.category?options.category:'uncategored';
    this.timeIsOut=false;
    //console.log(addTime);
    //console.log(addTime + this.defaultExpirationDate);
    //console.log('cacheElement Инициализация успешна.');
    Object.defineProperty(this,'value',{
        get:function(){
            let cTime=(new Date()).getTime();
            if (addTime+liveTime>cTime && !this.timeIsOut){
                return value;
            }else{
                this.timeIsOut=true;
                return null;
            }
        }
    });
    this.refreshTime=function(){
        addTime=(new Date()).getTime();
    };
};
stones.cacheElement.prototype.defaultExpirationDate=300000;
stones.cache=function(opt){
    let _data={};
    
    this.reFreshTime=function(_key,category){
        category=category?category:'uncategored';
        if (typeof(_data[category])==='undefined') return false;
        if (Object.keys(_data[category]).indexOf(_key)>-1){
            _data[category][_key].refreshTime();
            return true;
        }else{
            return false;
        }        
    }
    this.setData=function(key,val,category){
        category=category?category:'uncategored';
        if (typeof(_data[category])==='undefined') _data[category]={};
        _data[category][key]=new stones.cacheElement({
            value:val,
            category:category
        });
    };
    this.getData=function(_key,category){
        category=category?category:'uncategored';
        if (typeof(_data[category])==='undefined') return null;
        //let _key=key+category;
        if (Object.keys(_data[category]).indexOf(_key)>-1){
            return _data[category][_key].value;
        }else{
            return null;
        }
    };
    this.unSet=function(_key,category){
        category=category?category:'uncategored';
        if (typeof(_data[category])==='undefined') return false;
        //let _key=key+category;
        if (Object.keys(_data[category]).indexOf(_key)>-1){
            _data[category][_key].timeIsOut=true;
            return true;
        }else{
            return false;
        }
    };
    this.unSetCategory=function(category){
        if (typeof(_data[category])==='undefined'){
            return false;
        }else{
            _data[category]={};
            return true;
        }
    };
    this.clearAll=function(){
        _data={};
    };
    console.log('cache: Инициализация успешна!');
};

stones.cacheRuntime=new stones.cache({});

//stones.cacheRuntime.setData('testKey',123);
//console.log('store check',stones.cacheRuntime.getData('testKey'));