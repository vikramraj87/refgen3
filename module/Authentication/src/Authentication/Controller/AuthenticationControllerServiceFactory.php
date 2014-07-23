<?php
namespace Authentication\Controller;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;
use Authentication\Adapter\FacebookAdapter,
    Authentication\Adapter\GoogleAdapter,
    Authentication\Service\AuthenticationService;
class AuthenticationControllerServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ServiceLocatorInterface $sl */
        $sl = $serviceLocator->getServiceLocator();

        /** @var FacebookAdapter $facebookAdapter */
        $facebookAdapter = $sl->get('Authentication\Adapter\Facebook');

        /** @var GoogleAdapter $googleAdapter */
        $googleAdapter = $sl->get('Authentication\Adapter\Google');

        /** @var AuthenticationService $authService */
        $authService = $sl->get('Authentication\Service\Authentication');

        /** @var AuthenticationController $controller */
        $controller = new AuthenticationController();
        $controller->setAuthService($authService);
        $controller->setFacebookAdapter($facebookAdapter);
        $controller->setGoogleAdapter($googleAdapter);
        return $controller;
    }

} 