<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// --- RUTE PUBLIK (TIDAK BUTUH TOKEN) ---
$routes->post('auth/login', 'Auth::login');

// --- RUTE TERPROTEKSI (WAJIB TOKEN) ---
$routes->group('api', ['filter' => 'auth'], function($routes) {
    $routes->resource('barang');
    $routes->resource('kategori');
    $routes->post('auth/logout', 'Auth::logout');
});
