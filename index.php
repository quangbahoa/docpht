<?php ini_set('display_errors', 1);

use Tracy\Debugger;

/**
 * This file is part of the DocPHT project.
 * 
 * @author Valentino Pesce
 * @copyright (c) Valentino Pesce <valentino@iltuobrand.it>
 * @copyright (c) Craig Crosby <creecros@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$autoload = 'vendor/autoload.php';

$configurationFile = 'src/config/config.php';

$installFolder = 'install';

if (!file_exists($configurationFile)) {
    include 'install/config.php';
} elseif (file_exists($configurationFile) && file_exists($installFolder)) {
    $files = glob($installFolder.'/*');
    foreach($files as $file){
        if(is_file($file)) {
            if (is_writable($file)) {
                unlink($file);
            }
        }
    }
    if (is_dir_empty($installFolder)) {
        rmdir($installFolder);
    } elseif (file_exists($installFolder)) {
        echo '<b>It is not possible to remove the installation folder automatically, remove the "install" folder manually.</b>';
    }
} elseif (file_exists($autoload)) {
require $autoload;

if(session_status() !== PHP_SESSION_ACTIVE) session_start();

require $configurationFile;

Debugger::enable(Debugger::DEVELOPMENT); // IMPORTANT not to use in production

$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory(__DIR__ . '/src');
$loader->setTempDirectory(__DIR__ . '/temp');
$loader->register();

$app            = System\App::instance();
$app->request   = System\Request::instance();
$app->route     = System\Route::instance($app->request);

$route = $app->route;

include 'src/route.php';

$route->end();
}

function is_dir_empty($dir) 
{
    if (!is_readable($dir)) return NULL; 
    return (count(scandir($dir)) == 2);
}