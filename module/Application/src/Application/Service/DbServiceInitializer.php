<?php
namespace Application\Service;

use Zend\ServiceManager\InitializerInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface;

class DbServiceInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param AdapterAwareInterface $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if($instance instanceof AdapterAwareInterface) {
            $adapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
            $instance->setDbAdapter($adapter);
        }
    }

} 