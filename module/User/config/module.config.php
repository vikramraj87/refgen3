<?php
use Zend\Mvc\Controller\ControllerManager,
    Zend\ServiceManager\ServiceManager;
use User\Controller\UserController,
    User\Service\FacebookService;

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
                        'controller' => 'User\Controller\User',
                        'action'     => 'index'
                    )
                ),
                'may_terminate' => true,
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
            ),
            'auth' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/auth',
                    'defaults' => array(
                        'controller' => 'User\Controller\Auth'
                    )
                ),
                'child_routes' => array(
                    'facebook' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/facebook',
                            'defaults' => array(
                                'action' => 'facebook'
                            )
                        )
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'FacebookService' => function(ServiceManager $sm) {
                $config = $sm->get('config');
                $facebookConfig = $config['facebook'];

                $service = new FacebookService(
                    $facebookConfig['client_id'],
                    $facebookConfig['client_secret'],
                    $facebookConfig['redirect_url'],
                    $facebookConfig['scopes']
                );

                return $service;
            }
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);