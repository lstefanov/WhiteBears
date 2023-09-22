<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');

$routes->setDefaultController('Login');
$routes->get('/', 'Login::index', ['filter' => 'authFilter']);
$routes->get('/register', 'Register::index', ['filter' => 'guestFilter']);
$routes->post('/register', 'Register::register', ['filter' => 'guestFilter']);

$routes->get('/login', 'Login::index', ['filter' => 'guestFilter']);
$routes->post('/login', 'Login::authenticate', ['filter' => 'guestFilter']);

$routes->get('/logout', 'Login::logout', ['filter' => 'authFilter']);
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'authFilter']);



$routes->get('/vat-purchase-journals/add', 'VatPurchaseJournals\Add::index', ['filter' => 'authFilter']);
$routes->post('/vat-purchase-journals/submit', 'VatPurchaseJournals\Add::submit', ['filter' => 'authFilter']);
$routes->get('/vat-purchase-journals/submit-preview', 'VatPurchaseJournals\Add::submit_preview', ['filter' => 'authFilter']);
$routes->get('/vat-purchase-journals/finish', 'VatPurchaseJournals\Add::finish', ['filter' => 'authFilter']);
$routes->get('/vat-purchase-journals/done', 'VatPurchaseJournals\Add::done', ['filter' => 'authFilter']);


$routes->get('/vat-purchase-journals/history', 'VatPurchaseJournals\History::index', ['filter' => 'authFilter']);
$routes->get('/vat-purchase-journals/view/(:any)', 'VatPurchaseJournals\History::view/$1', ['filter' => 'authFilter']);
$routes->get('/vat-purchase-journals/delete/(:any)', 'VatPurchaseJournals\History::delete/$1', ['filter' => 'authFilter']);
$routes->get('/vat-purchase-journals/download/(:any)', 'VatPurchaseJournals\History::download/$1', ['filter' => 'authFilter']);


$routes->get('/vat-purchase-journals/export/view/', 'VatPurchaseJournals\Export::view/', ['filter' => 'authFilter']);
$routes->get('/vat-purchase-journals/export/export/', 'VatPurchaseJournals\Export::export/', ['filter' => 'authFilter']);
