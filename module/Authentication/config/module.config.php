<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Authentication\Adapter\Facebook' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $config = $sm->get('config')['facebook'];
                $adapter = new \Authentication\Adapter\FacebookAdapter(
                    $config['client_id'],
                    $config['client_secret'],
                    $config['redirect_url'],
                    $config['scopes']
                );
                return $adapter;
            },
            'Authentication\Adapter\Google' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $config = $sm->get('config')['google'];
                $adapter = new \Authentication\Adapter\GoogleAdapter(
                    $config['client_id'],
                    $config['client_secret'],
                    $config['redirect_url'],
                    $config['scopes']
                );
                return $adapter;
            },
            'Authentication\Service\Authentication' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $userTable = $sm->get('User\Table\User');
                $service = new \Authentication\Service\AuthenticationService();
                $service->setUserTable($userTable);
                return $service;
            }
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Authentication\Controller\Authentication' => 'Authentication\Controller\AuthenticationController'
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'authService' => function(\Zend\View\HelperPluginManager $pm) {
                $sm = $pm->getServiceLocator();
                /** @var \Authentication\Service\AuthenticationService $service */
                $service = $sm->get('Authentication\Service\Authentication');
                $helper = new \Authentication\View\Helper\Authentication($service);
                return $helper;
            }
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