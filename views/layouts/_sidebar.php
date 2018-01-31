<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */

$controller = Yii::$app->controller;
if(!Yii::$app->user->isGuest){
?>
<div id="sidebar" class="sidebar responsive">
    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
<?php echo Html::a('<i class="ace-icon icon ion-bag"></i>', ['/ingreso'], ['class'                           => 'btn btn-success']);?>
            <?php echo Html::a('<i class="ace-icon fa fa-exchange"></i>', ['/transferencia'], ['class'       => 'btn btn-info']);?>
            <?php echo Html::a('<i class="ace-icon icon ion-cube"></i>', ['/productos'], ['class'            => 'btn btn-warning']);?>
            <?php echo Html::a('<i class="ace-icon icon ion-android-contacts"></i>', ['/usuarios'], ['class' => 'btn btn-danger']);?>
</div>
        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
<?php echo Html::a('', ['/ingreso'], ['class'                   => 'btn btn-success']);?>
            <?php echo Html::a('', ['/transferencia'], ['class' => 'btn btn-info']);?>
            <?php echo Html::a('', ['/productos'], ['class'     => 'btn btn-warning']);?>
            <?php echo Html::a('', ['/usuarios'], ['class'      => 'btn btn-danger']);?>
</div>
    </div>
<?=\cornernote\ace\widgets\Menu::widget(
	[
		'options' => ['class' => 'nav nav-list'],
		'items'   => [

			[
				'label' => 'INGRESOS',
				'icon'  => 'icon ion-bag',
				'url'   => ['/ingreso'],

			],
			[
				'label' => 'TRANSFER. <i class="fa fa-star fa-spin text-danger"></i>',
				'icon'  => 'fa fa-exchange',
				'url'   => ['#'],
				'encode'=>false,
				'items'=>[
					['label' => 'LISTADO', 'icon' => 'icon fa fa-list', 'url' => ['/transferencia'], ],
					['label' => 'REG. RAPIDO', 'icon' => 'icon fa fa-check', 'url' => ['/transfrapida'], ],
				]
			],
			[
				'label' => 'INVENTARIOS',
				'icon'  => 'fa fa-adjust',
				'url'   => ['/inventario'],

			],
			[
				'label' => 'G. LABORATORIOS',
				'icon'  => 'fa fa-flask',
				'url'   => ['/laboratorios'],
			],
			[
				'label' => 'G. CATEGORIAS',
				'icon'  => 'fa fa-cubes',
				'url'   => ['/categorias'],
			],

			[
				'label' => 'G. PRODUCTOS ',
				'icon'  => 'icon ion-cube',
				'url'   => ['/productos'],
				'encode'=>false,
			],
			[
				'label' => 'G. SUCURSAL',
				'icon'  => 'fa fa-hospital-o',
				'url'   => ['/botica'],
			],
            [
                'label' => 'STOCK',
                'icon'  => 'fa fa-bar-chart',
                'url'   => ['/stock'],
            ],
			[
				'label'   => 'INFORMES ',
				'icon'    => 'icon fa fa-book',
				'url'     => '#',
				'items'   => [
					['label' => 'INF. DIARIO', 'icon' => 'icon fa fa-calendar', 'url' => ['/infodiario'], ],
					['label' => 'INF. RANKING', 'icon' => 'icon fa fa-star', 'url' => ['/inforanking'], ],
					['label' => 'INF. UTILIDADES', 'icon' => 'icon fa fa-line-chart', 'url' => ['/infoutilidades'], ],
					['label' => 'INF. REQUERIMIENTOS', 'icon' => 'icon fa fa-shopping-basket', 'url' => ['/requerimientos'], ],
					[
						'label' => 'INF. ALCOHOL <i class="ion-android-checkbox-outline text-success bigger-130"></i>',
						'icon' => 'icon fa fa-shopping-basket',
						'url' => ['/inforeportalcohol'],
						'encode'=>false,
					],
				],

			],
			[
				'label'   => 'AGENDAS',
				'icon'    => 'fa fa-address-book',
				'url'     => '#',
				'items'   => [
					['label' => 'G. CLIENTES', 'icon' => 'icon ion-android-contacts', 'url' => ['/clientes'], ],
					['label' => 'G. PROVEEDORES', 'icon' => 'icon ion-android-people', 'url' => ['/proveedores'], ],
				],
			],
			[
				'label'   => 'G. DOCUMENTOS',
				'icon'    => 'icon ion-ios-book',
				'url'     => ['/talonarios'],
				'items'   => [
					['label' => 'COMPROBANTES', 'icon' => 'fa fa-book', 'url' => ['/comprobante'], ],
					['label' => 'TALONARIOS', 'icon' => 'fa fa-list-alt', 'url' => ['/talonarios'], ],
				],
			],
			[
				'label' => 'G. USUARIOS',
				'icon'  => 'fa fa-users',
				'url'   => ['/usuarios'],
			],
			[
				'label' => 'AUDITAR',
				'icon'  => 'fa fa-cloud',
				'url'   => ['/audiciones'],

			],
		],

	]
)?>
<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>
    <script>
        try {
            ace.settings.check('sidebar', 'collapsed');
        } catch (e) {
        }
    </script>
</div>
<?php }?>
