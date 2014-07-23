<?php
namespace Authentication\Service;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;
use User\Table\UserTable;
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var UserTable $table */
        $table = $serviceLocator->get('User\Table\User');

        /** @var AuthenticationService $service */
        $service = new AuthenticationService();
        $service->setUserTable($table);
        return $service;
    }

} 