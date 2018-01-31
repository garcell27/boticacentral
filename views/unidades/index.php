<div class="row">
    <div class="col-xs-12">
        <div id="lista-unidades" class="unidades-index">
            <?= $this->render('_listaund', [
                'searchModel' => $searchModel,
                'dataProvider'=> $dataProvider,
                'producto'=>$producto,
            ]) ?>
        </div>
        <div id="registro-unidades"></div>
    </div>
</div>


