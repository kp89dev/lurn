<?php

if (env('APP_ENV') == 'production') {
    $mysql = [
        'read' => [
            'host' => [
                env('DB_HOST_SLAVE_1'),
                env('DB_HOST_SLAVE_2'),
                env('DB_HOST_SLAVE_3'),
            ]
        ],
        'write' => [
            'host' => env('DB_HOST')
        ]
    ];
} else {
    $mysql = [
        'host' => env('DB_HOST', 'localhost')
    ];
}

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_OBJ,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix'   => '',
        ],

        'mysql' => [
            'driver'    => 'mysql',
            'port'      => env('DB_PORT', '3306'),
            'database'  => (isset( $_SERVER['HTTP_HOST']) && strpos( $_SERVER['HTTP_HOST'], 'amazonaws.com') !== false) ? 'lurn_nation_testing' : env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
            'options'   => [
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::ATTR_PERSISTENT => env('DB_PERSISTENT_CONNECTIONS', false),
            ]
        ] + $mysql,

        env('TEST_IMPORT_DB_CONNECTION', 'importtest') => [
            'driver'    => 'mysql',
            'port'      => env('TEST_IMPORT_DB_PORT', '3306'),
            'host'      => env('TEST_IMPORT_DB_HOST', 'localhost'),
            'database'  => env('TEST_IMPORT_DB_DATABASE', 'forgetest'),
            'username'  => env('TEST_IMPORT_DB_USERNAME', 'forge'),
            'password'  => env('TEST_IMPORT_DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
            'options'   => [
                PDO::ATTR_EMULATE_PREPARES => true,
            ]
        ],

        'inbox' => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'ibblue_laravelinboxblueprint',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],

        'lurninsider'      => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'lurninsi_laravellurninsider_live',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],

        'cop'              => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'circleof_laravelcircleofprofit',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],

        'cop-classroom'    => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'circleof_classroom',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],

        'listacademy'      => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'listacad_laravelcms',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],

        'seocrashcourse'   => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'lurncom_laravelseocrashcourse',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],

        'copywriting'      => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'lurncom_laravelcopywriting',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],

        'publishacademy'    => [
            'driver'    => 'mysql',
            'host'      => '52.3.19.166',
            'port'      => '3306',
            'database'  => 'publisha_laravel',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],

        'fbacademy'    => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'lurnfb_lurncms',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],
        'ori'    => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'outranki_members',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],
        'richdadinsider'    => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'lurncom_laravelrdinsider',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],
        'fmrules'    => [
            'driver'    => 'mysql',
            'host'      => '67.225.203.125',
            'port'      => '3306',
            'database'  => 'fivemone_lurncms',
            'username'  => 'ajseidl',
            'password'  => 'zC@3JvS~J3+tgZ',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null
        ],

        'pgsql'            => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],

],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'cluster' => false,

        'default' => [
            'host'     => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
