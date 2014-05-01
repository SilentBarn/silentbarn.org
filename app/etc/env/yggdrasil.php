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
        'baseUri' => 'http://barn.silentbarn.com/',
        'assetUri' => 'http://barn.silentbarn.com/',
        'hostname' => 'barn.silentbarn.com',
        'media' => '/home/mike/www/barn.shadowmere.net/www-data/media',
        'mediaPublic' => 'http://barn.silentbarn.com/media/' ],

    'cookies' => [
        'secure' => FALSE ],

    'profiling' => [
        'query' => FALSE
    ]];
