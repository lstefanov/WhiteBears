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




$routes->get('/reference/dds-vs-items-from-invoice', 'Reference\DdsVsItems::view/$1', ['filter' => 'authFilter']);
$routes->get('/reference/comparison-of-tax-bases', 'Reference\ComparisonTaxBases::view/$1', ['filter' => 'authFilter']);


$routes->get('/nomenclatures/export-invoices-entities', 'Nomenclatures\ExportInvoicesEntities::view', ['filter' => 'authFilter']);
$routes->get('/nomenclatures/export-invoices-entities/export', 'Nomenclatures\ExportInvoicesEntities::export', ['filter' => 'authFilter']);

$routes->get('/nomenclatures/synchronization', 'Nomenclatures\Synchronization::view', ['filter' => 'authFilter']);
$routes->get('/nomenclatures/add-sync-file', 'Nomenclatures\Synchronization::add', ['filter' => 'authFilter']);
$routes->post('/nomenclatures/add-sync-file-submit', 'Nomenclatures\Synchronization::submit', ['filter' => 'authFilter']);
$routes->get('/nomenclatures/add-sync-file-finalize', 'Nomenclatures\Synchronization::submit_finalize', ['filter' => 'authFilter']);
$routes->get('/nomenclatures/synchronization-delete/(:any)', 'Nomenclatures\Synchronization::delete/$1', ['filter' => 'authFilter']);
$routes->get('/nomenclatures/synchronization-view-file/(:any)', 'Nomenclatures\Synchronization::view_file/$1', ['filter' => 'authFilter']);
$routes->get('/nomenclatures/synchronization-download-file/(:any)', 'Nomenclatures\Synchronization::download_file/$1', ['filter' => 'authFilter']);

$routes->get('/nomenclatures/reference', 'Nomenclatures\Reference::view', ['filter' => 'authFilter']);
$routes->get('/nomenclatures/reference-export', 'Nomenclatures\Reference::export', ['filter' => 'authFilter']);
$routes->get('/nomenclatures/reference-export-invalid', 'Nomenclatures\Reference::export_invalid', ['filter' => 'authFilter']);





$routes->get('/fixes/vat-purchase-journals/sting-export-date-full', 'Fixes\VatPurchaseJournals::stingExportDateFull/');
$routes->get('/fixes/vat-purchase-journals/aster-export-date-full', 'Fixes\VatPurchaseJournals::asterExportDateFull/');
$routes->get('/fixes/purchase-by-document-prices/export-to-files', 'Fixes\PurchaseByDocumentPrices::export_to_files/');
$routes->get('/fixes/purchase-by-document-prices/delete-invalids', 'Fixes\PurchaseByDocumentPrices::delete_invalids/');


$routes->get('/test/txt/parse', 'Test\txt::parse/');
$routes->get('/test/txt2/parse', 'Test\txt2::parse/');
$routes->get('/test/avd/parse', 'Test\Avd::parse/');
$routes->get('/test/sss/parse', 'Test\Sss::parse/');
$routes->get('/test/sting/parse', 'Test\Sting::parse/');
$routes->post('/test/sting/execute', 'Test\Sting::execute/');