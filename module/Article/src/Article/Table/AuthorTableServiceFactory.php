<?php
namespace Article\Table;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface,
    Zend\Db\Adapter\Adapter;

class AuthorTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AuthorTable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Adapter $adapter */
        $adapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        $table = new AuthorTable();
        $table->setDbAdapter($adapter);
        return $table;
    }

} 