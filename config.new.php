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
    'email' => 'john.doe@neoflow.ch',
    'session' => array(
        'name' => 'neoflow-cms', // false (system default) | string
        'lifetime' => 1800, // false (system default) | int (seconds)
    ),
    'orm' => array(
        'caching' => false, // true | false
    ),
    'queryBuilder' => array(
        'caching' => false, // true | false
    ),
    'cache' => true, // true (auto detection) | apc | apcu | file | false (disabled)
    'logger' => array(
        'extension' => 'txt', // txt | log | ... | false
        'prefix' => 'log_', // log_ | ... | false
        'level' => 'debug', // error | warning | info | debug | false
    ),
    'debugging' => array(
        'debugBar' => true, // true | false
    ),
    'services' => array(
        '\\Neoflow\\CMS\\Service\\MailService',
        '\\Neoflow\\CMS\\Service\\NavitemService',
        '\\Neoflow\\CMS\\Service\\SectionService',
        '\\Neoflow\\CMS\\Service\\AuthService',
        '\\Neoflow\\Framework\\Support\\Validation\\ValidationService',
    ),
);
