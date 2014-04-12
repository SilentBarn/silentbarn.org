<?php

return [
    'app' => [
        'environment' => 'yggdrasil',
        'responseMode' => 'view',
        'modules' => [
            'admin' => 'Admin' ],
        'jsEnvironment' => 'production' ],

    'cache' => [
        'adapter' => 'redis' ],

    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'silentbarn',
        'persistent' => TRUE ],

    'paths' => [
        'baseUri' => 'http://barn.shadowmere.net/',
        'assetUri' => 'http://barn.shadowmere.net/',
        'hostname' => 'barn.shadowmere.net',
        'media' => '/home/mike/www/barn.shadowmere.net/www-data/media',
        'mediaPublic' => 'http://barn.shadowmere.net/media/' ],

    'cookies' => [
        'secure' => FALSE ],

    'profiling' => [
        'query' => FALSE
    ]];
