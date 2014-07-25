<?php
namespace Troubleshooting\Service;

use Troubleshooting\Table\ExceptionTable;
use Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    Zend\EventManager\EventInterface,
    Zend\Mvc\MvcEvent,
    Zend\Log\Logger;

class ErrorHandlingService extends AbstractListenerAggregate
{
    /** @var ExceptionTable */
    private $exceptionTable;

    /** @var Logger */
    private $logger;

    /**
     * @param mixed $exceptionTable
     */
    public function setExceptionTable(ExceptionTable $exceptionTable)
    {
        $this->exceptionTable = $exceptionTable;
    }

    /**
     * @param \Zend\Log\Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('dispatch.error', array($this, 'handleException'));
    }

    public function handleException(EventInterface $event)
    {
        /** @var MvcEvent $event */

        $exception = $event->getResult()->exception;
        if($exception) {
            $this->saveException($exception);
        }
    }

    private function saveException(\Exception $e)
    {
        try {
            $this->exceptionTable->saveException($e);
        } catch(\Exception $e) {
            $this->logException($e);
        }
    }

    private function logException(\Exception $e)
    {
        $trace = $e->getTraceAsString();
        $i = 1;
        do {
            $messages[] = $i++ . ': ' . $e->getMessage();
        } while($e = $e->getPrevious());

        $log = "Exception:\n" . implode("\n", $messages);
        $log .= "\nTrace:\n" . $trace;
        $this->logger->err($log);
    }


} 