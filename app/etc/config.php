<?php

return [
    'app' => [
        'environment' => 'local',
        'errorReporting' => TRUE,
        // can be 'api' or 'view'
        'responseMode' => 'api',
        // router namespace modules
        'modules' => [
            'api' => 'Api' ],
        'assetVersion' => 1,
        'assetMode' => 'development' ],

    'paths' => [
        'baseUri' => 'http://phalcon.dev/',
        'assetUri' => 'http://phalcon.dev/',
        'hostname' => 'phalcon.dev',
        'media' => '/var/www/media',
        'mediaPublic' => 'http://phalcon.dev/public/media/' ],

    'session' => [
        // can be 'redis' or 'files'
        'adapter' => 'files',
        'name' => 'phalcon',
        'lifetime' => 1440,
        'cookieLifetime' => 86400 ],

    'cache' => [
        // can be 'redis' or 'files'
        'adapter' => 'files',
        'prefix' => '',
        // only used for files adapter
        // should have web user group write
        'dir' => '/tmp/' ],

    'cookies' => [
        // 14 days
        'expire' => 60*60*24*14,
        'path' => '/',
        'secure' => TRUE,
        'httpOnly' => TRUE ],

    'redis' => [
        'cache' => [
            'host' => 'localhost',
            'port' => 6379 ],
        'session' => [
            'host' => 'localhost',
            'port' => 6379,
            'prefix' => 'session:' ]],

    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'phalcon',
        'persistent' => TRUE ],

    'mongodb' => [
        'host' => 'localhost',
        'port' => 27017,
        'username' => '',
        'password' => '',
        'dbname' => 'phalcon' ],

    'profiling' => [
        'system' => TRUE,
        'query' => TRUE ],

    'settings' => [
        'cookieToken' => 'cookie_token' ],

    'instagram' => [
        'userId' => '500398575',
        'mediaUrl' => 'https://api.instagram.com/v1/users/%s/media/recent/?client_id=%s&count=%s',
        'clientId' => '' ],

    'mailgun' => [
        'from' => 'postmaster@silentbarn.com',
        'fromname' => 'Silent Barn Web',
        'to' => [
            'rentals' => '',
            'events' => '',
            'stewdios' => '' ],
        'smtp' => [
            'hostname' => '',
            'username' => '',
            'password' => '' ]
    ]];