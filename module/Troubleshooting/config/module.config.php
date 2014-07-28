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
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Troubleshooting\Controller\Test' => 'Troubleshooting\Controller\TestController'
        )
    ),
    'router' => array(
        'routes' => array(
            'test_error' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/test/error',
                    'defaults' => array(
                        'controller' => 'Troubleshooting\Controller\Test',
                        'action'     => 'error'
                    )
                )
            )
        )
    )
);