<?php
$this->addNamespace('\\Neoflow\\Module\\');

// Backend routes
$this->addRoutes(array(
    array('mod_hello_world_backend_index', 'any', '/backend/module/hello-world', 'HelloWorld\\Controller\\Backend'),
    array('mod_hello_world_update', 'post', '/backend/module/hello-world/update', 'HelloWorld\\Controller\\Backend@update'),
    array('mod_hello_world_bla', 'any', '/backend/module/hello-world/bla', 'HelloWorld\\Controller\\Backend@bla'),
    array('mod_hello_world_frontend_index', 'any', '/hello-world', 'HelloWorld\\Controller\\Frontend'),
));
