<?php
namespace Authorization\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Authorization\Service\AuthorizationService;

class HasAccess extends AbstractHelper
{
    private $service;

    public function __construct(AuthorizationService $authService)
    {
        $this->service = $authService;
    }

    public function __invoke($resource, $privilege)
    {
        return $this->service->hasAccess($resource, $privilege);
    }
} 