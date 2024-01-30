<?php
/* @var $this yii\web\View */

use yii\web\View;
use yii\bootstrap\Tabs;
use app\widgets\JSRegister;
use yii\helpers\Url;
$this->title='Склад';
$this->registerJsFile(Yii::$app->assetManager->publish('@app/web/js/sklad-controller.js')[1],[
    'depends'=>['yii\web\JqueryAsset','yii\jui\JuiAsset'],
    'position'=>View::POS_END
]);
$active=Yii::$app->request->get('active');
if ($active){
    $active= explode('-', $active);
}
?>
<?php JSRegister::begin([
    'key' => 'SkladColor',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    var page=1;
    $("[data-column-name]").skladColors({
        updateColorsUrl:"<?=(bool)\Yii::$app->user->identity->can('/sklad/updatecolors')?Url::to(['/sklad/updatecolors']):null?>",
    });
    $("#sklad-info").infoBlind();
    $('[data-toggle=tab]').click(function(e){
        uri=new URI(window.location);
        uri.setQuery('active',$(this).attr('href'));
        history.pushState({page:page++},null,uri.toString());
    });
    $(window).on('popstate',function(event){
        console.log(event);
        var q=URI.parseQuery(URI.parse(event.target.location.href).query);
        if ($.type(q)==='object'&&q.active){
            console.log(q);
            $('[href="'+q.active+'"]').tab('show');
        }else{
            window.location.href=event.target.location.href;
        }
        
    });
</script>
<?php JSRegister::end();?>
    <div class="sklad">
    <p id="sklad-info"></p>
    <?=Tabs::widget([
        'items'=>[
            [
                'label'=>'Краски',
                'content'=>$this->render('_colors',['colorDate'=>$colorDate,'maxRow'=>$maxRow,'chemikal'=>$chemikal,'maxDate'=>$colorMaxDate]),
                'active'=>!$active||$active[1]==='tab0'
                
            ],
            [
                'label'=>'Пакеты пэ',
                'content'=>$this->render('_paket',[
                    'paketFirmAll'=>$paketFirmAll,
                    'colls'=>['38x50', '30x40', '22x34', '36x45', '45x50', '60x50', '70x50', '50x60', '70x60'],
                    'maxDate'=>$paketMaxDate
                ]),
                'active'=>$active&&$active[1]==='tab1'
            ],
            [
                'label'=>'Бумага',
                'content'=>$this->render('_bumaga',[
                    'bumaga'=>$bumaga,
                    'maxDate'=>$bumagaMaxDate
                ]),
                'active'=>$active&&$active[1]==='tab2'
            ]
        ]
    ])?>
    </div>
<p><?= ''//\yii\helpers\VarDumper::dumpAsString(Yii::$app->request->get(),10,true)?></p>
