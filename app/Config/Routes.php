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

$routes->get('/builder', 'Builder::index');
$routes->post('/builder/build', 'Builder::build');
$routes->get('/builder/ajaxGetFields/(:any)', 'Builder::ajaxGetFields/$1');

//home
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');

//login
$routes->get('/login', 'Login::index');
$routes->post('/login/login', 'Login::login');
$routes->get('/login/login', 'Login::login');
$routes->get('/login/logout', 'Login::logout');

//admin
$routes->get('/admin', 'Admin::index');
$routes->get('/admin/ssp', 'Admin::ssp');
$routes->get('/admin/detail/(:num)', 'Admin::detail/$1');
$routes->post('/admin/insert', 'Admin::insert');
$routes->post('/admin/update', 'Admin::update');
$routes->get('/admin/delete/(:num)', 'Admin::delete/$1');

//admin-profile
$routes->get('/adminprofile', 'AdminProfile::index');
$routes->post('/adminprofile/change_password', 'AdminProfile::changePassword');

//dashboard
$routes->get('/dashboard', 'Dashboard::index');

//subscriber / guest
$routes->get('/subscriber', 'Subscriber::index');
$routes->get('/subscriber/ssp', 'Subscriber::ssp');
$routes->get('/subscriber/detail/(:num)', 'Subscriber::detail/$1');
$routes->get('/subscriber/sspRoom/(:num)', 'Subscriber::sspRoom/$1');
$routes->get('/subscriber/checkout/(:num)', 'Subscriber::checkout/$1');
$routes->get('/subscriber/checkout_room/(:num)/(:num)', 'Subscriber::checkoutRoom/$1/$2');
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

//Role
$routes->get('/role', 'Role::index');
$routes->get('/role/ssp', 'Role::ssp');
$routes->get('/role/edit/(:num)', 'Role::edit/$1');
$routes->get('/role/delete/(:num)', 'Role::delete/$1');
$routes->post('/role/update', 'Role::update');
$routes->post('/role/insert', 'Role::insert');

// App
$routes->get('/app', 'App::index');
$routes->get('/app/ssp', 'App::ssp');
$routes->get('/app/edit/(:num)', 'App::edit/$1');
$routes->get('/app/delete/(:num)', 'App::delete/$1');
$routes->post('/app/update', 'App::update');
$routes->post('/app/insert', 'App::insert');

// Inbox
$routes->get('/inbox', 'Inbox::index');
$routes->get('/inbox/ssp', 'Inbox::ssp');
$routes->get('/inbox/edit/(:any)', 'Inbox::edit/$1');
$routes->get('/inbox/delete/(:any)', 'Inbox::delete/$1');
$routes->post('/inbox/update', 'Inbox::update');
$routes->post('/inbox/insert', 'Inbox::insert');

// Locality (tourist info)
$routes->get('/locality', 'Locality::index');
$routes->get('/locality/ssp', 'Locality::ssp');
$routes->get('/locality/edit/(:any)', 'Locality::edit/$1');
$routes->get('/locality/delete/(:any)', 'Locality::delete/$1');
$routes->post('/locality/update', 'Locality::update');
$routes->post('/locality/insert', 'Locality::insert');

// Hotel Facility
$routes->get('/facility', 'Facility::index');
$routes->get('/facility/ssp', 'Facility::ssp');
$routes->get('/facility/edit/(:any)', 'Facility::edit/$1');
$routes->get('/facility/delete/(:any)', 'Facility::delete/$1');
$routes->post('/facility/update', 'Facility::update');
$routes->post('/facility/insert', 'Facility::insert');

// Element
$routes->get('/element', 'Element::index');
$routes->get('/element/ssp', 'Element::ssp');
$routes->get('/element/edit/(:any)', 'Element::edit/$1');
$routes->get('/element/delete/(:any)', 'Element::delete/$1');
$routes->post('/element/update', 'Element::update');
$routes->post('/element/insert', 'Element::insert');

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
