<?php
namespace Article\Table;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface,
    Zend\Db\Adapter\Adapter;

class ArticleTableServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return ArticleTable
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Adapter $adapter */
        $adapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');

        /** @var JournalTable $journalTable */
        $journalTable = $serviceLocator->get('Article\Table\Journal');

        /** @var AuthorTable $authorTable */
        $authorTable = $serviceLocator->get('Article\Table\Author');

        /** @var AbstractParaTable $abstractTable */
        $abstractTable = $serviceLocator->get('Article\Table\AbstractPara');

        /** @var ArticleTable $table */
        $table = new ArticleTable();
        $table->setDbAdapter($adapter);
        $table->setAbstractParaTable($abstractTable);
        $table->setJournalTable($journalTable);
        $table->setAuthorTable($authorTable);
        return $table;
    }

} 