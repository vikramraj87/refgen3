<?php
namespace Collection\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Collection\Table\CollectionTable;
use Authentication\Service\AuthenticationService;

class CollectionHelper extends AbstractHelper
{
    private $table;

    private $authService;

    public function __construct(CollectionTable $table, AuthenticationService $authService)
    {
        $this->table = $table;
        $this->authService = $authService;
    }

    public function __invoke()
    {
        return $this;
    }

    public function recents($current = 0)
    {
        if(!$this->authService->hasIdentity()) {
            throw new \RuntimeException('No identity found');
        }
        return $this->table->fetchRecentByUserId(
            $this->authService->getIdentity()->getId(), $current
        );
    }

    public function allCollections()
    {
        if(!$this->authService->hasIdentity()) {
            throw new \RuntimeException('No identity found');
        }
        return $this->table->fetchEntireByUserId($this->authService->getIdentity()->getId());
    }

} 