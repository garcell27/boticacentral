<?php
/* @var $this yii\web\View */
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;

$this->title = 'TRANSFERENCIA RAPIDA';
$this->params['breadcrumbs'][] = $this->title;

?>

<div id="stock-index" class="widget-box widget-color-purple">
    <div class="widget-header">
        <h4 class="widget-title">STOCK DE PRODUCTOS GESTIONABLES PARA TRANSFERENCIA</h4>
        <div class="widget-toolbar"></div>
    </div>
    <div class="widget-body">
        <div class="widget-main">
            <div class="row">
                <div class="col-xs-10 col-xs-offset-1">
                    <select id="btcentral-select" class="form-control">
                        <?php foreach ($botcentrals as $btc):?>
                        <option value="<?= $btc->idbotica?>" >
                            <?=$btc->nomrazon;?>
                        </option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <h3 v-if="loading" class="text-center"><i class="fa fa-spin fa-spinner"></i> Obteniendo informacion</h3>
            <div v-else>
                <div class="space-12"></div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th v-for="columna in datatabla.columnas">{{ columna.label }}</th>
                        </tr>                        
                    </thead>
                    <tbody>
                        <tr v-for="row in datatabla.data">
                              <td v-for="columna in datatabla.columnas">
                                {{ getcampo(row,columna.attr) }}
                              </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<?php
Modal::begin([
    'id' => 'modal',
    'size'=> Modal::SIZE_DEFAULT,
    'header' => '<h4 class="modal-title">Registro de Transferencia</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well text-center'><i class='fa fa-3x fa-spin fa-spinner'></i> <h2>Cargando Contenido</h2></div>";

Modal::end();
?>
<div id="plantilla" class="hidden"></div>

<?php
$script=<<<JS

    var mivue = new Vue({
        el:'#stock-index',
        data:{
            stocks:null,
            loading:true,
            datatabla:{
                columnas:[
                    {label:'PRODUCTO',attr:'producto'},
                    {label:'LABORATORIO',attr:'laboratorio'},
                ],
                data:[]
            },
        },
        created:function(){
             var btselect=$("#btcentral-select").val();
             $.ajax({
                url:miurl+'transfrapida/lista-stock/'+btselect,
                type:'get',
                success:function(data){
                    mivue.\$data.datatabla.data=data;
                    mivue.\$data.loading=false;    
                }
             });
        },

        methods:{
            getcampo:function(fila,campo){
                var valor='';
                if(campo !== null){
                    valor=fila[campo];
                }else{

                }
                return valor;
            }
        }

    });

    $(document).ready(function(){
        $('#plantilla').html($('#modal .modal-content').html());
    });
    $(document).on('click', '#transferencia-index-link', (function() {
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
JS;
$this->registerJs('var miurl="'.Yii::$app->homeUrl.'";');
$this->registerJs($script);
?>