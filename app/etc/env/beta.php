<?php

return [
    'app' => [
        'environment' => 'beta',
        'responseMode' => 'view',
        'modules' => [
            'admin' => 'Admin' ],
        'assetVersion' => 41,
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
        'baseUri' => 'http://beta.silentbarn.org/',
        'assetUri' => 'http://beta.silentbarn.org/assets/%s/',
        'hostname' => 'beta.silentbarn.org',
        'media' => '/home/mike/www/beta.silentbarn.org/www-data/media',
        'mediaPublic' => 'http://beta.silentbarn.org/media/' ],

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