<?php

return [
    'app' => [
        'environment' => 'yggdrasil',
        'responseMode' => 'view',
        'modules' => [
            'admin' => 'Admin' ],
        'assetVersion' => 16,
        'assetMode' => 'production' ],

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
        'assetUri' => 'http://beta.silentbarn.com/assets/%s/',
        'hostname' => 'beta.silentbarn.com',
        'media' => '/home/mike/www/barn.shadowmere.net/www-data/media',
        'mediaPublic' => 'http://beta.silentbarn.com/media/' ],

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