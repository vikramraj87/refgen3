<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 25/05/14
 * Time: 5:10 PM
 */

namespace Article\Entity;


class Author
{
    /** @var int */
    protected $id = 0;

    /** @var string */
    protected $lastName = '';

    /** @var string */
    protected $foreName = '';

    /** @var string */
    protected $initials = '';

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
     * @param string $foreName
     */
    public function setForeName($foreName)
    {
        $this->foreName = $foreName;
    }

    /**
     * @return string
     */
    public function getForeName()
    {
        return $this->foreName;
    }

    /**
     * @param string $initials
     */
    public function setInitials($initials)
    {
        $this->initials = $initials;
    }

    /**
     * @return string
     */
    public function getInitials()
    {
        return $this->initials;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Returns the name suitable for vancouver citation.
     *
     * @return string
     */
    public function getName()
    {
        return $this->lastName . ' ' . $this->initials;
    }

    /**
     * Factory function to create an author object from array of data
     *
     * @param array $data
     * @return Author
     */
    static public function createFromArray(array $data = array())
    {
        $id       = isset($data['id'])        ? (int) $data['id']  : 0;
        $lastName = isset($data['last_name']) ? $data['last_name'] : '';
        $foreName = isset($data['fore_name']) ? $data['fore_name'] : '';
        $initials = isset($data['initials'])  ? $data['initials']  : '';

        $author = new self();
        $author->setId($id);
        $author->setLastName($lastName);
        $author->setForeName($foreName);
        $author->setInitials($initials);
        return $author;
    }

    /**
     * Serializes the object to array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'last_name' => $this->lastName,
            'fore_name' => $this->foreName,
            'initials'  => $this->initials
        );
    }
}