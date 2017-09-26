<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Crypt;
use App\Helpers\AuthToken;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Session\Adapter\Files as Session;

// Initializing a DI Container
$di = new FactoryDefault();

/** Common config */
$di->set('config', $config);

/**
 * Overriding Response-object to set the Content-type header globally
 */
$di->setShared(
    'response',
    function () {
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'utf-8');

        return $response;
    }
);

$di->set(
    "dispatcher",
    function () {
        // Create an event manager
        $eventsManager = new EventsManager();

        // Attach a listener for type "dispatch"
        $eventsManager->attach(
            "dispatch",
            function (Event $event, $dispatcher) {
                // ...
            }
        );

        $dispatcher = new Dispatcher();

        // Bind the eventsManager to the view component
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    },
    true
);

/** Database */
$di->set(
    "db",
    function () use ($config) {
        return new DbAdapter(
            [
                "host"     => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname"   => $config->database->dbname,
                'options' => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                )
            ]
        );
    }
);

$di->setShared(
    'response',
    function () {
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'utf-8');

        return $response;
    }
);

/**
 * Crypt service
 */
$di->set('crypt', function () use ($config) {
    $crypt = new Crypt();

    $crypt->setKey($config->application->cryptSalt);
    return $crypt;
});

/**
 * AuthToken (JWT handler)
 */
$di->set('AuthToken', function () use ($config, $di) {
    return new AuthToken($config, $di->get("security"));
});

$di->setShared(
    "session",
    function () {
        $session = new Session();

        $session->start();

        return $session;
    }
);

// Initializing application
$app = new Phalcon\Mvc\Micro();
require __DIR__ . '/routes.php';
/**
 * Loading routes from the routes.php file
 */
$di->set('router', $app);
