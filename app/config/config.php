<?php

return new \Phalcon\Config(
    [
        'database' => [
            'adapter'           => 'Mysql',
            'host'              => 'localhost',
            'port'              => 3306,
            'username'          => 'root',
            'password'          => '',
            'dbname'            => 'memoria',
        ],

        'application' => [
            'controllersDir'    => APP_DIR.'/Controllers/',
            'modelsDir'         => APP_DIR.'/models/',
            'businessDir'       => APP_DIR.'/business/',
            'helpersDir'        => APP_DIR.'/helpers/',
            'exceptionsDir'     => APP_DIR.'/Exceptions/',
            'baseUri'           => "/apimemoria/",
            'publicUrl'         => "/apimemoria/",
            'cryptSalt'         => 'eEAfR|_&G&f,+vU]:jFr!!A&+71w1Ms9',
        ],

        'jwt' => [
            'hashkey'           => 'j_x:C!YXy|CKq?Ui+Al8h.Pe3/g;<+1Q*aFsw',
            'tokenExpiration'   => '3600', //tiempo en [s]
            'protected'         => true
        ],

        'max_edition_time' => '3600', //tiempo en [s]

        'noLoginRequired' => [
            'users' => [
                'post' => [
                    '*' => true
                ]
            ],
            'services' => [
                'get' => [
                    '*' => true
                ]
            ],
            'service_types' => [
                'get' => [
                    '*' => true
                ]
            ],
            'price_ranges' => [
                'get' => [
                    '*' => true
                ]
            ]
        ],

        'adminLoginRequired' => [
            'service_types' => [
                'post' => [
                    '*' => true
                ],
                'put' => [
                    '*' => true
                ],
                'delete' => [
                    '*' => true
                ]
            ],
            'analitics' => [
                'get' => [
                    '*' => true
                ]
            ]
        ]
    ]
);