<?php
return array(
    'google' => array(
        'client_id'     => '506827998295-f4q0jdkqdbhcn6jntr4c11ujuur9eon0.apps.googleusercontent.com',
        'client_secret' => 'HTTAU6BQhSaObrlUAM-mwAUG',
        'redirect_url'  => 'http://www.kivirefgen.com/auth/google',
        'scopes'        => array(
            'email',
            'profile'
        )
    ),
    'facebook' => array(
        'client_id'       => '342149289269654',
        'client_secret'   => '5c6cdc413ae119bee0177931177ebe00',
        'redirect_url' => 'http://www.kivirefgen.com/auth/facebook',
        'scopes'       => array(
            'email'
        )
    ),
    'db' => array(
        'driver' => 'Pdo',
        'dsn'    => 'mysql:host=localhost;dbname=refgen',
        'username' => 'usr',
        'password' => 'D3fyGr@v1ty'
    ),
    'php_settings' => array(
        'error_reporting' => E_ALL,
        'display_errors' => 'on',
        'display_startup_errors' => 'on',
    )
);