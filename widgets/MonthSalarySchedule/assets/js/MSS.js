/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var stones=stones||{};
stones.widgets=stones.widgets||{};
stones.widgets.MSS=function(options){
    alert('init');
};
stones.widgets.MSS.prototype=Object.create(stones.baseComponent.prototype);

Object.defineProperty(stones.widgets.MSS.prototype, 'constructor', { 
    value: stones.widgets.MSS, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});
