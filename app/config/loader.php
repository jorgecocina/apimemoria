<?php

$loader = new \Phalcon\Loader();
$loader->registerNamespaces(
    [
        'App\Business'    => $config->application->businessDir,
        'App\Controllers' => $config->application->controllersDir,
        'App\Models'      => $config->application->modelsDir,
        'App\Helpers'     => $config->application->helpersDir,
        'App\Exceptions'  => $config->application->exceptionsDir,
    ]
);

$loader->register();