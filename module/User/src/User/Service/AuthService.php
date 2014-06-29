<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 20/06/14
 * Time: 7:16 PM
 */

namespace User\Service;

use User\Entity\User,
    User\Table\UserTable;
use Zend\Session\Container;

class AuthService
{
    /** @var Container */
    private $container;

    /** @var UserTable */
    private $userTable;

    public function __construct(UserTable $userTable)
    {
        $this->userTable = $userTable;
        $this->container = new Container('user');
    }

    public function persist(User $user)
    {
        $foundUser = $this->userTable->checkUser($user);
        if(null === $foundUser) {
            return false;
        }
        $this->container->user = $user;
        return true;
    }

    public function getUser()
    {
        if($this->container->offsetExists('user')) {
            return $this->container->offsetGet('user');
        }
        return null;
    }

    public function logOut()
    {
        if($this->container->offsetExists('user')) {
            $this->container->offsetUnset('user');
        }
        return true;
    }
} 