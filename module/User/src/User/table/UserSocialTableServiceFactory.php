<?php

namespace User\table;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserSocialTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        $table = new UserSocialTable();
        $table->setDbAdapter($adapter);
        return $table;
    }

} 