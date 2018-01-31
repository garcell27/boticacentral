<?php
use yii\helpers\Json;

$imgPerfil=$directoryAsset.'/img/avatar.png';

?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $imgPerfil ?>" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->namefull;?>
                </p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    ['label' => 'MENU PRINCIPAL', 'options' => ['class' => 'header']],
                    [
                        'label' => 'PROCESOS',
                        'icon' => 'fa fa-tasks',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Catalogo', 'icon' => 'fa fa-shopping-cart', 'url' => ['/catalogo'],],
                            ['label' => 'Ventas', 'icon' => 'fa fa-shopping-cart', 'url' => ['/ventas'],],
                            ['label' => 'Stock', 'icon' => 'fa fa-shopping-cart', 'url' => ['/stock'],'visible' => Yii::$app->user->identity->idrole!=3?true:false,],
                            ['label' => 'Ingreso de Mercaderia', 'icon' => 'fa fa-shopping-cart', 'url' => ['/ingreso'],'visible' => Yii::$app->user->identity->idrole!=3?true:false,],

                        ],
                    ],
                    [
                        'label' => 'REGISTROS',
                        'icon' => 'fa fa-barcode',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gestión de Clientes', 'icon' => 'fa fa-users', 'url' => ['/clientes'],],
                            [
                                'label' => 'Gestión de Productos',
                                'icon' => 'fa fa-dropbox',
                                'url' => '#',
                                'items'=>[
                                    ['label' => 'Laboratorios', 'icon' => 'fa fa-dropbox', 'url' => ['/laboratorios'],],
                                    ['label' => 'Categorias', 'icon' => 'fa fa-dropbox', 'url' => ['/categorias'],],
                                    ['label' => 'Productos', 'icon' => 'fa fa-dropbox', 'url' => ['/productos'],],
                                ],
                            ],
                            ['label' => 'Gestión de Botica', 'icon' => 'fa fa-ambulance', 'url' => ['/botica'],],
                            ['label' => 'Gestión de Documentos', 'icon' => 'fa fa-ambulance', 'url' => ['/talonarios'],],
                            ['label' => 'Gestión de Proveedores', 'icon' => 'fa fa-users', 'url' => ['/proveedores'],],
                        ],
                        'visible' => Yii::$app->user->identity->idrole!=3?true:false,
                    ],
                    [
                        'label' => 'Gestion de Usuarios',
                        'icon' => 'fa fa-users',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Usuarios', 'icon' => 'fa fa-user', 'url' => ['/usuarios'],],
                            ['label' => 'Roles', 'icon' => 'fa fa-thumb-tack', 'url' => ['/roles'],],
                            ['label' => 'Operaciones', 'icon' => 'fa fa-key', 'url' => ['/operacion'],],

                        ],
                        'visible' => Yii::$app->user->identity->idrole==1?true:false,
                    ],
                    /*[
                        'label' => 'Same tools',
                        'icon' => 'fa fa-share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug'],],
                            [
                                'label' => 'Level One',
                                'icon' => 'fa fa-circle-o',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'fa fa-circle-o',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],*/
                ],
            ]
        ) ?>

    </section>

</aside>
