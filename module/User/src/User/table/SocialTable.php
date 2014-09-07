<?php
namespace User\table;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\AbstractTableGateway;

class SocialTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'socials';

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
     * Returns name for id if exists. Else, creates one and returns the last inserted id
     *
     * @param string $name
     * @return int
     */
    public function fetchIdByName($name = '')
    {
        $data = $this->select(array(
                'name' => $name
            )
        )->current();
        if(false === $data) {
            return null;
        }
        return (int) $data['id'];
    }

    /**
     * Returns id for name
     *
     * @param int $id
     * @return string|null
     */
    public function fetchNameById($id = 0)
    {
        $id = (int) $id;
        $data = $this->select(array(
                'id' => $id
            )
        )->current();
        if(false !== $data) {
            return $data['name'];
        }
        return null;
    }
} 