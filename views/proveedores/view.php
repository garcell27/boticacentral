<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
?> 
<div class="widget-header">
        <h5 class="widget-title">INFORMACION DEL PROVEEDOR</h5>
        <div class="widget-toolbar">
            <?= Html::a('<i class="ace-icon fa fa-expand"></i>', '#', [                    
                    'class'     => 'orange',                    
                    'data-action' => 'fullscreen',
            ]);?>
            <?= Html::a('<i class="ace-icon fa fa-edit"></i>', '#', [
                    'id'        => 'proveedores-index-link',
                    'class'     => 'blue',
                    'title' => Yii::t('app', 'Update'),
                    'data-url' =>Url::to(['update','id'=>$model->idproveedor]),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
            ]);?>
            <?= count($model->labproveedores)>0?'':Html::a('<i class="ace-icon fa fa-trash"></i>', '#', [
                    'id'            =>  'proveedores-delete-link',
                    'class'         =>  'red',
                    'title' => Yii::t('yii', 'Delete'),
                    'data-url'      =>   Url::to(['delete','id'=>$model->idproveedor]),
                    'pjax-container'=>'proveedores-grid-pjax',
                    'data-pjax' => '0',
            ]);?>
        </div>
    </div>
    <div class="widget-body">
        <div class="widget-main">
             <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'idproveedor',
                    'nomproveedor',
                    'direccion',
                    'tipodoc',
                    'docidentidad',
                ],
            ]) ?>
            <div class="pull-right">
                <?= Html::a('<i class="ace-icon fa fa-plus"></i>','#',[
                    'id'=>'proveedores-index-link',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'class'=>'btn btn-success btn-xs',
                    'data-url'=> Url::to(['addlaboratorio','id'=>$model->idproveedor]),
                    'data-pjax' => '0',
                ]);?>
            </div>
            <h4 class="page-header"><?= count($model->labproveedores)?> LABORATORIOS</h4>
            <div class="row">
                <?php foreach ($model->labproveedores as $lab):?>
                <div class="col-md-6">
                    <div class="well well-sm">
                        <?= $lab->laboratorio->nombre;?>
                        <div class="pull-right">
                            <?= Html::a('<i class="ace-icon fa fa-trash"></i>', '#', [
                                    'id'            =>  'proveedor-delete-link',
                                    'class'         =>  'red',
                                    'title' => Yii::t('yii', 'Delete'),
                                    'data-url'      =>   Url::to(['delete-asiglab','id'=>$lab->idlabprov]),
                                    'idprov'=>$model->idproveedor,
                                    'data-pjax' => '0',
                            ]);?>
                        </div>
                    </div>                        
                </div>
                <?php endforeach;?>
            </div>
        </div>
        
    </div>
