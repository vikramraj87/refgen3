<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController',
            'User\Controller\Auth' => 'User\Controller\AuthController'
        )
    ),
    'router' => array(
        'routes' => array(
            'user' => array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/user',
                    'defaults' => array(
                        'controller' => 'User\Controller\User'
                    )
                ),
                'may_terminate' => false,
                'child_routes'  => array(
                    'login' => array(
                        'type'    => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route'    => '/login',
                            'defaults' => array(
                                'action' => 'login'
                            )
                        )
                    ),
                    'login-email-required' => array(
                        'type'    => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route'    => '/login-email-required',
                            'defaults' => array(
                                'action' => 'login-email-required'
                            )
                        )
                    ),
                    'logout' => array(
                        'type'    => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route'    => '/logout',
                            'defaults' => array(
                                'action' => 'logout'
                            )
                        )
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'User\Table\UserSocial' => 'User\Table\UserSocialTableServiceFactory',
            'User\Table\User'       => 'User\Table\UserTableServiceFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);