<?php
namespace Troubleshooting\Table;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;
class ExceptionTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ExceptionTraceTable $traceTable */
        $traceTable = $serviceLocator->get('Troubleshooting\Table\ExceptionTrace');

        /** @var ExceptionTable $table */
        $table = new ExceptionTable();
        $table->setTraceTable($traceTable);
        return $table;
    }

}