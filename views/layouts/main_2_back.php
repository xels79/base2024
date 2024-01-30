<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use app\widgets\MNav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\bootstrap\Alert;
use yii\widgets\Pjax;
use app\widgets\JSRegister;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

//AppAsset::register($this);
if (!isset($this->params['assetBundle'])){
    AppAsset::register($this);
}else{
    $this   ->assetManager
            ->getBundle($this->params['assetBundle'])
            ->register($this);

}
$iconB=Html::tag('span',null,['class'=>'icon-bar']);
$iconB.=$iconB.$iconB;
$this->registerJs("$('#calculator1').calculator();", yii\web\View::POS_READY);
$this->registerJs('$("#current-user-profile").click(function(){'
        . '$.custom.setupUserDialog({'
        . '"userChangeUrl":"'.Url::to(['/admin/setup/userchange']).'",'
        . 'editId:$("#current-user-profile").attr("data-key")'
        . '});'
        . '});', yii\web\View::POS_READY);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <div class="informer-main"></div>
    <div id="wrapper" class="wrapper">
        <?php
            NavBar::begin([
                'brandLabel' => $this->context->logo,
                'brandOptions'=>$this->context->brandOptions,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
                'renderInnerContainer'=>false
            ]);
            $user=Yii::$app->user->identity;
            if (Yii::$app->user->isGuest)
                $this->context->mMenu[]=['label' => 'Вход', 'url' => ['/site/login']];
            else{
                echo Nav::widget([
                    'options' => ['class' => 'nav navbar-nav'],
                    'items'=>[
                        Html::tag('p',Yii::$app->formatter->asDate(time(),'medium'),['class'=>'navbar-text nav-text-m']),
                        Html::tag('p', (!Yii::$app->user->identity->realname? Yii::$app->user->identity->username: Yii::$app->user->identity->realname), ['class'=>'navbar-text nav-text-m'])
                    ]
                ]);
            }
            echo Nav::widget([
                'options' => ['class' => 'nav navbar-nav navbar-right'],
                'activateParents'=>true,
                'items' => $this->context->mMenu,

            ]);
            NavBar::end();
        ?>

        <div id="page-wrapper">
            <?= Html::tag('div',Html::tag('div',$this->context->sideMenu(),['class'=>'side-nav-conteiner hidden-print']),['class'=>'side-nav side-nav-open'])?>
            <div class="container-fluid">
            <?=$this->renderFile('@app/views/layouts/pageHeader.php')?>
            <?= $content ?>
            </div>
                <?php Pjax::begin(['id'=>'hddPjaxInfo'])?>
                <?php Pjax::end()?>

        </div>
        <div class="footer hidden-print">
            <div class="pull-left">&copy; Астерион 2016</div>
            <div class="pull-right"><?=Yii::$app->name.' v'.\yii::$app->version?></div>
        </div>
    </div>

<?=Html::img(Yii::getAlias($this->assetManager->publish('@app/web/pic/loader.gif')[1],null,'pic1'),['style'=>['display'=>'none'],'id'=>'loadingBig'])?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
