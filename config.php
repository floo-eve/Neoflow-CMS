<?php
return array(
    'url' => 'http://localhost/lahaina-cms',
    'database' => array(
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'lahaina',
        'charset' => 'UTF8',
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
//    'languages' => ['de', 'en']
);