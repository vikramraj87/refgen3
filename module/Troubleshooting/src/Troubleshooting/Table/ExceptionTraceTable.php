<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 23/07/14
 * Time: 10:51 PM
 */

namespace Troubleshooting\Table;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\AdapterAwareInterface;
class ExceptionTraceTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'exception_traces';

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

    public function fetchTracesByExceptionId($exceptionId = 0)
    {
        $exceptionId = (int) $exceptionId;
        $select = $this->getSql()
                        ->select()
                        ->where(array('exception_id' => $exceptionId))
                        ->order('position');
        $rowset = $this->selectWith($select);
        $traces = array();
        foreach($rowset as $row) {
            $traces[] = $row['trace'];
        }
        return $traces;
    }

    public function saveTraces($traceString = '', $exceptionId = 0)
    {
        $exceptionId = (int) $exceptionId;
        if(0 === $exceptionId) {
            return true;
        }

        $traceArray = explode("\n", $traceString);
        $data = array('exception_id' => $exceptionId);
        $pos = 1;
        foreach($traceArray as &$trace) {
            $trace = preg_replace('/#[0-9]+ /', '', $trace);
            $data['position'] = $pos;
            $data['trace']    = $trace;
            $this->insert($data);
            $pos++;
        }
        unset($trace);
        return true;
    }
} 