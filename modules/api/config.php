<?php

return [
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/api/modules/v1',
            'class' => 'app\modules\api\modules\v1\Module',
        ],
    ],
    'components' => [
        'request' => [
            'class' => 'yii\base\Request',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],

        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule'],
            ],
        ]
    ],
];