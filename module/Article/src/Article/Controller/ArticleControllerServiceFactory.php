<?php
namespace Article\Controller;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Article\Table\ArticleTable;

class ArticleControllerServiceFactory implements FactoryInterface
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
        $controller = new ArticleController();
        $controller->setTable($sl->get('Article\Table\Article'));
        return $controller;
    }

} 