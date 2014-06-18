<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 17/06/14
 * Time: 4:02 PM
 */

namespace User\Entity;


interface AuthStrategyInterface
{
    /**
     * @param string $clientId
     * @return $this
     */
    public function setClientId($clientId = '');

    /**
     * @param string $clientSecret
     * @return $this
     */
    public function setClientSecret($clientSecret = '');

    /**
     * @param string $redirectUrl
     * @return $this
     */
    public function setRedirectUrl($redirectUrl = '');

    /**
     * @param array $scopes
     * @return $this
     */
    public function setScopes(array $scopes = array());

    /**
     * @return \User\Entity\User|null
     */
    public function getUser();
} 