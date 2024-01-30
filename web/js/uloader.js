/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



(function( $ ) {
  $.widget( "mProg.upLoader", {
 
    options: { 
      otherData: {},            //Дополнительные данные на сервер
      loggerId:null,            //Id контейнера для логов
      fileConteynerId:'file',  //Id контейнера с файлом по умолчанию
      fileManagerId:null,       //Id окна файл менеджера
      fileManagerPjaxId:null,   //Если используется Pjax
      fileVarName:null,         //Имя переменной сервера для передачи файла
      url:null,                 //Запос на сервер
      afterRemove:null,         //Функция которая выполнится после удаления
      loaderId:null,            //Контейнер с информером загрузки
      //onFileComlpetePjax:null,  //Перегрузить контейнер Pjax когда файл загружен
      onCompletePjaxReload:null //Перегрузить дополнительный контейнер Pjax когда всё завершино
      
    },
 
    _create: function() {
        console.debug('upLoader:_create:');
        if (this.options.fileManagerPjaxId){
            $(this.options.fileManagerPjaxId).on('pjax:success',{thisObj:this},function(e){
                console.debug('upLoader:Pjax','success');
                e.data.thisObj._removeInit();
            });
        }
        this._removeInit();
        var self=this;
        $(this.element).dmUploader({//drag-and-drop-zone
            url: this.options.url,
            fileName:this.options.fileVarName,
            extraData:this.options.otherData,
            dataType: 'json',
            maxFileSize:1073741824,
            maxFiles:120,
            //allowedTypes: 'image/*,application/x-compressed,application/x-zip-compressed,multipart/x-zip,application/zip',
            extFilter:'pdf;eps;cdr;ai;ps­d;tif;jpg;jpeg;pn­g;doc;xls;xlsx;r­ar;zip;',
            onInit: function(){
                  console.debug('upLoader:onInit',this);
                  self.addLog('default', 'Загрузчик запущен');
            },
            onBeforeUpload: function(id){
              var pos=$('#'+self.options.fileConteynerId+id).position().top;
              var fH=$('#'+self.options.fileConteynerId+id).height()+11;
              console.debug('onBeforeUpload',fH);
              if (pos<0||pos>$(self.options.loaderId).height()){
                  $(self.options.loaderId).animate({
                      scrollTop:($('#'+self.options.fileConteynerId+id).index()*fH)
                  });
              }
              self.addLog('default', 'Начинается загрузка файла : ' + $('#'+self.options.fileConteynerId+id+' .file-id').next().text());
              self.updateFileStatus(id, 'default', 'Загружается...');
            },
            onNewFile: function(id, file){
                self.addFile(id, file);
            },
            onComplete: function(){
                self.addLog('default', 'Все загрузки завершены');
                if (self.options.onCompletePjaxReload){
                    setTimeout(function(){
                        $.pjax.reload(self.options.onCompletePjaxReload,{timeout:300000});                    
                    },3000);
                }
            },
            onUploadProgress: function(id, percent){
              var percentStr = percent + '%';
              self.updateFileProgress(id, percentStr);
            },
            onUploadSuccess: function(id, data){
                console.debug('upLoader:onUploadSuccess',id );
                self.addLog('success', 'Загрузка файла :' + $('#'+self.options.fileConteynerId+id+' .file-id').next().text() + ' завершена.');
                //.$.danidemo.addLog('#debug', 'info', 'Ответ сервера #' + id + ': ' + JSON.stringify(data));
                self.updateFileStatus(id, 'success', 'Загрузка завершена.');
                self.updateFileProgress(id, '100%');
                if (self.options.fileManagerPjaxId){
                    $.pjax.reload(self.options.fileManagerPjaxId,{timeout:300000});//,fragment:'#fileZPjax1'});
                }
                setTimeout(function(){
                    $('#'+self.options.fileConteynerId+id).animate({opacity:"hide"},2000,function(){
                        $('#'+self.options.fileConteynerId+id).remove();
                    });
                },3000);

                //
            },
            onUploadError: function(id, message){
              self.updateFileStatus(id, 'error', message);
              self.addLog('danger', 'Ошибка загрузки #' + $('#'+self.options.fileConteynerId+id+' .file-id').next().text() + ': ' + message);
            },
            onFileTypeError: function(file){
              self.addLog('danger', 'Файл \'' + file.name + '\' неможет быть добавлен: неверный тип');
            },
            onFileSizeError: function(file){
              self.addLog('danger', 'Файл \'' + file.name + '\' неможет быть добавлен: размер привышает 25Mb');
            },
            onFallbackMode: function(message){
              self.addLog('info', 'Browser not supported(do something else here!): ' + message);
            }

        });
    },
 
    _setOption: function( key, value ) { 
        this._super( "_setOption", key, value );
    },
    destroy: function() {
        console.debug('upLoader:destroy:');
        //$.Widget.prototype.destroy.call( this );
    },
    _pB:function(rem){
        if (rem)
            return this.options.fileConteynerId+'_pB'
        else
            return '#'+this.options.fileConteynerId+'_pB'
    },
    addLog: function(status, str){
        console.debug('upLoader:addLog',this.options.loggerId);
        if (this.options.loggerId){
            var d = new Date();
            var li = $('<li />', {'class': 'list-group-item list-group-item-' + status});
            var message = '[' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds() + '] ';
            message += str;
            li.html(message);
            $(this.options.loggerId).prepend(li);
        }
    },
    addFile: function(i, file){
        var template = '<div id="'+this.options.fileConteynerId + i + '">' +
                          // '<img src="http://placehold.it/48.png" class="image-preview" />' +
                           '<span class="file-id">#' + i + '</span>'+
                           '<div class="mProgress">'+
                               '<div id="'+this._pB(true)+i+'" class="mProgress-bar">'+
                                   ' <span>' + file.name + '</span>'+'<span class="file-size">(' + this.humanizeSize(file.size) + ')</span>'+'<span class="file-status">Ожидает загрузки</span>' +
                               '</div>'+
                           '</div>'+
                       '</div>';
        var i = $(this.options.loaderId).attr('file-counter');
        if (!i){
                $(this.options.loaderId).empty();

                i = 0;
        }
        i++;
        if (this.options.loaderId){
            $(this.options.loaderId).attr('file-counter', i);
            $(this.options.loaderId).prepend(template);
            $(this._pB()+(i-1)).mProgress();
        }
    },
    updateFileStatus: function(i, status, message){
        $('#'+this.options.fileConteynerId + i).find('span.file-status').html(message).addClass('file-status-' + status);
    },
    updateFileProgress: function(i, percent){
        //$('#file' + i).find('div.progress-bar').width(percent);
        $(this._pB()+i).mProgress('width',percent);
        $('#'+this.options.fileConteynerId + i).find('span.file-status').text(percent + ' Завершено');
    },

    humanizeSize: function(size) {
        var i = Math.floor( Math.log(size) / Math.log(1024) );
        return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
    },
    _removeInit: function(){
        console.debug('upLoader:removeInit',this.options.fileManagerId);
        if (this.options.fileManagerId){
            var self=this;
            //console.debug('upLoader:removeInit',$(this.options.fileManagerId+' > li > a'));
            $(this.options.fileManagerId+' > li > a').click(function(e){
                e.preventDefault();
                var aj=$.fn.getUrlForAjaxFromHref($(this).attr('href'));
                if (confirm('Удалить файл "'+decodeURIComponent(aj.fName)+'" ?')){
                    var aj={url:$(this).attr('href'),data:{}};
                    aj.type='post';
                    aj.data.ajax=true;
                    console.debug('upLoader:remove',aj);
                    $.ajax(aj).done(function(data){
                        console.debug('upLoader:remove:answer',data);
                        console.debug('upLoader:remove:answer.fileManagerPjaxId',self.options.fileManagerPjaxId);
                        if (self.options.fileManagerPjaxId)
                            $.pjax.reload(self.options.fileManagerPjaxId,{timeout:300000});
                        if ($.isFunction(self.options.afterRemove))
                            self.options.afterRemove.call({fileName:decodeURIComponent(aj.fName)});
                    });
                }
            });
        }
    },
  });
}( jQuery ) );

