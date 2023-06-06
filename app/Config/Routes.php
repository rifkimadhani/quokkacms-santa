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

// Theme
$routes->get('/theme', 'Theme::index');
$routes->get('/theme/ssp', 'Theme::ssp');
$routes->get('/theme/edit/(:any)/(:any)', 'Theme::edit/$1/$2');
$routes->get('/theme/delete/(:any)/(:any)', 'Theme::delete/$1/$2');
$routes->post('/theme/update', 'Theme::update');
$routes->post('/theme/insert', 'Theme::insert');

// EPG
$routes->get('/livetv_epg', 'Livetv_EPG::index');
$routes->get('/livetv_epg/ssp', 'Livetv_EPG::ssp');
$routes->get('/livetv_epg/edit/(:any)', 'Livetv_EPG::edit/$1');
$routes->get('/livetv_epg/delete/(:any)', 'Livetv_EPG::delete/$1');
$routes->post('/livetv_epg/update', 'Livetv_EPG::update');
$routes->post('/livetv_epg/insert', 'Livetv_EPG::insert');
$routes->post('/livetv_epg/export', 'Livetv_EPG::export');

// Room
$routes->get('/room', 'Room::index');
$routes->get('/room/ssp', 'Room::ssp');
$routes->get('/room/edit/(:any)', 'Room::edit/$1');
$routes->get('/room/delete/(:any)', 'Room::delete/$1');
$routes->post('/room/update', 'Room::update');
$routes->post('/room/insert', 'Room::insert');

// Room Type
$routes->get('/roomtype', 'RoomType::index');
$routes->get('/roomtype/ssp', 'RoomType::ssp');
$routes->get('/roomtype/edit/(:any)', 'RoomType::edit/$1');
$routes->get('/roomtype/delete/(:any)', 'RoomType::delete/$1');
$routes->post('/roomtype/update', 'RoomType::update');
$routes->post('/roomtype/insert', 'RoomType::insert');

// VOD
$routes->get('/vod', 'VOD::index');
$routes->get('/vod/ssp', 'VOD::ssp');
$routes->get('/vod/edit/(:any)', 'VOD::edit/$1');
$routes->get('/vod/delete/(:any)', 'VOD::delete/$1');
$routes->post('/vod/update', 'VOD::update');
$routes->post('/vod/insert', 'VOD::insert');

// VOD Genre
$routes->get('/vodgenre', 'VODGenre::index');
$routes->get('/vodgenre/ssp', 'VODGenre::ssp');
$routes->get('/vodgenre/edit/(:any)', 'VODGenre::edit/$1');
$routes->get('/vodgenre/delete/(:any)', 'VODGenre::delete/$1');
$routes->post('/vodgenre/update', 'VODGenre::update');
$routes->post('/vodgenre/insert', 'VODGenre::insert');

// Live TV
$routes->get('/livetv', 'LiveTV::index');
$routes->get('/livetv/ssp', 'LiveTV::ssp');
$routes->get('/livetv/edit/(:any)', 'LiveTV::edit/$1');
$routes->get('/livetv/delete/(:any)', 'LiveTV::delete/$1');
$routes->post('/livetv/update', 'LiveTV::update');
$routes->post('/livetv/insert', 'LiveTV::insert');

//live tv category
$routes->get('/livetvcategory', 'LiveTvCategory::index');
$routes->get('/livetvcategory/ssp', 'LiveTvCategory::ssp');
$routes->get('/livetvcategory/edit/(:any)', 'LiveTvCategory::edit/$1');
$routes->get('/livetvcategory/delete/(:any)', 'LiveTvCategory::delete/$1');
$routes->post('/livetvcategory/update', 'LiveTvCategory::update');
$routes->post('/livetvcategory/insert', 'LiveTvCategory::insert');

//livetv package
$routes->get('/livetvpackage', 'LiveTvPackage::index');
$routes->get('/livetvpackage/ssp', 'LiveTvPackage::ssp');
$routes->get('/livetvpackage/edit/(:any)', 'LiveTvPackage::edit/$1');
$routes->get('/livetvpackage/delete/(:any)', 'LiveTvPackage::delete/$1');
$routes->post('/livetvpackage/update', 'LiveTvPackage::update');
$routes->post('/livetvpackage/insert', 'LiveTvPackage::insert');
$routes->get('/livetvpackage/assoc/(:any)', 'LiveTvPackage::assoc/$1');
$routes->post('/livetvpackage/assoc_update', 'LiveTvPackage::assoc_update');

//language
$routes->get('/language', 'Language::index');
$routes->get('/language/ssp', 'Language::ssp');
$routes->get('/language/edit/(:any)', 'Language::edit/$1');
$routes->get('/language/delete/(:any)', 'Language::delete/$1');
$routes->post('/language/update', 'Language::update');
$routes->post('/language/insert', 'Language::insert');

//currency
$routes->get('/currency', 'Currency::index');
$routes->get('/currency/ssp', 'Currency::ssp');
$routes->get('/currency/edit/(:any)', 'Currency::edit/$1');
$routes->get('/currency/delete/(:any)', 'Currency::delete/$1');
$routes->post('/currency/update', 'Currency::update');
$routes->post('/currency/insert', 'Currency::insert');

//setting
$routes->get('/setting', 'Setting::index');
$routes->get('/setting/ssp', 'Setting::ssp');
$routes->get('/setting/edit/(:any)', 'Setting::edit/$1');
$routes->get('/setting/delete/(:any)', 'Setting::delete/$1');
$routes->post('/setting/update', 'Setting::update');
$routes->post('/setting/insert', 'Setting::insert');

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
