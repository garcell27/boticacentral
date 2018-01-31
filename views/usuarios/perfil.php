<?php
use kartik\detail\DetailView;
use kartik\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$this->title = 'PERFIL DE USUARIO';

?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'idusuario',
        'username',
        'email:email',
        'namefull',
        [
            'label'=>'ROL',
            'value'=>$model->role->nombre
        ],
        [
            'label'=>'ACCIONES',
            'value'=> Html::a('Cambiar Contraseña','#',[
                'id'=>'usuario-index-link',
                        'class' => 'btn btn-xs btn-primary',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                        'data-url' => Url::to(['cambiaclave','id'=>$model->idusuario]),
            ]),
            'format'=>'raw'
        ],
    ],
]) ?>
<?php
Modal::begin([
    'id' => 'modal',
    'header' => '<h4 class="modal-title">Registro de Usuarios</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well text-center'><i class='fa fa-3x fa-spin fa-spinner'></i> <h2>Cargando Contenido</h2></div>";

Modal::end();
?>
<div id="plantilla" class="hidden"></div>
<?php
$this->registerJs("
     $(document).ready(function(){
        $('#plantilla').html($('#modal .modal-content').html());
    });
    $(document).on('click', '#usuario-index-link', (function() {
        var indexlink=$(this).data('url');
        $.ajax({
            url:indexlink,
            type:'get',
            beforeSend:function(){
                var html= $('#plantilla').html();
                if($('#modal .modal-content').html()!==html){
                    $('#modal .modal-content').html(html);
                }
            },            
            success:function(data){
                $('#modal .modal-content').html(data);
                $('#modal').modal();
            }        
        });        
    }));            
"); ?>    