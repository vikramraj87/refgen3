<?php
namespace Collection\View\Helper;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;
use Collection\Table\CollectionTable,
    Authentication\Service\AuthenticationService;

class CollectionHelperServiceFactory implements FactoryInterface
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

        /** @var CollectionTable $table */
        $table = $sl->get('Collection\Table\Collection');

        /** @var AuthenticationService $service */
        $service = $sl->get('Authentication\Service\Authentication');

        return new CollectionHelper($table, $service);
    }

} 