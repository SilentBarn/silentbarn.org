<?php

return [
    'app' => [
        'environment' => 'sanctum',
        'responseMode' => 'view',
        'modules' => [
            'admin' => 'Admin' ],
        'assetVersion' => 1,
        'assetMode' => 'production',
        'analytics' => [
            'enabled' => TRUE,
            'code' => 'UA-37484438-1'
        ]],
    'cache' => [
        'adapter' => 'redis' ],

    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '##SQLPASSWORD##',
        'dbname' => 'silentbarn',
        'persistent' => TRUE ],

    'paths' => [
        'baseUri' => 'https://sanctum.silentbarn.org/',
        'assetUri' => 'https://sanctum.silentbarn.org/assets/%s/',
        'hostname' => 'sanctum.silentbarn.org',
        'media' => '/var/www/sanctum.silentbarn.org/www-data/media',
        'mediaPublic' => 'https://sanctum.silentbarn.org/media/' ],

    'cookies' => [
        'secure' => FALSE ],

    'profiling' => [
        'query' => FALSE ],

    'instagram' => [
        'clientId' => '##INSTAGRAMCLIENTID##' ],

    'mailgun' => [
        'to' => [
            'rentals' => '##MAILGUNTORENTALS##',
            'events' => '##MAILGUNTOEVENTS##',
            'stewdios' => '##MAILGUNTOSTEWDIOS##' ],
        'smtp' => [
            'hostname' => '##MAILGUNHOSTNAME##',
            'username' => '##MAILGUNUSERNAME##',
            'password' => '##MAILGUNPASSWORD##' ]
    ]];