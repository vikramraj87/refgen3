<?php
namespace User\Entity;

use DateTime;
class User
{
    /** @var int */
    private $id = 0;

    /** @var string */
    private $socialId = '';

    /** @var string */
    private $email = '';

    /** @var string */
    private $firstName = '';

    /** @var string */
    private $middleName = '';

    /** @var string */
    private $lastName = '';

    /** @var string */
    private $name = '';

    /** @var int */
    private $role = 1; // 1 - user 2 - moderator 3 - admin

    /** @var string */
    private $pictureLink = '';

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $middleName
     * @return $this
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $pictureLink
     * @return $this
     */
    public function setPictureLink($pictureLink)
    {
        $this->pictureLink = $pictureLink;
        return $this;
    }

    /**
     * @return string
     */
    public function getPictureLink()
    {
        return $this->pictureLink;
    }

    /**
     * @param string $socialId
     * @return $this
     */
    public function setSocialId($socialId)
    {
        $this->socialId = $socialId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSocialId()
    {
        return $this->socialId;
    }

    /**
     * @param int $role
     * return $this
     */
    public function setRole($role)
    {
        $this->role = (int) $role;
        return $this;
    }

    /**
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }
}