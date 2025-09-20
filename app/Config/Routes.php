<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->group('my2', function($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('login', 'Login::index');
    $routes->post('login/authenticate', 'Login::authenticate');
    $routes->get('login_ok', 'Login::login_ok');
    $routes->get('login/logout', 'Login::logout');
});

// 세션 관리 라우트 추가
$routes->group('session', function($routes) {
    $routes->get('check', 'SessionController::checkSession');
    $routes->post('extend', 'SessionController::extendSession');
    $routes->post('update-activity', 'SessionController::updateActivity');
    $routes->get('logout', 'SessionController::logout');
});