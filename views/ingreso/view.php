<?php

use kartik\helpers\Html;
use kartik\widgets\Typeahead;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Ingreso */
Yii::$app->formatter->locale = 'es-ES';
if ($model->tipo == 'I') {
	$this->title = 'INGRESO POR INVENTARIO';
	$urlform     = 'unidades/addinventarioing';
	$attributes  = [
		[
			'columnas' => [
				[
					'name'  => 'PROVEEDOR',
					'value' => $model->proveedor->nomproveedor,
				],
				[
					'name'  => 'COMPROBANTE',
					'value' => $model->comprobante->abreviatura.': '.$model->n_comprobante,
				],
			],
		],
		[
			'columnas' => [
				[
					'name'  => 'F. REGISTRO',
					'value' => Yii::$app->formatter->asDate($model->f_registro, 'php:d/m/Y'),
				],
				[
					'name'  => 'BOTICA',
					'value' => $model->botica->nomrazon,
				],
				[
					'name'  => 'ESTADO',
					'value' => $model->getLabelestado(),
				],
			],
		]
	];
} else {
	$this->title = 'INGRESO POR COMPRA';
	$urlform     = 'unidades/addcompra';
	$attributes  = [
		[
			'columnas' => [
				[
					'name'  => 'PROVEEDOR',
					'value' => $model->proveedor->nomproveedor,
				],
				[
					'name'  => 'COMPROBANTE',
					'value' => $model->comprobante->abreviatura.': '.$model->n_comprobante,
				],
				[
					'name'  => 'F. EMISION',
					'value' => Yii::$app->formatter->asDate($model->f_emision, 'php:d/m/Y'),
				],
			],
		],
		[
			'columnas' => [
				[
					'name'  => 'BOTICA',
					'value' => $model->botica->nomrazon,
				],

				[
					'name'  => 'ESTADO',
					'value' => $model->getLabelestado(),
				],
				[
					'name'  => 'IGV',
					'value' => $model->conigv==1?'Si':'No',
				],
			],
		]
	];
}
$this->params['breadcrumbs'][] = ['label' => 'INGRESOS', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->idingreso;
?>
<div class="widget-box widget-color-blue2">
    <div class="widget-header">
        <h3 class="widget-title">INFORMACION DEL INGRESO</h3>
        <div class="widget-toolbar">
            <?php
            if ($model->tipo == 'I' && $model->estado == 'P') {
                echo Html::a('<i class="ace-icon fa fa-check-square-o"></i>', 
                        ['cerrarinv','id'=>$model->idingreso], [
                        'class'        => 'white',
                        'data-confirm' => '¿Desea Finalizar el inventario?'
                ]);
            }
            if ($model->tipo == 'C' && $model->estado == 'P') {
                echo Html::a('<i class="ace-icon fa fa-check-square-o"></i>', 
                        ['confirmcompra','id'=>$model->idingreso], [
                        'class'        => 'white',
                        'data-confirm' => '¿Desea Confirmar toda la compra?'
                ]);
            }?>
            <?=Html::a('<i class="ace-icon fa fa-reply"></i>', ['index'], ['class' => 'white'])?>
		</div>
    </div>
    <div class="widget-body">
        <div class="widget-main">
            <div class="profile-user-info profile-user-info-striped">
				<?php foreach ($attributes as $fila) {?>
					<div class="profile-info-row">
					<?php foreach ($fila['columnas'] as $campo) {?>
						<div class="profile-info-name">
						<?=$campo['name']?>
						</div>
						<div class="profile-info-value">
						<?=$campo['value']?>
						</div>
						<?php }?>
					</div>
				<?php }?>
			</div>
        </div>
        <div class="widget-toolbox no-padding">
            <h5 class="text-center">DETALLE DE INVENTARIO</h5>
			<?php if ($model->estado == 'P') {
				$template = '';
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
									'url'           => Url::to(['verproducto', 'id'           => $model->idingreso]).'?q=%QUERY',
									'wildcard'      => '%QUERY'
								],
								'templates'   => [
									'notFound'   => '<div class="text-danger" style="padding:0 8px">Consulta no encontrada</div>',
									'suggestion' => new JsExpression('Handlebars.compile("<div>{{value}} <span class=\"badge badge-info\">{{laboratorio}}</span></div>")')
								]
							]
						],
						'pluginEvents'      => [
							'typeahead:select' => 'function(e, d) {
												$.ajax({
													url:"'.Url::to([
									$urlform,
									'idingreso'  => $model->idingreso,
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
				<div id="lista-detalle">
				<?php
				if ($model->tipo == 'I') {
					echo $this->render('_listadetInventario', [
							'model' => $model,
						]);
				} else {
					echo $this->render('_listadetCompra', [
							'model' => $model,
						]);
				}
				?>
				</div>
        </div>

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
        var tabla=$(this).data('tabla');
        var idingreso=$(this).data('idingreso');
        if(confirm('¿Desea Eliminar el item asignado?')){
            $.ajax({
                url:indexlink,
                type:'get',
                success:function(data){
                    actualizaTabla();
                }
            });
        }
        return false;
    }));
    $(document).on('click','#update-detalle-item', (function(){
    	 var indexlink=$(this).data('url');
    	 if(confirm('¿Desea Modificar el item seleccionado?')){
			$.ajax({
				url:indexlink,
				type:'get',
				success:function(data){
					$('#modal .modal-content').html(data);
					$('#modal').modal();
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
    function actualizaTabla(){
        $.ajax({
            url:'".Url::to(['ingreso/actabladet', 'id' => $model->idingreso])."',
            type:'get',
            success:function(result){
                $('#lista-detalle').html(result);
            }
        });
    }
 ", $this::POS_END);?>
