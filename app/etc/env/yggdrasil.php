<?php

return [
    'app' => [
        'environment' => 'yggdrasil',
        'responseMode' => 'view',
        'modules' => [
            'admin' => 'Admin' ],
        'jsEnvironment' => 'development' ],

    'cache' => [
        'adapter' => 'redis' ],

    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '##SQLPASSWORD##',
        'dbname' => 'silentbarn',
        'persistent' => TRUE ],

    'paths' => [
        'baseUri' => 'http://beta.silentbarn.com/',
        'assetUri' => 'http://beta.silentbarn.com/',
        'hostname' => 'beta.silentbarn.com',
        'media' => '/home/mike/www/barn.shadowmere.net/www-data/media',
        'mediaPublic' => 'http://beta.silentbarn.com/media/' ],

    'cookies' => [
        'secure' => FALSE ],

    'profiling' => [
        'query' => FALSE
    ]];
