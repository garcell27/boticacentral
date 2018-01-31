<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\Typeahead;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\models\Stock;
/* @var $this yii\web\View */
/* @var $model app\models\Inventario */

$this->title = "INVENTARIO ACTIVO EN BOTICA : ".$model->botica->nomrazon;
$this->params['breadcrumbs'][] = ['label' => 'Inventarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$columnas=[
    [
        'attribute'=>'codproducto',
        'value'=>function($model){
            return $model->unidad->producto->idproducto;
        },
        'hAlign'=> GridView::ALIGN_RIGHT,
    ],
    [
        'attribute'=>'producto',
        'value'=>function($model){
            return $model->unidad->producto->descripcion;
        },
    ],
    [
        'header'=>'LABORATORIO',
        'value'=>function($model){
            return $model->unidad->producto->laboratorio->nombre;
        },
    ],
    [
       'attribute'=>'cantestimada',
        'hAlign'=> GridView::ALIGN_RIGHT,
        'format'=>['decimal', 2],
    ],
    [
        'attribute'=>'cantinventariada',
        'hAlign'=> GridView::ALIGN_RIGHT,
        'format'=>['decimal', 2],
    ],
    [
        'attribute'=>'cantvendida',
        'hAlign'=> GridView::ALIGN_RIGHT,
        'format'=>['decimal', 2],
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{deletedetalle}',
        'buttons' => [
            'deletedetalle' => function ($url, $model, $key) {
                if($model->accion==null){
                    return Html::a('<span class="fa fa-trash"></span>', $url, [
                        'id'=>'ingreso-delete-link',
                        'class' => 'btn btn-danger btn-xs',
                        'title' => Yii::t('yii', 'Delete'),
                        'data-confirm'=>'¿Desea eliminar este registro?',
                        'data-pjax' => '0',
                    ]);
                }else{
                    return '';
                }

            },
        ]
    ],

];

$progreso=count($model->detalleInventarios)*100/$stocks;
if($progreso<25){
    $progresolbl='danger';
}elseif($progreso<65){
    $progresolbl='warning';

}else if($progreso<100){
    $progresolbl='info';
}else{
    $progresolbl='success';
}
$prodajustados=0;
$prodexcedentes=0;
$prodfaltantes=0;
$indiceajustados=0;
$indiceexcedentes=0;
$indicefaltantes=0;
$excedentexprocesar=0;
$faltantexprocesar=0;
foreach($model->detalleInventarios as $item){
    if($item->cantestimada==($item->cantinventariada+$item->cantvendida)){
        $prodajustados++;
    }elseif($item->cantestimada<($item->cantinventariada+$item->cantvendida)){
        $prodexcedentes++;
        if($item->accion==null){
            $excedentexprocesar++;
        }
    }elseif($item->cantestimada>($item->cantinventariada+$item->cantvendida)){
        $prodfaltantes++;
        if($item->accion==null){
            $faltantexprocesar++;
        }
    }

}
if(count($model->detalleInventarios)>0){
    $indiceajustados=$prodajustados/count($model->detalleInventarios)*100;
    $indiceexcedentes=$prodexcedentes/count($model->detalleInventarios)*100;
    $indicefaltantes=$prodfaltantes/count($model->detalleInventarios)*100;
}
?>

<div class="widget-box widget-color-blue2">
    <div class="widget-header">
        <h3 class="widget-title">INFORMACION DEL INVENTARIO</h3>
        <div class="widget-toolbar">
            <?=$model->botica->tipo_almacen==1?Html::a('<i class="ace-icon fa fa-eraser"></i>', ['vaciar','id'=>$model->idinventario], ['class' => 'white']):''?>
            <?=Html::a('<i class="ace-icon fa fa-reply"></i>', ['index'], ['class' => 'white'])?>
        </div>
    </div>
    <div class="widget-body">
        <div class="widget-main">
            <div class="infobox-container">
                <div class="infobox infobox-black">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-dropbox"></i>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-data-number"><?= $stocks;?></span>
                        <div class="infobox-content">
                            PRODUCTOS
                        </div>
                    </div>

                </div>
                <div class="infobox infobox-blue">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-pencil-square-o"></i>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-data-number"><?= count($model->detalleInventarios);?></span>
                        <div class="infobox-content">
                            INVENTARIADOS
                        </div>
                    </div>
                    <div class="badge badge-inverse"> <?= number_format($progreso,2)?>% </div>
                </div>
                <div class="infobox infobox-green">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-check-square-o"></i>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-data-number"><?=$prodajustados?></span>
                        <div class="infobox-content">
                            CONFORMES
                        </div>
                    </div>
                    <div class="badge badge-info">
                        <?= number_format($indiceajustados,2)?>%
                    </div>
                </div>
                <div class="infobox infobox-red">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-plus-square-o"></i>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-data-number"><?=$prodexcedentes?></span>
                        <div class="infobox-content">
                            CON EXCEDENTES
                        </div>
                    </div>
                    <div class="stat stat-success">
                        + <?= number_format($indiceexcedentes,2)?>%
                    </div>
                </div>
                <div class="infobox infobox-orange">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-minus-square-o"></i>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-data-number"><?=$prodfaltantes?></span>
                        <div class="infobox-content">
                            CON FALTANTES
                        </div>
                    </div>
                    <div class="stat stat-important">
                        - <?= number_format($indicefaltantes,2)?>%
                    </div>
                </div>
            </div>
            <?php if($model->estado!='C'){?>
            <div>
                <h6>PROGRESO</h6>
                <div class="progress pos-rel" data-percent="<?= number_format($progreso,2)?>%">
                    <div class="progress-bar progress-bar-<?= $progresolbl;?>" style="width: <?= $progreso?>%;"></div>
                </div>
            </div>
            <?php }?>
            <div class="space-6"></div>
            <div >
                <?php
                    if($prodajustados>0){
                        echo Html::a('REPORTE DE CONFORMES',
                            [
                                'reporte-detalle',
                                'id'=>$model->idinventario,
                                'tipo'=>'conforme'
                            ],
                            [
                                'target'=>'_blank',
                                'class'=>'btn btn-default'
                            ]).' ';
                    }

                    if($prodexcedentes>0){
                        echo Html::a('REPORTE DE EXCEDENTES',
                            [
                                'reporte-detalle',
                                'id'=>$model->idinventario,
                                'tipo'=>'excedente'
                            ],
                            [
                                'target'=>'_blank',
                                'class'=>'btn btn-grey'
                            ]).' ';
                    }
                    if($prodfaltantes>0){
                        echo Html::a('REPORTE DE FALTANTES',
                            [
                                'reporte-detalle',
                                'id'=>$model->idinventario,
                                'tipo'=>'faltante'
                            ],
                            [
                                'target'=>'_blank',
                                'class'=>'btn btn-purple'
                            ]).' ';
                    }
                    if($prodexcedentes>0 && $progreso==100 && $excedentexprocesar>0){
                        echo Html::a('REGULARIZAR EXCEDENTES',
                                [
                                    'regularizaingreso',
                                    'id'=>$model->idinventario,
                                ],
                                [
                                    'class'=>'btn btn-yellow',
                                    'data-confirm'=>'¿Desea regularizar los productos excedentes?'
                                ]).' ';
                    }
                    if($prodfaltantes>0 && $progreso==100 && $faltantexprocesar>0){
                        echo Html::a('REGULARIZAR FALTANTES',
                            [
                                'regularizasalida',
                                'id'=>$model->idinventario,
                            ],
                            [
                                'class'=>'btn btn-light',
                                'data-confirm'=>'¿Desea regularizar los productos faltantes?'
                            ]);
                    }
                    if($progreso==100 && $excedentexprocesar==0 && $faltantexprocesar==0 && $model->estado=='A'){
                        echo Html::a('ARCHIVAR INVENTARIO',
                            [
                                'archivar',
                                'id'=>$model->idinventario,
                            ],
                            [
                                'class'=>'btn btn-inverse',
                                'data-confirm'=>'¿Desea archivar todo el inventario?'
                            ]);
                    }
                ?>
            </div>
        </div>
        <div class="widget-toolbox padding-8">
            <h5>DETALLE DE INVENTARIO</h5>
            <?php if ($model->estado == 'A') {
                $template = '<div class="profile-user-info profile-user-info-striped">'.
                                '<div class="profile-info-row">'.
                                    '<div class="profile-info-name">CODIGO </div>'.
                                    '<div class="profile-info-value">{{idproducto}}</div>'.
                                '</div>'.
                                '<div class="profile-info-row">'.
                                    '<div class="profile-info-name">PRODUCTO </div>'.
                                    '<div class="profile-info-value">{{value}}</div>'.
                                '</div>'.
                                '<div class="profile-info-row">'.
                                    '<div class="profile-info-name">LABORATORIO </div>'.
                                    '<div class="profile-info-value">{{laboratorio}}</div>'.
                                '</div>'.
                                '<div class="profile-info-row">'.
                                    '<div class="profile-info-name">DISPONIBLE </div>'.
                                    '<div class="profile-info-value">{{disponible}}</div>'.
                                '</div>'.
                            '</div>';
                echo Typeahead::widget([
                    'id'           => 'search-prod',
                    'name'         => 'searchProductos',
                    'options'      => [
                        'placeholder' => 'Buscar Producto',
                        'class'       => 'mayusc form-control'
                    ],
                    'dataset' => [
                        [
                            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                            'display'        => 'value',
                            //'prefetch'=>Url::to(['verproducto', 'id' => $model->idinventario]).'?q=',
                            'remote'         => [
                                'url'           => Url::to(['verproducto', 'id' => $model->idinventario]).'?q=%QUERY',
                                'wildcard'      => '%QUERY'
                            ],
                            'templates'   => [
                                'notFound'   => '<div class="text-danger" style="padding:0 8px">Consulta no encontrada</div>',
                                'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                            ]
                        ]
                    ],
                    'pluginOptions'=>[
                        'highlight'=>true,
                        'minLength'=>0,
                    ],
                    'pluginEvents'      => [
                        'typeahead:select' => 'function(e, d) {
                            $.ajax({
                                url:"'.Url::to([
                                        'agregadetalle',
                                        'idinventario'  => $model->idinventario,
                                        'idproducto' => '',
                                ]).'"+d.idproducto,
                                type:"get",
                                beforeSend:function(){
                                    //$("#search-prod").attr("disabled","disabled");
                                },
                                success:function(data){
                                    //$("#form-add-detalle").slideUp("fast",function(){
                                        //$("#form-add-detalle").html(data).slideDown("fast");
                                    //});
                                    $("#modal .modal-content").html(data);
                                    $("#modal").modal();
                                }
                            });
                        }',

                    ]
                ]);?><br>
            <?php }?>
            <?= GridView::widget([
                'id'=>'detalleinv-grid',
                'dataProvider' => $dpdetalles,
                'filterModel' => $smdetalles,
                'panelPrefix'=>'widget-box widget-',
                'columns' => $columnas,
                'pjax'=>true,
                'panel' => [
                    'heading'=>'<i class="fa fa-adjust"></i> DETALLE DE INVENTARIO',
                    'type'=>'info',
                ],
                'toolbar'=>[]
            ]); ?>
        </div>
    </div>
</div>
<?php
Modal::begin([
    'id'     => 'modal',
    'size'=>Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title">Registro de Ingresos</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well'></div>";

Modal::end();
?>
