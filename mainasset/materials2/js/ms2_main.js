/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var stones=stones||{};
stones.materials2=stones.materials2||{};
stones.materials2.select=stones.materials2.select||{};


stones.materials2.select.eng=function(options){
    stones.materials2.select.container.call(this,options);
};

stones.materials2.select.eng.prototype=Object.create(stones.materials2.select.container.prototype);
Object.defineProperty(stones.materials2.select.eng.prototype, 'constructor', { 
    value: stones.materials2.select.eng, 
    enumerable: false, // false, чтобы данное свойство не появлялось в цикле for in
    writable: true 
});




