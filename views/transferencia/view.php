<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\widgets\Alert;
use kartik\widgets\Typeahead;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Transferencia */

$this->title = 'TRANSFERENCIA :'.$model->idtransferencia;
$this->params['breadcrumbs'][] = ['label' => 'Transferencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="widget-box widget-color-blue2">
    <div class="widget-header">
        <h3 class="widget-title">INFORMACION DE LA TRANSFERENCIA</h3>
        <div class="widget-toolbar">
            <?php
            if ($model->estado == 'P') {
                echo Html::a('<i class="ace-icon fa fa-paper-plane"></i> ',
                    ['enviar','id'=>$model->idtransferencia], [
                        'class'        => ' white',
                        'data-confirm' => '¿Desea Enviar la transferencia a su destino'
                    ]);
            } ?>
            <?=Html::a('<i class="ace-icon fa fa-reply"></i>', ['index'], ['class' => 'white'])?>
        </div>
    </div>
    <div class="widget-body">
        <div class="widget-main no-padding">
            <div class="profile-user-info profile-user-info-striped">
                <div class="profile-info-row">
                    <div class="profile-info-name">FECHA REG:</div>
                    <div class="profile-info-value">
                        <?= Yii::$app->formatter->asDatetime($model->create_at,'dd/MM/yyyy hh:mm a')?>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name">ORIGEN:</div>
                    <div class="profile-info-value">
                        <?= $model->botorigen->nomrazon;?>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name">DESTINO:</div>
                    <div class="profile-info-value">
                        <?= $model->botdestino->nomrazon;?>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name">ESTADO:</div>
                    <div class="profile-info-value">
                        <?= $model->getLblEstado();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="widget-toolbox padding-8">
        <h5>DETALLE DE INVENTARIO</h5>
        <?php if ($model->estado == 'P') {
            $template = 'Handlebars.compile("<div>{{value}} '.
                '<span class=\"badge badge-info\">{{laboratorio}}</span> '.
                '<span class=\"badge badge-warning\">{{stock}}</span>'.
                '</div>")';
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
                        'remote'         => [
                            'url'           => Url::to(['verproducto', 'id'           => $model->idtransferencia]).'?q=%QUERY',
                            'wildcard'      => '%QUERY'
                        ],
                        'templates'   => [
                            'notFound'   => '<div class="text-danger" style="padding:0 8px">Consulta no encontrada</div>',
                            'suggestion' => new JsExpression($template)
                        ]
                    ]
                ],
                'pluginEvents'      => [
                    'typeahead:select' => 'function(e, d) {
                                    $.ajax({
                                        url:"'.Url::to(['add-item',
                            'idtransferencia'  => $model->idtransferencia,
                            'idproducto' => '',
                        ]).'"+d.idproducto,
                                        type:"get",
                                        
                                        success:function(data){                                            
                                            $("#modal .modal-content").html(data);
                                            $("#modal").modal();
                                        }
                                    });
                                }',

                ]
            ]);?><br>
        <?php }?>
        <?php Pjax::begin(['id'=>'lista-detalle']);?>
        <?php if(!count($model->items)):
            echo Alert::widget([
                'type' => Alert::TYPE_INFO,
                'body' => 'No contiene ningun detalle agregado',
                'closeButton'=>false,
            ]);
        else:?>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>LABORATORIO</th>
                    <th>CANTIDAD</th>
                    <th>UNIDAD</th>
                    <?php if($model->estado=='P'){?><th></th><?php }?>
                </tr>

                </thead>
                <tbody>
                <?php foreach ($model->items as $detalle):?>
                    <tr>
                        <td><?= $detalle->unidad->producto->descripcion ?></td>
                        <td><?= $detalle->unidad->producto->laboratorio->nombre ?></td>
                        <td class="text-right"><?= $detalle->cantidad ?></td>
                        <td><?= $detalle->unidad->descripcion ?></td>
                        <?php if($model->estado=='P'){?>
                            <td><?=Html::a('<i class="btn-xs fa fa-trash"></i>','#',[
                                    'id'=>'delete-detalle-item',
                                    'data-url'=>  Url::to([
                                        'eliminadetalle',
                                        'id'=>$detalle->iddetalle,
                                    ]),
                                    'class'=>'btn btn-danger btn-xs',
                                ]);?></td>
                        <?php }?>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        <?php endif;?>
        <?php Pjax::end();?>
    </div>

</div>
<?php
Modal::begin([
    'id'     => 'modal',
    'header' => '<h4 class="modal-title">Registro de Ingresos</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well'></div>";

Modal::end();
?>
<?php
$this->registerJs("
    $(document).on('click', '#delete-detalle-item', (function() {
        var indexlink=$(this).data('url');
        if(confirm('¿Desea Eliminar el item asignado?')){
            $.ajax({
                url:indexlink,
                type:'get',
                success:function(data){
                   $.pjax.reload({container:'#lista-detalle'});
                }
            });
        }
        return false;
    }));
    $(document).on('click', '#cancela-det', (function() {
        $('#modal').modal('hide');
        $('#search-prod').val('');
        return false;
    }));
   
 ", $this::POS_END);?>