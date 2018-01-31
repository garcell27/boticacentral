<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Ingreso';

$fieldOptions1 = [
	'options'       => ['class'       => 'form-group has-feedback'],
	'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
	'options'       => ['class'       => 'form-group has-feedback'],
	'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<div class="main-container">
    <div class="main-content">
        <div class="col-sm-10 col-sm-offset-1">
            <div class="login-container">
                <div class="center">
                    <h1> <span class="white">Sistema de boticas</span></h1>
                </div>
                <div class="space-6"></div>
                <div class="position-relative">
                    <div id="login-box" class="login-box widget-box visible no-border">
                        <div class="widget-body">
                            <div class="widget-main">
                                <h4 class="header blue lighter bigger">Ingrese Usuario y Contraseña</h4>
<?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]);?>

<?=$form
	->field($model, 'username', $fieldOptions1)
	->label(false)
	->textInput(['placeholder' => $model->getAttributeLabel('username')])?>

<?=$form
	->field($model, 'password', $fieldOptions2)
	->label(false)
	->passwordInput(['placeholder' => $model->getAttributeLabel('password')])?>
<div class="row">
                                                        <div class="col-xs-8">
<?=$form->field($model, 'rememberMe')->checkbox()?>
</div>
                                                        <!-- /.col -->
                                                        <div class="col-xs-4">
<?=Html::submitButton('Acceder', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button'])?>
</div>
                                                        <!-- /.col -->
                                                    </div>


<?php ActiveForm::end();?>
</div>

                        </div>
                    </div>

                </div>
            </div>

    <!-- /.login-box-body -->
        </div>

    </div>
    <!-- /.login-logo -->

</div><!-- /.login-box -->
