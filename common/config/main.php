<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            //'class' => 'yii\caching\FileCache',
            'class'=>'yii\redis\Cache',


        ],
        'authManage2' =>[
            'class'=>'yii\rbac\DbManager',
        ],

        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '172.17.0.1',
            'password'=>'123456',
            'port' => 6379,
            'database' => 0,
        ],
        'sphinx' => [
            'class' => 'yii\sphinx\Connection',
            'dsn' => 'mysql:host=172.17.0.1;port=3306;dbname=basic',
            'username' => 'root',
            'password' => 'dev2312',
        ],

        'view' => [
            'class' => 'yii\web\View',
            'renderers' => [
                'tpl' => [
                    'class' => 'yii\smarty\ViewRenderer',
                    //'cachePath' => '@runtime/Smarty/cache',
                ],
                'twig' => [
                    'class' => 'yii\twig\ViewRenderer',
                    'cachePath' => '@runtime/Twig/cache',
                    // Array of twig options:
                    'options' => [
                        'auto_reload' => true,
                    ],
                    'globals' => ['html' => '\yii\helpers\Html'],
                    'uses' => ['yii\bootstrap'],
                ],
                // ...
            ],
        ],

    ],

];
