<?php
namespace User\Service;

use User\Entity\User;
use Zend\Session\Container;

abstract class AbstractAuthProvider
{
    /** @var string */
    protected $clientId = '';

    /** @var string */
    protected $clientSecret = '';

    /** @var string */
    protected $redirectUrl = '';

    /** @var array|string */
    protected $scopes;

    /** @var string */
    protected $error;

    public function __construct($clientId, $clientSecret, $redirectUrl, $scopes)
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
     * @param array|string $scopes
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    protected function persist(User $user)
    {
        $user->setId(1);
        $container = new Container('user');
        $container->user = $user;
        return true;
    }

    abstract public function getLoginUrl();

    abstract public function authenticate();
} 