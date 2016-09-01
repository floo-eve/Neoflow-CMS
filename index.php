<?php
// Set PHP error log
// Don't forget to disable when running as production
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

// Include autoload
include 'framework/autoload.php';

// Initialize application
$app = new \Neoflow\CMS\App(__DIR__);

// Execute application
$app->execute();

// Publish application
$app->publish();
