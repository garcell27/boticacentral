<?php

use kartik\widgets\Alert;
use kartik\widgets\Typeahead;
use yii\helpers\Url;
use yii\web\JsExpression;

?>
	<div class="widget-box" id="det-ingreso-<?=$model->idsalida?>">
	    <div class="widget-header">
	        <h5 class="widget-title">DETALLE DE SALIDA</h5>
	        <div class="widget-toolbar">
	            <a href="#" data-action="fullscreen" class="orange2">
	                <i class="ace-icon fa fa-expand"></i>
	            </a>
        
                </div>
	    </div>
	    <div class="widget-body">
	        <div class="widget-main">
	
		<p>
                   
		<?=Typeahead::widget([
                        'name'         => 'searchProductos-'.$model->idsalida,
                        'options'      => [
                            'placeholder' => 'Buscar Producto',
                            'class'=>'mayusc'
                        ],
                        'dataset' => [
                            [
                                'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                'display'        => 'value',
                                'remote'         => [
                                    'url'           => Url::to(['verproducto', 'id'=> $model->idsalida]).'?q=%QUERY',
                                    'wildcard'      => '%QUERY'
                                ],
                                'templates' => [
                                    'notFound' => '<div class="text-danger" style="padding:0 8px">Consulta no encontrada</div>',
                                    'suggestion' => new JsExpression('Handlebars.compile("<div>{{value}} <span class=\"badge badge-info\">{{laboratorio}}</span></div>")')
                                ]
                            ]
                        ],
                        'pluginEvents'      => [
                            'typeahead:select' => 'function(e, d) {
                                $.ajax({
                                    url:"'.Url::to([
                                                'unidades/addsalida',
                                                'idsalida'  => $model->idsalida,
                                                'idproducto' => '',
                                        ]).'"+d.idproducto,
                                    type:"get",
                                    beforeSend:function(){
                                        $("#form-add-detalle-'.$model->idsalida.'").slideUp("slow");
                                    },
                                    success:function(data){
                                        $("#form-add-detalle-'.$model->idsalida.'").html(data).slideDown("slow");
                                    }
                                });
                            }',

                        ]
                ]);?>
		</p>
		<div id="form-add-detalle-<?=$model->idsalida?>"></div>		
	        <div id="detalle-table-<?=$model->idsalida?>">
                    <?=$this->render('_listadetSalida', [
			'model' => $model,
                    ])?>
                </div>
	    </div>
	</div>
    </div>
	
<?php

$this->registerJs(
        "
    $(document).on('click', '#cancela-det-".$model->idsalida."', (function() {
        $('#form-add-detalle-".$model->idsalida."').slideUp('slow');
        $('#searchProductos').val()=='';
        return false;
    }));

    "
);

?>
