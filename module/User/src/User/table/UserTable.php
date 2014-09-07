<?php
namespace User\table;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\Sql\Expression;
use User\Entity\User;

class UserTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'users';

    /** @var UserSocialTable */
    private $userSocialTable;

    /** @var RoleTable */
    private $roleTable;

    /** @var UserEmailTable */
    private $userEmailTable;

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
     * Checks for the existence of the user. If user exists, the user
     * object is populated with the data from the database. If not found,
     * new entry is created and the user is populated with that
     *
     * @param User $user
     * @param string $social
     * @return null|User
     * @throws \RuntimeException
     */
    public function checkUser(User $user, $social = '')
    {
        //1. User Id is provided
        if($user->getId()) {
            $userData = $this->select(array(
                    'id' => $user->getId()
                )
            )->current();
            if(false === $userData) {
                return null;
            }
            $socialData = $this->userSocialTable->checkUserIdAndSocial(
                $userData['id'],
                $social,
                $user->getSocialId()
            );
            $role = $this->roleTable->fetchRoleById($userData['role']);
            $user->setId($userData['id']);
            $user->setRole($role);
            return $user;
        }

        $socialData = $this->userSocialTable->fetchBySocialAndSocialId($social, $user->getSocialId());
        //2. Social Id exists
        if($socialData) {
            $userData = $this->select(array(
                    'id' => $socialData['userId']
                )
            )->current();
            $role = $this->roleTable->fetchRoleById($userData['role']);
            $user->setId($userData['id']);
            $user->setRole($role);

            $this->userEmailTable->checkEmail($user->getEmail(), $user->getId());
            $this->updateLastLoggedIn($user);
            return $user;
        }

        $userId = $this->userEmailTable->fetchUserIdByEmail($user->getEmail());
        //3. Email exists
        if($userId) {
            $userData = $this->select(array(
                    'id' => $userId
                )
            )->current();
            $role = $this->roleTable->fetchRoleById($userData['role']);
            $user->setId($userData['id']);
            $user->setRole($role);

            $this->userSocialTable->checkUserIdAndSocial($user->getId(), $social, $user->getSocialId());
            $this->updateLastLoggedIn($user);
            return $user;
        }

        //4. None exists
        $isAffected = (bool) $this->insert(array(
                'created_on'  => date('Y-m-d H:i:s')
            )
        );
        if(!$isAffected) {
            throw new \RuntimeException('Failed insertion in "user" Table');
        }
        $userId = $this->getLastInsertValue();
        $userData = $this->select(array(
                'id' => $userId
            )
        )->current();
        $role = $this->roleTable->fetchRoleById($userData['role']);
        $user->setId($userData['id']);
        $user->setRole($role);

        $this->userEmailTable->checkEmail($user->getEmail(), $user->getId());
        $this->userSocialTable->checkUserIdAndSocial($user->getId(), $social, $user->getSocialId());
        $this->updateLastLoggedIn($user);
        return $user;
    }

    /**
     * Fetches data of all users
     *
     * @return array
     */
    public function fetchAllUsers()
    {
        $select = $this->getSql()
                       ->select()
                       ->order('created_on DESC');
        $rowset = $this->selectWith($select);

        $users = array();

        foreach($rowset as $row) {
            $users[$row['id']] = array(
                'role'  => $row['role'],
                'createdOn' => $row['created_on'],
                'lastLoggedIn' => $row['last_logged_in']
            );
        }

        $rowset = $this->userEmailTable->select();

        foreach($rowset as $row) {
            $users[$row['user_id']]['emails'][] = $row['email'];
        }

        $rowset = $this->userSocialTable->select();

        foreach($rowset as $row) {
            $users[$row['user_id']]['socials'][$row['social']] = $row['social_id'];
        }

        return $users;
    }

    /**
     * Returns the total number of users stored in the database
     *
     * @return int
     */
    public function getTotalCount()
    {
        $select = $this->getSql()
            ->select()
            ->columns(array('count' => new Expression('COUNT(*)')));
        $row = $this->selectWith($select)->current();
        return $row['count'];
    }

    private function updateLastLoggedIn(User $user)
    {
        $this->update(array(
                'last_logged_in' => date('Y-m-d H:i:s')
            ), array(
                'id' => $user->getId()
            )
        );
    }

    private function updateUserFromData(User &$user, $userData, $socialData)
    {
        $role = $this->roleTable->fetchRoleById($userData['role']);
        if(null === $role) {
            throw new \RuntimeException('Invalid User role from the database for user with id: ' . $userData['id']);
        }
        $user->setId($userData['id'])
             ->setRole($role);
    }

    /**
     * Setter for $this->userSocialTable
     *
     * @param \User\table\UserSocialTable $userSocialTable
     */
    public function setUserSocialTable(UserSocialTable $userSocialTable)
    {
        $this->userSocialTable = $userSocialTable;
    }

    /**
     * @param RoleTable $roleTable
     */
    public function setRoleTable(RoleTable $roleTable)
    {
        $this->roleTable = $roleTable;
    }

    public function setUserEmailTable(UserEmailTable $userEmailTable)
    {
        $this->userEmailTable = $userEmailTable;
    }

}