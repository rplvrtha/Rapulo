<?php
use Rapulo\Features\Auth\AuthController;
$router->middleware('auth', 'Rapulo\Middleware\AuthMiddleware');

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->group(['auth'], function($router) {
    $router->get('/dashboard', [AuthController::class, 'dashboard']);
});