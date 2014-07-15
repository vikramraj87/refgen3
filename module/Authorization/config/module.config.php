<?php
return array(
    'authorization' => array(
        'guest' => array(
            'User\Controller\User' => array(
                'login'
            )
        ),
        'member' => array(
            'User\Controller\User' => array(
                'profile',
                'logout'
            ),
            'Collection\Controller\Collection'
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Authorization\Service\Authorization' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $authService = $sm->get('Authentication\Service\Authentication');
                $config      = $sm->get('config')['authorization'];
                $service = new \Authorization\Service\AuthorizationService($config, $authService);
                return $service;
            }
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'hasAccess' => function(\Zend\View\HelperPluginManager $pm) {
                $sm = $pm->getServiceLocator();
                /** @var \Authorization\Service\AuthorizationService $service */
                $service = $sm->get('Authorization\Service\Authorization');
                $helper = new \Authorization\View\Helper\HasAccess($service);
                return $helper;
            }
        )
    )
);