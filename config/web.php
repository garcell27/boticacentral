<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'name'=>'Botica Central',
    'basePath' => dirname(__DIR__),
    'language'=>'es',
    'bootstrap' => ['log'],
    'modules' => [
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',
            // format settings for displaying each date attribute
            'displaySettings' => [
                'date' => 'dd/MM/yyyy',
                'time' => 'H:i:s A',
                'datetime' => 'd-m-Y H:i:s A',
            ],

            // format settings for saving each date attribute
            'saveSettings' => [
                'date' => 'php:Y-m-d',
                'time' => 'H:i:s',
                'datetime' => 'Y-m-d H:i:s',
            ],
            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,

        ],

        'gridview' =>  [
            'class' => '\kartik\grid\Module',
            'i18n' => [
                 'class' => 'yii\i18n\PhpMessageSource',
                 'basePath' => '@kvgrid/messages',
                 'forceTranslation' => true
             ]
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
    ],
    'components' => [
        'formatter' => [
            'locale' => 'es-ES', //your language locale
            'defaultTimeZone' => 'America/Lima', // time zone
            'thousandSeparator'=>'',
            'decimalSeparator' => '.',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'du87DkPLVRwqwObMyYdxr1g8xyFMN5ql',
            'enableCsrfValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Usuarios',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),

        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                [
                    'pattern'=>'',
                    'route'=>'site/index',
                    'suffix'=>''
                ],
                [
                    'pattern'=>'api',
                    'route'=>'api/default',
                    'suffix'=>''
                ],
                /*[
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/proveedores',
                ],*/
                [
                    'pattern'=>'<controller>',
                    'route'=>'<controller>/index',
                    'suffix'=>''
                ],
                [
                    'pattern'=>'<controller>/<id:\d+>',
                    'route'=>'<controller>/view',
                    'suffix'=>''
                ],
                [
                    'pattern'=>'<controller>/<action>/<id:\d+>',
                    'route'=>'<controller>/<action>',
                    'suffix'=>''
                ],
                [
                    'pattern'=>'<controller>/<action>',
                    'route'=>'<controller>/<action>',
                    'suffix'=>''
                ]
            ],
        ],
       

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [ //here
            'crud' => [ // generator name
                'class' => 'yii\gii\generators\crud\Generator', // generator class
                'templates' => [ //setting for out templates
                    'myCrud' => '@app/templates/crud/nuevo', // template name => path to template
                ]
            ]
        ],
    ];
}

return $config;
