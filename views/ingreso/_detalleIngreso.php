<?php


if ($model->tipo == 'I') {
    echo $this->render('_detInventario', [
            'model' => $model,
    ]);
} else {
    echo $this->render('_detCompra', [
            'model' => $model,
    ]);
}
if($model->estado=='P'){
    $this->registerJs(
            "
        $(document).on('click', '#cancela-det-".$model->idingreso."', (function() {
            $('#form-add-detalle-".$model->idingreso."').slideUp('slow');
            $('#search-prod-".$model->idingreso."').val('');    
            $('#search-prod-".$model->idingreso."').removeAttr('disabled','disabled'); 
            return false;
        }));
        
        "
    );
}
?>
