<?php
return array(
    'service_manager' => array(
        'invokables' => array(
            'Troubleshooting\Table\ExceptionTrace' => 'Troubleshooting\Table\ExceptionTraceTable'
        ),
        'factories' => array(
            'Troubleshooting\Table\Exception'       => 'Troubleshooting\Table\ExceptionTableServiceFactory',
            'Troubleshooting\Service\LoggerStream'  => 'Troubleshooting\Service\LoggerStreamServiceFactory',
            'Troubleshooting\Service\Logger'        => 'Troubleshooting\Service\LoggerServiceFactory',
            'Troubleshooting\Service\ErrorHandling' => 'Troubleshooting\Service\ErrorHandlingServiceFactory'
        )
    )
);