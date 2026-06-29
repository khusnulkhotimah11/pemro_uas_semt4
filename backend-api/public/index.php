<?php

// --- INJEKSI CORS MANUAL DI SINI ---
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// -----------------------------------

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

// Trim database and environment variables to prevent accidental spaces/tabs copied from clipboard
foreach ([
    'CI_ENVIRONMENT',
    'database.default.hostname', 'database.default.database', 'database.default.username', 'database.default.password', 'database.default.port',
    'database_default_hostname', 'database_default_database', 'database_default_username', 'database_default_password', 'database_default_port'
] as $key) {
    if (isset($_ENV[$key])) {
        $_ENV[$key] = trim($_ENV[$key]);
    }
    if (isset($_SERVER[$key])) {
        $_SERVER[$key] = trim($_SERVER[$key]);
    }
    $val = getenv($key);
    if ($val !== false) {
        putenv("$key=" . trim($val));
    }
}

if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
    $db_host = $_ENV['database_default_hostname'] ?? $_SERVER['database_default_hostname'] ?? getenv('database_default_hostname') ?? 'not set';
    $db_user = $_ENV['database_default_username'] ?? $_SERVER['database_default_username'] ?? getenv('database_default_username') ?? 'not set';
    $db_pass = $_ENV['database_default_password'] ?? $_SERVER['database_default_password'] ?? getenv('database_default_password') ?? 'not set';
    $db_port = $_ENV['database_default_port'] ?? $_SERVER['database_default_port'] ?? getenv('database_default_port') ?? 'not set';
    $db_name = $_ENV['database_default_database'] ?? $_SERVER['database_default_database'] ?? getenv('database_default_database') ?? 'not set';
    
    $pass_len = strlen($db_pass);
    $pass_masked = $pass_len > 0 ? ($db_pass[0] . '...' . $db_pass[$pass_len - 1]) : 'empty';
    
    error_log("DIAGNOSTIC - Host: $db_host | Port: $db_port | User: $db_user | Db: $db_name | PassLength: $pass_len | PassMasked: $pass_masked");
}

$paths = new Paths();

if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    define('ENVIRONMENT', 'development');
}

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
