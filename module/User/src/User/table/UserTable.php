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

    /** @var UserSocialTable */
    private $userSocialTable;

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

    public function checkUser(User $user, $social = 0)
    {
        $social = (int) $social;

        // 1. User Id is provided
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
                $user->getSocialId(),
                $user->getPictureLink()
            );
            $user = $this->userFromData($userData, $socialData);
            $this->updateLastLoggedIn($user);
            return $user;
        }

        // 2. Social Id and User Id already there in the User Social Table
        $socialData = $this->userSocialTable->fetchBySocialAndSocialId($social, $user->getSocialId());
        if($socialData) {
            $userData = $this->select(array(
                    'id' => $socialData['userId']
                )
            )->current();
            $user = $this->userFromData($userData, $socialData);
            $this->updateLastLoggedIn($user);
            return $user;
        }

        // 3. Email is already registered but the social data is new
        $userData = $this->select(
            array(
                'email' => $user->getEmail()
            )
        )->current();
        if($userData) {
            $socialData = $this->userSocialTable->checkUserIdAndSocial(
                $userData['id'],
                $social,
                $user->getSocialId(),
                $user->getPictureLink()
            );
            $user = $this->userFromData($userData, $socialData);
            $this->updateLastLoggedIn($user);
            return $user;
        }

        // 4. None of the entries found. Create new user
        $data  = array(
            'email'       => $user->getEmail(),
            'first_name'  => $user->getFirstName(),
            'middle_name' => $user->getMiddleName(),
            'last_name'   => $user->getLastName(),
            'name'        => $user->getName(),
            'created_on'  => date('Y-m-d H:i:s')
        );
        $numRowsAffected = $this->insert($data);
        if(!$numRowsAffected) {
            throw new \RuntimeException('Failed insertion in "user" Table');
        }
        return $this->checkUser($user, $social);
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

    private function userFromData($userData, $socialData)
    {
        $user = new User();
        $user->setId($userData['id'])
             ->setEmail($userData['email'])
             ->setFirstName($userData['first_name'])
             ->setMiddleName($userData['middle_name'])
             ->setLastName($userData['last_name'])
             ->setName($userData['name'])
             ->setRole($userData['role'])
             ->setSocialId($socialData['socialId'])
             ->setPictureLink($socialData['picture']);
        return $user;
    }

    /**
     * @param \User\table\UserSocialTable $userSocialTable
     */
    public function setUserSocialTable(UserSocialTable $userSocialTable)
    {
        $this->userSocialTable = $userSocialTable;
    }


}