<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 21/07/14
 * Time: 6:14 PM
 */

namespace Article\Table;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Db\Adapter\Adapter;
class AbstractParaTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AbstractParaTable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Adapter $adapter */
        $adapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        $table = new AbstractParaTable();
        $table->setDbAdapter($adapter);
        return $table;
    }

} 