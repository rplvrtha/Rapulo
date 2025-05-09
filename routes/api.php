<?php
$router->group(['api'], function($router) {
    $router->get('/api/test', function() {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'API Test']);
    });
});