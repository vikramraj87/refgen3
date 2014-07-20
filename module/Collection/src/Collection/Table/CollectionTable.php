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

    public function fetchCollectionById($id = 0, $userId = 0)
    {
        $id = (int) $id;
        $rowset = $this->select(array(
                'id' => $id,
                'user_id' => $userId
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

    public function fetchRecentByUserId($userId = 0, $current = 0)
    {
        $current = (int) $current;

        $where = array();
        $where['user_id'] = $userId;
        if(0 != $current) {
            $where['id != ?'] = $current;
        }

        $select = $this->getSql()
                       ->select()
                       ->where($where)
                       ->order('updated_on DESC')
                       ->limit(10);
        $rowset = $this->selectWith($select);
        $collections = array();
        foreach($rowset as $row) {
            $collections[] = array(
                'id'   => $row['id'],
                'name' => $row['name']
            );
        }
        return $collections;
    }

    public function fetchEntireByUserId($userId = 0)
    {
        $select = $this->getSql()
                       ->select()
                       ->where(array('user_id' => $userId))
                       ->order('updated_on DESC');
        $rowset = $this->selectWith($select);
        $collections = array();
        foreach($rowset as $row) {
            $collections[] = array(
                'id' => $row['id'],
                'name' => $row['name']
            );
        }
        return $collections;
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

        if(0 == $userId) {
            throw new \InvalidArgumentException('User Id required to create a new collection');
        }

        $data = array(
            'name'     => $collection->getName(),
            'user_id'  => $userId,
            'created_on' => date('Y-m-d H:i:s')
        );

        $result = (bool) $this->insert($data);
        if(!$result) {
            return false;
        }
        $id = $this->getLastInsertValue();
        $collection->setId($id);

        $articles = $collection->getArticles();

        $result = $this->collectionArticleTable()->saveArticles($articles, $id);
        if(!$result) {
            /*
             * Todo: Check deleting collection to check for integrity constraints.
             * Todo: Potential problem. If not deleted properly, won't allow people to
             * Todo: use the same name when they retry
             */
            $this->delete(array('id' => $id));
            return false;
        }
        return $this->fetchCollectionById($id, $userId);
    }

    public function updateCollection(Collection $collection)
    {
        $id = $collection->getId();

        $savedCollection = $this->select(array(
                'id' => $id
            )
        )->current();
        if(!$savedCollection) {
            throw new \InvalidArgumentException(
                'The collection to update identified by id: ' . $id . 'doesn\'t exist'
            );
        }

        $data = array(
            'name' => $collection->getName(),
            'updated_on' => date('Y-m-d H:i:s')
        );
        $result = (bool) $this->update($data, array('id' => $id));
        if(!$result) {
            return false;
        }

        $result = $this->collectionArticleTable()->saveArticles($collection->getArticles(), $id);
        if(!$result) {
            /*
             * Todo: Potential problem. If not deleted properly, won't allow people to
             * Todo: use the same name when they retry
             */
            // $this->delete(array('id' => $id));
            return false;
        }
        return $this->fetchCollectionById($id, $savedCollection['user_id']);

    }

    public function deleteCollectionById($id = 0, $userId = 0)
    {
        $id = (int) $id;
        $userId = (int) $userId;
        $collectionData = $this->select(array(
                'id' => $id,
                'user_id' => $userId
            )
        )->current();
        if($collectionData) {
            $this->collectionArticleTable()->delete(array(
                    'collection_id' => $collectionData['id']
                )
            );
            $this->delete(array(
                    'id' => $collectionData['id']
                )
            );
        }
    }

    private function collectionArticleTable()
    {
        if(null === $this->collectionArticleTable) {
            $this->collectionArticleTable = new CollectionArticleTable();
            $this->collectionArticleTable->setDbAdapter($this->adapter);
        }
        return $this->collectionArticleTable;
    }
}