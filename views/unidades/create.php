<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Unidades */

$this->title = 'REGISTRAR UNIDAD';
?>
<div class="widget-box widget-color-green">
    <div class="widget-header">
        <h3 class="widget-title"><?= Html::encode($this->title) ?></h3>
    </div>

    <?php
    if($model->tipo=='P'){
        echo $this->render('_formprimary', [
            'model' => $model,
        ]);
    }else{
        echo $this->render('_form', [
            'model' => $model,
        ]);
    }

?>
</div>
