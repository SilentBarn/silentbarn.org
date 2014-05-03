<?php

return [
    'app' => [
        'environment' => 'skullsgaard',
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
        'userId' => '500398575',
        'mediaUrl' => 'https://api.instagram.com/v1/users/%s/media/recent/?client_id=%s&count=%s',
        'clientId' => '##INSTAGRAMCLIENTID##'
    ]];