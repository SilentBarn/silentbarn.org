<?php

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

define( 'ROOT_PATH', __DIR__ );
define( 'APP_PATH', __DIR__ . '/../app' );
define( 'VENDOR_PATH', __DIR__ . '/..vendor' );
set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path() );

// required for phalcon/incubator
//
include __DIR__ . "/../vendor/autoload.php";

// bootstrap our application
//
$config = include APP_PATH . '/config/config.php';
$localConfig = include APP_PATH . '/config/config.local.php';
$config->merge( $localConfig );

include APP_PATH . '/config/loader.php';
include APP_PATH . '/config/services.php';
include APP_PATH . '/config/constants.php';
include APP_PATH . '/config/helpers.php';

// use the application autoloader to autoload the classes
// autoload the dependencies found in composer
//
$loader->registerDirs(
    array(
        ROOT_PATH
    ))->register();

\Phalcon\DI::setDefault( $di );
