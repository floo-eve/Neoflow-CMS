<?phpuse \Neoflow\CMS\App;

// Set PHP error log// Don't forget to disable when running as productionini_set('display_errors', true);ini_set('display_startup_errors', true);error_reporting(E_ALL);

// Include autoloadinclude 'framework/autoload.php';

// Initialize application$app = new App(__DIR__);

// Execute applciation$app->execute();

// Publis application$app->publish();
