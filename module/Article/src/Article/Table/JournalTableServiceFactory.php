<?php
namespace Article\Table;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Db\Adapter\Adapter;

class JournalTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return JournalTable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Adapter $adapter */
        $adapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        $table = new JournalTable();
        $table->setDbAdapter($adapter);
        return $table;
    }
}