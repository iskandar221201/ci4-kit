<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =========================================================
// Public Routes
// =========================================================
$routes->get('/', 'Home::index');

// NOTE: AuthController and DashboardController are placeholder
// controllers that need to be created by the developer.
// They are not part of the kit itself — they are usage examples.
$routes->post('login', 'AuthController::login');

// =========================================================
// API Routes — Public (no auth required)
// =========================================================
$routes->get('api/ping', 'Api\PingController::index');

// =========================================================
// API Routes — Protected (apiKeyFilter)
// =========================================================
$routes->group('api', ['filter' => 'apiKeyFilter'], static function (RouteCollection $routes): void {
    // Health check (authenticated)
    $routes->get('protected', 'Api\PingController::check');

    // User resource (CRUD)
    $routes->get('users', 'Api\UserController::index');
    $routes->post('users', 'Api\UserController::create');
    $routes->get('users/(:num)', 'Api\UserController::show/$1');
    $routes->put('users/(:num)', 'Api\UserController::update/$1');
    $routes->delete('users/(:num)', 'Api\UserController::delete/$1');
});

// =========================================================
// Web Routes — Protected (authFilter)
// =========================================================
// NOTE: DashboardController is a placeholder — create it in app/Controllers/.
$routes->group('', ['filter' => 'authFilter'], static function (RouteCollection $routes): void {
    $routes->get('dashboard', 'DashboardController::index');
});

// Shield auth routes (login, register, magic-link, etc.)
service('auth')->routes($routes);
