<?php
use kartik\widgets\Alert;
use kartik\widgets\Typeahead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\web\JsExpression;
?>
<div class="widget-box" id="det-ingreso-<?=$model->idingreso?>">
    <div class="widget-header">
        <h5 class="widget-title">DETALLE DE COMPRA</h5>
        <div class="widget-toolbar">
            <a href="#" data-action="fullscreen" class="orange2">
                <i class="ace-icon fa fa-expand"></i>
            </a>
            <?php if($model->estado=='P'){
                echo Html::a('<i class="ace-icon fa fa-close"></i>', '#', [
                    'id'        => 'ingreso-cerrar-link',
                    'class'     => 'purple',
                    'data-url'  => Url::to(['confingreso', 'id'  => $model->idingreso]),
                    'data-pjax' => '0',
            ]);}?>
        </div>        
    </div>
    <div class="widget-body">
        <div class="widget-main">
           <?php if ($model->estado == 'P') {
                $template='';
                echo Typeahead::widget([
                        'id'=>'search-prod-'.$model->idingreso,
                        'name'  =>  'searchProductos',
                        'options'   => [
                            'placeholder' => 'Buscar Producto',
                            'class'=>'mayusc form-control'
                        ],
                        'dataset' => [
                            [
                                'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                'display'        => 'value',
                                'remote'         => [
                                    'url'           => Url::to(['verproducto', 'id'=> $model->idingreso]).'?q=%QUERY',
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
                                                'unidades/addcompra',
                                                'idingreso'  => $model->idingreso,
                                                'idproducto' => '',
                                        ]).'"+d.idproducto,
                                    type:"get",
                                    beforeSend:function(){
                                        $("#search-prod-'.$model->idingreso.'").attr("disabled","disabled");                                        
                                    },
                                    success:function(data){
                                        $("#form-add-detalle-'.$model->idingreso.'").slideUp("fast",function(){
                                            $("#form-add-detalle-'.$model->idingreso.'").html(data).slideDown("fast");
                                        });
                                    }
                                });
                            }',

                        ]
                ]);?>                    
            <div id="form-add-detalle-<?=$model->idingreso?>"></div>
            <hr>
            <?php }?>                
            <div id="detalle-table-<?=$model->idingreso?>">
                <?=$this->render('_listadetCompra', [
                        'model' => $model,
                ])?>
            </div> 
        </div>
    </div>
</div>

