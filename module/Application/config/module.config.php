<?php
use Zend\Mvc\Controller\ControllerManager;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    )
                )
            ),
            'search' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/search[/:term[/page/:page]]',
                    'constraints' => array(
                        'page' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'search',
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
            'Application\Service\ErrorHandling' => function(\Zend\ServiceManager\ServiceManager $sm) {
                    $logger = $sm->get('Application\Logger\Error');
                    $service = new \Application\Service\ErrorHandlingService($logger);
                    return $service;
                },
            'Application\Logger\Error' => function (\Zend\ServiceManager\ServiceManager $sm) {
                $fileName = 'log_' . date('F_d') . '.log';
                $log = new \Zend\Log\Logger();
                $writer = new \Zend\Log\Writer\Stream('./data/logs/' . $fileName);
                $log->addWriter($writer);
                return $log;
            }
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'Application\Controller\Index' => function (ControllerManager $cm) {
                    $sm = $cm->getServiceLocator();
                    $service = $sm->get('Pubmed\Service\Pubmed');
                    $controller = new \Application\Controller\IndexController();
                    $controller->setPubmedService($service);
                    return $controller;
                }
        ),
        'invokables' => array(
            'Application\Controller\Error' => 'Application\Controller\ErrorController'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true, //true
        'display_exceptions'       => true, //true
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'session' => array(
        'remember_me_seconds' => 2419200,
        'use_cookies'         => true,
        'cookie_httponly'     => true
    )
);
