<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Rapulo\Core\Router;
use Rapulo\Core\Cache;
use Rapulo\Core\Queue;

session_start();

Cache::init(__DIR__ . '/../storage/cache');
Queue::init(__DIR__ . '/../storage/queue');

$router = new Router();
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';
$router->dispatch();