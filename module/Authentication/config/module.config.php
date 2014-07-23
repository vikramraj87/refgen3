<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Authentication\Adapter\Facebook'       => 'Authentication\Adapter\FacebookAdapterServiceFactory',
            'Authentication\Adapter\Google'         => 'Authentication\Adapter\GoogleAdapterServiceFactory',
            'Authentication\Service\Authentication' => 'Authentication\Service\AuthenticationServiceFactory'
        )
    ),
    'controllers' => array(
        'factories' => array(
            'Authentication\Controller\Authentication' => 'Authentication\Controller\AuthenticationControllerServiceFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'authService' => 'Authentication\View\Helper\AuthenticationHelperServiceFactory'
        )
    ),
    'router' => array(
        'routes' => array(
            'auth' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/auth',
                    'defaults' => array(
                        'controller' => 'Authentication\Controller\Authentication'
                    )
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/login[/:reason]',
                            'constraints' => array(
                                'reason' => '[a-zA-Z_-]+'
                            ),
                            'defaults' => array(
                                'action' => 'login',
                                'reason' => ''
                            )
                        )
                    ),
                    'logout' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'action' => 'logout'
                            )
                        )
                    ),
                    'facebook' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/facebook',
                            'defaults' => array(
                                'action' => 'facebook'
                            )
                        )
                    ),
                    'google' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/google',
                            'defaults' => array(
                                'action' => 'google'
                            )
                        )
                    )
                )
            )
        )
    )
);