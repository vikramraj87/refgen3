<?php
namespace Collection\Table;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class CollectionTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return CollectionTable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var CollectionArticleTable $collectionArticleTable */
        $collectionArticleTable = $serviceLocator->get('Collection\Table\CollectionArticle');

        /** @var CollectionTable $table */
        $table = new CollectionTable();
        $table->setCollectionArticleTable($collectionArticleTable);
        return $table;
    }

} 