<?php
namespace Troubleshooting;


use Zend\Mvc\MvcEvent;
use Troubleshooting\Service\ErrorHandlingService;
class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()
                          ->getEventManager();
        $serviceManager = $e->getApplication()
                            ->getServiceManager();

        /** @var ErrorHandlingService $errorHandlingService */
        $errorHandlingService = $serviceManager->get('Troubleshooting\Service\ErrorHandling');
        $errorHandlingService->attach($eventManager);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
} 