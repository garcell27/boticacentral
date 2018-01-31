<?php
use kartik\growl\GrowlAsset;
use cornernote\ace\web\AceAsset;
use yii\helpers\Html;
use app\assets\AppAsset;
/* @var $this \yii\web\View */
/* @var $content string */
AceAsset::register($this);


if (Yii::$app->controller->action->id === 'login' || Yii::$app->controller->action->id === 'signup') {
    /**
     * Do not use this code in your template. Remove it.
     * Instead, use the code  $this->layout = '//main-login'; in your controller.
     */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {
   // app\assets\AppAsset::register($this);
    //dmstr\web\AdminLteAsset::register($this);

    //$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    
    GrowlAsset::register($this);
    AppAsset::register($this);
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
       <title><?= Html::encode($this->title . ' :: ' . Yii::$app->name) ?></title>
       <link rel="icon" type="image/png" sizes="32x32" href="<?= Yii::$app->homeUrl ?>img/favicon-32x32.png">
       <link rel="icon" type="image/png" sizes="96x96" href="<?= Yii::$app->homeUrl ?>img/favicon-96x96.png">
       <link rel="icon" type="image/png" sizes="16x16" href="<?= Yii::$app->homeUrl ?>img/favicon-16x16.png">
        <?php $this->head() ?>
        <script src="https://cdn.jsdelivr.net/npm/vue"></script>
        <style>
            .mayusc{
                text-transform: uppercase;
            }
            .modal-fullscreen{
                margin: 0;
                margin-right: auto;
                margin-left: auto;
                width: 100%;
            }
            @media (min-width: 768px) {
                .modal-fullscreen  {
                    width: 100%;
                }
            }
            @media (min-width: 992px) {
                .modal-fullscreen  {
                    width: 100%;
                }
            }
            @media (min-width: 1200px) {
                .modal-fullscreen{
                    width: 100%;
                }
            }
            .widget-toolbox > div > ul.pagination {
               margin: 0;
            }
        </style>


    </head>
    <?php $this->beginBody() ?>
    <body class="no-skin">
        <?= $this->render('_navbar') ?>
        <div class="main-container" id="main-container">
            <?= $this->render('_sidebar') ?>
            <?= $this->render('_content', ['content' => $content]) ?>
            <?= $this->render('_footer') ?>
        </div>
    </body>
    <?php $this->endBody() ?>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
