<?php
/* @var $this yii\web\View */
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\AList;
use app\widgets\JSRegister;

$this->registerJsFile($this->assetManager->publish('@app/web/js/base_widget.js')[1],[
        'depends' => [yii\jui\JuiAsset::className()],
        'position'=> \yii\web\View::POS_END
    ],'base_widget');
$this->registerJsFile($this->assetManager->publish('@app/web/js/banks_search.js')[1],[
        'depends' => [yii\jui\JuiAsset::className()],
        'position'=> \yii\web\View::POS_END
    ],'banks_search');
$this->registerJsFile($this->assetManager->publish('@app/web/js/simple_form.js')[1],[
        'depends' => [\yii\web\JqueryAsset::className()],
        'position'=> \yii\web\View::POS_END
    ],'simple_form');
$this->registerJsFile($this->assetManager->publish('@app/web/js/parent_controller.js')[1],[
        'depends' => [yii\jui\JuiAsset::className()],
        'position'=> \yii\web\View::POS_END
    ],'parent_controller');
$this->registerJsFile($this->assetManager->publish('@app/web/js/mainP.js')[1],[
        'depends' => [\yii\web\JqueryAsset::className()],
        'position'=> \yii\web\View::POS_END
    ],'mainpjs');

$this->title = 'Главная';
?>
<?php JSRegister::begin([
    'key' => 'setupInit',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    $('#requestCustomer').mainPController({
        requestCustomer:"<?=Url::to(['/admin/mainpage/ajaxlist_zak'])?>",
        bankSearchUrl:"<?=Url::to(['/banks/searchbank'])?>",
        loadPicUrl:"<?=Yii::$app->assetManager->publish('@app/web/pic/loader-hor.gif')[1]?>",
        pointPicUrl:"<?=Yii::$app->assetManager->publish('@app/web/pic/w-point.gif')[1]?>"
    });
</script>
<?php JSRegister::end();?>

<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-5">
                <div class="row">
                    <div class="col-xs-6">
                    <ul class="a-list">
                        <li><a class="btn btn-main" disabled="disabled">Заказы</a></li>
                        <li><a class="btn btn-main" disabled="disabled">Новый заказ</a></li>
                    </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                    <ul class="a-list">
                        <li><?=Html::a('Заказчики','#',['class'=>'btn btn-main', 'id'=>'requestCustomer'])?>
                        <li><a class="btn btn-main" disabled="disabled">Подрядчики</a></li>
                        <li><a class="btn btn-main" disabled="disabled">Поставщики</a></li>
                    </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                    <ul class="a-list">
                        <li><a class="btn btn-main-font-black" disabled="disabled">Чёрный список</a></li>
                    </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-5 hidden-xs">
                <?=$this->context->pic1;?>
            </div>
            <div class="col-md-1 hidden-sm hidden-xs">
                <?=$this->context->pic2;?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                    <ul class="a-list a-list-inline">
                        <li><a class="btn btn-blue" disabled="disabled">Реквизиты</a></li>
                        <li><a class="btn btn-blue" disabled="disabled">Счета</a></li>
                        <li><a class="btn btn-blue" disabled="disabled">Сверки</a></li>
                        <li><a class="btn btn-blue" disabled="disabled">Должники</a></li>
                    </ul>                
            </div>
        </div>
    </div>
</div>