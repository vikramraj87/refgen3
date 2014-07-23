<?php
namespace Authentication\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;

class FacebookAdapterServiceFactory implements FactoryInterface
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
        $fbConfig = $config['facebook'];
        $adapter = new FacebookAdapter(
            $fbConfig['client_id'],
            $fbConfig['client_secret'],
            $fbConfig['redirect_url'],
            $fbConfig['scopes']
        );
        return $adapter;
    }

} 