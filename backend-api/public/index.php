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

$paths = new Paths();

if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
    if (!defined('ENVIRONMENT')) {
        $env = $_ENV['CI_ENVIRONMENT'] ?? $_SERVER['CI_ENVIRONMENT'] ?? getenv('CI_ENVIRONMENT') ?: 'production';
        define('ENVIRONMENT', $env);
    }
    $bootFile = $paths->appDirectory . '/Config/Boot/' . ENVIRONMENT . '.php';
    if (!is_file($bootFile)) {
        header('Content-Type: application/json', true, 500);
        echo json_encode([
            'error' => 'Path check failed',
            'env_evaluated' => ENVIRONMENT,
            'appDirectory' => $paths->appDirectory,
            'bootFile' => $bootFile,
            'is_file' => is_file($bootFile),
            '__DIR__' => __DIR__,
            '$_SERVER_CI_ENV' => $_SERVER['CI_ENVIRONMENT'] ?? 'not set',
            'getenv_CI_ENV' => getenv('CI_ENVIRONMENT'),
            'HTTP_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'
        ]);
        exit;
    }
}

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
