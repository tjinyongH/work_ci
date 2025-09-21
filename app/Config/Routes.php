<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// 인증 관련 라우트 (게스트만 접근 가능)
$routes->group('', ['filter' => 'guest'], function($routes) {
    $routes->get('login', 'Login::index');
    $routes->post('login/authenticate', 'Login::authenticate');
    $routes->get('register', 'Login::register');
    $routes->post('register/process', 'Login::processRegister');
});

// 인증이 필요한 라우트
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Login::dashboard');
    $routes->post('logout', 'Login::logout');
});

// 세션 관리 라우트 (AJAX)
$routes->group('session', function($routes) {
    $routes->get('check', 'Login::checkSession');
    $routes->post('extend', 'Login::extendSession');
    $routes->post('update-activity', 'SessionController::updateActivity');
});

// 기존 my2 라우트 (하위 호환성을 위해 유지)
$routes->group('my2', function($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('login', 'Login::index');
    $routes->post('login/authenticate', 'Login::authenticate');
    $routes->get('login_ok', 'Login::dashboard');
    $routes->get('login/logout', 'Login::logout');
});

// 세션 관리 라우트 추가 (기존)
$routes->group('session', function($routes) {
    $routes->get('check', 'SessionController::checkSession');
    $routes->post('extend', 'SessionController::extendSession');
    $routes->post('update-activity', 'SessionController::updateActivity');
    $routes->get('logout', 'SessionController::logout');
});