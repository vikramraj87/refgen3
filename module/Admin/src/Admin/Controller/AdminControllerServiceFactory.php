<?php
namespace Admin\Controller;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;
use User\Table\UserTable,
    Collection\Table\CollectionTable,
    Troubleshooting\Table\ExceptionTable,
    Article\Table\ArticleTable;
class AdminControllerServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();

        /** @var UserTable $userTable */
        $userTable = $sl->get('User\Table\User');

        /** @var CollectionTable $collectionTable */
        $collectionTable = $sl->get('Collection\Table\Collection');

        /** @var ExceptionTable $exceptionTable */
        $exceptionTable = $sl->get('Troubleshooting\Table\Exception');

        /** @var ArticleTable $articleTable */
        $articleTable = $sl->get('Article\Table\Article');

        /** @var AdminController $controller */
        $controller = new AdminController();
        $controller->setUserTable($userTable);
        $controller->setCollectionTable($collectionTable);
        $controller->setExceptionTable($exceptionTable);
        $controller->setArticleTable($articleTable);
        return $controller;
    }

} 