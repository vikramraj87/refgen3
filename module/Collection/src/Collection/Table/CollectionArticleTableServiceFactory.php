<?php
namespace Collection\Table;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;
use Article\Table\ArticleTable;

class CollectionArticleTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return CollectionArticleTable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ArticleTable $articleTable */
        $articleTable = $serviceLocator->get('Article\Table\Article');

        /** @var CollectionArticleTable $table */
        $table = new CollectionArticleTable();
        $table->setArticleTable($articleTable);
        return $table;
    }
}