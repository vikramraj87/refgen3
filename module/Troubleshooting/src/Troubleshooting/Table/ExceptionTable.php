<?php
namespace Troubleshooting\Table;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\AdapterAwareInterface;
use Troubleshooting\Entity\Exception;

class ExceptionTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'exceptions';

    /** @var ExceptionTraceTable */
    private $traceTable;

    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     * @return AdapterAwareInterface
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * @param \Troubleshooting\Table\ExceptionTraceTable $traceTable
     */
    public function setTraceTable(ExceptionTraceTable $traceTable)
    {
        $this->traceTable = $traceTable;
    }

    public function fetchExceptionById($id)
    {
        $id = (int) $id;
        $data = $this->select(array(
                'id' => $id
            )
        )->current();

        $exception = new Exception();
        $exception->setId($data['id']);
        $exception->setCode($data['code']);
        $exception->setMessage($data['message']);
        $exception->setClass($data['class']);
        $exception->setFile($data['file']);
        $exception->setLine($data['line']);
        $exception->setRaisedOn(new \DateTime($data['raised_on']));
        $exception->setTrace($this->traceTable->fetchTracesByExceptionId($data['id']));

        $prevData = $this->select(array(
                'previous_of' => $exception->getId()
            )
        )->current();

        if(false !== $prevData) {
            $exception->setPrevious($this->fetchExceptionById($prevData['id']));
        }

        return $exception;
    }

    public function saveException(\Exception $e, $previousOf = null)
    {
        $data = array(
            'class'       => get_class($e),
            'code'        => $e->getCode(),
            'message'     => $e->getMessage(),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'previous_of' => $previousOf
        );
        $this->insert($data);
        $id = $this->getLastInsertValue();
        if($id) {
            $this->traceTable->saveTraces($e->getTraceAsString(), $id);
            if(null != $e->getPrevious())  {
                $this->saveException($e->getPrevious(), $id);
            }
        }
        return true;
    }
}