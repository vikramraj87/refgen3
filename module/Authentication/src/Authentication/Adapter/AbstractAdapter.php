<?php
namespace Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use User\Entity\User,
    User\Table\UserTable;

abstract class AbstractAdapter implements AdapterInterface
{
    /** @var int */
    protected $id = -1;

    /** @var string */
    protected $clientId = '';

    /** @var string */
    protected $clientSecret = '';

    /** @var string */
    protected $redirectUrl = '';

    /** @var array */
    protected $scopes = array();

    /** @var string */
    protected $loginUrl = '';

    public function __construct($clientId, $clientSecret, $redirectUrl, array $scopes = array())
    {
        $this->setClientId($clientId);
        $this->setClientSecret($clientSecret);
        $this->setRedirectUrl($redirectUrl);
        $this->setScopes($scopes);
        $this->init();
    }

    protected function init() {}

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @param array $scopes
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->loginUrl;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $data
     * @return User
     */
    protected function identityFromData(array $data = array())
    {
        $identity = new User();
        $identity->setSocialId($data['socialId'])
                 ->setEmail($data['email'])
                 ->setFirstName($data['firstName'])
                 ->setLastName($data['lastName'])
                 ->setMiddleName($data['middleName'])
                 ->setName($data['name'])
                 ->setPictureLink($data['picture']['link']);
        return $identity;
    }
}