<?php
namespace Collection\Table;

use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface;
use Collection\Entity\Collection;
use Collection\Table\CollectionArticleTable;

class CollectionTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'collections';

    /** @var CollectionArticleTable */
    protected $collectionArticleTable;

    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     * @return AdapterAwareInterface
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchCollectionById($id = 0)
    {
        $id = (int) $id;
        $rowset = $this->select(array(
                'id' => $id
            )
        );
        $data = $rowset->current();
        if(!$data) {
            return null;
        }
        $createdOn = null == $data['created_on'] ?
            null : new \DateTime($data['created_on']);
        $updatedOn = null == $data['updated_on'] ?
            null : new \DateTime($data['updated_on']);

        $collection = new Collection();
        $collection->setId($data['id']);
        $collection->setName($data['name']);
        $collection->setCreatedOn($createdOn);
        $collection->setUpdatedOn($updatedOn);

        $articles = $this->collectionArticleTable()
                         ->fetchArticlesByCollectionId($collection->getId());
        $collection->setArticles($articles);
        return $collection;
    }

    /**
     * @param \Collection\Table\CollectionArticleTable $collectionArticleTable
     */
    public function setCollectionArticleTable($collectionArticleTable)
    {
        $this->collectionArticleTable = $collectionArticleTable;
    }

    public function saveCollection(Collection $collection, $userId = 0)
    {
        $userId = (int) $userId;
        $data = array(
            'id'       => $collection->getId(),
            'name'     => $collection->getName(),
            'user_id'  => $userId,
            'articles' => $collection->getArticles()
        );
        return $this->save($data);
    }

    private function collectionArticleTable()
    {
        if(null === $this->collectionArticleTable) {
            $this->collectionArticleTable = new CollectionArticleTable();
            $this->collectionArticleTable->setDbAdapter($this->adapter);
        }
        return $this->collectionArticleTable;
    }

    private function save(array $data)
    {
        $id       = $data['id'];
        $articles = $data['articles'];

        unset($data['id']);
        unset($data['articles']);

        if($id < 1) {
            if($data['user_id'] < 1) {
                throw new \InvalidArgumentException(
                    'User Id required to create a new collection'
                );
            }
            $result =  (bool) $this->insert($data);
            if(!$result) {
                return false;
            }
            $id = $this->getLastInsertValue();
        } else {
            $savedCollection = $this->select(array(
                    'id' => $id
                )
            )->current();
            if(!$savedCollection) {
                throw new \InvalidArgumentException(
                    'The collection to update identified by id: ' . $id . 'doesn\'t exist'
                );
            }
            $now = (new \DateTime())->format('Y-m-d H:i:s');
            $data['updated_on'] = $now;
            $data['user_id']    = $savedCollection['user_id'];
            $result = $this->update($data, array(
                    'id' => $id
                )
            );
            if(!$result) {
                return false;
            }
        }
        return $this->collectionArticleTable()->saveArticles($articles, $id);
    }
} 