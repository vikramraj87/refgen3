<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Navigation\Controller\Navigation' => 'Navigation\Controller\NavigationController'
        )
    ),
    'router' => array(
        'routes' => array(
            'navigation' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/navigation',
                    'defaults' => array(
                        'controller' => 'Navigation\Controller\Navigation',
                        'action'     => 'navigation'
                    )
                )
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )
);