<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\UnidadesSearch;
use app\models\SugerenciaSearch;
use yii\helpers\Json;
use app\models\DetalleProducto;
/* @var $this yii\web\View */
/* @var $model app\models\Productos */

$this->title = $model->descripcion;

$searchUnd = new UnidadesSearch();
$searchUnd->idproducto=$model->idproducto;
$dataProviderUnd = $searchUnd->search(Yii::$app->request->queryParams);

$searchSug= new SugerenciaSearch();
$searchSug->idproducto=$model->idproducto;
$dataProviderSug= $searchSug->search(Yii::$app->request->queryParams);


?>

<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-xs-12">
                    <div class="widget-box widget-color-blue2">
                        <div class="widget-header">
                            <h3 class="widget-title">
                                INFORMACION DETALLADA
                            </h3>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main no-padding">
                                <?= DetailView::widget([
                                    'model' => $model,
                                    'attributes' => [
                                        'idproducto',
                                        [
                                            'attribute'=>'idcategoria',
                                            'value'=>$model->categoria->descripcion,
                                        ],
                                        [
                                            'attribute'=>'idlaboratorio',
                                            'value'=>$model->laboratorio->nombre,
                                        ],
                                        'descripcion',
                                        'caducidad:boolean',
                                    ],
                                ]); ?>
                            </div>
                            <?php if($model->detalle!=null || $model->detalle!=''){?>
                            <div class="widget-toolbox clearfix">
                                <h3>ADICIONAL</h3>
                                <dl class="dl-horizontal">
                                    <?php
                                        $detalle=new DetalleProducto();
                                        $detalle->procesar($model->detalle);
                                        foreach($detalle->attributes as $campo =>$valor){
                                            if($valor!=null){
                                                echo '<dt>'.$detalle->getAttributeLabel($campo).'</dt>';
                                                echo '<dd>'.$valor.'</dd>';
                                            }
                                        }
                                    ?>
                                </dl>

                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space"></div>
            <?= $this->render('../unidades/index', [
                'searchModel' => $searchUnd,
                'dataProvider'=> $dataProviderUnd,
                'producto'=>$model,
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $this->render('_viewStock', [
                'model'=>$model
            ]) ?>
            <div class="space"></div>
            <?= $this->render('../sugerencia/index', [
                'searchModel' => $searchSug,
                'dataProvider'=> $dataProviderSug,
                'producto'=>$model,
            ]) ?>
        </div>
    </div>
</div>
