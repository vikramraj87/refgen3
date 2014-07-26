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
        $this->acl->addRole(new GenericRole('guest'));
        $this->acl->addRole(new GenericRole('user'));
        $this->acl->addRole(new GenericRole('moderator'), 'user');
        $this->acl->addRole(new GenericRole('admin'), 'moderator');

        /** Resources */
        $this->acl->addResource('Authentication\Controller\Authentication');
        $this->acl->addResource('Collection\Controller\Collection');
        $this->acl->addResource('Admin\Controller\Admin');

        /** Permissions */
        $this->acl->allow(
            'guest',
            'Authentication\Controller\Authentication',
            array('login', 'facebook', 'google')
        );
        $this->acl->allow('user', 'Authentication\Controller\Authentication', 'logout');
        $this->acl->allow('user', 'Collection\Controller\Collection');
        $this->acl->allow('moderator', 'Admin\Controller\Admin');

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