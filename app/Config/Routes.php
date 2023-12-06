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

$routes->get('/vat-purchase-journals/export-aster/view/', 'VatPurchaseJournals\ExportAster::view/', ['filter' => 'authFilter']);
$routes->get('/vat-purchase-journals/export-aster/export/', 'VatPurchaseJournals\ExportAster::export/', ['filter' => 'authFilter']);


$routes->get('/partners/providers', 'Partners::providers', ['filter' => 'authFilter']);
$routes->get('/partners/businesses', 'Partners::businesses', ['filter' => 'authFilter']);
$routes->get('/partners/companies', 'Partners::companies', ['filter' => 'authFilter']);


$routes->get('/businesses/manage', 'Businesses::manage', ['filter' => 'authFilter']);
$routes->post('/businesses/save', 'Businesses::save', ['filter' => 'authFilter']);
$routes->post('/businesses/change-status', 'Businesses::change_status', ['filter' => 'authFilter']);


$routes->get('/companies/manage', 'Companies::manage', ['filter' => 'authFilter']);
$routes->post('/companies/save', 'Companies::save', ['filter' => 'authFilter']);
$routes->post('/companies/change-status', 'Companies::change_status', ['filter' => 'authFilter']);




$routes->get('/purchase-by-document/add', 'PurchaseByDocument\Add::index', ['filter' => 'authFilter']);
$routes->post('/purchase-by-document/submit', 'PurchaseByDocument\Add::submit', ['filter' => 'authFilter']);
$routes->get('/purchase-by-document/submit-preview', 'PurchaseByDocument\Add::submit_preview', ['filter' => 'authFilter']);
$routes->get('/purchase-by-document/finish', 'PurchaseByDocument\Add::Finish', ['filter' => 'authFilter']);
$routes->get('/purchase-by-document/done', 'PurchaseByDocument\Add::done', ['filter' => 'authFilter']);
$routes->get('/purchase-by-document/fioniks-farma-add-debug', 'PurchaseByDocument\Debug\FioniksFarma::display', ['filter' => 'authFilter']);

$routes->get('/purchase-by-document/history', 'PurchaseByDocument\History::index', ['filter' => 'authFilter']);
$routes->get('/purchase-by-document/delete/(:any)', 'PurchaseByDocument\History::delete/$1', ['filter' => 'authFilter']);
$routes->get('/purchase-by-document/download/(:any)', 'PurchaseByDocument\History::download/$1', ['filter' => 'authFilter']);
$routes->get('/purchase-by-document/view/(:any)', 'PurchaseByDocument\History::view/$1', ['filter' => 'authFilter']);









$routes->get('/test/txt/parse', 'Test\txt::parse/');
$routes->get('/test/txt2/parse', 'Test\txt2::parse/');