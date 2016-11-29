<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
                'parsers' => [
                    'application/json' => 'yii\web\JsonParser'],
            'cookieValidationKey' => 'fdgdbr3Frgw3gbdge',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
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
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                //'GET users/<id:\w+>' => 'users/get',
                //'<controller:\w+>/<id:\w+>'=>'<controller>/view',
                'GET users/<id:\w+>' => 'users/get', //возвращаем id пользователя
                'POST users/<id:\w+>' => 'users/post', //возвращаем id пользователя
                'PUT users/<id:\w+>' => 'users/put', //возвращаем "PUT"
                'PUT users/friends/<user_id:\w+>' => 'users/putfriends',  //возвращаем "PUT FRIENDS"
                /******************/
                'GET levels/data/<id:\w+>' => 'levels/getdata', //возвращаем id уровня
                'POST levels/data/<id:\w+>' => 'levels/postdata', //возвращаем id уровня
                'PUT levels/data/<id:\w+>' => 'levels/putdata', //возвращаем "PUT DATA"
                /******************/
                'GET levels/progress' => 'levels/getprogress', //возвращаем "GET PROGRESS"
                'POST levels/progress' => 'levels/postprogress', //возвращаем "POST PROGRESS"
                'PUT levels/progress' => 'levels/putprogress', //возвращаем "PUT PROGRESS"
                /******************/
                'GET levels/score' => 'levels/score' //возвращаем "GET SCORE"
                //чтобы проверить id вводим любое число после users/ или levels/



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
    ];
}

return $config;
