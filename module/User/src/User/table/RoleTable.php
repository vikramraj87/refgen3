<?php
namespace User\table;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\AbstractTableGateway;

class RoleTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'roles';

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

    public function fetchRoleById($id = 0)
    {
        $id = (int) $id;
        $data = $this->select(array(
                'id' => $id
            )
        )->current();
        if(false === $data) {
            return null;
        }
        return $data['role'];
    }
} 