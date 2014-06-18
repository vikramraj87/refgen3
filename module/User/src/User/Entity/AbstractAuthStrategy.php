<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 17/06/14
 * Time: 4:05 PM
 */

namespace User\Entity;


abstract class AbstractAuthStrategy implements AuthStrategyInterface
{
    /** @var string */
    protected $clientId     = '';

    /** @var string */
    protected $clientSecret = '';

    /** @var string */
    protected $redirectUrl  = '';

    /** @var array */
    protected $scopes       = array();

    /**
     * @param string $clientId
     * @return $this
     */
    public function setClientId($clientId = '')
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @param string $clientSecret
     * @return $this
     */
    public function setClientSecret($clientSecret = '')
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * @param string $redirectUrl
     * @return $this
     */
    public function setRedirectUrl($redirectUrl = '')
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * @param array $scopes
     * @return $this
     */
    public function setScopes(array $scopes = array())
    {
        $this->scopes = $scopes;
        return $this;
    }


}