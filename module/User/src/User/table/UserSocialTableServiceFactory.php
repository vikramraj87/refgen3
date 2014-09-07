<?php
namespace User\table;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface;
use User\Table\UserSocialTable;
class UserSocialTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SocialTable $socialTable */
        $socialTable = $serviceLocator->get('User\Table\Social');

        $table = new UserSocialTable();
        $table->setSocialTable($socialTable);
        return $table;
    }

} 