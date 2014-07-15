<?php
namespace Authorization\Role;

use Zend\Permissions\Acl\Role\RoleInterface;
class Guest implements RoleInterface
{
    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId()
    {
        return 'guest';
    }

} 