<?php
use yii\helpers\Html;
use cornernote\ace\web\AceAsset;

AceAsset::register($this);

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <link rel="icon" type="image/png" sizes="32x32" href="<?= Yii::$app->homeUrl ?>img/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="<?= Yii::$app->homeUrl ?>img/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= Yii::$app->homeUrl ?>img/favicon-16x16.png">
        <?php $this->head() ?>
    </head>
    <?php $this->beginBody() ?>
    <body class="no-skin">
        <div class="main-container" id="main-container">
            <?= $this->render('_content', ['content' => $content]) ?>
        </div>
    </body>
    <?php $this->endBody() ?>
</html>
<?php $this->endPage() ?>