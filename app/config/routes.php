<?php

use App\Exceptions\Http404Exception;
use App\Controllers\ServicetypesController;
use App\Controllers\ReportsController;
use App\Controllers\ServicesController;
use App\Controllers\UsersController;
use App\Controllers\PricerangesController;
use \Phalcon\Mvc\Micro\Collection;

// Setting DI container
$app->setDI($di);

/**
 * Users controller
 */
$usersCollection = new Collection();
$usersCollection->setHandler(new UsersController());
$usersCollection->setPrefix('/users');
$usersCollection->get('/{id:[1-9][0-9]*}', 'getByIdAction');
$usersCollection->post('/', 'addNewAction');
$usersCollection->put('/{id:[1-9][0-9]*}', 'updateAction');
$usersCollection->post('/login', 'loginAction');
$app->mount($usersCollection);

/**
 * Services controller
 */
$servicesCollection = new Collection();
$servicesCollection->setHandler(new ServicesController());
$servicesCollection->setPrefix('/services');
$servicesCollection->get('/{id:[1-9][0-9]*}', 'getAction');
$servicesCollection->get('/find', 'getListAction');
$servicesCollection->get('/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}', 'getListPRTAction');
$servicesCollection->get('/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}/radius/{rad:[1-9][0-9]*}', 'getListPRTAction');
$servicesCollection->get('/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}/service_type/{type:[1-9][0-9]*}', 'getListPTRAction');
$servicesCollection->get('/radius/{rad:[1-9][0-9]*}/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}', 'getListRPTAction');
$servicesCollection->get('/service_type/{type:[1-9][0-9]*}/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}', 'getListTPRAction');
$servicesCollection->get('/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}/radius/{rad:[1-9][0-9]*}/service_type/{type:[1-9][0-9]*}', 'getListPRTAction');
$servicesCollection->get('/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}/service_type/{type:[1-9][0-9]*}/radius/{rad:[1-9][0-9]*}', 'getListPTRAction');
$servicesCollection->get('/radius/{rad:[1-9][0-9]*}/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}/service_type/{type:[1-9][0-9]*}', 'getListRPTAction');
$servicesCollection->get('/radius/{rad:[1-9][0-9]*}/service_type/{type:[1-9][0-9]*}/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}', 'getListRTPAction');
$servicesCollection->get('/service_type/{type:[1-9][0-9]*}/radius/{rad:[1-9][0-9]*}/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}', 'getListTRPAction');
$servicesCollection->get('/service_type/{type:[1-9][0-9]*}/position/{x:-{0,1}[1-9][0-9]*\.[0-9]+},{y:-{0,1}[1-9][0-9]*\.[0-9]+}/radius/{rad:[1-9][0-9]*}', 'getListTPRAction');
$servicesCollection->delete('/{id:[1-9][0-9]*}', 'deleteAction');
$app->mount($servicesCollection);

/**
 * Resports controller
 */
$reportsCollection = new Collection();
$reportsCollection->setHandler(new ReportsController());
$reportsCollection->setPrefix('/reports');
$reportsCollection->post('/addnew', 'newServiceAction');
$reportsCollection->post('/add', 'newAction');
$reportsCollection->delete('/{id:[1-9][0-9]*}', 'deleteAction');
$app->mount($reportsCollection);


/**
 * Service types controller
 */
$servicetypesCollection = new Collection();
$servicetypesCollection->setHandler(new ServicetypesController());
$servicetypesCollection->setPrefix('/service_types');
$servicetypesCollection->post('/', 'addAction');
$servicetypesCollection->get('/', 'getListAction');
$servicetypesCollection->get('/{id:[1-9][0-9]*}', 'getAction');
$servicetypesCollection->put('/{id:[1-9][0-9]*}', 'updateAction');
$servicetypesCollection->delete('/{id:[1-9][0-9]*}', 'deleteAction');
$app->mount($servicetypesCollection);


/**
 * Price ranges controller
 */
$pricerangesCollection = new Collection();
$pricerangesCollection->setHandler(new PricerangesController());
$pricerangesCollection->setPrefix('/price_ranges');
//$pricerangesCollection->post('/', 'addAction');
$pricerangesCollection->get('/', 'getListAction');
$pricerangesCollection->get('/service_type/{id:[1-9][0-9]*}', 'getListAction');
$pricerangesCollection->get('/{id:[1-9][0-9]*}', 'getAction');
//$pricerangesCollection->put('/{id:[1-9][0-9]*}', 'updateAction');
//$pricerangesCollection->delete('/{id:[1-9][0-9]*}', 'deleteAction');
$app->mount($pricerangesCollection);


// not found URLs
$app->notFound( 
    function () use ($app) {
        $exception = new Http404Exception('URI not found: ' . $app->request->getMethod() . ' ' . $app->request->getURI());
        throw $exception;
    }
);