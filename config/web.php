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
            'errorAction' => 'users/error',
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
                'GET users/<id:\d+>'                =>  'users/get', //���������� id ������������
                'POST users/<id:\d+>'               =>  'users/post', //���������� id ������������
                'PUT users/<id:\d+>'                =>  'users/put', //���������� "PUT"
                'PUT users/friends/<user_id:\d+>'   =>  'users/putfriends',  //���������� "PUT FRIENDS"
                /******************/
                'GET levels/data/<id:\d+>'          =>  'levels/getdata', //���������� id ������
                'POST levels/data/<id:\d+>'         =>  'levels/postdata', //���������� id ������
                'PUT levels/data/<id:\d+>'          =>  'levels/putdata', //���������� "PUT DATA"
                /******************/
                'GET levels/progress'               =>  'levels/getprogress', //���������� "GET PROGRESS"
                'POST levels/progress'              =>  'levels/postprogress', //���������� "POST PROGRESS"
                'PUT levels/progress'               =>  'levels/putprogress', //���������� "PUT PROGRESS"
                /******************/
                'GET levels/score'                  =>  'levels/score', //���������� "GET SCORE"
                //����� ��������� id ������ ����� ����� ����� users/ ��� levels/
                'GET bundle'                        =>  'bundle/get',
                'POST bundle'                       =>  'bundle/post',
                'GET bundle/number'                 =>  'bundle/getnumber',
                /******************/
                'POST auth'                         =>  'social/postauth',
                'POST lives'                        =>  'social/postlives',
                'POST boosters'                     =>  'social/postboosters',
                'POST products'                     =>  'social/postproducts',
                'POST progress'                     =>  'social/postprogress',
                'POST boosters/buy'                 =>  'social/postboostersbuy',

                'GET user'                          =>  'social/getuser',
                'GET lives'                         =>  'social/getlives',
                'GET boosters'                      =>  'social/getboosters',
                'GET products'                      =>  'social/getproducts',
                'GET progress'                      =>  'social/getprogress'

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
