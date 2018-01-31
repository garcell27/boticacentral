<?php

use kartik\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TalonariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'TALONARIOS';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="talonarios-index">

    <h1><?=Html::encode($this->title)?></h1>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<p>
<?=Html::a('Create Talonarios', ['create'], ['class' => 'btn btn-success'])?>
</p>
<?=GridView::widget([
		'dataProvider' => $dataProvider,
		'columns'      => [
			['class'      => 'yii\grid\SerialColumn'],

			'idtalonario',
			[
				'attribute' => 'idcomprobante',
				'value'     =>

function ($model) {
					return $model->comprobante->descripcion;
				}
			],
			[
				'header' => 'N° DOCUMENTO',
				'value'  => function ($model) {
					return $model->getNdocumento();
				}
			],
			[
				'attribute' => 'idbotica',
				'value'     => function ($model) {
					return $model->botica->nomrazon;
				}
			],

			['class' => 'yii\grid\ActionColumn'],
		],
	]);?>
</div>
