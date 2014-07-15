<?php
namespace Authorization\Service;

use Authorization\Role\Guest,
    Authorization\Role\Member;
use Authentication\Service\AuthenticationService;
use Zend\Permissions\Acl\Acl,
    Zend\Permissions\Acl\Role\RoleInterface;

class AuthorizationService {
    /** @var RoleInterface[] */
    private $roles;

    /** @var \Zend\Permissions\Acl\Acl */
    private $acl;

    /** @var AuthenticationService */
    private $authService;

    public function __construct(array $config = array(), AuthenticationService $authService)
    {
        $this->roles = array(new Guest(), new Member());
        $this->acl = new Acl();
        $this->authService = $authService;
        $this->init($config);
    }

    public function hasResource($resource)
    {
        return $this->acl->hasResource($resource);
    }

    public function hasAccess($resource, $privilege = null)
    {
        return $this->acl->isAllowed($this->getRole()->getRoleId(), $resource, $privilege);
    }

    private function getRole()
    {
        $role = new Guest();
        if($this->authService->hasIdentity()) {
            $role = new Member();
        }
        return $role;
    }

    private function init(array $config = array())
    {
        if(empty($config)) {
            return;
        }
        $acl = $this->acl;
        foreach($this->roles as $role) {
            $acl->addRole($role);
            /** @var RoleInterface $role */
            if(array_key_exists($role->getRoleId(), $config)) {
                foreach($config[$role->getRoleId()] as $resource => $privileges) {
                    if(is_array($privileges)) {
                        if(!$acl->hasResource($resource)) {
                            $acl->addResource($resource);
                        }
                    } else {
                        $resource = $privileges;
                        $privileges = null;
                        if(!$acl->hasResource($resource)) {
                            $acl->addResource($resource);
                        }
                    }
                    $acl->allow($role, $resource, $privileges);
                }
            }
        }

    }
} 