<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

/* @var $this \yii\web\View */
/* @var $content string */
$imgPerfil=$directoryAsset.'/img/avatar.png';
/*if(Yii::$app->user->identity->foto==null){

}else{
    $dtimg= Json::decode(Yii::$app->user->identity->mimefoto);
    $imgPerfil='data:'.$dtimg['type'].';base64,'.base64_encode(Yii::$app->user->identity->foto);
}*/

?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">




                <!-- User Account: style can be found in dropdown.less -->

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                            <img src="<?= $imgPerfil ?>" class="user-image" alt="User Image"/>

                        <span class="hidden-xs">
                            <?php if(Yii::$app->user->identity->namefull==null){
                                echo Yii::$app->user->identity->username;
                            }else{
                                echo Yii::$app->user->identity->namefull;
                            }?>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">

                                <img src="<?= $imgPerfil ?>" class="img-circle" alt="User Image"/>


                            <p>
                                <?php if(Yii::$app->user->identity->namefull==null){
                                    echo Yii::$app->user->identity->username;
                                }else{
                                    echo Yii::$app->user->identity->namefull;
                                }?> - <?= Yii::$app->user->identity->role->nombre ?>

                            </p>
                        </li>
                        <!-- Menu Body -->

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= Url::to(['site/perfil'])?>" class="btn btn-default btn-flat">Perfil</a>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Salir',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>


            </ul>
        </div>
    </nav>
</header>
