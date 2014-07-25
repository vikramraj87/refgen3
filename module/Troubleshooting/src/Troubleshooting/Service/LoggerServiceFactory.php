<?php
namespace Troubleshooting\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Log\Logger,
    Zend\Log\Writer\Stream;

class LoggerServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Logger
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Stream $writer */
        $writer = $serviceLocator->get('Troubleshooting\Service\LoggerStream');

        /** @var Logger $log */
        $log = new Logger();
        $log->addWriter($writer);
        return $log;
    }

} 