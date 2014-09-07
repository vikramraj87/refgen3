<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 28/06/14
 * Time: 11:20 PM
 */

namespace Collection\Table;

use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface;
use Article\Table\ArticleTable,
    Article\Entity\Article;

class CollectionArticleTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'collection_articles';

    /** @var ArticleTable */
    protected $articleTable;

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
     * Fetches articles belonging to a collection
     * identified by the collection id
     *
     * @param int $collectionId
     * @return array
     */
    public function fetchArticlesByCollectionId($collectionId = 0)
    {
        $collectionId = (int) $collectionId;
        $select = $this->getSql()->select()
                                 ->where(array('collection_id' => $collectionId))
                                 ->order('position');
        $rowset = $this->selectWith($select);
        $articleIds = array();
        foreach($rowset as $row) {
            $articleIds[] = $row['article_id'];
        }
        return $this->articleTable->fetchArticlesByIds($articleIds);
    }

    /**
     * Saves articles belonging to the collection
     *
     * @param array $articles
     * @param $collectionId
     * @return bool
     */
    public function saveArticles(array $articles = array(), $collectionId)
    {
        $collectionId = (int) $collectionId;
        $this->delete(array(
                'collection_id' => $collectionId
            )
        );
        if(empty($articles)) {
            return true;
        }
        $pos = 1;
        foreach($articles as $article) {
            /** @var Article $article */

            $data = array(
                'collection_id' => $collectionId,
                'article_id'    => $article->getId(),
                'position'      => $pos
            );
            $this->insert($data);
            $pos++;
        }
        return true;
    }

    /**
     * @param \Article\Table\ArticleTable $articleTable
     */
    public function setArticleTable(ArticleTable $articleTable)
    {
        $this->articleTable = $articleTable;
    }
}