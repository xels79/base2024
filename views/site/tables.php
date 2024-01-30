<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use app\widgets\JSRegister;
use app\models\tables\Productions;
use app\models\tables\DependTables;
use yii\helpers\Json;
use app\models\tables\WorkOrproductType;

$this->title = 'Таблицы';
$this->beginContent('@app/views/layouts/indexJS.php');

?>

<?php JSRegister::begin([
    'key' => 'setupInit',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    var opt={
            bodyBackgrounColor:'#66c2d1',
            loadPicUrl:"<?=Yii::$app->assetManager->publish('@app/web/pic/loader-hor.gif')[1]?>",
            requestUrl:"<?=Yii::$app->urlManager->createUrl(['tables/list'])?>",
            validateUrl:"<?=Yii::$app->urlManager->createUrl(['tables/validate'])?>",
            form:{
                requestUrl:"<?=Yii::$app->urlManager->createUrl(['tables/addedit'])?>",
                removeUrl:"<?=Yii::$app->urlManager->createUrl(['tables/remove'])?>",
                fields:{
                    id:['prim'],
                    name:['string','Название'],
                    category:['integer','Категория',null,{0:'А',1:'Б',2:'В'}]
                }
            }
        };
    $('#requestWorkTypes').tablesControllerDialog($.extend({},opt,{
        title:'Вид работ',
        formName:"Worktypes",
    }));
    $('#requestProdaction').tablesControllerDialog($.extend({},opt,{
        title:'Продукция',
        formName:"Productions",
    }));
    $('#requestPostPrint').tablesControllerDialog($.extend({},opt,{
        title:'Постпечать',
        formName:"Postprint",
    }));
    //requestWOPType
    $('#requestWOPType').tablesControllerDialog($.extend({},opt,{
        title:'Тип мат./раб.',
        formName:"WorkOrproductType",
    }));
    $('#requestMaterials').click(function(){
        var d=$('#'+$(this).attr('id')+'_dialog');
        if (!d.length){
            d=$.fn.creatTag('div',{
                id:$(this).attr('id')+'_dialog'
            });
            $('body').append(d);
            d.tablesControllerMaterialTypes({
                but:'#requestMaterials',
                title:'Материалы',
                bodyBackgrounColor:'#66c2d1',
                formName:"Tablestypes",
                loadPicUrl:"<?=Yii::$app->assetManager->publish('@app/web/pic/loader-hor.gif')[1]?>",
                bigLoaderPicUrl:"<?=Yii::$app->assetManager->publish('@app/web/pic/loader.gif')[1]?>",
//                requestUrl:"<?=Yii::$app->urlManager->createUrl(['tables/list'])?>",
//                validateUrl:"<?=Yii::$app->urlManager->createUrl(['tables/validate'])?>",
                materialRequestListUrl:"<?=Yii::$app->urlManager->createUrl(['material/list'])?>",
                materialTablesListUrl:"<?=Yii::$app->urlManager->createUrl(['material/subtableslist'])?>",
                suppliersRequestUrl:"<?=Yii::$app->urlManager->createUrl(['material/getsuppliers'])?>",
                subtablegetlistUrl:"<?=Yii::$app->urlManager->createUrl(['material/subtablegetlist'])?>",
                subtabladdeditUrl:"<?=Yii::$app->urlManager->createUrl(['material/subtabladdedit'])?>",
                removeSubTUrl:"<?=Yii::$app->urlManager->createUrl(['material/subtableremove'])?>",
                referenceColumnName:"<?=DependTables::$reference?>",
                subTableDDListUrl:"<?=Yii::$app->urlManager->createUrl(['material/subtablegetallnamesfordd'])?>",
                onAddComplit:function(data){
                    console.log(data);
                    if (data.dependTables){
                        d=$.ui.dialog({
                            title:'Материал добавлен',
                            modal:true,
                            width:700,
                            closeOnEscape:true,
                        });
                        d.uiDialog.css('background-color','white');
                        $.each(data.dependTables,function(){
                            d.element.append($.fn.creatTag('p',{text:this}));
                        });
                    }
                },
                materialForm:{
                    formName:'Tablestypes',
                    requestUrl:"<?=Yii::$app->urlManager->createUrl(['material/addedit'])?>",
                    removeUrl:"<?=Yii::$app->urlManager->createUrl(['material/remove'])?>",
                    onReady:function(oldV){
                        var defV=null;
                        if (oldV['Tablestypes-struct']){
                            defV=JSON.parse(oldV['Tablestypes-struct']);
                        }
                        if (!defV) return;
                        console.group('Корекция выбранных');
                        var structL=$('#Tablestypes-structlist');
                        if (!structL.length){console.log('Tablestypes-structlis - не найден, конец.'); console.groupEnd(); return;}
                        var listSel=structL.prev().children('.list-group:first-child'),listEv=structL.prev().children('.list-group:last-child');
                        if (!listSel.length||!listEv.length){console.log('.list-group - не найден , конец.'); console.groupEnd(); return;}
                        console.log('oldV',defV);
                        console.log('Проверка выбраных');
                        $.each(listEv.children('a'),function(){
                            if ($.inArray($(this).text(),defV)>-1){
                                listSel.append($.fn.creatTag('a',{class:'list-group-item ui-state-disabled',text:$(this).text()}));
                                console.log('Обработали:',$(this).text());
                                $(this).remove();
                            }
                        });
                        console.log('Сохранение резыльтата',oldV['Tablestypes-struct']);
                        structL.val(oldV['Tablestypes-struct']);
                                
                        console.log('Корекция завершена, конец.');
                        console.groupEnd();
                    },
                    fields:{
                        id:['prim'],
                        name:['strind','Название'],
                        structlist:[function(el,defVal){
                            if (!defVal) defVal=[];
                            var input=$.fn.creatTag('input',{
                                type:'hidden',
                                id:'Tablestypes-structlist'
                            });
                            input.val(JSON.stringify(defVal));
                            el.append($.fn.creatTag('h5',{text:'Структура:',style:{width:'100%','text-align':'left'}}));
                            var inf=$.fn.creatTag('div',{'class':'inf'});
                            var help=$.fn.creatTag('a',{'class':'btn glyphicon glyphicon-question-sign'});
                            var d=null;
                            inf.append($.fn.creatTag('p',{'html':'Выбранно:'})).find('p').append(help).find('.btn').mouseenter(function(e){
//                                e.preventDefault();
                                console.log('tt');
                                if (d)return;
                                d=$.ui.dialog({
                                    title:false,
                                    //modal:true,
                                    closeOnEscape:true,
                                    show: { effect: "fade", duration: 1800 },
                                    position:{my: "left top", at: "right top", of: $(this)}
                                });
                                d.uiDialog.css('background-color','white');
                                d.uiDialogTitlebar.remove();
                                d.element.text('Вы берите необходимые таблицы. Первая таблица будет основной, последующие зависимыми. После сохранения изменения невозножны!');
                            });
                            inf.find('p').find('.btn').mouseleave(function(e){
                                if (d){
                                    d.close();
                                    d.uiDialog.remove();
                                    delete d;
                                    d=null;
                                }
                            });
                            inf.append($.fn.creatTag('p',{'text':'Доступно:'}));

                            el.append(inf);
                            el.append($.fn.twoEditableLists(defVal,<?=Json::encode(array_keys(DependTables::structBas()))?>,function(e,ui){
                                if ($(this).index()) return;
                                var vl=[];
                                $.each($(this).parent().children(':first-child').children(),function(){
                                    console.log(this);
                                    vl[vl.length]=$(this).text();
                                });
                                console.log('index: '+$(this).index(),vl);
                                input.val(JSON.stringify(vl));
                            }));
                            el.append(input);
                            
                            this.oldVal['Tablestypes-structlist']=defVal;
                            console.log(JSON.parse(input.val()));
                        },'Структура'],
                        struct:['string','Структура',null,null,'hide']
                    }
                },
            });
        }
        if (!d.tablesControllerMaterialTypes('isOpen'))
            d.tablesControllerMaterialTypes('open');
    });
    $('#requestTechnicalsOptions').tecnicalsOptions({
        getUrl:"<?=Yii::$app->urlManager->createUrl(['tables/gettecnicalsoptions'])?>"
    });
</script>
<?php JSRegister::end();?>


<table class="table a-main-table">
    <tr>
        <td>
            <ul class="a-list">
                <li><?=Html::a('Вид работ','#',['class'=>'btn btn-main', 'id'=>'requestWorkTypes'])?></li>
                <li><?=Html::a('Постпечать','#',['class'=>'btn btn-main', 'id'=>'requestPostPrint'])?></li>
                <li><?=Html::a('Продукция','#',['class'=>'btn btn-main', 'id'=>'requestProdaction'])?></li>
                <li><?=Html::a('Материалы','#',['class'=>'btn btn-main', 'id'=>'requestMaterials'])?></li>
                <li><?=Html::a('Тип мат./раб.','#',['class'=>'btn btn-main', 'id'=>'requestWOPType','title'=>'Тип материалов или вид работ.'])?></li>
                <li><?=Html::a('Настройка технички','#',['class'=>'btn btn-main', 'id'=>'requestTechnicalsOptions'])?></li>
            </ul>            
        </td>
        <td class="pic1" align="right" style="background-image: url('<?=$this->context->publishPic1?>')"></td>
        <td><?=$this->context->pic2;?></td>
    </tr>
</table>
<?php $this->endContent();?>