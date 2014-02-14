<?php

return [
    'app' => [
        'environment' => 'local',
        'responseMode' => 'view',
        'modules' => [
            'api' => 'Api' ]],

    'cache' => [
        'adapter' => 'redis' ],

    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'silentbarn',
        'persistent' => TRUE ],

    'paths' => [
        'baseUri' => 'http://barn.dev/',
        'assetUri' => 'http://barn.dev/',
        'hostname' => 'barn.dev' ],

    'cookies' => [
        'secure' => TRUE ],

    'profiling' => [
        'query' => FALSE
    ]];
