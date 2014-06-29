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
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\Adapter\Exception\InvalidQueryException;
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

    public function fetchArticlesByCollectionId($collectionId = 0)
    {
        $collectionId = (int) $collectionId;
        $rowset = $this->select(array(
                'collection_id' => $collectionId
            )
        );
        $articleIds = array();
        foreach($rowset as $row) {
            $articleIds[] = $row['article_id'];
        }
        return $this->articleTable()->fetchArticlesByIds($articleIds);
    }

    /**
     * @param \Article\Table\ArticleTable $articleTable
     */
    public function setArticleTable($articleTable)
    {
        $this->articleTable = $articleTable;
    }

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
            try {
                $this->insert($data);
            } catch(InvalidQueryException $e) {
                /**
                 * todo: raise an event for integrity constraint and log the error
                 */
                return false;
            } catch(\Exception $e) {
                /**
                 * todo: raise an event and log the unknown error
                 */
                return false;
            }
            $pos++;
        }
        return true;
    }

    /**
     * Lazy loading of article table
     *
     * @return ArticleTable
     */
    private function articleTable()
    {
        if(null === $this->articleTable) {
            $this->articleTable = new ArticleTable();
            $this->articleTable->setDbAdapter($this->adapter);
        }
        return $this->articleTable;
    }
} 