<?php
namespace Collection\Service;

use DateTime;
use Zend\Session\Container;
use Collection\Entity\Collection;
use Article\Table\ArticleTable,
    Article\Entity\Article;
class CollectionService implements \Countable
{
    /** @var Collection */
    private $activeCollection;

    /** @var Container */
    private $container;

    /** @var ArticleTable */
    private $articleTable;

    public function __construct(ArticleTable $articleTable, Container $container = null)
    {
        $this->articleTable = $articleTable;
        $this->container = $container ?: new Container('collection');
        if(!$this->container->offsetExists('data')) {
            $this->activeCollection = new Collection();
        } else {
            $data = $container->offsetGet('data');
            $this->deSerialize($data);
        }
    }

    /**
     * De-serializes the array to object
     *
     * @param array $data
     */
    private function deSerialize(array $data = array())
    {
        $articles = $this->articleTable->fetchArticlesByIds(explode(',',$data['itemIds']));

        $collection = new Collection(
            $articles,
            $data['id'],
            $data['name'],
            $data['createdOn'],
            $data['updatedOn'],
            $data['edited']
        );

        $this->activeCollection = $collection;
    }

    /**
     * Serializes the object to array and stores it in
     * session container
     */
    private function serialize()
    {
        $this->container->offsetSet('data', $this->activeCollection->serialize());
    }

    /**
     * Adds an article identified by id to the collection
     *
     * @param int $articleId
     * @return Article|null
     */
    public function addArticle($articleId = 0)
    {
        $articleId = (int) $articleId;
        if(!$articleId) {
            return null;
        }

        $article = $this->articleTable->fetchArticleById($articleId);
        if($article) {
            $this->activeCollection->addItem($article);
            $this->serialize();
        }
        return $article;
    }

    /**
     * Removes multiple articles from the collection
     *
     * @param array $articleIds
     */
    public function removeArticles(array $articleIds = array())
    {
        $this->activeCollection->removeItems($articleIds);
        $this->serialize();
    }

    /**
     * Removes single article from the collection
     *
     * @param int $articleId
     */
    public function removeArticle($articleId = 0)
    {
        $articleId = (int) $articleId;
        if(!$articleId) {
            return;
        }
        $this->activeCollection->removeItem($articleId);
        $this->serialize();
    }

    /**
     * Moves up the selected items in the collection
     *
     * @param array $items
     */
    public function moveUpItems($items = array())
    {
        $this->activeCollection->moveUpItems($items);
        $this->serialize();
    }

    /**
     * Moves down the selected items in the collection
     *
     * @param array $items
     */
    public function moveDownItems($items = array())
    {
        $this->activeCollection->moveDownItems($items);
        $this->serialize();
    }

    /**
     * Sorts the items in the order mentioned
     *
     * @param array $ids
     */
    public function sortItems($ids = array())
    {
        $this->activeCollection->sort($ids);
        $this->serialize();
    }

    /**
     * Returns whether the item exists in the collection
     *
     * @param int $itemId
     * @return bool
     */
    public function hasItem($itemId = 0)
    {
        return $this->activeCollection->hasItem($itemId);
    }

    /**
     * Returns whether the active collection is edited
     *
     * @return bool
     */
    public function isEdited()
    {
        return $this->activeCollection->isEdited();
    }

    /**
     * Returns the items of the collection
     *
     * @return Collection\ItemInterface[]
     */
    public function getItems()
    {
        return $this->activeCollection->getItems();
    }

    /**
     * Returns the created on date
     *
     * @return DateTime
     */
    public function getCreatedOn()
    {
        return $this->activeCollection->getCreatedOn();
    }

    /**
     * Returns the updated on date
     *
     * @return DateTime
     */
    public function getUpdatedOn()
    {
        return $this->activeCollection->getUpdatedOn();
    }

    /**
     * Returns the id of the active collection
     *
     * @return int
     */
    public function getId()
    {
        return $this->activeCollection->getId();
    }

    /**
     * Returns the name of the active collection
     *
     * @return string
     */
    public function getName()
    {
        return $this->activeCollection->getName();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->activeCollection);
    }

    /**
     * Sets the passed collection as active collection
     *
     * @param Collection $collection
     */
    public function setCollection(Collection $collection = null)
    {
        if(null == $collection) {
            $collection = new Collection();
        }
        $this->activeCollection = $collection;
        $this->serialize();
    }
}