<?php
error_reporting(0);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$loader = require __DIR__ . '/../vendor/autoload.php';

use Laravel\RouteFilter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

$log_path = getenv('HOME') . '/laravel-deoplete.log';
$logger = new Logger('Laravel');
$stream = new StreamHandler($log_path, Logger::DEBUG);
$logger->pushHandler($stream);

try {
    $root   = $argv[1];
    $base   = $argv[2];

    $externalClassLoader = require $root . "/vendor/autoload.php";

    // Verifica se o projeto contem composer
    if (!file_exists($root . "/composer.json")) {
        throw new \Exception('Arquivo composer.json não foi encontrado');
    }

    $routeFilter = new RouteFilter($root, $logger);
    $routeList = $routeFilter->getRoutes($base);
    echo $routeList;

} catch (\Exception $e) {
    $logger->error("Mensagem: " . $e->getMessage() . " Linha: " . $e->getLine() );
} catch (\Throwable $e){
    $logger->error("Mensagem: " . $e->getMessage() . " Linha: " . $e->getLine() );
}
