<?php
namespace Authentication\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

class GoogleAdapterServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $googleConfig = $config['google'];
        $adapter = new GoogleAdapter(
            $googleConfig['client_id'],
            $googleConfig['client_secret'],
            $googleConfig['redirect_url'],
            $googleConfig['scopes']
        );
        return $adapter;
    }
} 