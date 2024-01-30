/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var stones=stones||{};

stones.baseComponent=function(options,reqw){
    let _hasError=false;
    reqw=typeof(reqw)!=='undefined'?reqw:[];
    Object.defineProperty(this, 'hasError', {
        get:function(){
            return _hasError;
        }
    });
    if (typeof(options)==='object'){
        let keys=Object.keys(options);
        for (let i in keys){
            if (typeof(this[keys[i]])!=='undefined'){
                this[keys[i]]=options[keys[i]];
            }else{
                console.error(this,'baseComponent - неизвестное свойство"'+keys[i]+'"');
                break;
            }
        }
    }
    for (let i in reqw){
        if (typeof(this[reqw[i]])==='undefined' || this[reqw[i]]===null){
            console.error(this+' - не указан обязательный параметр "'+reqw[i]+'"');
            _hasError=true;
            return;
        }
    }
};