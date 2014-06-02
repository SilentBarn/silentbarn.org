<?php

return [
    'app' => [
        'environment' => 'skullsgaard',
        'responseMode' => 'view',
        'modules' => [
            'admin' => 'Admin' ],
        'assetVersion' => 1,
        'assetMode' => 'development' ],

    'cache' => [
        'adapter' => 'redis' ],

    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '##SQLPASSWORD##',
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
        'query' => FALSE ],

    'instagram' => [
        'clientId' => '##INSTAGRAMCLIENTID##' ],

    'mailgun' => [
        'to' => [
            'rentals' => '##MAILGUNTO##',
            'events' => '##MAILGUNTO##',
            'stewdios' => '##MAILGUNTO##' ],
        'smtp' => [
            'hostname' => '##MAILGUNHOSTNAME##',
            'username' => '##MAILGUNUSERNAME##',
            'password' => '##MAILGUNPASSWORD##' ]
    ]];