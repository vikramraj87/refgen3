<?php
namespace User\Entity;

use DateTime;
class User
{
    /** @var int */
    protected $id = 0;

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $email = '';

    /** @var string|null  */
    protected $facebookId = null;

    /** @var DateTime */
    protected $createdOn;

    /** @var DateTime */
    protected $lastLoggedIn;

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string|null $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \DateTime $createdOn
     */
    public function setCreatedOn(DateTime $createdOn = null)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param \DateTime $lastLoggedIn
     */
    public function setLastLoggedIn(DateTime $lastLoggedIn = null)
    {
        $this->lastLoggedIn = $lastLoggedIn;
    }

    /**
     * @return \DateTime
     */
    public function getLastLoggedIn()
    {
        return $this->lastLoggedIn;
    }


}