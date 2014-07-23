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

        /** @var UserTable $table */
        $table = new UserTable();
        $table->setDbAdapter($adapter);
        $table->setUserSocialTable($userSocialTable);
        return $table;
    }

} 