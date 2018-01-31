<?php
/* @var $this yii\web\View */
use yii\bootstrap\Html;
use yii\helpers\Url;
$this->title = 'Sincronizacion de los Puntos de venta';
?>

    <input type="hidden" id="boticas" value="<?= htmlspecialchars($boticasjs);?>"/>
    <input type="hidden" id="miurl" value="<?= Yii::$app->homeUrl;?>"/>
    <input type="hidden" id="urlverifaciones" value="<?= Url::to(['verificamodificaciones','tabla'=>'']);?>"/>
    <input type="hidden" id="urlsincronizaciones" value="<?= Url::to(['sincronizabotica','id'=>'']);?>"/>
<p>
    <?= Html::button('Verificar modificaciones', [
            'class'=>'btn btn-info',
            'id'=>'verifica-sistema',
        ])?>
    <?= Html::button('Sincronizar a todas las boticas', [
        'class'=>'btn btn-info',
        'id'=>'sincroniza-boticas',
    ])?>
</p>
<div id="mensajes" class="row">
    
</div>

<?php
$script= <<<JS
    var boticas=JSON.parse($("#boticas").attr("value"));
    var verificar=[
        {table:"roles",valor:0},
        {table:"unidades",valor:0},
        {table:"clientes",valor:0},
        {table:"comprobante",valor:0},
        {table:"sugerencia",valor:0},
    ];

    $(document).on("click", "#verifica-sistema",function(){
        for(var i=0;i<verificar.length;i++){
            verificarTablas(verificar[i]);
        }
    });
    $(document).on("click", "#sincroniza-boticas",function(){       
        for(var i=0;i<boticas.length;i++){
            sincronizabotica(boticas[i]);
        }
    });
    function verificarTablas(registro){
        $.ajax({
            url:$("#urlverifaciones").attr("value")+registro.table,
            async:false,
            cache:false,
            type:"get",
            beforeSend:function(){
                var contenido='<h3 class="center"><i class="fa fa-3x fa-spin fa-spinner"></i> <br>'+
                    'Actualizando tabla :'+registro.table+'</h3>';
                if($("#mensajes div#cl"+registro.table).length){
                    $("#cl"+registro.table).html(contenido);
                }else{
                    $("#mensajes").append('<div id="cl'+registro.table+'" class="col-sm-4">'+contenido+'</div>');    
                }
            
            },
            success:function(html){                
                $("#cl"+registro.table).html(html);                
            }
        });       
    }
    function sincronizabotica(botica){
        $.ajax({
            url:$("#urlsincronizaciones").attr("value")+botica.idbotica,
            async:false,
            cache:false,
            type:"get",
            beforeSend:function(){
                var contenido='<h3 class="center"><i class="fa fa-3x fa-spin fa-spinner"></i> <br>'+
                    'Actualizando botica :'+botica.nomrazon+'</h3>';
                if($("#mensajes div#msbotica-"+botica.idbotica).length){
                    $("#cl"+botica.idbotica).html(contenido);
                }else{
                    $("#mensajes").append('<div id="msbotica-'+botica.idbotica+'" class="col-sm-4">'+contenido+'</div>');    
                }
            
            },
            success:function(html){                
                $("#msbotica-"+botica.idbotica).html(html);                
            }
        });       
    }
JS;
$this->registerJs($script);
?>