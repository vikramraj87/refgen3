<?php
namespace Troubleshooting\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Log\Writer\Stream;
class LoggerStreamServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fileName = 'log_' . date('F_d') . '.log';
        $writer = new Stream('./data/logs/' . $fileName);
        return $writer;
    }

} 