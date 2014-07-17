<?php
namespace Collection\Service;

use Zend\Session\Container,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\ArrayAdapter;
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

    /** @var bool */
    private $edited = false;

    public function addArticle($articleId = 0)
    {
        $articleId = (int) $articleId;

        if(!$articleId) {
            return false;
        }

        $article = $this->articleTable->fetchArticleById($articleId);
        if(null == $article) {
            return false;
        }

        $preCount = count($this->activeCollection);
        $this->activeCollection->addArticle($article);
        $postCount = count($this->activeCollection);

        if($postCount > $preCount) {
            $this->edited = true;
        }
        $this->serialize();
        return true;
    }

    public function removeArticle($articleId = 0)
    {
        $articleId = (int) $articleId;

        if(!$articleId) {
            return false;
        }

        $preCount = count($this->activeCollection);
        $this->activeCollection->removeArticle($articleId);
        $postCount = count($this->activeCollection);

        if($preCount > $postCount) {
            $this->edited = true;
        }
        $this->serialize();
        return true;
    }


    public function init()
    {
        $container = $this->container();
        if(!$container->offsetExists('data')) {
            $this->activeCollection = new Collection();
            $this->edited = false;
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
        $this->edited = $data['edited'];
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
            'articleIds' => $articleIds,
            'edited'     => $this->edited
        );
        $this->container()->offsetSet('data', $data);
    }

    public function getActiveCollection()
    {
        return $this->activeCollection;
    }

    public function setCollection(Collection $collection = null)
    {
        if(null == $collection) {
            $collection = new Collection();
        }
        $this->activeCollection = $collection;
        $this->edited = false;
        $this->serialize();
    }

    public function isEdited()
    {
        return $this->edited;
    }

    /*
    public function saveChanges($name)
    {
        $this->getActiveCollection()->setName($name);
        $result = (bool) $this->table->saveCollection($this->getActiveCollection());
        if(!$result) {
            throw new \RuntimeException('Error saving collection');
        }
        $collection = $this->table->fetchCollectionById($result);
        $this->setCollection($collection);
        return $result;

    } */
} 