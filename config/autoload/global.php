<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
        )
    ),
    'google' => array(
        'scopes'        => array(
            'email',
            'profile'
        )
    ),
    'facebook' => array(
        'scopes'       => array(
            'email'
        )
    ),
    'db' => array(
        'driver' => 'Pdo',
        'driverOptions' => array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        )
    )
);
