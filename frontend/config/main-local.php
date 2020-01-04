<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'spCe7S8d3W0P0HSBt7ae5LC_WObTqAxz',
        ],

        'class'=>'yii\redis\Cache',
        'redis'=>[        //配置redis
            'class' => 'yii\redis\Connection',
            'hostname' => '172.17.0.1',
            'password'=>'123456',
            'port' => 6379,
            'database' => 0,
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
