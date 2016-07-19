<?php
return array(
    'url' => 'http://localhost/neoflow-cms',
    'database' => array(
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'neoflow-cms',
        'charset' => 'UTF8',
    ),
    'session' => array(
        'name' => 'neoflow-cms', // false (system default) | string
        'lifetime' => 3600 // false (system default) | int (seconds)
    ),
    'cache' => false, // auto | apcu | file | apc | false
    'logger' => array(
        'extension' => 'txt', // txt | log | ... | false
        'prefix' => 'log_', // log_ | ... | false
        'level' => 'debug' // error | warning | info | debug | false
    ),
    'debugging' => array(
        'debugBar' => true, // true | false
    ),
);
