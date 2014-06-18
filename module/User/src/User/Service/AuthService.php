<?php
namespace User\Service;

use User\Entity\AuthStrategyInterface;

class AuthService
{
    /** @var \User\Entity\AuthStrategyInterface */
    private $strategy;

    public function __construct(AuthStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return null|\User\Entity\User
     */
    public function getUser()
    {
        $user = $this->strategy->getUser();
        return $user;
    }
} 