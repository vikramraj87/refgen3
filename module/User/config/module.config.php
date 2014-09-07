<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController'
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
            'User\Table\Social'    => 'User\Table\SocialTable',
            'User\Table\Role'      => 'User\Table\RoleTable',
            'User\Table\UserEmail' => 'User\Table\UserEmailTable'
        ),
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