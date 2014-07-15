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
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'User\Table\User' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                $table = new \User\Table\UserTable();
                $table->setDbAdapter($adapter);
                return $table;
            }
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);