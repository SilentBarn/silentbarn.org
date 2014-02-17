<?php

return [
    'app' => [
        'environment' => 'skullsgaard',
        'responseMode' => 'view',
        'modules' => []],

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
        'hostname' => 'barn.dev',
        'media' => '/home/mike/Projects/Silent-Barn/public/media',
        'mediaPublic' => 'http://barn.dev/media/' ],

    'cookies' => [
        'secure' => FALSE ],

    'profiling' => [
        'query' => FALSE
    ]];
