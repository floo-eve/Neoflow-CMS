<?php// Include framework functionsrequire_once 'functions/is_assoc.php';require_once 'functions/is_json.php';require_once 'functions/is_url.php';require_once 'functions/slugify.php';require_once 'functions/normalize_directory.php';require_once 'functions/normalize_url.php';// Register autoload for application and framework classesspl_autoload_register(function ($className) {    $className = ltrim($className, '\\');    $className = str_replace('\\', '/', $className);    $directories = array(        'framework' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR,        'application' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR,    );    foreach (scandir('modules') as $folder) {        if ($folder !== '.' && $folder !== '..') {            $directory = 'modules' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;            if (is_dir($directory)) {                $directories[] = $directory;            }        }    }    foreach ($directories as $directory) {        $classFile = $directory . $className . '.php';        if (is_file($classFile)) {            include_once $classFile;        }    }});