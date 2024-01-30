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
                'brandLabel' => 'Астерион база v'.\yii::$app->version,
                'brandUrl' => $this->context->brandUrl(),
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
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

        <div id="maincontainer" class="container"<?=$this->context->contSz?(' style="width:'.$this->context->contSz.'px;"'):''?>>
            <?= Breadcrumbs::widget([
                'homeLink'=>false,
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <ul class="list-inline">
                <li>&copy; Астерион <?= date('Y') ?></li>
                <li class="pull-right"><?= Yii::powered() ?></li>
            </ul>
        </div>
    </footer>
<?=Html::img(Yii::getAlias($this->assetManager->publish('@app/web/pic/loader.gif')[1],null,'pic1'),['style'=>['display'=>'none'],'id'=>'loadingBig'])?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
