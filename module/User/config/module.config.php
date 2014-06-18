<?php
use Zend\Mvc\Controller\ControllerManager,
    Zend\ServiceManager\ServiceManager;
use User\Controller\UserController;

return array(
    'controllers' => array(
        'factories'  => array(
            'User\Controller\User' => function(ControllerManager $cm) {
                /** @var \Zend\ServiceManager\ServiceManager $sm */
                $sm = $cm->getServiceLocator();

                include_once 'Google/Client.php';
                $config = $sm->get('config');
                $googleConfig = $config['google'];
                $googleClient = new \Google_Client();
                $googleClient->setClientId($googleConfig['client_id']);
                $googleClient->setClientSecret($googleConfig['client_secret']);
                $googleClient->setRedirectUri($googleConfig['redirect_uri']);
                $googleClient->setScopes($googleConfig['scopes']);
                $googleClient->setAccessType('online');
                $googleClient->setApprovalPrompt('auto');
                $controller = new UserController();
                $controller->setGoogleClient($googleClient);
                return $controller;
            }
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
                    'google' => array(
                        'type'    => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/google_auth',
                            'defaults' => array(
                                'action' => 'google-auth'
                            )
                        )
                    ),
                    'facebook' => array(
                        'type'    => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route'    => '/fb_auth',
                            'defaults' => array(
                                'action' => 'fb-auth'
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
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);