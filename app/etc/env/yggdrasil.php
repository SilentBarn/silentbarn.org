<?php

return [
    'app' => [
        'environment' => 'yggdrasil',
        'responseMode' => 'view',
        'modules' => [
            'admin' => 'Admin' ],
        'assetVersion' => 43,
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
        'baseUri' => 'http://silentbarn.org/',
        'assetUri' => 'http://silentbarn.org/assets/%s/',
        'hostname' => 'silentbarn.org',
        'media' => '/home/mike/www/barn.shadowmere.net/www-data/media',
        'mediaPublic' => 'http://silentbarn.org/media/' ],

    'cookies' => [
        'secure' => FALSE ],

    'profiling' => [
        'query' => FALSE ],

    'recaptcha' => [
        'siteKey' => '6LcVkRYUAAAAAGFfY3PnCEJ4r0hdlWOYsvnRm6pS',
        'secretKey' => '##RECAPTCHASECRET##' ],

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