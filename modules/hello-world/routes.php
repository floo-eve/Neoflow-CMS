<?php
$this->addNamespace('\\Neoflow\\Module\\');


// Backend routes
$this->addRoutes(array(
    array('mod_hello_world_index', 'any', '/backend/module/hello-world', 'HelloWorld\\Controller\\Backend'),
    array('mod_hello_world_frontend', 'any', '/hello-world', 'HelloWorld\\Controller\\Frontend'),
));