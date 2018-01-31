<?php
use cebe\gravatar\Gravatar;
use yii\helpers\Url;
use kartik\helpers\Html;
/* @var $this \yii\web\View */
?>

<div id="navbar" class="navbar navbar ace-save-state">

    <div class="navbar-container ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only"><?= Yii::t('app', 'Toggle sidebar'); ?></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="navbar-header pull-left">
            <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                <small>
                    <?= Html::img(['img/favicon-16x16.png'])?>
                    <?= Yii::$app->name ?>
                </small>
            </a>
        </div>

        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li class="light-blue">

                    <?php if (Yii::$app->user && !Yii::$app->user->isGuest) { ?>
                        <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                            <?= Html::img(['img/avatar2.png'],[
                                'class'=>'nav-user-photo'
                            ]); ?>
                            <span class="user-info">
				<small>Bienvenido,</small> 
                                <?= Yii::$app->user->identity->namefull==null?
                                    Yii::$app->user->identity->username:
                                    Yii::$app->user->identity->namefull ?>
    			    </span>
                            <i class="ace-icon fa fa-caret-down"></i>

                        </a>
                        <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                            <li>
                                <?= Html::a(
                                    '<i class="ace-icon fa fa-user"></i> Perfil',
                                    ['/usuarios/perfil']
                                ) ?>
                            </li>
                            <li class="divider"></li>
                            <li>
                                 <?= Html::a(
                                    '<i class="ace-icon fa fa-power-off"></i> Salir',
                                    ['/site/logout'],
                                    ['data-method' => 'post']
                                ) ?>
                                
                            </li>
                        </ul>
                    <?php } ?>

                </li>
            </ul>
        </div>
    </div>
</div>