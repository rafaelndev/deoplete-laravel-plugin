<?php
error_reporting(0);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$loader = require __DIR__ . '/../vendor/autoload.php';

use Laravel\RouteFilter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log_path = getenv('HOME') . '/laravel-deoplete.log';
$logger = new Logger('Laravel');
$logger->pushHandler(new StreamHandler($log_path, Logger::DEBUG));

try {
    $root   = $argv[1];
    $base   = $argv[2];

    $externalClassLoader = require $root . "/vendor/autoload.php";

    // Verifica se o projeto contem composer
    if (!file_exists($root . "/composer.json")) {
        throw new \Exception('Arquivo composer.json não foi encontrado');
    }

    $routeFilter = new RouteFilter($root, $base);
    $routeList = $routeFilter->getRouteList();

    // Envia a lista de rotas por string ao VIM/deoplete
    foreach ($routeList as $route) {
        echo $route;
    }
} catch (\Exception $e) {
    $logger->error($e->getMessage(), $e->getTrace());
} catch (\Throwable $e){
    $logger->error($e->getMessage(), $e->getTrace());
}
