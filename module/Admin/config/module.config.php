<?php
return array(
    'controllers' => array(
        'factories' => array(
            'Admin\Controller\Admin' => 'Admin\Controller\AdminControllerServiceFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Admin',
                        'action'     => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'users' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/users',
                            'defaults' => array(
                                'action' => 'users'
                            )
                        )
                    ),
                    'exceptions' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/exceptions',
                            'defaults' => array(
                                'action' => 'exceptions'
                            )
                        )
                    )
                )
            )
        )
    )
);