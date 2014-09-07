<?php
namespace User\table;

use PhpOffice\PhpWord\Exception\Exception;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\AdapterAwareInterface;

class UserEmailTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'user_emails';

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

    public function checkEmail($email = '', $userId = 0)
    {
        $userId = (int) $userId;
        $data = $this->select(array(
                'email' => $email
            )
        )->current();
        if(false === $data) {
            $result = (bool) $this->insert(array(
                    'email' => $email,
                    'user_id' => $userId
                )
            );
            if(false === $result) {
                throw new \RuntimeException('Failed adding email "' . $email . '" for user with user_id "' . $userId . '"');
            }
        }
        return true;
    }

    public function fetchEmailsByUserId($userId = 0)
    {
        $userId = (int) $userId;
        $data = $this->select(array(
                'user_id' => $userId
            )
        );
        if(false === $data) {
            return null;
        }
        $emails = array();

        foreach($data as $email) {
            $emails[] = $email['email'];
        }
        return $emails;
    }

    public function fetchUserIdByEmail($email = '')
    {
        $data = $this->select(array(
                'email' => $email
            )
        )->current();
        if(false === $data) {
            return null;
        }
        return $data['user_id'];
    }
} 