<?php
$this->addNamespace('\\Neoflow\\Module\\HelloWorld\\');

// Module routes
$this->addRoutes(array(
    array('hello_world_backend_index', 'any', '/backend/module/hello-world', 'Backend@index'),
    array('hello_world_backend_update', 'post', '/backend/module/hello-world/update', '\Backend@update'),
    array('hello_world_frontend_index', 'any', '/hello-world', 'Frontend@index'),
));
