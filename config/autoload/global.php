<?php
return array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn'    => 'mysql:host=localhost;dbname=refgen3'
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
        )
    )
);
