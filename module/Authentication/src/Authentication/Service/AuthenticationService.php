<?php
namespace Authentication\Service;

use User\Entity\User,
    User\Table\UserTable;

use Authentication\Adapter\AbstractAdapter;

use Zend\Authentication\Adapter,
    Zend\Authentication\AuthenticationService as ZendAuthenticationService,
    Zend\Authentication\Exception as AuthenticationException,
    Zend\Authentication\Result;

class AuthenticationService extends ZendAuthenticationService
{
    /** @var UserTable */
    private $userTable;

    public function authenticate(Adapter\AdapterInterface $adapter = null)
    {
        /** @var \Authentication\Adapter\AbstractAdapter $adapter*/

        if (!$adapter) {
            if (!$adapter = $this->getAdapter()) {
                throw new AuthenticationException\RuntimeException('An adapter must be set or passed prior to calling authenticate()');
            }
        }
        $result = $adapter->authenticate();

        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        if ($result->isValid()) {
            $identity = $this->userTable->checkUser($result->getIdentity(), $adapter->getId());
            $result = new Result(Result::SUCCESS, $identity);
            $this->getStorage()->write($result->getIdentity());
        }

        return $result;
    }

    /**
     * @param \User\Table\UserTable $userTable
     */
    public function setUserTable($userTable)
    {
        $this->userTable = $userTable;
    }

    public function getRoleId()
    {
        $roleId = 'Guest';
        if($this->hasIdentity()) {
            $id = $this->getIdentity()->getRole();
            switch($id) {
                case '1':
                case 'user':
                case 'User':
                    $roleId = 'User';
                    break;
                case '2':
                case 'moderator':
                case 'Moderator':
                    $roleId = 'Moderator';
                    break;
                case '3':
                case 'admin':
                case 'Admin':
                    $roleId = 'Admin';
                    break;
            }
        }
        return $roleId;
    }
}