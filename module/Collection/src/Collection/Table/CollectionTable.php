<?php
namespace Collection\Table;

use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\Sql\Expression;
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

    /**
     * Fetches collection by id
     *
     * @param int $id
     * @param int $userId
     * @return Collection|null
     */
    public function fetchCollectionByIdAndUserId($id = 0, $userId = 0)
    {
        $id = (int) $id;
        $userId = (int) $userId;
        $data = $this->select(array(
                'id' => $id,
                'user_id' => $userId
            )
        )->current();
        if(!$data) {
            return null;
        }
        $createdOn = null == $data['created_on'] ?
            null : new \DateTime($data['created_on']);
        $updatedOn = null == $data['updated_on'] ?
            null : new \DateTime($data['updated_on']);

        $articles = $this->collectionArticleTable
                         ->fetchArticlesByCollectionId($id);
        $collection = new Collection(
            $articles,
            (int) $data['id'],
            $data['name'],
            $createdOn,
            $updatedOn,
            false,
            $userId
        );
        return $collection;
    }

    /**
     * Returns recently modified collections
     *
     * @param int $userId
     * @param int $current
     * @return array
     */
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
                'id'   => (int) $row['id'],
                'name' => $row['name']
            );
        }
        return $collections;
    }

    /**
     * Returns all the collections belonging to the user
     *
     * @param int $userId
     * @return array
     */
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
                'id'   => (int) $row['id'],
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

    /**
     * Saves or updates the collection in the database
     *
     * @param Collection $collection
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function saveCollection(Collection $collection)
    {
        // Get the id and user id
        $id = $collection->getId();
        $userId = $collection->getUserId();

        if(0 == $userId) {
            throw new \InvalidArgumentException('User Id required to create a new collection');
        }

        if(0 != $id) { // Update the collection
            $savedCollection = $this->select(array(
                    'id' => $id
                )
            )->current();
            if(!$savedCollection) {
                throw new \InvalidArgumentException(
                    'The collection to update identified by id: ' . $id . 'doesn\'t exist'
                );
            }
            $updatedOn = new \DateTime();
            $data = array(
                'name' => $collection->getName(),
                'updated_on' => $updatedOn->format('Y-m-d H:i:s')
            );
            $result = (bool) $this->update($data, array('id' => $id));
            if(!$result) {
                return false;
            }
            $collection->setUpdatedOn($updatedOn);
        } else {    // Create new collection
            $createdOn = new \DateTime();
            $data = array(
                'name'     => $collection->getName(),
                'user_id'  => $collection->getUserId(),
                'created_on' => $createdOn->format('Y-m-d H:i:s')
            );
            $result = (bool) $this->insert($data);
            if(!$result) {
                return false;
            }
            $id = $this->getLastInsertValue();
            $collection->setId($id);
            $collection->setCreatedOn($createdOn);
        }

        $articles = $collection->getItems();

        $result = $this->collectionArticleTable->saveArticles($articles, $collection->getId());
        if(!$result) {
            $this->delete(array('id' => $id));
            return false;
        }
        // Reset the edit status
        $collection->resetEdit();
        return true;
    }

    public function deleteCollectionByIdAndUserId($id = 0, $userId = 0)
    {
        $id = (int) $id;
        $userId = (int) $userId;
        $collection = $this->fetchCollectionByIdAndUserId($id, $userId);
        if($collection) {
            $this->collectionArticleTable->delete(array('collection_id' => $collection->getId()));
            return (bool) $this->delete(array('id' => $collection->getId()));
        }
        return false;
    }

    public function fetchCollectionCountsByUser()
    {
        $select = $this->getSql()
                       ->select()
                       ->columns(array('user_id', 'count' => new Expression('COUNT(*)')))
                       ->group('user_id');
        $rowset = $this->selectWith($select);
        $collectionCounts = array();
        foreach($rowset as $row) {
            $collectionCounts[$row['user_id']] = array(
                'collections' => $row['count']
            );
        }
        return $collectionCounts;
    }

    public function getTotalCount()
    {
        $select = $this->getSql()
                       ->select()
                       ->columns(array('count' => new Expression('COUNT(*)')));
        $row = $this->selectWith($select)->current();
        return $row['count'];
    }
}