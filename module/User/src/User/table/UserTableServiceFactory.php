<?php
namespace User\table;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Db\Adapter\Adapter;
use User\Table\UserTable;

class UserTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Adapter $adapter */
        $adapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');

        /** @var UserSocialTable $userSocialTable */
        $userSocialTable = $serviceLocator->get('User\Table\UserSocial');

        /** @var RoleTable $roleTable */
        $roleTable = $serviceLocator->get('User\Table\Role');

        /** @var UserEmailTable $userEmailTable */
        $userEmailTable = $serviceLocator->get('User\Table\UserEmail');

        /** @var UserTable $table */
        $table = new UserTable();
        $table->setDbAdapter($adapter);
        $table->setUserSocialTable($userSocialTable);
        $table->setRoleTable($roleTable);
        $table->setUserEmailTable($userEmailTable);
        return $table;
    }

} 