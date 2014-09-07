<?php
namespace Authorization\Service;

use Authentication\Service\AuthenticationService;
use Zend\Permissions\Acl\Acl,
    Zend\Permissions\Acl\Role\GenericRole;

class AuthorizationService {
    /** @var GenericRole[] */
    private $roles;

    /** @var \Zend\Permissions\Acl\Acl */
    private $acl;

    /** @var AuthenticationService */
    private $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->acl = new Acl();

        /** Roles */
        $this->acl->addRole(new GenericRole('Guest'));
        $this->acl->addRole(new GenericRole('User'));
        $this->acl->addRole(new GenericRole('Moderator'), 'User');
        $this->acl->addRole(new GenericRole('Admin'), 'Moderator');

        /** Resources */
        $this->acl->addResource('Authentication\Controller\Authentication');
        $this->acl->addResource('Collection\Controller\Collection');
        $this->acl->addResource('Admin\Controller\Admin');

        /** Permissions */
        $this->acl->allow(
            'Guest',
            'Authentication\Controller\Authentication',
            array('login', 'facebook', 'google', 'new-google')
        );
        $this->acl->allow('User', 'Authentication\Controller\Authentication', 'logout');
        $this->acl->allow('User', 'Collection\Controller\Collection');
        $this->acl->allow('Moderator', 'Admin\Controller\Admin');

        $this->authService = $authService;
    }

    public function hasResource($resource)
    {
        return $this->acl->hasResource($resource);
    }

    public function hasAccess($resource, $privilege = null)
    {
        return $this->acl->isAllowed($this->authService->getRoleId(), $resource, $privilege);
    }
}