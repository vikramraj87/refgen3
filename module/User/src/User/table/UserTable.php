<?php
namespace User\table;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\AdapterAwareInterface;
use User\Entity\User;
use DateTime;

class UserTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'users';

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
     * Checks for the existence of the user. If found, returns the user
     * from the tale. Else, a new user entry is made in the table and the
     * same is fetched back and returned. Returns null  if no user is
     * found and the new user couldn't be saved
     *
     * @param User $user
     * @return User|null
     */
    public function checkUser(User $user)
    {
        $foundUser = null;
        $facebookId = $user->getFacebookId();
        $email = $user->getEmail();
        $updateFlag = false;

        if(!empty($facebookId)) {
            $foundUser = $this->fetchUserByFacebookId($facebookId);
            if($foundUser instanceof User) {
                return $this->updateLastLoggedIn($foundUser);
            }
            $updateFlag = true;
        }
        $foundUser = $this->fetchUserByEmail($email);
        if($foundUser instanceof User) {
            if($updateFlag) {
                $foundUser->setFacebookId($facebookId);
                $foundUser = $this->updateFacebookId($foundUser);
            }
            return $this->updateLastLoggedIn($foundUser);
        }
        $foundUser = $this->saveUser($user);
        return $foundUser;
    }

    /**
     * Fetches user by id
     *
     * @param int $id
     * @return null|User
     */
    public function fetchUserById($id = 0)
    {
        $id = (int) $id;
        $rowset = $this->select(array(
            'id' => $id
        ));
        $data = $rowset->current();
        if(false === $data) {
            return null;
        }
        return $this->userFromData($data);
    }

    private function fetchUserByEmail($email = '')
    {
        $rowset = $this->select(array(
            'email' => $email
        ));
        $data = $rowset->current();
        if(false === $data) {
            return null;
        }
        return $this->userFromData($data);
    }

    private function fetchUserByFacebookId($facebookId = '')
    {
        $rowset = $this->select(array(
            'facebook_id' => $facebookId
        ));
        $data = $rowset->current();
        if(false === $data) {
            return null;
        }
        return $this->userFromData($data);
    }

    private function saveUser(User $user)
    {
        $now = new DateTime('now');
        $data = array(
            'email' => $user->getEmail(),
            'name'  => $user->getName(),
            'facebook_id' => $user->getFacebookId(),
            'last_logged_in' => $now->format('Y-m-d H:i:s')
        );
        $numRowsAffected = $this->insert($data);
        return $this->fetchUserByEmail($user->getEmail());
    }

    private function updateFacebookId(User $user)
    {
        $this->update(array(
                'facebook_id' => $user->getFacebookId()
            ), array(
                'id' => $user->getId()
            )
        );
        return $this->fetchUserById($user->getId());
    }

    private function updateLastLoggedIn(User $user)
    {
        $lastLoggedIn = new \DateTime('now');
        $lastLoggedInStr = $lastLoggedIn->format('Y-m-d H:i:s');
        $this->update(array(
                'last_logged_in' => $lastLoggedInStr
            ), array(
                'id' => $user->getId()
            )
        );
        $user->setLastLoggedIn($lastLoggedIn);
        return $user;
    }

    private function userFromData($data)
    {
        $createdOn = null == $data['created_on']        ? null : new DateTime($data['created_on']);
        $lastLoggedIn = null == $data['last_logged_in'] ? null : new DateTime($data['last_logged_in']);

        $user = new User;
        $user->setId($data['id']);
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setFacebookId($data['facebook_id']);
        $user->setCreatedOn($createdOn);
        $user->setLastLoggedIn($lastLoggedIn);
        return $user;
    }
}