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
                'may_terminate' => false
            )
        )
    ),
    'service_manager' => array(
        'invokables' => array(
            'User\Table\UserSocial' => 'User\Table\UserSocialTable'
        ),
        'factories' => array(
            'User\Table\User'       => 'User\Table\UserTableServiceFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);