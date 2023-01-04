<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->get('/dashboard', 'Dashboard::index');

//subscriber / guest
$routes->get('/subscriber', 'Subscriber::index');
$routes->get('/subscriber/ssp', 'Subscriber::ssp');
$routes->get('/subscriber/edit/(:num)', 'Subscriber::edit/$1');
$routes->get('/subscriber/delete/(:num)', 'Subscriber::delete/$1');
$routes->post('/subscriber/insert', 'Subscriber::insert');
$routes->post('/subscriber/update', 'Subscriber::update');

//subscriber group
$routes->get('/subscribergroup', 'SubscriberGroup::index');
$routes->get('/subscribergroup/ssp', 'SubscriberGroup::ssp');
$routes->get('/subscribergroup/edit/(:num)', 'SubscriberGroup::edit/$1');
$routes->get('/subscribergroup/delete/(:num)', 'SubscriberGroup::delete/$1');
$routes->post('/subscribergroup/insert', 'SubscriberGroup::insert');
$routes->post('/subscribergroup/update', 'SubscriberGroup::update');

//Message
$routes->get('/message', 'Message::index');
$routes->get('/message/ssp', 'Message::ssp');
$routes->get('/message/edit/(:num)', 'Message::edit/$1');
$routes->get('/message/delete/(:num)', 'Message::delete/$1');
$routes->post('/message/insert', 'Message::insert');
$routes->post('/message/update', 'Message::update');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
