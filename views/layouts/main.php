<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\bootstrap\Alert;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
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
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name.' v'.\yii::$app->version,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top ',
                ],
            ]);
            $user=Yii::$app->user->identity;
            if (Yii::$app->user->isGuest)
                $this->context->mMenu[]=['label' => 'Вход', 'url' => ['/site/login']];
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'activateItems'=>true,
                'activateParents'=>true,
                'items' => $this->context->mMenu,
                
            ]);
            NavBar::end();
        ?>

        <div id="maincontainer" class="container">
            <?= Breadcrumbs::widget([
                'homeLink'=>false,
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'options'=>['class'=>['breadcrumb','hidden-print']]
            ]) ?>
            <?= $content ?>
                <?php Pjax::begin(['id'=>'hddPjaxInfo'])?>
                <?=$this->context->role==='admin'?app\widgets\FreeSpace::widget([
                        'id'=>'hddInfo',
                        'folders'=>[
                            [
                                'label'=>'Данные свободно:',
                                'path'=>\yii::getAlias('@file')
                            ],
                            [
                                'label'=>'Системный свободно:',
                                'path'=>'/'
                            ]
                        ],
                        'options'=>[
                            'class'=>'free-space-info col-xs-12 col-sm-8 col-md-8 col-lg-8 hidden-print'
                        ],
                    ]):''?>
                <?php Pjax::end()?>

        </div>
    </div>

    <footer class="footer hidden-print"<?=$this->context->contSz?(' style="width:'.$this->context->contSz.'px;"'):''?>>
        <div class="container">
            <ul class="list-inline">
                <li>&copy; Астерион 2016</li>
                <li class="pull-right"><?= Yii::powered() ?></li>
            </ul>
        </div>
    </footer>
<?=Html::img(Yii::getAlias($this->assetManager->publish('@app/web/pic/loader.gif')[1],null,'pic1'),['style'=>['display'=>'none'],'id'=>'loadingBig'])?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
