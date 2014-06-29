<?php
namespace Collection\Service;

use Zend\Session\Container;
use Collection\Entity\Collection,
    Collection\Table\CollectionTable;
use Article\Table\ArticleTable,
    Article\Entity\Article;
class CollectionService
{
    /** @var Collection */
    private $activeCollection;

    /** @var Container */
    private $container;

    /** @var ArticleTable */
    private $articleTable;

    /** @var CollectionTable */
    private $table;

    public function addArticle()
    {

    }

    public function removeArticle()
    {

    }


    public function init()
    {
        $container = $this->container();
        if(!$container->offsetExists('data')) {
            return;
        }
        $data = $container->offsetGet('data');
        $this->deSerialize($data);
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function setArticleTable(ArticleTable $table) {
        $this->articleTable = $table;
    }

    public function setTable(CollectionTable $table) {
        $this->table = $table;
    }

    private function container()
    {
        if(null === $this->container) {
            $this->container = new Container('collection');
        }
        return $this->container;
    }

    private function deSerialize(array $data = array())
    {

        $articles = $this->articleTable->fetchArticlesByIds($data['articleIds']);
        $collection = new Collection();
        $collection->setId($data['id']);
        $collection->setName($data['name']);
        $collection->setArticles($articles);
        $this->activeCollection = $collection;
    }

    private function serialize()
    {
        $collection = $this->activeCollection;
        $articleIds = array();
        foreach($this->activeCollection->getArticles() as $article) {
            /** @var Article $article */
            $articleIds[] = $article->getId();
        }
        $data = array(
            'id'         => $collection->getId(),
            'name'       => $collection->getName(),
            'articleIds' => $articleIds
        );
        $this->container()->offsetSet('data', $data);
    }

    public function getActiveCollection()
    {
        return $this->activeCollection;
    }
} 